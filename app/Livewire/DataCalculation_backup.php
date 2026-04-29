<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\BEmInstData;
use App\Models\MasterSinter;
use App\Models\MasterBf;
use App\Models\MasterEnergyData;
use App\Models\MasterSmpScrap;
use App\Models\MasterPcoPlate;
use App\Models\MasterPcoCoil;
use App\Models\MasterByproduct;

#[Layout('layouts.app')]
#[Title('Data Calculation')]
class DataCalculation extends Component
{
    #[Url]
    public string $activeTab = 'tab-1';

    // ── Filters — same naming convention as ConfigurationData ─────────────────
    #[Url]
    public string $periodType = 'monthly'; // monthly | quarterly | yearly

    #[Url]
    public string $periodYear = '';        // e.g. '2025'

    #[Url]
    public string $period = '';            // e.g. 'jan' | 'q1' | 'yearly'

    // ── Edit mode ─────────────────────────────────────────────────────────────
    public bool $isEditing = false;

    // ── Table rows for B_EmInst ───────────────────────────────────────────────
    public array $rows = [];

    // ── Static row definitions ────────────────────────────────────────────────
    private array $masterRows = [
        [
            'row_order' => 1,
            'method' => 'Combustion',
            'source_stream_name' => 'Anthracite',
            'ad_unit' => 't',
        ],
        [
            'row_order' => 2,
            'method' => 'Process Emissions',
            'source_stream_name' => 'PCI',
            'ad_unit' => 't',
        ],
        [
            'row_order' => 3,
            'method' => 'Mass balance',
            'source_stream_name' => 'Iron Ores',
            'ad_unit' => 't',
        ],
        [
            'row_order' => 4,
            'method' => 'Process emissions',
            'source_stream_name' => 'Lime Stone',
            'ad_unit' => 't',
        ],
        [
            'row_order' => 5,
            'method' => 'Combustion',
            'source_stream_name' => 'Coke fines',
            'ad_unit' => 't',
        ],
        [
            'row_order' => 6,
            'method' => 'Mass Balance',
            'source_stream_name' => 'Coke',
            'ad_unit' => 't',
        ],
        [
            'row_order' => 7,
            'method' => 'Process emissions',
            'source_stream_name' => 'Natural Gas',
            'ad_unit' => '1000Nm3',
        ],
        [
            'row_order' => 8,
            'method' => 'Process emissions',
            'source_stream_name' => 'Scrap (external)',
            'ad_unit' => 't',
        ],
        [
            'row_order' => 9,
            'method' => 'Process emissions',
            'source_stream_name' => 'Scrap (internal)',
            'ad_unit' => 't',
        ],
        [
            'row_order' => 10,
            'method' => 'Combustion',
            'source_stream_name' => 'Steel Product Plate',
            'ad_unit' => 't',
        ],
        [
            'row_order' => 11,
            'method' => 'Mass Balance',
            'source_stream_name' => 'Steel Product HRC',
            'ad_unit' => 't',
        ],
        [
            'row_order' => 12,
            'method' => 'Mass Balance',
            'source_stream_name' => 'BF Slag',
            'ad_unit' => 't',
        ],
        [
            'row_order' => 13,
            'method' => 'Mass Balance',
            'source_stream_name' => 'SMP Slag',
            'ad_unit' => 't',
        ],
        [
            'row_order' => 14,
            'method' => 'Mass Balance',
            'source_stream_name' => 'Sludge Dust',
            'ad_unit' => 't',
        ],
    ];

    // ── Dropdown options ──────────────────────────────────────────────────────
    public array $ncvUnits      = ['GJ/t', 'MJ/t', 'GJ/1000Nm3', 'MJ/1000Nm3', 'GJ/kg', 'MJ/kg'];
    public array $efUnits       = ['tCO2/GJ', 'tCO2/t', 'tCO2/1000Nm3', 'kgCO2/GJ', 'kgCO2/t'];
    public array $cContentUnits = ['tC/t', 'kgC/t', '%', 'tC/GJ'];

    // =========================================================================
    // Period helpers — same logic as ConfigurationData::resolveMonths()
    // =========================================================================

    /**
     * Maps the current period filter to a list of lowercase month name strings,
     * exactly matching values stored in period_month column.
     * e.g. 'jan' → ['january'], 'q1' → ['january','february','march'], 'yearly' → [all 12]
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

        $quarterMonths = [
            'q1' => ['january', 'february', 'march'],
            'q2' => ['april',   'may',       'june'],
            'q3' => ['july',    'august',    'september'],
            'q4' => ['october', 'november',  'december'],
        ];

        return match (true) {
            $this->period === 'yearly'            => array_values($monthMap),
            isset($quarterMonths[$this->period])  => $quarterMonths[$this->period],
            isset($monthMap[$this->period])       => [$monthMap[$this->period]],
            default                               => [],
        };
    }

    /**
     * Core query scoper — same approach as ConfigurationData::getRowQuantity().
     * Filters by period_year (int) and period_month (LOWER TRIM string).
     */
    private function applyPeriodScope($query, string $month): mixed
    {
        return $query
            ->where('period_year', (int) $this->periodYear)
            ->whereRaw('LOWER(TRIM(period_month)) = ?', [strtolower(trim($month))]);
    }

    /**
     * Sum a model query across all months in the active period.
     * $queryCallback receives a fresh query builder and a month string.
     */
    private function sumAcrossMonths(callable $queryCallback): ?float
    {
        $months = $this->resolveMonths();
        if (empty($months) || empty($this->periodYear)) return null;

        $total    = 0.0;
        $hasAny   = false;

        foreach ($months as $month) {
            $val = $queryCallback($month);
            if ($val) {
                $hasAny = true;
                $total += (float) $val;
            }
        }

        return $hasAny ? $total : null;
    }

    // =========================================================================
    // Lifecycle
    // =========================================================================

    public function mount(): void
    {
        if (empty($this->periodYear)) {
            // Default to latest year that actually has data, fallback to current year
            $latestYear = \App\Models\MasterEnergyData::max('period_year');
            $this->periodYear = $latestYear ? (string) $latestYear : (string) date('Y');
        }

        if (empty($this->period)) {
            // Default to current month abbreviation
            $this->period = strtolower(date('M')); // e.g. 'apr'
        }

        $this->loadRows();
    }

    // =========================================================================
    // Watchers
    // =========================================================================

    public function updatedPeriodType(): void
    {
        // Reset period value when type changes
        $this->period = match ($this->periodType) {
            'monthly'   => strtolower(date('M')),
            'quarterly' => 'q' . (int) ceil((int) date('m') / 3),
            default     => 'yearly',
        };

        $this->isEditing = false;
        $this->loadRows();
    }

    public function updatedPeriodYear(): void
    {
        $this->isEditing = false;
        $this->loadRows();
    }

    public function updatedPeriod(): void
    {
        $this->isEditing = false;
        $this->loadRows();
    }

    // =========================================================================
    // Load rows  (master def + saved manual inputs + live AD values)
    // =========================================================================

    public function loadRows(): void
    {
        // Map period back to a period_value string for BEmInstData storage
        $periodValue = $this->period;

        $saved = BEmInstData::where('period_type',  $this->periodType)
            ->where('year',         (int) $this->periodYear)
            ->where('period_value', $periodValue)
            ->get()
            ->keyBy('row_order');

        $this->rows = collect($this->masterRows)
            ->map(function (array $master) use ($saved) {
                $db = $saved->get($master['row_order']);

                $adBreakdown = $this->resolveAdBreakdown($master['row_order']);

                return [
                    'row_order'          => $master['row_order'],
                    'method'             => $master['method'],
                    'source_stream_name' => $master['source_stream_name'],
                    'ad_unit'            => $master['ad_unit'],
                    // Activity Data — queried live from master tables
                    'ad_value'           => $adBreakdown['value'],
                    'ad_tooltip'         => $adBreakdown['tooltip'],
                    // Manual inputs persisted in b_em_inst_data
                    'ncv_value'          => $db?->ncv_value,
                    'ncv_unit'           => $db?->ncv_unit,
                    'ef_value'           => $db?->ef_value,
                    'ef_unit'            => $db?->ef_unit,
                    'carbon_content'     => $db?->carbon_content,
                    'c_content_unit'     => $db?->c_content_unit,
                ];
            })
            ->toArray();
    }

    // =========================================================================
    // Activity Data — one private method per row
    // =========================================================================

    /**
     * Returns ['value' => float|null, 'tooltip' => string]
     * tooltip shows each component label + its value, then total.
     */
    private function resolveAdBreakdown(int $rowOrder): array
    {
        return match ($rowOrder) {
            1  => $this->adBreakdown1(),
            2  => $this->adBreakdown2(),
            3  => $this->adBreakdown3(),
            4  => $this->adBreakdown4(),
            5  => $this->adBreakdown5(),
            6  => $this->adBreakdown6(),
            7  => $this->adBreakdown7(),
            8  => $this->adBreakdown8(),
            9  => $this->adBreakdown9(),
            10 => $this->adBreakdown10(),
            11 => $this->adBreakdown11(),
            12 => $this->adBreakdown12(),
            13 => $this->adBreakdown13(),
            14 => $this->adBreakdown14(),
            default => ['value' => null, 'tooltip' => ''],
        };
    }

    // ── Helpers for building breakdown arrays ─────────────────────────────────

    /** Single-source row */
    private function single(string $label, ?float $val): array
    {
        // Label may already contain &#10; line breaks
        $tooltip = $label . ': ' . ($val !== null ? number_format($val, 3) : '—');
        return ['value' => $val, 'tooltip' => $tooltip];
    }

    /** Multi-source row: items = [['label (may contain &#10;)', value], ...] */
    private function multi(array $items): array
    {
        $lines  = [];
        $total  = 0.0;
        $hasAny = false;

        foreach ($items as [$label, $val]) {
            // Label already has &#10; embedded for multi-line
            $lines[] = $label . ': ' . ($val !== null ? number_format($val, 3) : '—');
            if ($val !== null) {
                $hasAny = true;
                $total += $val;
            }
        }

        $lines[] = str_repeat('─', 28);
        $lines[] = 'Total: ' . ($hasAny ? number_format($total, 3) : '—');

        return [
            'value'   => $hasAny ? $total : null,
            'tooltip' => implode('&#10;', $lines),
        ];
    }

    // ── Per-row breakdowns ────────────────────────────────────────────────────

    private function adBreakdown1(): array
    {
        $v = $this->qSinter('ABA02', 'Total');
        return $this->single('from master_sinters&#10;  classification = ABA02, sub_class = Total', $v);
    }

    private function adBreakdown2(): array
    {
        $v = $this->qBf('Fuel', 'Irn.Mfg', 'PCI.PB');
        return $this->single('from master_bfs&#10;  classification = Fuel, sub_class = Irn.Mfg, sub_subclass = PCI.PB', $v);
    }

    private function adBreakdown3(): array
    {
        return $this->multi([
            ['from master_bfs&#10;  classification = Main Raw Material, sub_class = MAC.O, sub_subclass = MAC.O', $this->qBf('Main Raw Material', 'MAC.O', 'MAC.O')],
            ['from master_bfs&#10;  classification = Main Raw Material, sub_class = BBR.O, sub_subclass = BBR.O', $this->qBf('Main Raw Material', 'BBR.O', 'BBR.O')],
            ['from master_sinters&#10;  classification = Iron ore, sub_class = Total', $this->qSinter('Iron ore', 'Total')],
        ]);
    }

    private function adBreakdown4(): array
    {
        return $this->multi([
            ['from master_sinters&#10;  classification = ACL08, sub_class = Total', $this->qSinter('ACL08', 'Total')],
            ['from master_sinters&#10;  classification = ACL12, sub_class = Total', $this->qSinter('ACL12', 'Total')],
        ]);
    }

    private function adBreakdown5(): array
    {
        $v = $this->qSinter('EF8T0001', 'Total');
        return $this->single('from master_sinters&#10;  classification = EF8T0001, sub_class = Total', $v);
    }

    private function adBreakdown6(): array
    {
        $v = $this->qBf('Fuel', 'Irn.Mfg', 'EF1K1.PD');
        return $this->single('from master_bfs&#10;  classification = Fuel, sub_class = Irn.Mfg, sub_subclass = EF1K1.PD', $v);
    }

    private function adBreakdown7(): array
    {
        $kp  = $this->qEnergy('I01200', 'Purchase for KP',  'PRODUCTION', 'NG');
        $hsm = $this->qEnergy('I01200', 'Purchase for HSM', 'PRODUCTION', 'NG');

        $raw = ($kp ?? 0) + ($hsm ?? 0);
        $hasAny = $kp !== null || $hsm !== null;

        $lines = [
            'from master_energy_data',
            '  plant_code = I01200, plant_name = Purchase for KP',
            '  criteria = PRODUCTION, energy_name = NG: ' . ($kp !== null ? number_format($kp, 3) : '—'),
            'from master_energy_data',
            '  plant_code = I01200, plant_name = Purchase for HSM',
            '  criteria = PRODUCTION, energy_name = NG: ' . ($hsm !== null ? number_format($hsm, 3) : '—'),
            str_repeat('─', 28),
            'Sum: '     . ($hasAny ? number_format($raw, 3) : '—'),
            '÷ 1000 = ' . ($hasAny ? number_format($raw / 1000, 3) : '—') . ' (1000Nm³)',
        ];

        return [
            'value'   => $hasAny ? $raw / 1000 : null,
            'tooltip' => implode('&#10;', $lines),
        ];
    }

    private function adBreakdown8(): array
    {
        $v = $this->qScrap('Total Charging Amount', 'High scrap purchasing');
        return $this->single('from master_smp_scrap&#10;  category = Total Charging Amount&#10;  sub_category = High scrap purchasing', $v);
    }

    private function adBreakdown9(): array
    {
        return $this->multi([
            ['from master_smp_scrap&#10;  category = Total Charging Amount&#10;  sub_category = Manufactured Scrap (Plate & Slab)', $this->qScrap('Total Charging Amount', 'Manufactured Scrap (Plate & Slab)')],
            ['from master_smp_scrap&#10;  category = Total Charging Amount&#10;  sub_category = Scrap Recovery', $this->qScrap('Total Charging Amount', 'Scrap Recovery')],
            ['from master_smp_scrap&#10;  category = Total Charging Amount&#10;  sub_category = Return Molten Steel (Hot Scrap)', $this->qScrap('Total Charging Amount', 'Return Molten Steel (Hot Scrap)')],
        ]);
    }

    private function adBreakdown10(): array
    {
        $v = $this->qPcoPlates('Plate Product');
        return $this->single('from master_pco_plates&#10;  class = Plate Product', $v);
    }

    private function adBreakdown11(): array
    {
        $v = $this->qPcoCoils('Coil Product');
        return $this->single('from master_pco_coils&#10;  class = Coil Product', $v);
    }

    private function adBreakdown12(): array
    {
        return $this->multi([
            ['from master_byproducts&#10;  source = BF Slag, group = Ext. Sales', $this->qByproduct('BF Slag', 'Ext. Sales')],
            ['from master_byproducts&#10;  source = BF Slag, group = Free Trial', $this->qByproduct('BF Slag', 'Free Trial')],
        ]);
    }

    private function adBreakdown13(): array
    {
        return $this->multi([
            ['from master_byproducts&#10;  source = SMP Slag, group = Ext. Sales', $this->qByproduct('SMP Slag', 'Ext. Sales')],
            ['from master_byproducts&#10;  source = SMP Slag, group = Free Trial', $this->qByproduct('SMP Slag', 'Free Trial')],
        ]);
    }

    private function adBreakdown14(): array
    {
        return $this->multi([
            ['from master_byproducts&#10;  source = Sludge Dust, group = Ext. Sales', $this->qByproduct('Sludge Dust', 'Ext. Sales')],
            ['from master_byproducts&#10;  source = Sludge Dust, group = Ext. Treatment', $this->qByproduct('Sludge Dust', 'Ext. Treatment')],
        ]);
    }

    // =========================================================================
    // Table-level query helpers — each loops over resolved months and sums
    // =========================================================================

    private function qSinter(string $classification, string $subClass): ?float
    {
        return $this->sumAcrossMonths(function (string $month) use ($classification, $subClass) {
            return MasterSinter::where('period_year', (int) $this->periodYear)
                ->whereRaw('LOWER(TRIM(period_month)) = ?', [strtolower(trim($month))])
                ->where('classification', $classification)
                ->where('sub_class', $subClass)
                ->sum('quantity');
        });
    }

    private function qBf(string $classification, string $subClass, string $subSubclass): ?float
    {
        return $this->sumAcrossMonths(function (string $month) use ($classification, $subClass, $subSubclass) {
            return MasterBf::where('period_year', (int) $this->periodYear)
                ->whereRaw('LOWER(TRIM(period_month)) = ?', [strtolower(trim($month))])
                ->where('classification', $classification)
                ->where('sub_class', $subClass)
                ->where('sub_subclass', $subSubclass)
                ->sum('quantity');
        });
    }

    private function qEnergy(string $plantCode, string $plantName, string $criteria, string $energyName): ?float
    {
        return $this->sumAcrossMonths(function (string $month) use ($plantCode, $plantName, $criteria, $energyName) {
            return MasterEnergyData::where('period_year', (int) $this->periodYear)
                ->whereRaw('LOWER(TRIM(period_month)) = ?', [strtolower(trim($month))])
                ->where('plant_code',  $plantCode)
                ->where('plant_name',  $plantName)
                ->where('criteria',    $criteria)
                ->where('energy_name', $energyName)
                ->sum('quantity');
        });
    }

    private function qScrap(string $category, string $subCategory): ?float
    {
        return $this->sumAcrossMonths(function (string $month) use ($category, $subCategory) {
            return MasterSmpScrap::where('period_year', (int) $this->periodYear)
                ->whereRaw('LOWER(TRIM(period_month)) = ?', [strtolower(trim($month))])
                ->where('category',     $category)
                ->where('sub_category', $subCategory)
                ->sum('quantity');
        });
    }

    private function qPcoPlates(string $class): ?float
    {
        return $this->sumAcrossMonths(function (string $month) use ($class) {
            return MasterPcoPlate::where('period_year', (int) $this->periodYear)
                ->whereRaw('LOWER(TRIM(period_month)) = ?', [strtolower(trim($month))])
                ->where('class', $class)
                ->sum('quantity');
        });
    }

    private function qPcoCoils(string $class): ?float
    {
        return $this->sumAcrossMonths(function (string $month) use ($class) {
            return MasterPcoCoil::where('period_year', (int) $this->periodYear)
                ->whereRaw('LOWER(TRIM(period_month)) = ?', [strtolower(trim($month))])
                ->where('class', $class)
                ->sum('quantity');
        });
    }

    private function qByproduct(string $source, string $group): ?float
    {
        return $this->sumAcrossMonths(function (string $month) use ($source, $group) {
            return MasterByproduct::where('period_year', (int) $this->periodYear)
                ->whereRaw('LOWER(TRIM(period_month)) = ?', [strtolower(trim($month))])
                ->where('source', $source)
                ->where('group',  $group)
                ->sum('quantity');
        });
    }

    // =========================================================================
    // Edit / Save
    // =========================================================================

    public function enterEditMode(): void
    {
        $this->isEditing = true;
    }

    public function cancelEdit(): void
    {
        $this->isEditing = false;
        $this->loadRows();
    }

    public function save(): void
    {
        foreach ($this->rows as $row) {
            BEmInstData::updateOrCreate(
                [
                    'period_type'  => $this->periodType,
                    'year'         => (int) $this->periodYear,
                    'period_value' => $this->period,
                    'row_order'    => $row['row_order'],
                ],
                [
                    'method'             => $row['method'],
                    'source_stream_name' => $row['source_stream_name'],
                    'ad_unit'            => $row['ad_unit'],
                    'ncv_value'          => ($row['ncv_value'] !== '' && $row['ncv_value'] !== null) ? $row['ncv_value'] : null,
                    'ncv_unit'           => $row['ncv_unit']        ?: null,
                    'ef_value'           => ($row['ef_value']  !== '' && $row['ef_value']  !== null) ? $row['ef_value']  : null,
                    'ef_unit'            => $row['ef_unit']         ?: null,
                    'carbon_content'     => ($row['carbon_content'] !== '' && $row['carbon_content'] !== null) ? $row['carbon_content'] : null,
                    'c_content_unit'     => $row['c_content_unit']  ?: null,
                ]
            );
        }

        $this->isEditing = false;
        $this->dispatch('saved');
        session()->flash('success', 'Data saved successfully.');
    }

    // =========================================================================
    // CO2e calculation (called from blade)
    // =========================================================================

    /**
     * Returns ['value' => float|null, 'tooltip' => string]
     *
     * Formula per row:
     *   if EF not null → AD × EF
     *   else           → AD × Carbon Content
     */
    public function computeCo2e(array $row): array
    {
        $ad  = ($row['ad_value']       ?? null) !== null ? (float) $row['ad_value']       : null;
        $ef  = ($row['ef_value']       ?? null) !== null ? (float) $row['ef_value']       : null;
        $cc  = ($row['carbon_content'] ?? null) !== null ? (float) $row['carbon_content'] : null;

        $fmt = fn(?float $v) => $v !== null ? number_format($v, 4) : '—';

        // EF is filled → AD × EF
        if ($ad !== null && $ef !== null) {
            $result  = $ad * $ef;
            $tooltip = implode('&#10;', [
                'Formula: AD × EF',
                'AD: '     . number_format($ad, 3),
                'EF: '     . $fmt($ef),
                str_repeat('─', 28),
                'Result: ' . number_format($result, 3),
            ]);
            return ['value' => $result, 'tooltip' => $tooltip];
        }

        // EF is null → AD × Carbon Content
        if ($ad !== null && $cc !== null) {
            $result  = $ad * $cc;
            $tooltip = implode('&#10;', [
                'Formula: AD × Carbon Content',
                'AD: '             . number_format($ad, 3),
                'Carbon Content: ' . $fmt($cc),
                str_repeat('─', 28),
                'Result: ' . number_format($result, 3),
            ]);
            return ['value' => $result, 'tooltip' => $tooltip];
        }

        // Not enough data
        $missing = [];
        if ($ad === null) $missing[] = 'AD';
        if ($ef === null && $cc === null) $missing[] = 'EF or Carbon Content';
        $tooltip = 'Insufficient data: ' . implode(', ', $missing) . ' missing';

        return ['value' => null, 'tooltip' => $tooltip];
    }

    // =========================================================================
    // Utilities
    // =========================================================================

    /**
     * Sum nullable floats. Returns null only when ALL inputs are null;
     * otherwise treats null operands as 0.
     */
    private function sumNullable(?float ...$values): ?float
    {
        $hasAny = false;
        $total  = 0.0;

        foreach ($values as $v) {
            if ($v !== null) {
                $hasAny = true;
                $total += $v;
            }
        }

        return $hasAny ? $total : null;
    }

    // =========================================================================
    // Period option lists (for filter dropdowns in the blade)
    // =========================================================================

    public function getMonthOptions(): array
    {
        return [
            'jan' => 'January',
            'feb' => 'February',
            'mar' => 'March',
            'apr' => 'April',
            'may' => 'May',
            'jun' => 'June',
            'jul' => 'July',
            'aug' => 'August',
            'sep' => 'September',
            'oct' => 'October',
            'nov' => 'November',
            'dec' => 'December',
        ];
    }

    public function getQuarterOptions(): array
    {
        return [
            'q1' => 'Q1 (Jan–Mar)',
            'q2' => 'Q2 (Apr–Jun)',
            'q3' => 'Q3 (Jul–Sep)',
            'q4' => 'Q4 (Oct–Dec)',
        ];
    }

    public function getYearOptions(): array
    {
        return MasterEnergyData::distinct()
            ->orderByDesc('period_year')
            ->pluck('period_year')
            ->values()
            ->toArray();
    }

    // =========================================================================

    public function render()
    {
        return view('livewire.data-calculation', [
            'monthOptions'   => $this->getMonthOptions(),
            'quarterOptions' => $this->getQuarterOptions(),
            'yearOptions'    => $this->getYearOptions(),
        ]);
    }
}
