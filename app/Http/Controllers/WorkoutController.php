<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\Unit;
use App\Models\Workout;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WorkoutController extends Controller
{
    public function index(Request $request)
    {
        $workoutNames = Workout::select('name')
            ->get()->pluck('name')->unique();

        $workoutDateMin = Carbon::parse(Workout::min('date'));
        $workoutDateMax = Carbon::parse(Workout::max('date'));

        $chartLabels = collect();

        while ($workoutDateMin->lte($workoutDateMax)) {
            $chartLabels->push($workoutDateMin->toDateString());
            $workoutDateMin->addDay();
        }

        $exercises = Exercise::with([
            'workoutExercises:workout_id,exercise_id,amount,unit_id',
            'workoutExercises.workout:id,name,date',
            'workoutExercises.unit:id,name',
        ])
            ->get()
            ->map(function ($exercise) {
                return [
                    'name' => $exercise->name,
                    'workout_name' => $exercise->workoutExercises->first()->workout->name,
                    'workouts' => $exercise->workoutExercises->map(function ($workoutExercise) {
                        return [
                            'amount' => $workoutExercise->amount,
                            'unit' => $workoutExercise->unit->name,
                            'date' => $workoutExercise->workout->date,
                        ];
                    })
                        ->groupBy('date')
                        ->map(function ($day) {
                            return [
                                'amount_sum' => $day->sum('amount'),
                                'set_count' => $day->count('amount')
                            ];
                        }),
                ];
            });

        $workouts = $exercises->groupBy('workout_name');

        // return $workouts;

        return view('workouts.index', compact('workoutNames', 'workouts', 'chartLabels'));
    }

    public function show($date)
    {
        $workout = Workout::whereDate('date', $date)->get();

        return $workout;
    }
}
