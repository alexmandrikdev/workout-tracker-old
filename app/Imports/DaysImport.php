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

    public function __construct($sheetNames, $days = [])
    {
        $this->sheetNames = $sheetNames;
        $this->days = collect($days);

        if($this->days->isEmpty())
        {
            $this->dayImports = collect();
        }

    }

    public function sheets(): array
    {
        $imports = [];

        foreach($this->sheetNames as $sheetName)
        {
            $day = $this->days->isNotEmpty() ? $this->days[$sheetName] : [];

            $dayImport = new DayImport($sheetName, $day);

            if($this->days->isEmpty())
            {
                $this->dayImports->push($dayImport);
            }

            $imports[$sheetName] = $dayImport;
        }

        return $imports;
    }

    public function registerEvents(): array
    {
        if($this->days->isEmpty())
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
        return [];
    }

    public function getDays(): Collection
    {
        return $this->days;
    }
}
