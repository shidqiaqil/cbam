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
    // Setiap entry mendefinisikan 1 baris tabel beserta kondisi query-nya.
    // Key 'formula' opsional: string ekspresi PHP yang diterapkan ke raw value.
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

    /**
     * Mengembalikan array nama bulan (lowercase) berdasarkan nilai $period.
     * Contoh: 'q1' → ['january', 'february', 'march'], 'jan' → ['january']
     */
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

    /**
     * Menjumlahkan quantity dari MasterEnergyData untuk 1 bulan
     * berdasarkan satu atau lebih kondisi (OR antar kondisi).
     */
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
     * Membangun Collection baris tabel dari array definisi baris ($rowDefs).
     * Setiap baris dijumlahkan lintas bulan sesuai periode yang dipilih.
     * Jika ada key 'formula', formula string diterapkan ke raw value.
     *
     * Return: Collection of ['description', 'tooltip', 'quantity']
     */
    private function buildQuantityTable(array $rowDefs): Collection
    {
        $months = $this->resolveMonths();
        if (empty($months)) return collect();

        return collect($rowDefs)->map(function (array $rowDef) use ($months): array {
            // Jumlahkan quantity semua bulan dalam periode
            $rawValue = array_reduce(
                $months,
                fn(float $carry, string $month) => $carry + $this->sumEnergyData($rowDef['conditions'], $month),
                0.0
            );

            // Terapkan formula opsional (misal: '/850*0.65')
            $value = isset($rowDef['formula'])
                ? eval("return {$rawValue}{$rowDef['formula']};")
                : $rawValue;

            return [
                'description' => $rowDef['description'],
                'tooltip'     => $rowDef['tooltip'] ?? '',
                'quantity'    => round($value, 2),
            ];
        });
    }

    // =========================================================================
    // LIVEWIRE HOOKS
    // =========================================================================

    /** Reset period jika tahun dikosongkan */
    public function updatedPeriodYear(): void
    {
        if (empty($this->periodYear)) {
            $this->period = '';
        }
    }

    // =========================================================================
    // COMPUTED – FILTER
    // =========================================================================

    /** Daftar tahun yang tersedia di MasterEnergyData untuk dropdown Year */
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

    /**
     * Table 1 (kiri) – Fuel Input Quantity
     * Mengembalikan quantity BFG, LDG, COG, HSD untuk periode terpilih.
     */
    #[Computed]
    public function chpTableData(): Collection
    {
        return $this->buildQuantityTable($this->chpRows);
    }

    /**
     * Table 1 (kanan) – Fuel Input Emission
     * Mengalikan quantity setiap bahan bakar dengan emission conversion factor-nya.
     * Baris terakhir adalah Total (sum semua baris).
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
                'tj'          => round($tj, 2),
                'tooltip'     => '<ul><li>Table 1 Fuel Input Quantity [' . $row['description'] . '] * Emission Factor (tCO2/Nm³ or tCO2/ton) = ' . number_format($factor, 10) . '</li></ul>',
            ];
        });

        // Tambahkan baris Total di akhir
        $result->push([
            'description' => 'Total',
            'conversion'  => 'Total',
            'tj'          => round($total, 2),
            'tooltip'     => '<ul><li>Sum of all Fuel Input Emissions above</li></ul>',
        ]);

        return $result;
    }

    // =========================================================================
    // COMPUTED – TABLE 2: STEAM OUTPUT
    // =========================================================================

    /**
     * Table 2 (kiri) – Steam Output Quantity
     * Mengembalikan quantity steam untuk periode terpilih.
     */
    #[Computed]
    public function steamTableData(): Collection
    {
        return $this->buildQuantityTable($this->steamRows);
    }

    /**
     * Table 2 (kanan) – Steam Output Conversion
     * Menghitung konversi steam: Tj, EF, Emission, dan EF per Tj.
     */
    #[Computed]
    public function steamConversionTableData(): Collection
    {
        $steamQty    = $this->steamTableData()->first()['quantity'] ?? 0.0;
        $steamTjRaw  = $steamQty * $this->steamConversionFactor;
        $steamTj     = round($steamTjRaw, 2);
        $emission    = round($steamQty * $this->steamEmissionFactor, 2);
        $efPerTj     = $steamTjRaw > 0 ? round($emission / $steamTjRaw, 4) : 0.0;

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

    /**
     * Table 3 (kiri) – Electricity Output Quantity
     * Mengembalikan quantity listrik dalam kWh dan MWh untuk periode terpilih.
     */
    #[Computed]
    public function electricityTableData(): Collection
    {
        $data = $this->buildQuantityTable($this->electricityRows);
        $row  = $data->first();

        if ($row) {
            $row['quantity_mwh'] = round($row['quantity'] / 1000, 2);
            $row['tooltip_mwh']  = ($row['tooltip'] ?? '') . ' / 1000';
        }

        return collect([
            $row ?? ['description' => 'No data', 'quantity' => 0, 'quantity_mwh' => 0],
        ]);
    }

    /**
     * Table 3 (kanan) – Electricity Output Conversion
     * Mengalikan quantity listrik (kWh) dengan conversion factor (Tj/kWh).
     */
    #[Computed]
    public function electricityConversionTableData(): Collection
    {
        $elecQty = $this->electricityTableData()->first()['quantity'] ?? 0.0;
        $elecTj  = round($elecQty * $this->electricityConversionFactor, 10);

        return collect([
            [
                'conversion' => number_format($this->electricityConversionFactor, 11),
                'electricity' => $elecTj,
                'unit'        => 'Tj',
                'tooltip'     => '<ul><li>Electricity Output * Conversion (Tj/kWh)</li></ul>',
            ],
        ]);
    }

    // =========================================================================
    // COMPUTED – COKE TABLE (SINTER & BLAST FURNACE)
    // =========================================================================

    /**
     * Coke Table – data dari MasterSinter dan MasterBf.
     * Berbeda dengan tabel lain, sumber datanya bukan MasterEnergyData
     * sehingga tidak menggunakan buildQuantityTable().
     */
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
                'quantity' => round($sinterQty, 2),
                'unit'     => 'Ton',
                'tooltip'  => '<ul><li>Quantity from master_sinters where classification = EF8T0001, sub_class = Total</li></ul>',
            ],
            [
                'plant'    => 'Blast Furnace Plant',
                'source'   => 'Lump Coke',
                'quantity' => round($bfQty, 2),
                'unit'     => 'Ton',
                'tooltip'  => '<ul><li>Quantity from master_bfs where classification = Fuel, sub_class = Irn.Mfg, sub_subclass = EF1K1</li></ul>',
            ],
        ]);
    }

    // =========================================================================
    // COMPUTED – TABLE 5: POWER EMISSION FACTOR FROM KPE
    // =========================================================================

    /**
     * Menghitung total emission (tCO2) per byproduct gas baris 0..3 berdasarkan
     * nilai Tj dari `emissionTableData()` dikalikan emission factor (56.1 TCO2/Tj).
     * Juga menghitung Sum dan rasio terhadap produksi listrik (MWh).
     */
    #[Computed]
    public function powerEmissionKpeData(): Collection
    {
        // emissionTableData() sudah berisi kolom 'tj' untuk tiap baris
        $emRows = $this->emissionTableData()->values();

        $rows = collect();
        $totalEmission = 0.0;

        // factor per baris (semua 56.1 sesuai requirement)
        $factor = 56.1;

        for ($i = 0; $i < 4; $i++) {
            $tj = $emRows[$i]['tj'] ?? 0.0;
            $em = $tj * $factor;
            $totalEmission += $em;

            $rows->push([
                'factor' => $factor,
                'total_emission' => round($em, 2),
                'tooltip' => '<ul><li>Table 1.1 Fuel Input Conversion, column Byproduct Gas[' . $i . '] * Emission Factor (TCO2/Tj)[' . $i . ']</li></ul>',
            ]);
        }

        // Sum row
        $rows->push([
            'factor' => 'Total',
            'total_emission' => round($totalEmission, 4),
            'tooltip' => '<ul><li>Sum(TotalEmission[0]:[3])</li></ul>',
        ]);

        // Compute ratio: Sum / (Table 3 Electricity Output / 1000)
        $elec = $this->electricityTableData()->first() ?? ['quantity' => 0, 'quantity_mwh' => 0];
        $mwh = $elec['quantity_mwh'] ?? (isset($elec['quantity']) ? ($elec['quantity'] / 1000) : 0);
        $ratio = $mwh > 0 ? round($totalEmission / $mwh, 4) : 0;

        $rows->push([
            'factor' => 'Total / ( Electricity Output/1000 ) (tCO2/Mwh)',
            'total_emission' => $ratio,
            'tooltip' => '<ul><li>(Sum(TotalEmission[0]:[3])) / (Table 3 Electricity Output / 1000)</li></ul>',
        ]);

        return $rows;
    }

    // =========================================================================
// COMPUTED – TABLE 6: STEAM EMISSION FACTOR FROM KPE
// =========================================================================

    /**
     * Menghitung total emission (tCO2) per byproduct gas berdasarkan
     * nilai Tj dari `emissionTableData()` dikalikan emission factor masing-masing.
     * Factor berbeda tiap baris: BFG=260, LDG=182, COG=44.4, HSD=74.1
     * Juga menghitung Sum dan rasio terhadap produksi listrik (MWh).
     */
    #[Computed]
    public function steamEmissionKpeData(): Collection
    {
        $emRows  = $this->emissionTableData()->values();
        $factors = [260, 182, 44.4, 74.1]; // [0] BFG, [1] LDG, [2] COG, [3] HSD

        $rows          = collect();
        $totalEmission = 0.0;

        // Baris 0-3: per bahan bakar
        for ($i = 0; $i < 4; $i++) {
            $tj     = $emRows[$i]['tj'] ?? 0.0;
            $factor = $factors[$i];
            $em     = $tj * $factor;
            $totalEmission += $em;

            $rows->push([
                'factor'         => $factor,
                'total_emission' => round($em, 2),
                'tooltip'        => '<ul><li>Table 1.1 Fuel Input Conversion, column Byproduct Gas[' . $i . '] * Emission Factor (TCO2/Tj)[' . $i . ']</li></ul>',
            ]);
        }

        // Baris 4: Sum
        $rows->push([
            'factor'         => 'Total',
            'total_emission' => round($totalEmission, 4),
            'tooltip'        => '<ul><li>Sum(TotalEmission[0]:[3])</li></ul>',
        ]);

        // Ambil MWh dari Table 3 (dipakai baris 5 & 7)
        $elec = $this->electricityTableData()->first() ?? ['quantity' => 0, 'quantity_mwh' => 0];
        $mwh  = $elec['quantity_mwh'] ?? ($elec['quantity'] / 1000 ?? 0);

        // Baris 5: Sum / MWh
        $ratio = $mwh > 0 ? round($totalEmission / $mwh, 4) : 0;
        $rows->push([
            'factor'         => 'Total / ( Electricity Output/1000 ) (tCO2/MWh)',
            'total_emission' => $ratio,
            'tooltip'        => '<ul><li>(Sum(TotalEmission[0]:[3])) / (Table 3 Electricity Output / 1000)</li></ul>',
        ]);

        // Ambil nilai Emission dari Table 2.2 (baris unit = 'tCO2')
        $steamEmission = $this->steamConversionTableData()
            ->firstWhere('unit', 'tCO2')['steam'] ?? 0.0;

        // Baris 6: Sum - Steam Emission
        $netEmission = round($totalEmission - $steamEmission, 4);
        $rows->push([
            'factor'         => 'Total - Table 2.2 Emission (tCO2)',
            'total_emission' => $netEmission,
            'tooltip'        => '<ul><li>Table 6 Sum(TotalEmission[0]:[3]) - Table 2.2 Conversion Emission</li></ul>',
        ]);

        // Baris 7: Net Emission / MWh
        $netRatio = $mwh > 0 ? round($netEmission / $mwh, 4) : 0;
        $rows->push([
            'factor'         => 'Net Emission / ( Electricity Output/1000 ) (tCO2/MWh)',
            'total_emission' => $netRatio,
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
            // Filter
            'availableYears'                 => $this->availableYears,
            // Table 1
            'chpTableData'                   => $this->chpTableData,
            'emissionTableData'              => $this->emissionTableData,
            // Table 2
            'steamTableData'                 => $this->steamTableData,
            'steamConversionTableData'       => $this->steamConversionTableData,
            // Table 3
            'electricityTableData'           => $this->electricityTableData,
            'electricityConversionTableData' => $this->electricityConversionTableData,
            // Coke
            'cokeTableData'                  => $this->cokeTableData,
            // Table 5
            'powerEmissionKpeData'           => $this->powerEmissionKpeData,
            // Table 6
            'steamEmissionKpeData'           => $this->steamEmissionKpeData,
        ]);
    }
}
