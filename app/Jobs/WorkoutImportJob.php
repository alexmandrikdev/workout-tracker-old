<?php

namespace App\Jobs;

use App\Imports\DaysImport;
use App\Models\Exercise;
use App\Models\ImportStatus;
use App\Models\Set;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class WorkoutImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $sheetNames;
    private $days;
    private $excelPath;
    private $userId;

    public function __construct($sheetNames, $days, $excelPath, $userId)
    {
        $this->sheetNames = $sheetNames;
        $this->days = $days;
        $this->excelPath = $excelPath;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $daysImport = new DaysImport($this->sheetNames, $this->days, $this->userId, $this->job->getJobId());

        ImportStatus::create([
            'job_id' => $this->job->getJobId(),
            'user_id' => $this->userId,
            'importable_days' => $this->sheetNames->count(),
        ]);

        Excel::import($daysImport, $this->excelPath);

        $this->deleteUnusedSets();

        Storage::delete($this->excelPath);
    }

    private function deleteUnusedSets()
    {
        Set::doesntHave('workoutSets')->delete();
        Exercise::doesntHave('sets')->delete();
    }
}
