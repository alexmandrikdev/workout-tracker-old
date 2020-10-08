<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkoutsImport\GetSheetsRequest;
use App\Imports\DaysImport;
use App\Imports\SheetNamesImport;
use App\Imports\WorkoutsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class WorkoutImportController extends Controller
{
    public function importView(Request $request)
    {
        $sheets = null;

        return view('workouts.import', compact('sheets'));
    }

    public function getSheets(GetSheetsRequest $request)
    {
        $sheetNamesImport = new SheetNamesImport();

        Excel::import($sheetNamesImport, $request->file('excel'));

        $days = $sheetNamesImport->getSheetNames();

        $dateDelimeters = '(\.|-|/)';

        $days = $days->filter(function($day) use ($dateDelimeters) {
            return preg_match('((\d{4}' . $dateDelimeters . '\d{2}' . $dateDelimeters . '\d{2})|
                (\d{2}' . $dateDelimeters . '\d{2}' . $dateDelimeters . '\d{4}))', $day);
        });

        $daysImport = new DaysImport($days);

        Excel::import($daysImport, $request->file('excel'));

        return $daysImport->getDays();
    }
}
