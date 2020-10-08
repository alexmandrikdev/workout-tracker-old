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

    public function __construct($sheetNames)
    {
        $this->sheetNames = $sheetNames;
        $this->days = collect();
        $this->dayImports = collect();

    }

    public function sheets(): array
    {
        $imports = [];

        foreach($this->sheetNames as $sheetName)
        {
            $this->dayImports->push(new DayImport($sheetName));

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
                    $this->days->push($dayImport->getWorkouts());
                }
            }
        ];
    }

    public function getDays(): Collection
    {
        return $this->days;
    }
}
