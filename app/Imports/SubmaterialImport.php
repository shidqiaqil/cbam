<?php

namespace App\Imports;

use App\Models\MasterSmpSubmaterial;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class SubmaterialImport extends DefaultValueBinder implements ToCollection, WithHeadingRow, WithChunkReading
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

        $data = $rows->map(function ($row) {
            // dd($row);
            $raw = $row[self::getMonthCode($this->period_month)] ?? 0;

            $quantity = is_numeric($raw)
                ? (float) $raw
                : (float) str_replace(',', '.', str_replace('.', '', (string) $raw));

            return [
                'plant'          => $this->plant,
                'period_month'   => $this->period_month,
                'period_year'    => $this->period_year,
                'classification' => $row['classification'] ?? '',
                'sub_class'      => $row['sub_class'] ?? '',
                'unit'           => $row['unit'] ?? '',
                'quantity'       => round((float) ($row[self::getMonthCode($this->period_month)] ?? 0), 2),
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        })->filter(fn($row) => !empty($row['classification']));

        if ($data->isNotEmpty()) {
            // dd($data->toArray()); // <-- sini
            MasterSmpSubmaterial::insert($data->toArray());
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
