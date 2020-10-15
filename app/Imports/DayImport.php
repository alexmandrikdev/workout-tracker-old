<?php

namespace App\Imports;

use App\Models\Day;
use App\Models\Exercise;
use App\Models\Set;
use App\Models\Unit;
use App\Models\Workout;
use App\Models\WorkoutXExercise;
use App\Pivots\WorkoutSet;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class DayImport implements ToCollection, WithCalculatedFormulas
{
    private $sheetName;
    private $workouts;
    private $unit;
    private $restUnit;
    private $set;
    private $workout;

    public function __construct($sheetName, $workouts)
    {
        $this->workouts = collect($workouts)->map(function ($workout) {
            return Str::title($workout);
        });;
        $this->sheetName = $sheetName;

        $this->workout = null;
        $this->unit = null;
        $this->restUnit = null;
        $this->set = collect([
            'sort' => null,
            'exercises' => collect(),
        ]);
    }

    public function collection(Collection $rows)
    {
        if ($this->workouts->isEmpty()) {
            foreach ($rows as $rowIndex => $row) {
                $this->collectWorkoutNames($rows, $rowIndex);
            }
        } else {
            foreach ($rows as $rowIndex => $row) {
                $this->import($row);
            }
        }
    }

    private function collectWorkoutNames($rows, $rowIndex)
    {
        if ($this->thisColumnIsWorkoutName($rows, $rowIndex, 0)) {
            $this->workouts->push($rows[$rowIndex][0]);
        }
    }

    private function thisColumnIsWorkoutName($rows, $rowIndex, $colIndex)
    {
        return isset($rows[$rowIndex + 1][$colIndex]) && Str::lower($rows[$rowIndex + 1][$colIndex]) == 'exercise';
    }

    private function import($row)
    {
        if ($this->workouts->contains(Str::title($row[0]))) {
            return $this->workout = $this->createWorkout($row[0]);
        }

        if (!is_null($this->workout)) {
            return $this->importWorkout($row);
        }
    }

    private function createWorkout($nameField)
    {
        $workout = Workout::where([
            'name' => Str::title($nameField),
            'date' => $this->sheetName
        ])
            ->with('sets')
            ->first();

        if (is_null($workout)) {
            return Workout::create([
                'name' => Str::title($nameField),
                'date' => $this->sheetName
            ])
                ->load('sets');
        }

        $workout->sets()->detach();

        return $workout;
    }

    private function importWorkout($row)
    {
        if (Str::lower($row[0]) == 'exercise') {
            return $this->defineUnits($row);
        }

        if (is_null($row[1]) && is_null($row[2])) {
            if (Str::startsWith(Str::lower($row[0]), 'round')) {
                return $this->defineSet($row[0], 'round ');
            } elseif (Str::startsWith(Str::lower($row[0]), 'set')) {
                return $this->defineSet($row[0], 'set ');
            }
        }

        if (Str::startsWith(Str::lower($row[0]), 'total time')) {
            return $this->importTotalTime($row[1], Str::lower(Str::between($row[0], '(', ')')));
        }

        if (!is_null($this->workout) && !is_null($this->set) && !is_null($row[1])) {
            return $this->importExercise($row);
        }
    }

    private function defineUnits($row)
    {
        $this->unit = Unit::firstOrCreate(['name' => Str::lower(Str::between($row[1], '(', ')'))]);
        $this->restUnit = Unit::firstOrCreate(['name' => Str::lower(Str::between($row[2], '(', ')'))]);
    }

    private function defineSet($field, $prefix)
    {
        if ($this->set['sort'] !== null) {
            $this->attachSetToWorkout();
        }

        $sort = Str::after(Str::lower($field), $prefix);
        $this->set['sort'] = $sort;

        // if ($this->workout) {
        //     $this->set = $this->workout->sets->firstWhere('sort', $sort);

        //     if ($this->set) {
        //         $this->set->exercises()->detach();
        //     } else {
        //         $this->set = Set::create();
        //         $this->workout->sets()->attach($this->set);
        //     }
        // }
    }

    private function attachSetToWorkout()
    {
        if ($this->workout) {
            $this->workout->load('sets.exercises');

            $sets = $this->workout->sets;

            $set = null;

            // dd($sets);

            if ($sets->isNotEmpty()) {
                $set = $sets->filter(function ($set) {
                    $setExercisesCount = $set->exercises->count();
                    // dd($set->exercises);
                    // info($set->exercises);
                    // info($this->set['exercises']);
                    $filteredSetExercisesCount = $set->exercises->filter(function ($exercise, $key) {
                        if (!isset($this->set['exercises'][$key])) {
                            return false;
                        }

                        $newExercise = $this->set['exercises'][$key];

                        // dd($exercise->pivot);
                        // dd($newExercise);

                        return $exercise->pivot['exercise_id'] === $newExercise['id'] &&
                            $exercise->pivot['amount'] === $newExercise['amount'] &&
                            $exercise->pivot['unit_id'] === $newExercise['unit_id'] &&
                            $exercise->pivot['rest_amount'] === $newExercise['rest_amount'] &&
                            $exercise->pivot['rest_unit_id'] === $newExercise['rest_unit_id'];
                    })
                        ->count();
                    // dd($filteredSetExercisesCount);
                    return $setExercisesCount === $filteredSetExercisesCount;
                })->first();
            }

            if ($set) {
                $this->workout->sets()->attach($set);
            } else {
                $set = Set::create();

                foreach ($this->set['exercises'] as $exercise) {
                    $set->exercises()->attach($exercise['id'], collect($exercise)->except('id')->toArray());
                }

                $this->workout->sets()->attach($set);
            }

            $this->set['exercises'] = collect();
        }
    }

    private function importTotalTime($totalTime, $totalTimeUnit)
    {
        $this->attachSetToWorkout();

        $unit = Unit::updateOrCreate(['name' => $totalTimeUnit]);

        if ($this->workout) {
            $this->workout->update([
                'total_time' => $totalTime,
                'total_time_unit_id' => $unit->id
            ]);
        }

        $this->workout = null;
        $this->unit = null;
        $this->restUnit = null;
        $this->set = collect([
            'sort' => null,
            'exercises' => collect(),
        ]);
    }

    private function importExercise($row)
    {
        $exercise = Exercise::updateOrCreate([
            'name' => Str::title($row[0]),
        ]);

        $amount = null;
        $unitId = $this->unit ? $this->unit->id : null;
        $restAmount = null;
        $restUnitId = $this->restUnit ? $this->restUnit->id : null;

        if (is_numeric($row[1])) {
            $amount = $row[1];
            $restAmount = $row[2];
            $this->attachExerciseToSet($exercise, $amount, $restAmount, $unitId, $restUnitId);
        } elseif (preg_match('([a-zA-Z]+)', $row[1])) {
            preg_match('([0-9]+)', $row[1], $matches);
            $amount = $matches[0];
            preg_match('([a-zA-Z]+)', $row[1], $matches);
            $unit = $matches[0];

            $unitId = Unit::firstOrCreate([
                'name' => $unit,
            ]);

            $restAmount = $row[2];
            $this->attachExerciseToSet($exercise, $amount, $restAmount, $unitId, $restUnitId);
        } else {
            $amounts = explode(' ', $row[1]);
            $restAmounts = explode(' ', $row[2]);

            foreach ($amounts as $index => $amount) {
                if ($amount == '+') {
                    continue;
                }

                $restAmount = in_array('+', $amounts)
                    ? ($index == array_key_last($amounts) ? $restAmounts[0] : null)
                    : $restAmounts[$index];

                $this->attachExerciseToSet($exercise, $amount, $restAmount, $unitId, $restUnitId);
            }
        }
    }

    public function attachExerciseToSet($exercise, $amount, $restAmount, $unitId, $restUnitId)
    {
        $this->set['exercises']->push([
            'id' => $exercise->id,
            'amount' => $amount,
            'rest_amount' => $restAmount,
            'unit_id' => $unitId,
            'rest_unit_id' => $restUnitId,
        ]);
    }

    public function getWorkouts()
    {
        return $this->workouts;
    }

    public function getSheetName()
    {
        return $this->sheetName;
    }
}
