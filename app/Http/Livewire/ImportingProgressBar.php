<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class ImportingProgressBar extends Component
{
    public $progress = 0;

    public function hydrate()
    {
        if (session('importing')) {
            $importStatus = Cache::get('user-'. auth()->id() . '-import-status');

            $this->progress = $importStatus ? round(($importStatus['imported_days'] * 100) / $importStatus['importable_days']) : 100;

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
