<?php

namespace App\Imports;

use App\Models\MasterByproduct;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ByproductImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    private string $plant;
    private string $period_month; // tetap perlu
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
            if (empty($row['source'])) continue;

            $quantity = round((float) str_replace(',', '.', str_replace('.', '', (string) ($row['quantity'] ?? 0))), 2);

            MasterByproduct::updateOrCreate(
                [
                    'plant'        => $this->plant,
                    'period_month' => $this->period_month,
                    'period_year'  => $this->period_year,
                    'source'       => $row['source'] ?? '',
                    'group'        => $row['group'] ?? ''
                ],
                ['quantity' => $quantity]
            );
        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
