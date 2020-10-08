<?php

namespace App\Imports;

use App\Models\Day;
use App\Models\Workout;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Str;

class DayImport implements ToCollection
{
    private $sheetName;
    private $workouts;

    public function __construct($sheetName, $workouts)
    {
        $this->workouts = collect($workouts);
        $this->sheetName = $sheetName;
    }

    public function collection(Collection $rows)
    {
        if($this->workouts->isEmpty())
        {
            foreach($rows as $rowIndex => $row)
            {
                if($this->thisColumnIsWorkoutName($rows, $rowIndex, 0))
                {
                    $this->workouts->push($row[0]);
                }
            }
        }
        else
        {
            foreach($rows as $rowIndex => $row)
            {
                if($this->thisColumnIsWorkoutName($rows, $rowIndex, 0) && $this->workouts->contains($row[0]))
                {
                    $workout = Workout::updateOrCreate([
                        'name' => $row[0],
                        'user_id' => Auth::id(),
                        'date' => $this->sheetName
                    ]);
                }
            }
        }
    }

    public function getWorkouts()
    {
        return $this->workouts;
    }

    public function getSheetName()
    {
        return $this->sheetName;
    }

    private function thisColumnIsWorkoutName($rows, $rowIndex, $colIndex){
        return isset($rows[$rowIndex + 1][$colIndex]) && Str::lower($rows[$rowIndex + 1][$colIndex]) == 'exercise';
    }
}
