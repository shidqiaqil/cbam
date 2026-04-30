<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Livewire\DataCalculation; // For direct emissions total
use App\Livewire\ConfigurationData; // For indirect Table 8

#[Layout('layouts.app')]
#[Title('C_Emissions & Energy')]
class DataCalculationCEmission extends Component
{
    #[Url]
    public string $periodType = 'monthly';

    #[Url]
    public string $periodYear = '';

    #[Url]
    public string $period = '';

    // Computed emissions
    public ?float $directEmissions = null;
    public ?float $indirectEmissions = null;
    public ?float $totalEmissions = null;

    // =========================================================================
    // Period helpers (copied from DataCalculation)
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

        $quarterMonths = [
            'q1' => ['january', 'february', 'march'],
            'q2' => ['april', 'may', 'june'],
            'q3' => ['july', 'august', 'september'],
            'q4' => ['october', 'november', 'december'],
        ];

        return match (true) {
            $this->period === 'yearly'            => array_values($monthMap),
            isset($quarterMonths[$this->period])  => $quarterMonths[$this->period],
            isset($monthMap[$this->period])       => [$monthMap[$this->period]],
            default                               => [],
        };
    }

    // =========================================================================
    // Lifecycle & Watchers
    // =========================================================================

    public function mount(): void
    {
        if (empty($this->periodYear)) {
            $latestYear = \App\Models\MasterEnergyData::max('period_year');
            $this->periodYear = $latestYear ? (string) $latestYear : (string) date('Y');
        }

        if (empty($this->period)) {
            $this->period = strtolower(date('M'));
        }

        $this->loadEmissions();
    }

    public function updatedPeriodType(): void
    {
        $this->period = match ($this->periodType) {
            'monthly'   => strtolower(date('M')),
            'quarterly' => 'q' . (int) ceil((int) date('m') / 3),
            default     => 'yearly',
        };
        $this->loadEmissions();
    }

    public function updatedPeriodYear(): void
    {
        $this->loadEmissions();
    }
    public function updatedPeriod(): void
    {
        $this->loadEmissions();
    }

    // =========================================================================
    // Main computation
    // =========================================================================

    public function loadEmissions(): void
    {
        if (empty($this->periodYear) || empty($this->period)) {
            $this->directEmissions = $this->indirectEmissions = $this->totalEmissions = null;
            return;
        }

        // ── Direct: Total CO2e Fossil from B_EmInst ───────────────────────────
        // Instantiate DataCalculation and call loadRows() to populate rows,
        // then sum computeCo2e() across all 14 rows.
        $beminst = new DataCalculation();
        $beminst->periodType = $this->periodType;
        $beminst->periodYear = $this->periodYear;
        $beminst->period     = $this->period;
        $beminst->loadRows();

        $directTotal = 0.0;
        $hasAnyDirect = false;
        foreach ($beminst->rows as $row) {
            $co2e = $beminst->computeCo2e($row)['value'] ?? null;
            if ($co2e !== null) {
                $hasAnyDirect = true;
                $directTotal += $co2e;
            }
        }
        $this->directEmissions = $hasAnyDirect ? $directTotal : null;

        // ── Indirect: ConfigurationData emissionTableData8 index [2] ─────────
        // emissionTableData8 is a #[Computed] property on ConfigurationData.
        // We instantiate the component and set period context, then access
        // the computed property directly (it lazy-evaluates on first access).
        $config = new ConfigurationData();
        $config->periodYear = $this->periodYear;
        $config->period     = $this->period;

        // emissionTableData8 is a Livewire #[Computed] — access as property
        $table8 = $config->emissionTableData8;

        // index [2] = "Indirect total" row
        $indirectRow = $table8->values()->get(2);
        $this->indirectEmissions = $indirectRow ? ((float) ($indirectRow['quantity'] ?? 0) ?: null) : null;

        // ── Total ─────────────────────────────────────────────────────────────
        $this->totalEmissions = $this->sumNullable($this->directEmissions, $this->indirectEmissions);
    }

    private function sumNullable(?float ...$values): ?float
    {
        $hasAny = false;
        $total = 0.0;
        foreach ($values as $v) {
            if ($v !== null) {
                $hasAny = true;
                $total += $v;
            }
        }
        return $hasAny ? $total : null;
    }

    // =========================================================================
    // Filter options (copied from DataCalculation)
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
        return ['q1' => 'Q1 (Jan–Mar)', 'q2' => 'Q2 (Apr–Jun)', 'q3' => 'Q3 (Jul–Sep)', 'q4' => 'Q4 (Oct–Dec)'];
    }

    public function getYearOptions(): array
    {
        return \App\Models\MasterEnergyData::distinct()->orderByDesc('period_year')->pluck('period_year')->values()->toArray();
    }

    public function render()
    {
        return view('livewire.data-calculation-c-emission', [
            'monthOptions'   => $this->getMonthOptions(),
            'quarterOptions' => $this->getQuarterOptions(),
            'yearOptions'    => $this->getYearOptions(),
        ]);
    }
}
