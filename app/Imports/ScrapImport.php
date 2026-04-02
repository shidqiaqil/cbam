<?php

namespace App\Imports;

use App\Models\MasterSmpScrap;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class ScrapImport extends DefaultValueBinder implements ToCollection, WithHeadingRow, WithChunkReading
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
        $monthCode = self::getMonthCode($this->period_month);

        $data = $rows->map(function ($row) use ($monthCode) {
            $quantityRaw = $row[$monthCode] ?? 0;

            return [
                'plant'        => $this->plant,
                'period_month' => $this->period_month,
                'period_year'  => $this->period_year,
                'category'     => $row['category'] ?? '',
                'sub_category' => $row['sub_category'] ?? '',
                'unit'         => $row['unit'] ?? '',
                'quantity'       => round((float) str_replace(',', '.', str_replace('.', '', (string) ($row[self::getMonthCode($this->period_month)] ?? 0))), 3),
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        })->filter(fn($row) => !empty($row['category']));

        if ($data->isNotEmpty()) {
            MasterSmpScrap::insert($data->toArray());
        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    private static function getMonthCode(string $month): string
    {
        if (!array_key_exists($month, self::$monthMap)) {
            throw new \InvalidArgumentException(
                "Invalid month: '{$month}'. Expected values: " . implode(', ', array_keys(self::$monthMap))
            );
        }

        return self::$monthMap[$month];
    }
}
