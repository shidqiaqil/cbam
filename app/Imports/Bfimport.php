<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Imports\BfDataImport;
use App\Imports\BfPciCoalImport;
use App\Imports\BfQualityImport;

class BfImport implements WithMultipleSheets
{
    private string $plant;
    private string $period_month;
    private int $period_year;

    public function __construct(string $plant, string $period_month, int $period_year)
    {
        $this->plant = $plant;
        $this->period_month = $period_month;
        $this->period_year = $period_year;
    }

    public function sheets(): array
    {
        return [
            0 => new BfDataImport($this->plant, $this->period_month, $this->period_year),
            1 => new BfPciCoalImport($this->plant, $this->period_month, $this->period_year),
            2 => new BfQualityImport($this->plant, $this->period_month, $this->period_year),
        ];
    }
}
