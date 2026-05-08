<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MasterEnergyData;
use App\Models\MasterSinter;
use App\Models\MasterBf;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;

#[Title('CHP - Power Plant')]
class ConfigurationDataCHP extends Component
{
    // =========================================================================
    // URL STATE
    // =========================================================================

    #[Url]
    public string $periodYear = '';

    #[Url]
    public string $period = '';

    // =========================================================================
    // CONSTANTS / CONVERSION FACTORS
    // =========================================================================

    protected float $electricityConversionFactor = 0.00000933032; // Tj/kWh
    protected float $steamConversionFactor       = 0.00318;       // Tj/ton
    protected float $steamEmissionFactor         = 0.195;         // tCO2/ton

    protected array $emissionConversionFactors = [
        0.0000031380, // BFG
        0.0000083680, // LDG
        0.0000184100, // COG
        0.0000377000, // HSD
    ];

    // =========================================================================
    // ROW DEFINITIONS
    // =========================================================================

    protected array $chpRows = [
        [
            'description' => 'BFG for KPE',
            'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = I022CB, plant_name = Sales (KPE, Power generation), criteria = CONSUMPTION, energy_name = BFG</li></ul>',
            'conditions'  => [
                ['plant_code' => 'I022CB', 'plant_name' => 'Sales (KPE, Power generation)', 'criteria' => 'CONSUMPTION', 'energy_name' => 'BFG'],
            ],
        ],
        [
            'description' => 'LDG for KPE',
            'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = I022CB, plant_name = Sales (KPE, Power generation), criteria = CONSUMPTION, energy_name = LDG</li></ul>',
            'conditions'  => [
                ['plant_code' => 'I022CB', 'plant_name' => 'Sales (KPE, Power generation)', 'criteria' => 'CONSUMPTION', 'energy_name' => 'LDG'],
            ],
        ],
        [
            'description' => 'COG for KPE',
            'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = I022CB, plant_name = Sales (KPE, Power generation), criteria = CONSUMPTION, energy_name = COG</li></ul>',
            'conditions'  => [
                ['plant_code' => 'I022CB', 'plant_name' => 'Sales (KPE, Power generation)', 'criteria' => 'CONSUMPTION', 'energy_name' => 'COG'],
            ],
        ],
        [
            'description' => 'HSD for KPE',
            'tooltip'     => '<ul><li>(Quantity from Master_energy_data where plant_code = I022CB, plant_name = Sales (KPE, Power generation), criteria = CONSUMPTION, energy_name = Heavy Oil) / 850 * 0.65</li></ul>',
            'conditions'  => [
                ['plant_code' => 'I022CB', 'plant_name' => 'Sales (KPE, Power generation)', 'criteria' => 'CONSUMPTION', 'energy_name' => 'Heavy Oil'],
            ],
            'formula' => '/850*0.65',
        ],
    ];

    protected array $steamRows = [
        [
            'description' => 'Steam Output',
            'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = I01200, plant_name = Purchase, energy_name = STEAM, criteria = PRODUCTION</li></ul>',
            'conditions'  => [
                ['plant_code' => 'I01200', 'plant_name' => 'Purchase', 'energy_name' => 'STEAM', 'criteria' => 'PRODUCTION'],
            ],
        ],
    ];

    protected array $electricityRows = [
        [
            'description' => 'Electricity Output',
            'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = I01200, plant_name = Purchase (KPE), energy_name = POWER, criteria = PRODUCTION</li></ul>',
            'conditions'  => [
                ['plant_code' => 'I01200', 'plant_name' => 'Purchase (KPE)', 'energy_name' => 'POWER', 'criteria' => 'PRODUCTION'],
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

    private function buildQuantityTable(array $rowDefs): Collection
    {
        $months = $this->resolveMonths();
        if (empty($months)) return collect();

        return collect($rowDefs)->map(function (array $rowDef) use ($months): array {
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
                'quantity'    => (float) $value,
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
    // COMPUTED – TABLE 1: FUEL INPUT
    // =========================================================================

    #[Computed]
    public function chpTableData(): Collection
    {
        return $this->buildQuantityTable($this->chpRows);
    }

    #[Computed]
    public function emissionTableData(): Collection
    {
        $rows  = $this->chpTableData->values();
        $total = 0.0;

        $result = $rows->map(function (array $row, int $index) use (&$total): array {
            $factor = $this->emissionConversionFactors[$index] ?? 0.0;
            $tj     = $row['quantity'] * $factor;

            if ($index === 3) {
                $tj *= 1000;
            }

            $total += $tj;

            return [
                'description' => $row['description'],
                'conversion'  => $factor,
                'tj'          => $tj,
                'tooltip'     => '<ul><li>Table 1 Fuel Input Quantity [' . $row['description'] . '] * Emission Factor (tCO2/Nm³ or tCO2/ton) = ' . number_format($factor, 10) . '</li></ul>',
            ];
        });

        $result->push([
            'description' => 'Total',
            'conversion'  => 'Total',
            'tj'          => $total,
            'tooltip'     => '<ul><li>Sum of all Fuel Input Emissions above</li></ul>',
        ]);

        return $result;
    }

    // =========================================================================
    // COMPUTED – TABLE 2: STEAM OUTPUT
    // =========================================================================

    #[Computed]
    public function steamTableData(): Collection
    {
        return $this->buildQuantityTable($this->steamRows);
    }

    #[Computed]
    public function steamConversionTableData(): Collection
    {
        $steamQty = $this->steamTableData->first()['quantity'] ?? 0.0;
        $steamTj  = $steamQty * $this->steamConversionFactor;
        $emission = $steamQty * $this->steamEmissionFactor;
        $efPerTj  = $steamTj > 0 ? ($emission / $steamTj) : 0.0;

        return collect([
            [
                'conversion' => $this->steamConversionFactor,
                'steam'      => $steamTj,
                'unit'       => 'Tj',
                'tooltip'    => '<ul><li>Steam Output * Conversion (Tj/ton)</li></ul>',
            ],
            [
                'conversion' => 'EF Steam',
                'steam'      => $this->steamEmissionFactor,
                'unit'       => 'tCo2/ton',
                'tooltip'    => '<ul><li>0.195</li></ul>',
            ],
            [
                'conversion' => 'Emission',
                'steam'      => $emission,
                'unit'       => 'tCO2',
                'tooltip'    => '<ul><li>Steam Output * EF Steam</li></ul>',
            ],
            [
                'conversion' => 'EF Steam',
                'steam'      => $efPerTj,
                'unit'       => 'tCO2/Tj',
                'tooltip'    => '<ul><li>Emission / Steam Tj</li></ul>',
            ],
        ]);
    }

    // =========================================================================
    // COMPUTED – TABLE 3: ELECTRICITY OUTPUT
    // =========================================================================

    #[Computed]
    public function electricityTableData(): Collection
    {
        $data = $this->buildQuantityTable($this->electricityRows);
        $row  = $data->first();

        if ($row) {
            $row['quantity_mwh'] = $row['quantity'] / 1000;
            $row['tooltip_mwh']  = ($row['tooltip'] ?? '') . ' / 1000';
        }

        return collect([
            $row ?? ['description' => 'No data', 'quantity' => 0.0, 'quantity_mwh' => 0.0],
        ]);
    }

    #[Computed]
    public function electricityConversionTableData(): Collection
    {
        $elecQty = $this->electricityTableData->first()['quantity'] ?? 0.0;
        $elecTj  = $elecQty * $this->electricityConversionFactor;

        return collect([
            [
                'conversion'  => number_format($this->electricityConversionFactor, 11),
                'electricity' => $elecTj,
                'unit'        => 'Tj',
                'tooltip'     => '<ul><li>Electricity Output * Conversion (Tj/kWh)</li></ul>',
            ],
        ]);
    }

    // =========================================================================
    // COMPUTED – TABLE 4: COKE (SINTER & BLAST FURNACE)
    // =========================================================================

    #[Computed]
    public function cokeTableData(): Collection
    {
        $months = $this->resolveMonths();
        if (empty($months)) return collect();

        $sinterQty = array_reduce(
            $months,
            fn(float $carry, string $month) => $carry + (float) MasterSinter::where('period_year', $this->periodYear)
                ->whereRaw('LOWER(TRIM(period_month)) = ?', [strtolower(trim($month))])
                ->where('classification', 'EF8T0001')
                ->where('sub_class', 'Total')
                ->sum('quantity'),
            0.0
        );

        $bfQty = array_reduce(
            $months,
            fn(float $carry, string $month) => $carry + (float) MasterBf::where('period_year', $this->periodYear)
                ->whereRaw('LOWER(TRIM(period_month)) = ?', [strtolower(trim($month))])
                ->where('classification', 'Fuel')
                ->where('sub_class', 'Irn.Mfg')
                ->where('sub_subclass', 'EF1K1')
                ->sum('quantity'),
            0.0
        );

        return collect([
            [
                'plant'    => 'Sinter Plant',
                'source'   => 'Breezed Coke',
                'quantity' => $sinterQty,
                'unit'     => 'Ton',
                'tooltip'  => '<ul><li>Quantity from master_sinters where classification = EF8T0001, sub_class = Total</li></ul>',
            ],
            [
                'plant'    => 'Blast Furnace Plant',
                'source'   => 'Lump Coke',
                'quantity' => $bfQty,
                'unit'     => 'Ton',
                'tooltip'  => '<ul><li>Quantity from master_bfs where classification = Fuel, sub_class = Irn.Mfg, sub_subclass = EF1K1</li></ul>',
            ],
        ]);
    }

    // =========================================================================
    // COMPUTED – TABLE 5: POWER EMISSION FACTOR FROM KPE
    // =========================================================================

    #[Computed]
    public function powerEmissionKpeData(): Collection
    {
        $emRows        = $this->emissionTableData->values();
        $rows          = collect();
        $totalEmission = 0.0;
        $factor        = 56.1;

        for ($i = 0; $i < 4; $i++) {
            $tj = $emRows[$i]['tj'] ?? 0.0;
            $em = $tj * $factor;
            $totalEmission += $em;

            $rows->push([
                'factor'         => $factor,
                'total_emission' => $em,
                'tooltip'        => '<ul><li>Table 1.1 Fuel Input Conversion, column Byproduct Gas[' . $i . '] * Emission Factor (TCO2/Tj)[' . $i . ']</li></ul>',
            ]);
        }

        $rows->push([
            'factor'         => 'Total',
            'total_emission' => $totalEmission,
            'tooltip'        => '<ul><li>Sum(TotalEmission[0]:[3])</li></ul>',
        ]);

        $elec  = $this->electricityTableData->first() ?? ['quantity' => 0.0, 'quantity_mwh' => 0.0];
        $mwh   = $elec['quantity_mwh'] ?? ($elec['quantity'] / 1000);
        $ratio = $mwh > 0 ? ($totalEmission / $mwh) : 0.0;

        $rows->push([
            'factor'         => 'Total / ( Electricity Output/1000 ) (tCO2/Mwh)',
            'total_emission' => $ratio,
            'tooltip'        => '<ul><li>(Sum(TotalEmission[0]:[3])) / (Table 3 Electricity Output / 1000)</li></ul>',
        ]);

        return $rows;
    }

    // =========================================================================
    // COMPUTED – TABLE 6: STEAM EMISSION FACTOR FROM KPE
    // =========================================================================

    #[Computed]
    public function steamEmissionKpeData(): Collection
    {
        $emRows  = $this->emissionTableData->values();
        $factors = [260, 182, 44.4, 74.1];

        $rows          = collect();
        $totalEmission = 0.0;

        for ($i = 0; $i < 4; $i++) {
            $tj     = $emRows[$i]['tj'] ?? 0.0;
            $factor = $factors[$i];
            $em     = $tj * $factor;
            $totalEmission += $em;

            $rows->push([
                'factor'         => $factor,
                'total_emission' => $em,
                'tooltip'        => '<ul><li>Table 1.1 Fuel Input Conversion, column Byproduct Gas[' . $i . '] * Emission Factor (TCO2/Tj)[' . $i . ']</li></ul>',
            ]);
        }

        $rows->push([
            'factor'         => 'Total',
            'total_emission' => $totalEmission,
            'tooltip'        => '<ul><li>Sum(TotalEmission[0]:[3])</li></ul>',
        ]);

        $elec = $this->electricityTableData->first() ?? ['quantity' => 0.0, 'quantity_mwh' => 0.0];
        $mwh  = $elec['quantity_mwh'] ?? ($elec['quantity'] / 1000);

        $ratio = $mwh > 0 ? ($totalEmission / $mwh) : 0.0;
        $rows->push([
            'factor'         => 'Total / ( Electricity Output/1000 ) (tCO2/MWh)',
            'total_emission' => $ratio,
            'tooltip'        => '<ul><li>(Sum(TotalEmission[0]:[3])) / (Table 3 Electricity Output / 1000)</li></ul>',
        ]);

        $steamEmission = $this->steamConversionTableData->firstWhere('unit', 'tCO2')['steam'] ?? 0.0;

        $netEmission = $totalEmission - $steamEmission;
        $rows->push([
            'factor'         => 'Total - Table 2.2 Emission (tCO2)',
            'total_emission' => $netEmission,
            'tooltip'        => '<ul><li>Table 6 Sum(TotalEmission[0]:[3]) - Table 2.2 Conversion Emission</li></ul>',
        ]);

        $netRatio = $mwh > 0 ? ($netEmission / $mwh) : 0.0;
        $rows->push([
            'factor'         => 'Net Emission / ( Electricity Output/1000 ) (tCO2/MWh)',
            'total_emission' => $netRatio,
            'tooltip'        => '<ul><li>(Table 6 Sum - Table 2.2 Emission) / (Table 3 Electricity Output / 1000)</li></ul>',
        ]);

        return $rows;
    }

    // =========================================================================
    // COMPUTED – TABLE 7: EMISSION FACTOR (tCO2/Tj) & TOTAL EMISSION
    // =========================================================================

    #[Computed]
    public function table7Data(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        $emRows  = $this->emissionTableData->values();
        $factors = [56.1, 56.1, 56.1, 74.1]; // [0] BFG, [1] LDG, [2] COG, [3] HSD

        $rows  = collect();
        $total = 0.0;

        for ($i = 0; $i < 4; $i++) {
            $tj     = $emRows[$i]['tj'] ?? 0.0;
            $factor = $factors[$i];
            $em     = $tj * $factor;
            $total += $em;

            $rows->push([
                'factor'         => $factor,
                'total_emission' => $em,
                'unit'           => 'tCO2',
                'tooltip'        => '<ul><li>Table 1.1 Fuel Input Conversion, Byproduct Gas[' . $i . '] × Emission Factor (tCO2/Tj)[' . $i . ']</li><li>= ' . $emRows[$i]['tj'] . ' × ' . $factor . '</li></ul>',
            ]);
        }

        $rows->push([
            'factor'         => 'Total Emission',
            'total_emission' => $total,
            'unit'           => 'tCO2',
            'tooltip'        => '<ul><li>Sum(TotalEmission[0]:[3])</li></ul>',
        ]);

        return $rows;
    }

    // =========================================================================
    // COMPUTED – TABLE 8: EMISSION FROM GENERATION
    // =========================================================================

    #[Computed]
    public function table8Data(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        // Table 7 Total Emission = row[4] total_emission
        $t7TotalEmission = $this->table7Data->values()[4]['total_emission'] ?? 0.0;

        // Table 2 Quantity[0] = Steam Output quantity (ton)
        $steamQty  = $this->steamTableData->first()['quantity'] ?? 0.0;
        $steamEf   = $this->steamEmissionFactor; // 0.195 tCO2/ton

        // Steam emission
        $steamEmission = $steamQty * $steamEf;

        // Electricity = Table 7 Total Emission - Table 8 Quantity[1] (steam emission)
        $electricityEmission = $t7TotalEmission - $steamEmission;

        return collect([
            [
                'description' => 'Electricity',
                'quantity'    => $electricityEmission,
                'unit'        => 'tCO2',
                'tooltip'     => '<ul><li>Table 7 Total Emission[4] − Table 8 Quantity[1] (Steam)</li><li>= ' . $t7TotalEmission . ' − ' . $steamEmission . '</li></ul>',
            ],
            [
                'description' => 'Steam',
                'quantity'    => $steamEmission,
                'unit'        => 'tCO2',
                'tooltip'     => '<ul><li>Table 2 Quantity[0] × Steam Emission Factor (0.195)</li><li>= ' . $steamQty . ' × ' . $steamEf . '</li></ul>',
            ],
        ]);
    }

    // =========================================================================
    // COMPUTED – TABLE 9: EMISSION FACTOR FROM KPW
    // =========================================================================

    #[Computed]
    public function table9Data(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        // Table 8 Quantity[0] = Electricity emission
        $t8Elec = $this->table8Data->values()[0]['quantity'] ?? 0.0;

        // Table 3 Quantity[0] / 1000 = Electricity Output in MWh
        $elecKwh = $this->electricityTableData->first()['quantity'] ?? 0.0;
        $elecMwh = $elecKwh / 1000;

        $efElec = $elecMwh > 0 ? ($t8Elec / $elecMwh) : 0.0;

        return collect([
            [
                'description' => 'Electricity',
                'quantity'    => $efElec,
                'unit'        => 'tCO2/MWh',
                'tooltip'     => '<ul><li>Table 8 Quantity[0] / (Table 3 Quantity[0] / 1000)</li><li>= ' . $t8Elec . ' / ' . $elecMwh . '</li></ul>',
            ],
        ]);
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    public function render()
    {
        return view('livewire.configuration-data-chp', [
            'availableYears'                 => $this->availableYears,
            'chpTableData'                   => $this->chpTableData,
            'emissionTableData'              => $this->emissionTableData,
            'steamTableData'                 => $this->steamTableData,
            'steamConversionTableData'       => $this->steamConversionTableData,
            'electricityTableData'           => $this->electricityTableData,
            'electricityConversionTableData' => $this->electricityConversionTableData,
            'cokeTableData'                  => $this->cokeTableData,
            'powerEmissionKpeData'           => $this->powerEmissionKpeData,
            'steamEmissionKpeData'           => $this->steamEmissionKpeData,
            'table7Data'                     => $this->table7Data,
            'table8Data'                     => $this->table8Data,
            'table9Data'                     => $this->table9Data,
        ]);
    }
}
