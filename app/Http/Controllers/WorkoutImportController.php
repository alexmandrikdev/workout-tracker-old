<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkoutsImport\GetSheetsRequest;
use App\Imports\DaysImport;
use App\Imports\SheetNamesImport;
use App\Jobs\WorkoutImportJob;
use App\Models\Exercise;
use App\Models\Set;
use App\Models\Workout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class WorkoutImportController extends Controller
{
    public function getSheets(GetSheetsRequest $request)
    {
        $sheetNamesImport = new SheetNamesImport();

        $file = $request->file('excel');
        $excelPath = $file->storeAs('excels', Auth::id() . '.' . $file->extension());

        Excel::import($sheetNamesImport, $excelPath);

        $days = $sheetNamesImport->getSheetNames();

        $dateDelimeters = '(\.|-|/)';

        $days = $days->filter(function ($day) use ($dateDelimeters) {
            return preg_match('((\d{4}' . $dateDelimeters . '\d{2}' . $dateDelimeters . '\d{2})|
                (\d{2}' . $dateDelimeters . '\d{2}' . $dateDelimeters . '\d{4}))', $day);
        });

        $daysImport = new DaysImport($days);

        Excel::import($daysImport, $excelPath);

        $days = $daysImport->getDays();

        $days = $days->map(function ($workouts, $date) {
            return $workouts->map(function ($workout) use ($date) {
                return [
                    'name' => $workout,
                    'isImported' => Workout::where([
                        'date' => $date,
                        'name' => Str::title($workout)
                    ])
                        ->exists()
                ];
            });
        });

        $importedDays = $days->filter(function ($workouts) {
            return $workouts->count() == $workouts->where('isImported', true)->count();
        });

        $days = $days->diff($importedDays);

        return back()->with(['days' => $days, 'importedDays' => $importedDays, 'excelPath' => $excelPath]);
    }

    public function import(Request $request)
    {
        $days = $request->days;
        $dayStatuses = collect();

        for ($i = 0; $i < $request->daysCount; $i++) {
            if ($request->get('day' . $i . 'Status')) {
                $dayStatuses[$i] = $request->get('day' . $i . 'Status');
            }
        }

        $days = $dayStatuses->map(function ($day, $dayKey) use ($days, $request) {
            $dayWorkouts = collect($request->get('day' . $dayKey . 'Workouts'))
                ->filter(function ($dayWorkout, $dayWorkoutKey) use ($dayKey, $request) {
                    return $request->get('day' . $dayKey . 'Workout' . $dayWorkoutKey . 'Status') == 'on';
                })
                ->values();

            return [$days[$dayKey] => $dayWorkouts];
        });

        $sheetNames = collect($request->days)->filter(function ($day, $dayKey) use ($request) {
            return $request->get('day' . $dayKey . 'Status') == 'on';
        })->values();

        Workout::whereIn('date', $sheetNames)->update([
            'imported' => false
        ]);

        $days = $days->collapse();

        WorkoutImportJob::dispatch($sheetNames, $days, $request->excelPath, auth()->id());

        return back()->with([
            'status' => 'import_in_progress',
            'days' => $sheetNames
        ]);
    }

    public function getImportProgress(Request $request)
    {
        return Workout::select('date')
            ->whereIn('date', $request->days)
            ->where('imported', true)
            ->get()
            ->pluck('date')
            ->unique()
            ->count();
    }
}
