<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        $daysInMonth = Carbon::createFromDate($year, $month)->daysInMonth;

        $workouts = Workout::select('id', 'name', 'date')
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date')
            ->get()
            ->groupBy('date');

        $minWorkoutDate = Carbon::parse(Workout::min('date'));
        $maxWorkoutDate = Carbon::parse(Workout::max('date'));

        $days = collect();

        $date = Carbon::createFromDate($year, $month, 1)->startOfWeek();

        for ($i = 0; $i < 35; $i++) {
            $day = [];
            $day['date'] = $date;

            if ($date->month == $month) {
                if (isset($workouts[$date->toDateString()])) {
                    $workoutNames = $workouts[$date->toDateString()]->pluck('name');

                    if (request('workout_name')) {
                        $workoutNames = $workoutNames->filter(function ($value) {
                            return $value == request('workout_name');
                        });
                    }

                    $day['workouts'] = $workoutNames;
                } elseif ($date->lt(now()) && $date->gte($minWorkoutDate)) {
                    $day['workouts'] = collect('Rest');
                } else {
                    $day['workouts'] = collect();
                }

                $day['clickable'] = $day['workouts']->isNotEmpty() && !$day['workouts']->contains('Rest');
            }

            $days->push($day);
            $date = $date->copy()->addDay();
        }

        // return $days;

        $dayNames = collect();

        for ($date = now()->startOfWeek(); $date < now()->endOfWeek(); $date->addDay()) {
            $dayNames->push($date->locale(App::getLocale())->shortDayName);
        }


        $monthNames = collect();
        for ($month = 1; $month <= 12; $month++) {
            $monthNames->push(Str::title(Carbon::createFromDate(null, $month)->locale(App::getLocale())->monthName));
        }

        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        $workoutName = request('workout_name');
        return view('calendar.index', compact('days', 'dayNames', 'monthNames', 'minWorkoutDate', 'maxWorkoutDate', 'year', 'month', 'workoutName'));
    }
}
