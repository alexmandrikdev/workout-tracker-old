<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Str;

class DayImport implements ToCollection
{
    private $workouts;
    private $sheetName;

    public function __construct($sheetName)
    {
        $this->workouts = collect();
        $this->sheetName = $sheetName;
    }

    public function collection(Collection $rows)
    {
        foreach($rows as $key => $row)
        {
            if(isset($rows[$key + 1][0]) && Str::lower($rows[$key + 1][0]) == 'exercise')
            {
                $this->workouts->push($row[0]);
            }
        }
    }

    public function getWorkouts()
    {
        return [$this->sheetName => $this->workouts];
    }
}
