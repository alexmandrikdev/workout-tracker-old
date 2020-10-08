<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;

class SheetNamesImport implements /* WithMultipleSheets, */ WithEvents
{
    private $sheetNames;

    function __construct($sheetNames = null)
    {
        $this->sheetNames = collect($sheetNames);
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function(BeforeSheet $event) {
                $this->sheetNames->push($event->getSheet()->getTitle());
            },
        ];
    }

    public function getSheetNames(): Collection
    {
        return $this->sheetNames;
    }
}
