<?php

namespace App\Imports;


use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EnergyImport implements WithMultipleSheets
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
            0 => new EnergyDataImport($this->plant, $this->period_month, $this->period_year),
            1 => new EnergySalesImport($this->plant, $this->period_month, $this->period_year),
        ];
    }
}
