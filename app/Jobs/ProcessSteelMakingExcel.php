<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SubmaterialImport;
use App\Imports\ScrapImport;
use Illuminate\Support\Facades\Storage;
use App\Imports\SteelMakingImport;

class ProcessSteelMakingExcel implements ShouldQueue
{
    use Queueable;

    public string $filePath;
    public string $plant;
    public string $period_month;
    public int $period_year;

    public function __construct(string $filePath, string $plant, string $period_month, int $period_year)
    {
        $this->filePath = $filePath;
        $this->plant = $plant;
        $this->period_month = $period_month;
        $this->period_year = $period_year;
    }

    public function handle(): void
    {
        $fullPath = storage_path('app/' . $this->filePath);

        Excel::import(
            new SteelMakingImport($this->plant, $this->period_month, $this->period_year),
            $fullPath
        );

        Storage::delete($this->filePath);
    }
}
