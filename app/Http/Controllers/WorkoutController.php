<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WorkoutController extends Controller
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

        $minWorkoutDate = Workout::min('date');

        $days = collect();

        $date = Carbon::createFromDate($year, $month, 1)->startOfWeek();

        for ($i = 0; $i < 35; $i++) {
            $day = [];
            $day['date'] = $date;

            if ($date->month == $month) {
                $day['workouts'] = isset($workouts[$date->toDateString()])
                    ? $workouts[$date->toDateString()]->pluck('name')
                    : ($date->lt(now()) && $date->gte($minWorkoutDate) ? collect('Rest') : collect());
                $day['clickable'] = isset($workouts[$date->toDateString()]);

            }

            $days->push($day);
            $date = $date->copy()->addDay();
        }

        $dayNames = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];

        // return $days;
        return view('workouts.index', compact('days', 'dayNames'));
    }

    public function show($date)
    {
        $workout = Workout::whereDate('date', $date)->get();

        return $workout;
    }
}
