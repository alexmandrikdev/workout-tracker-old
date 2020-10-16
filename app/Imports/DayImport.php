<?php

namespace App\Imports;

use App\Models\Exercise;
use App\Models\Set;
use App\Models\Unit;
use App\Models\Workout;
use Illuminate\Support\Collection;
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
                $issetNextRow = isset($rows[$rowIndex + 1]);
                $this->import($row, $issetNextRow);
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

    private function import($row, $issetNextRow)
    {
        if ($this->workouts->contains(Str::title($row[0]))) {
            return $this->workout = $this->createWorkout($row[0]);
        }

        if (!is_null($this->workout)) {
            return $this->importWorkout($row, $issetNextRow);
        }
    }

    private function createWorkout($nameField)
    {
        $workout = Workout::where([
            'name' => Str::title($nameField),
            'date' => $this->sheetName
        ])
            ->first();

        if (is_null($workout)) {
            return Workout::create([
                'name' => Str::title($nameField),
                'date' => $this->sheetName
            ]);
        }

        $workout->sets()->delete();

        return $workout;
    }

    private function importWorkout($row, $issetNextRow)
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
            $this->attachSetToWorkout();
            return $this->importTotalTime($row[1], Str::lower(Str::between($row[0], '(', ')')));
        }

        if (!is_null($this->workout) && !is_null($this->set) && !is_null($row[1])) {
            return $this->importExercise($row, $issetNextRow);
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
    }

    private function attachSetToWorkout()
    {
        if ($this->workout) {
            $this->workout->load('sets.exercises');

            $sets = $this->workout->sets;

            $set = null;

            if ($sets->isNotEmpty()) {
                $set = $sets->filter(function ($set) {
                    $setExercisesCount = $set->exercises->count();
                    $filteredSetExercisesCount = $set->exercises->filter(function ($exercise, $key) {
                        if (!isset($this->set['exercises'][$key])) {
                            return false;
                        }

                        $newExercise = $this->set['exercises'][$key];

                        return $exercise->pivot['exercise_id'] === $newExercise['id'] &&
                            $exercise->pivot['amount'] === $newExercise['amount'] &&
                            $exercise->pivot['unit_id'] === $newExercise['unit_id'] &&
                            $exercise->pivot['rest_amount'] === $newExercise['rest_amount'] &&
                            $exercise->pivot['rest_unit_id'] === $newExercise['rest_unit_id'];
                    })
                        ->count();
                    return $setExercisesCount === $filteredSetExercisesCount;
                })->first();
            }

            if (!$set) {
                $set = Set::create();

                foreach ($this->set['exercises'] as $exercise) {
                    $set->exercises()->attach($exercise['id'], collect($exercise)->except('id')->toArray());
                }
            }


            $this->workout->sets()->attach($set);

            $this->set = collect([
                'sort' => null,
                'exercises' => collect(),
            ]);
        }
    }

    private function importTotalTime($totalTime, $totalTimeUnit)
    {
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
    }

    private function importExercise($row, $issetNextRow)
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
        } else {

            preg_match_all('((\d( )*([a-zA-Z]+))|\d)', $row[1], $matches);

            $amounts = collect($matches[0]);

            preg_match_all('(\d+)', $row[2], $matches);

            $restAmounts = collect($matches[0]);

            foreach ($amounts as $index => $amount) {
                if (!is_numeric($amount)) {
                    preg_match('([a-zA-Z]+)', $amount, $matches);
                    $unitId = Unit::firstOrCreate([
                        'name' => $matches[0]
                    ])->id;

                    preg_match('(\d+)', $amount, $matches);
                    $amount = $matches[0];
                } else {
                    $unitId = $this->unit ? $this->unit->id : null;
                }

                $restAmount = $amounts->count() === $restAmounts->count()
                    ? $restAmounts[$index]
                    : ($index == $amounts->keys()->last() && isset($restAmounts[0]) ? $restAmounts[0] : null);

                $this->attachExerciseToSet($exercise, $amount, $restAmount, $unitId, $restUnitId);
            }
        }

        if (!$issetNextRow) {
            $this->attachSetToWorkout();
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
