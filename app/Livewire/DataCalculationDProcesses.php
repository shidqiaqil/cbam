<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\MasterPcoCoil;
use App\Models\MasterPcoPlate;

class DataCalculationDProcesses extends Component
{
    public string $periodType = 'monthly';
    public string $periodYear = '';
    public string $period     = '';

    // ── (a) Total production levels ───────────────────────────────────────────
    public ?float $totalProduction = null; // Coil + Plate

    // ── (g) Attributed emissions ──────────────────────────────────────────────
    public ?float $dirEm = null; // Total Direct Emissions from C_Emissions

    // ── (h) Import and export of measurable heat ──────────────────────────────
    // Imported
    public ?float $hImportedHeat = null;   // Amount of net measurable heat (TJ) - imported
    public ?float $hImportedEF   = null;   // Emission factor (tCO2/TJ) - imported
    // Exported
    public ?float $hExportedHeat = null;   // Amount of net measurable heat (TJ) - exported
    public ?float $hExportedEF   = null;   // Emission factor (tCO2/TJ) - exported

    // =========================================================================
    // Period helpers
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
            'q2' => ['april',   'may',       'june'],
            'q3' => ['july',    'august',    'september'],
            'q4' => ['october', 'november',  'december'],
        ];

        return match (true) {
            $this->period === 'yearly'           => array_values($monthMap),
            isset($quarterMonths[$this->period]) => $quarterMonths[$this->period],
            isset($monthMap[$this->period])      => [$monthMap[$this->period]],
            default                              => [],
        };
    }

    private function sumAcrossMonths(callable $callback): ?float
    {
        $months = $this->resolveMonths();
        if (empty($months) || empty($this->periodYear)) return null;

        $total  = 0.0;
        $hasAny = false;

        foreach ($months as $month) {
            $val = $callback($month);
            if ($val) {
                $hasAny = true;
                $total += (float) $val;
            }
        }

        return $hasAny ? $total : null;
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

        $this->loadData();
    }

    public function updatedPeriodType(): void
    {
        $this->period = match ($this->periodType) {
            'monthly'   => strtolower(date('M')),
            'quarterly' => 'q' . (int) ceil((int) date('m') / 3),
            default     => 'yearly',
        };
        $this->loadData();
    }

    public function updatedPeriodYear(): void
    {
        $this->loadData();
    }
    public function updatedPeriod(): void
    {
        $this->loadData();
    }

    // =========================================================================
    // Main loader
    // =========================================================================

    public function loadData(): void
    {
        if (empty($this->periodYear) || empty($this->period)) {
            $this->totalProduction = $this->dirEm = null;
            return;
        }

        $this->loadSection_a();
        $this->loadSection_g();
        $this->loadSection_h();
    }

    // ── (a) Total Production Levels ───────────────────────────────────────────

    private function loadSection_a(): void
    {
        $coil = $this->sumAcrossMonths(
            fn($month) =>
            MasterPcoCoil::where('period_year', (int) $this->periodYear)
                ->whereRaw('LOWER(TRIM(period_month)) = ?', [strtolower(trim($month))])
                ->where('class', 'Coil Product')
                ->sum('quantity')
        );

        $plate = $this->sumAcrossMonths(
            fn($month) =>
            MasterPcoPlate::where('period_year', (int) $this->periodYear)
                ->whereRaw('LOWER(TRIM(period_month)) = ?', [strtolower(trim($month))])
                ->where('class', 'Plate Product')
                ->sum('quantity')
        );

        $this->totalProduction = $this->sumNullable($coil, $plate);
    }

    // ── (g) Directly Attributable Emissions ───────────────────────────────────
    // = Results of Total Direct Emissions from C_Emissions sheet

    private function loadSection_g(): void
    {
        $cEmission = new DataCalculationCEmission();
        $cEmission->periodType = $this->periodType;
        $cEmission->periodYear = $this->periodYear;
        $cEmission->period     = $this->period;
        $cEmission->loadEmissions();

        $this->dirEm = $cEmission->directEmissions;
    }

    // ── (h) Import and export of measurable heat ─────────────────────────────
    // Constants
    // - Steam energy conversion: 3.18 / 1000 (TJ/Ton)
    // - Steam Emission Factor (KPE): 0.195 tCO2/Ton
    private function loadSection_h(): void
    {
        $steamConversion = 3.18 / 1000; // TJ/Ton
        $steamEF         = 0.195;        // tCO2/Ton

        $config = new ConfigurationData();
        $config->periodYear = $this->periodYear;
        $config->period     = $this->period;

        // ── Imported ──────────────────────────────────────────────────────────
        // Source: ConfigurationData Tab Steel Slab - Table 4 - "Purchase KPE" (index [2])
        $table4 = $config->energyTableDataTable4();
        $purchaseKpe = $table4->values()->get(2)['power'] ?? null; // ton

        // Amount of net measurable heat (imported) = Purchase KPE * (3.18/1000)
        $this->hImportedHeat = $purchaseKpe !== null
            ? (float) $purchaseKpe * $steamConversion
            : null;

        // Emission factor (imported) = Purchase KPE * 0.195 / Amount of net measurable heat
        $this->hImportedEF = ($this->hImportedHeat !== null && $this->hImportedHeat > 0 && $purchaseKpe !== null)
            ? ((float) $purchaseKpe * $steamEF) / $this->hImportedHeat
            : null;

        // ── Exported ──────────────────────────────────────────────────────────
        // Source: ConfigurationData Tab Steel Slab - Table 3 - "Export to Coke Plant & Vendor" (index [0])
        $table3 = $config->energyTableDataTable3();
        $exportCoke = $table3->values()->get(0)['power'] ?? null; // ton

        // Amount of net measurable heat (exported) = Export to Coke Plant & Vendor * (3.18/1000)
        $this->hExportedHeat = $exportCoke !== null
            ? (float) $exportCoke * $steamConversion
            : null;

        // Emission factor (exported) = Export to Coke Plant & Vendor
        //   * Table 4.1 Conversion (tCO2/Ton) [3] (is_total row conversion)
        $table41 = $config->emissionTableData41();
        // Conv[3] = is_total row 'conversion' = blended EF (tCO2/Ton)
        $conv3 = $table41->firstWhere('is_total', true)['conversion'] ?? null;

        // Exported EF = Export to Coke Plant & Vendor * Conv[3]
        $this->hExportedEF = ($exportCoke !== null && $conv3 !== null)
            ? (float) $exportCoke * (float) $conv3 / $this->hExportedHeat
            : null;
    }

    // =========================================================================
    // Utilities
    // =========================================================================

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

    private function fmt(?float $v, int $dec = 3): string
    {
        return $v !== null ? number_format($v, $dec) : '—';
    }

    private function nl(string ...$lines): string
    {
        return implode('<br>', $lines);
    }

    // =========================================================================
    // Tooltip builders
    // =========================================================================

    public function getTooltipTotalProduction(): string
    {
        // Get individual breakdown
        $coil = $this->sumAcrossMonths(
            fn($month) =>
            MasterPcoCoil::where('period_year', (int) $this->periodYear)
                ->whereRaw('LOWER(TRIM(period_month)) = ?', [strtolower(trim($month))])
                ->where('class', 'Coil Product')
                ->sum('quantity')
        );

        $plate = $this->sumAcrossMonths(
            fn($month) =>
            MasterPcoPlate::where('period_year', (int) $this->periodYear)
                ->whereRaw('LOWER(TRIM(period_month)) = ?', [strtolower(trim($month))])
                ->where('class', 'Plate Product')
                ->sum('quantity')
        );

        return $this->nl(
            '• from master_pco_coils',
            '  class = Coil Product: ' . $this->fmt($coil),
            '',
            '• from master_pco_plates',
            '  class = Plate Product: ' . $this->fmt($plate),
            str_repeat('─', 20),
            'Total: ' . $this->fmt($this->totalProduction)
        );
    }

    public function getTooltipDirEm(): string
    {
        return $this->nl(
            'from C_Emissions sheet',
            '  Results → Total direct emissions',
            '',
            'Value: ' . $this->fmt($this->dirEm) . ' tCO2e'
        );
    }

    // =========================================================================
    // Filter options
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
        return \App\Models\MasterEnergyData::distinct()
            ->orderByDesc('period_year')
            ->pluck('period_year')
            ->values()
            ->toArray();
    }

    public function getTooltipHImportedHeat(): string
    {
        return $this->nl(
            'from ConfigurationData → Steel Slab → Table 4',
            '  Purchase KPE (index [2]) × (3.18 / 1000)',
            '',
            '• Purchase KPE: ' . $this->fmt($this->hImportedHeat !== null ? $this->hImportedHeat / (3.18 / 1000) : null) . ' ton',
            '• Steam conversion: 3.18 / 1000 TJ/Ton',
            '',
            'Value: ' . $this->fmt($this->hImportedHeat) . ' TJ'
        );
    }

    public function getTooltipHImportedEF(): string
    {
        $purchaseKpe = $this->hImportedHeat !== null ? $this->hImportedHeat / (3.18 / 1000) : null;
        return $this->nl(
            'Purchase KPE × Steam EF (0.195) / Amount of net measurable heat',
            '',
            '• Purchase KPE: ' . $this->fmt($purchaseKpe) . ' ton',
            '• Steam EF: 0.195 tCO2/Ton',
            '• Heat imported: ' . $this->fmt($this->hImportedHeat) . ' TJ',
            str_repeat('─', 20),
            'Value: ' . $this->fmt($this->hImportedEF) . ' tCO2/TJ'
        );
    }

    public function getTooltipHExportedHeat(): string
    {
        return $this->nl(
            'from ConfigurationData → Steel Slab → Table 3',
            '  Export to Coke Plant & Vendor (index [0]) × (3.18 / 1000)',
            '',
            '• Export to Coke Plant & Vendor: ' . $this->fmt($this->hExportedHeat !== null ? $this->hExportedHeat / (3.18 / 1000) : null) . ' ton',
            '• Steam conversion: 3.18 / 1000 TJ/Ton',
            '',
            'Value: ' . $this->fmt($this->hExportedHeat) . ' TJ'
        );
    }

    public function getTooltipHExportedEF(): string
    {
        $exportCoke = $this->hExportedHeat !== null ? $this->hExportedHeat / (3.18 / 1000) : null;
        return $this->nl(
            'from ConfigurationData → Steel Slab',
            '  Table 3 [0]: Export to Coke Plant & Vendor × Table 4.1 Conv[3] / Amount of Measurable Heat (Exported)',
            '',
            '• Export to Coke Plant & Vendor: ' . $this->fmt($exportCoke) . ' ton',
            '•  Table 4.1 Conv[3] (tCO2/Ton): blended EF (is_total row)',
            '•  Amount of Measurable Heat (Exported)',
            str_repeat('─', 20),
            'Value: ' . $this->fmt($this->hExportedEF) . ' tCO2'
        );
    }

    // =========================================================================

    public function render()
    {
        return view('livewire.data-calculation-d-processes', [
            'monthOptions'           => $this->getMonthOptions(),
            'quarterOptions'         => $this->getQuarterOptions(),
            'yearOptions'            => $this->getYearOptions(),
            'tooltipTotalProduction' => $this->getTooltipTotalProduction(),
            'tooltipDirEm'           => $this->getTooltipDirEm(),
            'tooltipHImportedHeat'   => $this->getTooltipHImportedHeat(),
            'tooltipHImportedEF'     => $this->getTooltipHImportedEF(),
            'tooltipHExportedHeat'   => $this->getTooltipHExportedHeat(),
            'tooltipHExportedEF'     => $this->getTooltipHExportedEF(),
        ]);
    }
}
