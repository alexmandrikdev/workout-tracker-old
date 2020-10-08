<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Str;

class DayImport implements ToCollection
{
    private $workouts;
    private $sheetName;
    private $importOnlyWorkoutNames;

    public function __construct($sheetName, $importOnlyWorkoutNames)
    {
        $this->workouts = collect();
        $this->sheetName = $sheetName;
        $this->importOnlyWorkoutNames = $importOnlyWorkoutNames;
    }

    public function collection(Collection $rows)
    {
        foreach($rows as $key => $row)
        {
            if($this->importOnlyWorkoutNames)
            {
                if(isset($rows[$key + 1][0]) && Str::lower($rows[$key + 1][0]) == 'exercise')
                {
                    $this->workouts->push($row[0]);
                }
            }
            else
            {

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
}
