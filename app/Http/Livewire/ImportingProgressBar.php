<?php

namespace App\Http\Livewire;

use App\Models\ImportStatus;
use Livewire\Component;

class ImportingProgressBar extends Component
{
    public $progress = 0;

    public function hydrate()
    {
        $importStatus = ImportStatus::latest()->first();

        if ($importStatus) {
            $this->progress = round(($importStatus->imported_days * 100) / $importStatus->importable_days);
        } else {
            session(['importing' => false]);
        }
    }

    public function render()
    {
        return view('livewire.importing-progress-bar');
    }
}
