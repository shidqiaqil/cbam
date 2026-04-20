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
    public string $period = ''; // jan, feb, ..., q1, q2, q3, q4, yearly

    // =========================================================================
    // CONSTANTS / CONVERSION FACTORS
    // =========================================================================

    /** Konversi energi listrik dari kWh ke Terajoule (Tj) */
    protected float $electricityConversionFactor = 0.00000933032; // Tj/kWh

    /** Konversi steam dari ton ke Terajoule (Tj) */
    protected float $steamConversionFactor = 0.00318; // Tj/ton  (3.18/1000)

    /** Emission factor steam */
    protected float $steamEmissionFactor = 0.195; // tCO2/ton

    /**
     * Emission conversion factor per bahan bakar (tCO2/Nm³ atau tCO2/ton):
     * [0] BFG, [1] LDG, [2] COG, [3] HSD
     */
    protected array $emissionConversionFactors = [
        0.0000031380, // BFG  (3.138  / 1_000_000)
        0.0000083680, // LDG  (8.368  / 1_000_000)
        0.0000184100, // COG  (18.41  / 1_000_000)
        0.0000377000, // HSD  (37.7   / 1_000_000)
    ];

    // =========================================================================
    // ROW DEFINITIONS
    // =========================================================================

    /** Table 1 – Fuel Input (Quantity) */
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

    /** Table 2 – Steam Output (Quantity) */
    protected array $steamRows = [
        [
            'description' => 'Steam Output',
            'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = I01200, plant_name = Purchase, energy_name = STEAM, criteria = PRODUCTION</li></ul>',
            'conditions'  => [
                ['plant_code' => 'I01200', 'plant_name' => 'Purchase', 'energy_name' => 'STEAM', 'criteria' => 'PRODUCTION'],
            ],
        ],
    ];

    /** Table 3 – Electricity Output */
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

    /**
     * Build table rows from row definitions.
     * All values stored as raw floats — NO rounding here.
     * Rounding only happens in the blade via number_format().
     *
     * Return: Collection of ['description', 'tooltip', 'quantity']
     */
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

            // Apply optional formula (e.g. '/850*0.65') — no round()
            $value = isset($rowDef['formula'])
                ? eval("return {$rawValue}{$rowDef['formula']};")
                : $rawValue;

            return [
                'description' => $rowDef['description'],
                'tooltip'     => $rowDef['tooltip'] ?? '',
                'quantity'    => (float) $value,  // raw, unrounded
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

    /**
     * Table 1.1 – Fuel Input Emission
     * All intermediate calculations kept as raw floats.
     * 'tj' and 'conversion' stored unrounded for use in downstream computed properties.
     */
    #[Computed]
    public function emissionTableData(): Collection
    {
        $rows  = $this->chpTableData()->values();
        $total = 0.0;

        $result = $rows->map(function (array $row, int $index) use (&$total): array {
            $factor = $this->emissionConversionFactors[$index] ?? 0.0;
            $tj     = $row['quantity'] * $factor;

            // HSD (index 3): unit input adalah liter, dikali 1000 untuk konversi
            if ($index === 3) {
                $tj *= 1000;
            }

            $total += $tj;

            return [
                'description' => $row['description'],
                'conversion'  => $factor,
                'tj'          => $tj,   // raw, unrounded
                'tooltip'     => '<ul><li>Table 1 Fuel Input Quantity [' . $row['description'] . '] * Emission Factor (tCO2/Nm³ or tCO2/ton) = ' . number_format($factor, 10) . '</li></ul>',
            ];
        });

        // Total row — raw sum, no round()
        $result->push([
            'description' => 'Total',
            'conversion'  => 'Total',
            'tj'          => $total,   // raw, unrounded
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

    /**
     * Table 2.2 – Steam Output Conversion
     * All values raw floats. Rounding only in blade.
     */
    #[Computed]
    public function steamConversionTableData(): Collection
    {
        $steamQty   = $this->steamTableData()->first()['quantity'] ?? 0.0;
        $steamTj    = $steamQty * $this->steamConversionFactor;   // raw
        $emission   = $steamQty * $this->steamEmissionFactor;      // raw
        $efPerTj    = $steamTj > 0 ? ($emission / $steamTj) : 0.0; // raw

        return collect([
            [
                'conversion' => $this->steamConversionFactor,
                'steam'      => $steamTj,       // raw
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
                'steam'      => $emission,      // raw
                'unit'       => 'tCO2',
                'tooltip'    => '<ul><li>Steam Output * EF Steam</li></ul>',
            ],
            [
                'conversion' => 'EF Steam',
                'steam'      => $efPerTj,       // raw
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
            $row['quantity_mwh'] = $row['quantity'] / 1000;  // raw, no round()
            $row['tooltip_mwh']  = ($row['tooltip'] ?? '') . ' / 1000';
        }

        return collect([
            $row ?? ['description' => 'No data', 'quantity' => 0.0, 'quantity_mwh' => 0.0],
        ]);
    }

    /**
     * Table 3.1 – Electricity Output Conversion
     * Raw float stored in 'electricity'. Rounding only in blade.
     */
    #[Computed]
    public function electricityConversionTableData(): Collection
    {
        $elecQty = $this->electricityTableData()->first()['quantity'] ?? 0.0;
        $elecTj  = $elecQty * $this->electricityConversionFactor;   // raw, no round()

        return collect([
            [
                'conversion'  => number_format($this->electricityConversionFactor, 11),
                'electricity' => $elecTj,   // raw
                'unit'        => 'Tj',
                'tooltip'     => '<ul><li>Electricity Output * Conversion (Tj/kWh)</li></ul>',
            ],
        ]);
    }

    // =========================================================================
    // COMPUTED – COKE TABLE (SINTER & BLAST FURNACE)
    // =========================================================================

    #[Computed]
    public function cokeTableData(): Collection
    {
        $months = $this->resolveMonths();
        if (empty($months)) return collect();

        // Sinter Plant – Breezed Coke
        $sinterQty = array_reduce(
            $months,
            fn(float $carry, string $month) => $carry + (float) MasterSinter::where('period_year', $this->periodYear)
                ->whereRaw('LOWER(TRIM(period_month)) = ?', [strtolower(trim($month))])
                ->where('classification', 'EF8T0001')
                ->where('sub_class', 'Total')
                ->sum('quantity'),
            0.0
        );

        // Blast Furnace Plant – Lump Coke
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
                'quantity' => $sinterQty,   // raw
                'unit'     => 'Ton',
                'tooltip'  => '<ul><li>Quantity from master_sinters where classification = EF8T0001, sub_class = Total</li></ul>',
            ],
            [
                'plant'    => 'Blast Furnace Plant',
                'source'   => 'Lump Coke',
                'quantity' => $bfQty,       // raw
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
        $emRows = $this->emissionTableData()->values();

        $rows          = collect();
        $totalEmission = 0.0;
        $factor        = 56.1;

        for ($i = 0; $i < 4; $i++) {
            $tj = $emRows[$i]['tj'] ?? 0.0;   // already raw
            $em = $tj * $factor;               // raw, no round()
            $totalEmission += $em;

            $rows->push([
                'factor'         => $factor,
                'total_emission' => $em,        // raw
                'tooltip'        => '<ul><li>Table 1.1 Fuel Input Conversion, column Byproduct Gas[' . $i . '] * Emission Factor (TCO2/Tj)[' . $i . ']</li></ul>',
            ]);
        }

        // Sum row — raw
        $rows->push([
            'factor'         => 'Total',
            'total_emission' => $totalEmission,     // raw
            'tooltip'        => '<ul><li>Sum(TotalEmission[0]:[3])</li></ul>',
        ]);

        // Ratio row — raw
        $elec  = $this->electricityTableData()->first() ?? ['quantity' => 0.0, 'quantity_mwh' => 0.0];
        $mwh   = $elec['quantity_mwh'] ?? ($elec['quantity'] / 1000);
        $ratio = $mwh > 0 ? ($totalEmission / $mwh) : 0.0;   // raw, no round()

        $rows->push([
            'factor'         => 'Total / ( Electricity Output/1000 ) (tCO2/Mwh)',
            'total_emission' => $ratio,     // raw
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
        $emRows  = $this->emissionTableData()->values();
        $factors = [260, 182, 44.4, 74.1]; // [0] BFG, [1] LDG, [2] COG, [3] HSD

        $rows          = collect();
        $totalEmission = 0.0;

        // Rows 0-3: per fuel — all raw
        for ($i = 0; $i < 4; $i++) {
            $tj     = $emRows[$i]['tj'] ?? 0.0;   // raw
            $factor = $factors[$i];
            $em     = $tj * $factor;               // raw, no round()
            $totalEmission += $em;

            $rows->push([
                'factor'         => $factor,
                'total_emission' => $em,    // raw
                'tooltip'        => '<ul><li>Table 1.1 Fuel Input Conversion, column Byproduct Gas[' . $i . '] * Emission Factor (TCO2/Tj)[' . $i . ']</li></ul>',
            ]);
        }

        // Row 4: Sum — raw
        $rows->push([
            'factor'         => 'Total',
            'total_emission' => $totalEmission,     // raw
            'tooltip'        => '<ul><li>Sum(TotalEmission[0]:[3])</li></ul>',
        ]);

        // MWh from Table 3
        $elec = $this->electricityTableData()->first() ?? ['quantity' => 0.0, 'quantity_mwh' => 0.0];
        $mwh  = $elec['quantity_mwh'] ?? ($elec['quantity'] / 1000);

        // Row 5: Sum / MWh — raw
        $ratio = $mwh > 0 ? ($totalEmission / $mwh) : 0.0;
        $rows->push([
            'factor'         => 'Total / ( Electricity Output/1000 ) (tCO2/MWh)',
            'total_emission' => $ratio,     // raw
            'tooltip'        => '<ul><li>(Sum(TotalEmission[0]:[3])) / (Table 3 Electricity Output / 1000)</li></ul>',
        ]);

        // Steam emission from Table 2.2 (unit = 'tCO2')
        $steamEmission = $this->steamConversionTableData()
            ->firstWhere('unit', 'tCO2')['steam'] ?? 0.0;   // already raw

        // Row 6: Sum - Steam Emission — raw
        $netEmission = $totalEmission - $steamEmission;
        $rows->push([
            'factor'         => 'Total - Table 2.2 Emission (tCO2)',
            'total_emission' => $netEmission,   // raw
            'tooltip'        => '<ul><li>Table 6 Sum(TotalEmission[0]:[3]) - Table 2.2 Conversion Emission</li></ul>',
        ]);

        // Row 7: Net Emission / MWh — raw
        $netRatio = $mwh > 0 ? ($netEmission / $mwh) : 0.0;
        $rows->push([
            'factor'         => 'Net Emission / ( Electricity Output/1000 ) (tCO2/MWh)',
            'total_emission' => $netRatio,  // raw
            'tooltip'        => '<ul><li>(Table 6 Sum - Table 2.2 Emission) / (Table 3 Electricity Output / 1000)</li></ul>',
        ]);

        return $rows;
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
        ]);
    }
}
