<?php

namespace App\Imports;

use App\Models\MasterEnergyData;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\MasterPco;
use App\Models\MasterPcoCoil;
use App\Models\MasterPcoPlate;
use App\Imports\PcoDataImport;
use App\Imports\PcoCoilImport;
use App\Imports\PcoPlateImport;

class PcoImport implements WithMultipleSheets
{
    private static array $monthMap = [
        'january'   => 'jan',
        'february'  => 'feb',
        'march'     => 'mar',
        'april'     => 'apr',
        'may'       => 'may',
        'june'      => 'jun',
        'july'      => 'jul',
        'august'    => 'aug',
        'september' => 'sep',
        'october'   => 'oct',
        'november'  => 'nov',
        'december'  => 'dec',
    ];

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
            0 => new PcoDataImport($this->plant, $this->period_month, $this->period_year),
            1 => new PcoCoilImport($this->plant, $this->period_month, $this->period_year),
            2 => new PcoPlateImport($this->plant, $this->period_month, $this->period_year),
        ];
    }
}
