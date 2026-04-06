<?php

namespace App\Imports;

use App\Models\MasterEnergyData;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use App\Models\MasterSinter;

class SinterImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    private static array $monthMap = [
        'january'   => '1_mon',
        'february'  => '2_mon',
        'march'     => '3_mon',
        'april'     => '4_mon',
        'may'       => '5_mon',
        'june'      => '6_mon',
        'july'      => '7_mon',
        'august'    => '8_mon',
        'september' => '9_mon',
        'october'   => '10_mon',
        'november'  => '11_mon',
        'december'  => '12_mon',
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
            if (empty($row['classification'])) continue;

            $monthCode = self::getMonthCode($this->period_month);
            $quantity = round((float) ($row[$monthCode] ?? 0), 2);

            MasterSinter::updateOrCreate(
                [
                    'plant'        => $this->plant,
                    'period_month' => $this->period_month,
                    'period_year'  => $this->period_year,
                    'classification' => $row['classification'] ?? '',
                    'sub_class'    => $row['sub_class'] ?? ''
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

    // Cek data mentah untuk memastikan tidak ada karakter tersembunyi atau format yang tidak terduga. taro sebelum proses map di collection() untuk melihat data mentahnya:
    // dd($rows->get(1));

    // // Lebih detail untuk cek karakter tersembunyi:
    // $row = $rows->get(1);
    // $monthCode = self::getMonthCode($this->period_month);
    // $val = (string) ($row[$monthCode] ?? 'NULL');
    // dd([
    //     'raw'    => $val,
    //     'hex'    => bin2hex($val),
    //     'length' => strlen($val),
    // ]);
}
