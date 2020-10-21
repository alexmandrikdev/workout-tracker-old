<?php

namespace App\Http\Livewire;

use App\Models\ImportStatus;
use Livewire\Component;

class ImportingProgressBar extends Component
{
    public $progress = 0;

    public function hydrate()
    {
        if (session('importing')) {
            $importStatus = ImportStatus::latest()->first();
            $this->progress = $importStatus ? round(($importStatus->imported_days * 100) / $importStatus->importable_days) : 100;
            if (!$importStatus) {
                session(['importing' => false]);
            }
        }
    }

    public function render()
    {
        return view('livewire.importing-progress-bar');
    }
}
