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
    public function show($date)
    {
        $workouts = Workout::whereDate('date', $date)
            ->with([
                'totalTimeUnit',
                'sets.exercises',
                'sets.exercises.pivot.unit',
                'sets.exercises.pivot.restUnit',
            ])
            ->orderBy('sort')
            ->get();

        $workouts = $workouts->map(function($workout){
            $sets = $this->groupingSets($workout->sets);

            return [
                'name' => $workout->name,
                'sort' => $workout->sort,
                'total_time' => $workout->total_time,
                'total_time_unit' => $workout->totalTimeUnit->name,
                'sets' => $sets,
            ];
        });

        return view('workouts.show', compact('workouts'));
    }

    private function groupingSets($sets)
    {
        $groupedSets = collect();

        $lastKey = 0;

        foreach($sets as $key => $set){
            if($key === 0 || $set->id !== $sets[$lastKey]->id){
                $groupedSets->push([
                    'data' => $set,
                    'count' => 1
                ]);
                $lastKey = $key;
            } else {
                $lastSet = $groupedSets->pop();
                $lastSet['count']++;
                $groupedSets->push($lastSet);
            }
        }

        return $groupedSets;
    }
}
