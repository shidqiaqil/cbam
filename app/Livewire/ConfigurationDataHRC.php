<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MasterEnergyData;
use App\Models\MasterPcoCoil;
use App\Models\MasterPcoPlate;
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
    public string $period = '';

    // =========================================================================
    // ROW DEFINITIONS
    // =========================================================================

    protected array $hrcRowsTable1 = [
        [
            'description' => 'Power HRP Plant',
            'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = IHA000, plant_name = Hot Strip Mill, criteria = CONSUMPTION, energy_name = POWER</li></ul>',
            'conditions'  => [
                ['plant_code' => 'IHA000', 'plant_name' => 'Hot Strip Mill', 'criteria' => 'CONSUMPTION', 'energy_name' => 'POWER'],
            ],
            'formula' => '/1000',
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
            $this->period === 'yearly'         => array_values($monthMap),
            isset($quarterMap[$this->period])  => $quarterMap[$this->period],
            isset($monthMap[$this->period])    => [$monthMap[$this->period]],
            default                            => [],
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
                $valueKey     => (float) $value,
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
    // COMPUTED – FILTER
    // =========================================================================

    #[Computed]
    public function availableYears(): Collection
    {
        return MasterEnergyData::distinct()
            ->orderByDesc('period_year')
            ->pluck('period_year')
            ->values();
    }

    // =========================================================================
    // COMPUTED – TABLE 1: Power
    // =========================================================================

    #[Computed]
    public function hrcTable1Data(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();
        return $this->buildTableData($this->hrcRowsTable1, 'power');
    }

    // =========================================================================
    // COMPUTED – TABLE 1.1: Emission from Table 1
    // =========================================================================

    #[Computed]
    public function hrcTable11Data(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        $powerHrp = $this->hrcTable1Data->first()['power'] ?? 0.0;
        $ef       = 0.87; // tCO2/MWh

        return collect([
            [
                'emission_factor' => $ef,
                'total_emission'  => $powerHrp * $ef,
                'unit'            => 'tCO2',
                'tooltip'         => '<ul><li>Table 1 Power HRP Plant × Emission Factor (tCO2/MWh) [0]</li><li>= ' . $powerHrp . ' × ' . $ef . '</li></ul>',
            ],
        ]);
    }

    // =========================================================================
    // COMPUTED – TABLE 2: COG
    // =========================================================================

    #[Computed]
    public function hrcTable2Data(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();
        return $this->buildTableData($this->hrcRowsTable2, 'cog');
    }

    // =========================================================================
    // COMPUTED – TABLE 2.1: By Product Gas from COG
    // =========================================================================

    #[Computed]
    public function hrcTable21Data(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        $cogHrp           = $this->hrcTable2Data->first()['cog'] ?? 0.0;
        $conversionFactor = 0.000018410; // TJ/m3
        $totalTJ          = $cogHrp * $conversionFactor;

        return collect([
            [
                'conversion_factor' => $conversionFactor,
                'by_product_gas'    => $totalTJ,
                'unit'              => 'Tj',
                'tooltip'           => '<ul><li>Table 2 COG for HRP (Nm3) × Conversion Factor (TJ/m3) [0]</li><li>= ' . $cogHrp . ' × ' . $conversionFactor . '</li></ul>',
            ],
        ]);
    }

    // =========================================================================
    // COMPUTED – TABLE 3: Natural Gas
    // =========================================================================

    #[Computed]
    public function hrcTable3Data(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();
        return $this->buildTableData($this->hrcRowsTable3, 'quantity');
    }

    // =========================================================================
    // COMPUTED – TABLE 3.1: By Product Gas from Natural Gas
    // =========================================================================

    #[Computed]
    public function hrcTable31Data(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        $ngQty            = $this->hrcTable3Data->first()['quantity'] ?? 0.0;
        $conversionFactor = 0.000036915; // TJ/m3
        $totalTJ          = $ngQty * $conversionFactor;

        return collect([
            [
                'conversion_factor' => $conversionFactor,
                'by_product_gas'    => $totalTJ,
                'unit'              => 'Tj',
                'tooltip'           => '<ul><li>Table 3 Natural Gas (Nm3) × Conversion Factor (TJ/m3) [0]</li><li>= ' . $ngQty . ' × ' . $conversionFactor . '</li></ul>',
            ],
        ]);
    }

    // =========================================================================
    // COMPUTED – TABLE 4: Natural Gas Total
    // =========================================================================

    #[Computed]
    public function hrcTable4Data(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        $steelPlate = new ConfigurationDataSteelPlate();
        $steelPlate->periodYear = $this->periodYear;
        $steelPlate->period     = $this->period;

        $byproduct0Plate = $steelPlate->steelPlateTable21()->first()['byproduct'] ?? 0.0;

        $cogHrp        = $this->hrcTable2Data->first()['cog'] ?? 0.0;
        $byproduct0Hrc = $cogHrp * 0.000018410;

        $naturalGas = $byproduct0Plate + $byproduct0Hrc;

        return collect([
            [
                'description' => 'COG PM + HRP',
                'tooltip'     => '<ul><li>Steel Plate Tab → Table 2.1 By Product Gas [0] + HRC Tab → Table 2.1 By Product Gas [0]</li><li>= ' . $byproduct0Plate . ' + ' . $byproduct0Hrc . '</li></ul>',
                'natural_gas' => $naturalGas,
                'unit'        => 'Tj',
            ],
        ]);
    }

    // =========================================================================
    // COMPUTED – TABLE 5: Electricity Summary
    // =========================================================================

    #[Computed]
    public function hrcTable5Data(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        $steelPlate = new ConfigurationDataSteelPlate();
        $steelPlate->periodYear = $this->periodYear;
        $steelPlate->period     = $this->period;

        $platePower = $steelPlate->steelPlateTable1()->first()['power']            ?? 0.0;
        $plateEf    = $steelPlate->steelPlateTable11()->first()['emission_factor'] ?? 0.0;

        $hrcPower = $this->hrcTable1Data->first()['power'] ?? 0.0;
        $hrcEf    = $this->hrcTable11Data->first()['emission_factor'] ?? 0.87;

        $totalMwh = $platePower + $hrcPower;
        $totalEf  = $totalMwh > 0
            ? (($platePower * $plateEf) + ($hrcPower * $hrcEf)) / $totalMwh
            : 0.0;

        $months = $this->resolveMonths();

        $coilQty = array_reduce(
            $months,
            fn(float $carry, string $month) => $carry + (float) MasterPcoCoil::where('period_year', $this->periodYear)
                ->whereRaw('LOWER(TRIM(period_month)) = ?', [strtolower(trim($month))])
                ->where('class', 'Coil Product')
                ->sum('quantity'),
            0.0
        );

        $plateQty = array_reduce(
            $months,
            fn(float $carry, string $month) => $carry + (float) MasterPcoPlate::where('period_year', $this->periodYear)
                ->whereRaw('LOWER(TRIM(period_month)) = ?', [strtolower(trim($month))])
                ->where('class', 'Plate Product')
                ->sum('quantity'),
            0.0
        );

        $plateHrcTon = $coilQty + $plateQty;
        return collect([
            [
                'items'      => 'Electricity Total',
                'tooltip'    => '<ul><li>Steel Plate Tab → Table 1 Power[0] + Steel HRC Tab → Table 1 Power[0]</li></ul>',
                'mwh'        => $totalMwh,
                'ef'         => $totalEf,
                'ef_tooltip' => '<ul><li>((MWh[Plate] × EF[Plate]) + (MWh[HRC] × EF[HRC])) / MWh[Total]</li><li>= ((' . $platePower . ' × ' . $plateEf . ') + (' . $hrcPower . ' × ' . $hrcEf . ')) / ' . $totalMwh . '</li></ul>',
            ],
            [
                'items'      => 'Plate',
                'tooltip'    => '<ul><li>Steel Plate Tab → Table 1 Power[0]</li></ul>',
                'mwh'        => $platePower,
                'ef'         => $plateEf,
                'ef_tooltip' => '<ul><li>Steel Plate Tab → Table 1.1 Emission Factor (tCO2/MWh) [0]</li></ul>',
            ],
            [
                'items'      => 'HRP',
                'tooltip'    => '<ul><li>Steel HRC Tab → Table 1 Power[0]</li></ul>',
                'mwh'        => $hrcPower,
                'ef'         => $hrcEf,
                'ef_tooltip' => '<ul><li>Steel HRC Tab → Table 1.1 Emission Factor (tCO2/MWh) [0]</li></ul>',
            ],
            [
                'items'      => 'Plate+HRC (Ton)',
                'tooltip'    => '<ul><li>Quantity from pco_coils where class = Coil Product + Quantity from pco_plates where class = Plate Product</li><li>= ' . $coilQty . ' + ' . $plateQty . '</li></ul>',
                'mwh'        => $plateHrcTon,
                'ef'         => null,
                'ef_tooltip' => '',
            ],
        ]);
    }

    // =========================================================================
    // COMPUTED – TABLE 6: By Product Gas Emission
    // =========================================================================

    #[Computed]
    public function hrcTable6Data(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        $byproduct0 = $this->hrcTable31Data->first()['by_product_gas'] ?? 0.0;

        $factor   = 56.1;
        $emission = $byproduct0 * $factor;

        return collect([
            [
                'emission_factor' => $factor,
                'ef_tooltip'      => '<ul><li>Emission Factor (tCO2/TJ) [0] = 56.1</li></ul>',
                'total_emission'  => $emission,
                'em_tooltip'      => '<ul><li>Table 2.1 By Product Gas [0] × Emission Factor (tCO2/TJ) [0]</li><li>= ' . $byproduct0 . ' × ' . $factor . '</li></ul>',
                'unit'            => 'tCO2',
            ],
        ]);
    }

    // =========================================================================
    // COMPUTED – TABLE 7: By Product Gas Emission
    // =========================================================================


    #[Computed]
    public function hrcTable7Data(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        // Steel Slab - Table 2 power[0] / 1000
        $slab = new ConfigurationData();
        $slab->periodYear = $this->periodYear;
        $slab->period     = $this->period;

        $slabMwh = ($slab->energyTableDataTable2->values()[0]['power'] ?? 0.0) / 1000;

        // Steel Slab - Table 2.1 "Emission Factor (tCO2/MWh)" [4] = last row emission_factor
        $slabEf = $slab->emissionTableData21->last()['emission_factor'] ?? 0.0;

        $slabTco2 = $slabMwh * $slabEf;

        // Table 5 values
        $t5 = $this->hrcTable5Data;
        $plateMwh  = $t5->values()[1]['mwh'] ?? 0.0;
        $plateEf   = $t5->values()[1]['ef']  ?? 0.0;
        $plateTco2 = $plateMwh * $plateEf;

        $hrpMwh  = $t5->values()[2]['mwh'] ?? 0.0;
        $hrpEf   = $t5->values()[2]['ef']  ?? 0.0;
        $hrpTco2 = $hrpMwh * $hrpEf;

        // Electricity Total
        $totalMwh  = $slabMwh + $plateMwh + $hrpMwh;
        $totalTco2 = $slabTco2 + $plateTco2 + $hrpTco2;
        $totalEf   = $totalMwh > 0 ? ($totalTco2 / $totalMwh) : 0.0;

        // Electricity Consumed = totalMwh / Table5 MWh[3] (Plate+HRC Ton)
        $plateHrcTon     = $t5->values()[3]['mwh'] ?? 0.0;
        $elecConsumed    = $plateHrcTon > 0 ? ($totalMwh / $plateHrcTon) : 0.0;

        return collect([
            [
                'items'      => 'Slab',
                'tooltip'    => '<ul><li>Steel Slab Tab → Table 2 Power[0] / 1000</li></ul>',
                'mwh'        => $slabMwh,
                'ef'         => $slabEf,
                'ef_tooltip' => '<ul><li>Steel Slab Tab → Table 2.1 Emission Factor (tCO2/MWh) [4]</li></ul>',
                'tco2'       => $slabTco2,
                'tco2_tooltip' => '<ul><li>MWh[0] × EF[0]</li><li>= ' . $slabMwh . ' × ' . $slabEf . '</li></ul>',
            ],
            [
                'items'      => 'Plate',
                'tooltip'    => '<ul><li>Table 5 MWh[1]</li></ul>',
                'mwh'        => $plateMwh,
                'ef'         => $plateEf,
                'ef_tooltip' => '<ul><li>Table 5 EF[1]</li></ul>',
                'tco2'       => $plateTco2,
                'tco2_tooltip' => '<ul><li>MWh[1] × EF[1]</li><li>= ' . $plateMwh . ' × ' . $plateEf . '</li></ul>',
            ],
            [
                'items'      => 'HRP',
                'tooltip'    => '<ul><li>Table 5 MWh[2]</li></ul>',
                'mwh'        => $hrpMwh,
                'ef'         => $hrpEf,
                'ef_tooltip' => '<ul><li>Table 5 EF[2]</li></ul>',
                'tco2'       => $hrpTco2,
                'tco2_tooltip' => '<ul><li>MWh[2] × EF[2]</li><li>= ' . $hrpMwh . ' × ' . $hrpEf . '</li></ul>',
            ],
            [
                'items'      => 'Electricity Total',
                'tooltip'    => '<ul><li>MWh[0] + MWh[1] + MWh[2]</li><li>= ' . $slabMwh . ' + ' . $plateMwh . ' + ' . $hrpMwh . '</li></ul>',
                'mwh'        => $totalMwh,
                'ef'         => $totalEf,
                'ef_tooltip' => '<ul><li>TCO2[3] / MWh[3]</li><li>= ' . $totalTco2 . ' / ' . $totalMwh . '</li></ul>',
                'tco2'       => $totalTco2,
                'tco2_tooltip' => '<ul><li>TCO2[0] + TCO2[1] + TCO2[2]</li><li>= ' . $slabTco2 . ' + ' . $plateTco2 . ' + ' . $hrpTco2 . '</li></ul>',
                'is_total'   => true,
            ],
            [
                'items'      => 'Electricity Consumed (MWh/Ton)',
                'tooltip'    => '<ul><li>MWh[3] / Table 5 MWh[3] (Plate+HRC Ton)</li><li>= ' . $totalMwh . ' / ' . $plateHrcTon . '</li></ul>',
                'mwh'        => $elecConsumed,
                'ef'         => null,
                'ef_tooltip' => '',
                'tco2'       => null,
                'tco2_tooltip' => '',
            ],
        ]);
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    public function render()
    {
        return view('livewire.configuration-data-hrc', [
            'availableYears'  => $this->availableYears,
            'hrcTable1Data'   => $this->hrcTable1Data,
            'hrcTable11Data'  => $this->hrcTable11Data,
            'hrcTable2Data'   => $this->hrcTable2Data,
            'hrcTable21Data'  => $this->hrcTable21Data,
            'hrcTable3Data'   => $this->hrcTable3Data,
            'hrcTable31Data'  => $this->hrcTable31Data,
            'hrcTable4Data'   => $this->hrcTable4Data,
            'hrcTable5Data'   => $this->hrcTable5Data,
            'hrcTable6Data'   => $this->hrcTable6Data,
            'hrcTable7Data'   => $this->hrcTable7Data,
        ]);
    }
}
