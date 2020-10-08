<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\AfterSheet;

class DaysImport implements WithMultipleSheets, WithEvents
{
    private $sheetNames;
    private $dayImports;
    private $days;
    private $importOnlyWorkoutNames;

    public function __construct($sheetNames, $importOnlyWorkoutNames = false)
    {
        $this->sheetNames = $sheetNames;
        $this->importOnlyWorkoutNames = $importOnlyWorkoutNames;
        $this->days = collect();
        $this->dayImports = collect();

    }

    public function sheets(): array
    {
        $imports = [];

        foreach($this->sheetNames as $sheetName)
        {
            $this->dayImports->push(new DayImport($sheetName, $this->importOnlyWorkoutNames));

            $imports[$sheetName] = $this->dayImports->last();
        }

        return $imports;
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function(AfterImport $event) {
                foreach($this->dayImports as $dayImport)
                {
                    $this->days[$dayImport->getSheetName()] = $dayImport->getWorkouts();
                }
            }
        ];
    }

    public function getDays(): Collection
    {
        return $this->days;
    }
}
