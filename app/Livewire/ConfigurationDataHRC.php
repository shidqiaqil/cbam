<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MasterEnergyData;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;

#[Title('Steel HRC')]
class ConfigurationDataHRC extends Component
{
    // =========================================================================
    // URL STATE
    // =========================================================================

    #[Url]
    public string $periodYear = '';

    #[Url]
    public string $period = ''; // jan, feb, ..., q1, q2, q3, q4, yearly

    // =========================================================================
    // ROW DEFINITIONS FOR TABLE 1
    // =========================================================================

    protected array $hrcRowsTable1 = [
        [
            'description' => 'Power HRP Plant',
            'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = IHA000, plant_name = Hot Strip Mill, criteria = CONSUMPTION, energy_name = POWER</li></ul>',
            'conditions'  => [
                ['plant_code' => 'IHA000', 'plant_name' => 'Hot Strip Mill', 'criteria' => 'CONSUMPTION', 'energy_name' => 'POWER'],
            ],
            'formula' => '/1000', // Convert kWh to MWh
        ],
    ];

    protected array $hrcRowsTable2 = [
        [
            'description' => 'COG for HRP',
            'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = IHA000, plant_name = Hot Strip Mill, criteria = CONSUMPTION, energy_name = COG</li></ul>',
            'conditions'  => [
                ['plant_code' => 'IHA000', 'plant_name' => 'Hot Strip Mill', 'criteria' => 'CONSUMPTION', 'energy_name' => 'COG'],
            ],
        ],
    ];

    protected array $hrcRowsTable3 = [
        [
            'description' => 'Natural Gas',
            'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = I01200, plant_name = Purchase for HSM, energy_name = NG, criteria = PRODUCTION</li></ul>',
            'conditions'  => [
                ['plant_code' => 'I01200', 'plant_name' => 'Purchase for HSM', 'criteria' => 'PRODUCTION', 'energy_name' => 'NG'],
            ],
        ],
    ];

    // =========================================================================
    // PERIOD HELPERS
    // =========================================================================

    private function resolveMonths(): array
    {
        $monthMap = [
            'jan' => 'january',
            'feb' => 'february',
            'mar' => 'march',
            'apr' => 'april',
            'may' => 'may',
            'jun' => 'june',
            'jul' => 'july',
            'aug' => 'august',
            'sep' => 'september',
            'oct' => 'october',
            'nov' => 'november',
            'dec' => 'december',
        ];

        $quarterMap = [
            'q1' => ['january', 'february', 'march'],
            'q2' => ['april', 'may', 'june'],
            'q3' => ['july', 'august', 'september'],
            'q4' => ['october', 'november', 'december'],
        ];

        return match (true) {
            $this->period === 'yearly'            => array_values($monthMap),
            isset($quarterMap[$this->period])     => $quarterMap[$this->period],
            isset($monthMap[$this->period])       => [$monthMap[$this->period]],
            default                               => [],
        };
    }

    // =========================================================================
    // QUERY HELPERS
    // =========================================================================

    private function sumEnergyData(array $conditions, string $month): float
    {
        if (empty($conditions)) return 0.0;

        return (float) MasterEnergyData::where('period_year', $this->periodYear)
            ->whereRaw('LOWER(TRIM(period_month)) = ?', [strtolower(trim($month))])
            ->where(function ($q) use ($conditions) {
                foreach ($conditions as $cond) {
                    $q->orWhere(function ($sub) use ($cond) {
                        $sub->where('plant_code',  $cond['plant_code'])
                            ->where('plant_name',  $cond['plant_name'])
                            ->where('energy_name', $cond['energy_name']);
                        if (isset($cond['criteria'])) {
                            $sub->where('criteria', $cond['criteria']);
                        }
                    });
                }
            })
            ->sum('quantity');
    }

    private function buildTableData(array $rowDefs, string $valueKey = 'power'): Collection
    {
        $months = $this->resolveMonths();
        if (empty($months)) return collect();

        return collect($rowDefs)->map(function (array $rowDef) use ($months, $valueKey): array {
            $rawValue = array_reduce(
                $months,
                fn(float $carry, string $month) => $carry + $this->sumEnergyData($rowDef['conditions'], $month),
                0.0
            );

            $value = isset($rowDef['formula'])
                ? eval("return {$rawValue}{$rowDef['formula']};")
                : $rawValue;

            return [
                'description' => $rowDef['description'],
                'tooltip'     => $rowDef['tooltip'] ?? '',
                $valueKey     => round($value, 2),
            ];
        });
    }

    // =========================================================================
    // LIVEWIRE HOOKS
    // =========================================================================

    public function updatedPeriodYear(): void
    {
        if (empty($this->periodYear)) {
            $this->period = '';
        }
    }

    // =========================================================================
    // COMPUTED PROPERTIES
    // =========================================================================

    #[Computed]
    public function availableYears(): Collection
    {
        return MasterEnergyData::distinct()
            ->orderByDesc('period_year')
            ->pluck('period_year')
            ->values();
    }

    #[Computed]
    public function hrcTable1Data(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();
        return $this->buildTableData($this->hrcRowsTable1, 'power');
    }

    #[Computed]
    public function hrcTable2Data(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();
        return $this->buildTableData($this->hrcRowsTable2, 'cog');
    }

    #[Computed]
    public function hrcTable3Data(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();
        return $this->buildTableData($this->hrcRowsTable3, 'quantity');
    }

    #[Computed]
    public function hrcEmissionTableData(): Collection
    {
        $rows1    = $this->hrcTable1Data();
        $powerHrp = $rows1->first()['power'] ?? 0.0;
        $ef       = 0.87; // tCO2/MWh

        return collect([
            [
                'ef_value'       => number_format($ef, 4),
                'total_emission' => number_format($powerHrp * $ef, 2),
                'unit'           => 'tCO2',
                'tooltip'        => 'Table 1 Power HRP Plant * Emission Factor (tCO2/Mwh)[0]',
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // NEW: Table 2.2 — 3.3 
    // -------------------------------------------------------------------------

    #[Computed]
    public function hrcTable22Data(): Collection
    {
        $rows2   = $this->hrcTable2Data();
        $cogHrp  = $rows2->first()['cog'] ?? 0.0; // Nm3
        $conversionFactor = 0.000018410; // TJ/m3 (= TJ/Nm3)
        $totalTJ = $cogHrp * $conversionFactor;

        return collect([
            [
                'conversion_factor' => number_format($conversionFactor, 9), // 0.000018410
                'by_product_gas'    => 'Table 2 COG for HRP * TJ Conversion (TJ/m3)[0]',
                'total_tj'          => number_format($totalTJ, 1),
                'unit'              => 'TJ',
                'tooltip'           => 'Table 2 COG for HRP (Nm3) * Conversion Factor (TJ/m3)',
            ],
        ]);
    }

    #[Computed]
    public function hrcTable33Data(): Collection
    {
        $rows3            = $this->hrcTable3Data();
        $ngQty            = $rows3->first()['quantity'] ?? 0.0; // Nm3
        $conversionFactor = 0.000036915; // TJ/m3
        $totalTJ          = $ngQty * $conversionFactor;

        return collect([
            [
                'conversion_factor' => number_format($conversionFactor, 9),
                'total_tj'          => number_format($totalTJ, 1),
                'unit'              => 'TJ',
                'tooltip'           => 'Table 3 Natural Gas (Nm3) * Conversion Factor (TJ/m3)[0]',
            ],
        ]);
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    public function render()
    {
        return view('livewire.configuration-data-hrc', [
            'availableYears'       => $this->availableYears,
            'hrcTable1Data'        => $this->hrcTable1Data,
            'hrcTable2Data'        => $this->hrcTable2Data,
            'hrcEmissionTableData' => $this->hrcEmissionTableData,
            'hrcTable22Data'       => $this->hrcTable22Data,  // ← NEW
            'hrcTable3Data'        => $this->hrcTable3Data,   // ← NEW
            'hrcTable33Data'       => $this->hrcTable33Data,  // ← N
        ]);
    }
}
