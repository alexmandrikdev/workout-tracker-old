<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkoutsImport\GetSheetsRequest;
use App\Imports\DaysImport;
use App\Imports\SheetNamesImport;
use App\Imports\WorkoutsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class WorkoutImportController extends Controller
{
    public function getSheets(GetSheetsRequest $request)
    {
        $sheetNamesImport = new SheetNamesImport();

        $excelPath = $request->file('excel')->store('excels');

        Excel::import($sheetNamesImport, $excelPath);

        $days = $sheetNamesImport->getSheetNames();

        $dateDelimeters = '(\.|-|/)';

        $days = $days->filter(function($day) use ($dateDelimeters) {
            return preg_match('((\d{4}' . $dateDelimeters . '\d{2}' . $dateDelimeters . '\d{2})|
                (\d{2}' . $dateDelimeters . '\d{2}' . $dateDelimeters . '\d{4}))', $day);
        });

        $daysImport = new DaysImport($days, true);

        Excel::import($daysImport, $request->file('excel'));

        $days = $daysImport->getDays();

        return back()->with(['days' => $days, 'excelPath' => $excelPath]);
    }

    public function import(Request $request)
    {
        // return $request;

        $days = $request->days;
        $dayStatuses = collect();

        for($i = 0; $i < $request->daysCount; $i++)
        {
            if($request->get('day' . $i . 'Status'))
            {
                $dayStatuses[$i] = $request->get('day' . $i . 'Status');
            }
        }

        $days = $dayStatuses->map(function($day, $dayKey) use ($days, $request) {
            $dayWorkouts = collect($request->get('day' . $dayKey . 'Workouts'))
                ->filter(function($dayWorkout, $dayWorkoutKey) use ($dayKey, $request) {
                    return $request->get('day' . $dayKey . 'Workout' . $dayWorkoutKey . 'Status') == 'on';
                })
                ->values();

            return [$days[$dayKey] => $dayWorkouts];
        });

        return $days;
    }
}
