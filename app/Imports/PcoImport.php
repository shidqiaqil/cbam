<?php

namespace App\Imports;

use App\Models\MasterEnergyData;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use App\Models\MasterPco;

class PcoImport implements ToCollection, WithHeadingRow, WithChunkReading
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

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            if (empty($row['criteria'])) continue;

            $monthCode = self::getMonthCode($this->period_month);
            $quantity = round((float) ($row[$monthCode] ?? 0), 2);

            MasterPco::updateOrCreate(
                [
                    'plant'        => $this->plant,
                    'period_month' => $this->period_month,
                    'period_year'  => $this->period_year,
                    'criteria'     => $row['criteria'] ?? '',
                    'unit'         => $row['unit'] ?? ''
                ],
                ['quantity' => $quantity]
            );
        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    private static function getMonthCode(string $month): string
    {
        $month = strtolower($month);
        if (!array_key_exists($month, self::$monthMap)) {
            throw new \InvalidArgumentException(
                "Invalid month: '{$month}'. Expected: " . implode(', ', array_keys(self::$monthMap))
            );
        }
        return self::$monthMap[$month];
    }
}
