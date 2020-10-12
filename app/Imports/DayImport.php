<?php

namespace App\Imports;

use App\Models\Day;
use App\Models\Exercise;
use App\Models\Unit;
use App\Models\Workout;
use App\Models\WorkoutXExercise;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class DayImport implements ToCollection, WithCalculatedFormulas
{
    private $sheetName;
    private $workouts;
    private $shouldImportWorkout;
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
        $this->shouldImportWorkout = false;

        $this->workout = null;
        $this->unit = null;
        $this->restUnit = null;
        $this->set = null;
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

    private function import($row)
    {
        if ($this->workouts->contains(Str::title($row[0]))) {
            $this->createWorkout($row[0]);
            return;
        }

        if (!is_null($this->workout)) {

            if (Str::lower($row[0]) == 'exercise') {
                $this->defineUnits($row);
                return;
            }

            if (is_null($row[1]) && is_null($row[2])) {
                if (Str::startsWith(Str::lower($row[0]), 'round')) {
                    $this->defineSet($row[0], 'round ');
                    return;
                } elseif (Str::startsWith(Str::lower($row[0]), 'set')) {
                    $this->defineSet($row[0], 'set ');
                    return;
                }
            }

            if (Str::startsWith(Str::lower($row[0]), 'total time')) {
                $this->importTotalTime($this->workout, $row[1], Str::lower(Str::between($row[0], '(', ')')));
                return;
            }

            if (!is_null($this->workout) && !is_null($this->set) && !is_null($row[1])) {
                $this->importExercise($row);
                return;
            }
        }
    }

    private function createWorkout($nameField)
    {
        $workout = Workout::where([
            'name' => Str::title($nameField),
            'user_id' => Auth::id(),
            'date' => $this->sheetName
        ])->first();

        if (is_null($workout)) {
            $this->workout = Workout::create([
                'name' => Str::title($nameField),
                'user_id' => Auth::id(),
                'date' => $this->sheetName
            ]);
            return;
        }

        $workout->workoutXExercises()->delete();

        $this->workout = $workout;
    }

    private function defineUnits($row)
    {
        $this->unit = Unit::updateOrCreate(['name' => Str::lower(Str::between($row[1], '(', ')'))]);
        $this->restUnit = Unit::updateOrCreate(['name' => Str::lower(Str::between($row[2], '(', ')'))]);
    }

    private function defineSet($field, $prefix)
    {
        $this->set = Str::after(Str::lower($field), $prefix);
    }

    private function importExercise($row)
    {
        $exercise = Exercise::updateOrCreate([
            'name' => Str::title($row[0]),
            'user_id' => Auth::id(),
        ]);

        if (is_numeric($row[1])) {
            $this->createWorkoutXExercise($exercise->id, $row[1], $row[2]);
        } elseif (preg_match('([a-zA-Z]+)', $row[1])) {
            preg_match('([0-9]+)', $row[1], $matches);
            info($row[1]);
            info($matches);
            $amount = $matches[0];
            preg_match('([a-zA-Z]+)', $row[1], $matches);
            $unit = $matches[0];

            $unit = Unit::updateOrCreate([
                'name' => $unit,
            ]);

            $this->createWorkoutXExercise($exercise->id, $amount, $row[2], $unit->id);
        } else {
            $amounts = explode(' ', $row[1]);
            $restAmounts = explode(' ', $row[2]);

            // info($amounts);
            // info($restAmounts);

            foreach ($amounts as $index => $amount) {
                if ($amount == '+') {
                    continue;
                }

                $restAmount = in_array('+', $amounts)
                    ? ($index == array_key_last($amounts) ? $restAmounts[0] : null)
                    : $restAmounts[$index];

                $this->createWorkoutXExercise($exercise->id, $amount, $restAmount);
            }
        }
    }

    private function createWorkoutXExercise($exerciseId, $amount, $restAmount, $unitId = null)
    {
        $unitId = is_null($unitId) ? $this->unit->id : $unitId;

        WorkoutXExercise::create([
            'workout_id' => $this->workout->id,
            'exercise_id' => $exerciseId,
            'set' => $this->set,
            'amount' => $amount,
            'unit_id' => $unitId,
            'rest_amount' => $restAmount,
            'rest_unit_id' => $this->restUnit->id,
        ]);
    }

    private function importTotalTime($workout, $totalTime, $totalTimeUnit)
    {
        $workout->update([
            'total_time' => $totalTime,
            'total_time_unit' => $totalTimeUnit
        ]);

        $this->workout = null;
        $this->unit = null;
        $this->restUnit = null;
        $this->set = null;
    }

    private function thisColumnIsWorkoutName($rows, $rowIndex, $colIndex)
    {
        return isset($rows[$rowIndex + 1][$colIndex]) && Str::lower($rows[$rowIndex + 1][$colIndex]) == 'exercise';
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
