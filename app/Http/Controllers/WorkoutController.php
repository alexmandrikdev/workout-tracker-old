<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Workout;
use Illuminate\Http\Request;

class WorkoutController extends Controller
{
    public function index(Request $request)
    {
        $workoutNames = Workout::select('name')
            ->get()->pluck('name')->unique();

        $workouts = Workout::with('exercises')
            ->get()
            ->map(function ($workout) {
                return [
                    'name' => $workout->name,
                    'date' => $workout->date,
                    'exercises' => $workout->exercises->groupBy('name')
                        ->map(function($exercise){
                            return [
                                'amount' => $exercise->sum('pivot.amount'),
                                'unit' => Unit::select('name')->find($exercise->first()->pivot->unit_id)->name,
                                'sets' => $exercise->count()
                            ];
                        }),
                ];
            });

        return $workouts;

        return view('workouts.index', compact('workoutNames'));
    }

    public function show($date)
    {
        $workout = Workout::whereDate('date', $date)->get();

        return $workout;
    }
}
