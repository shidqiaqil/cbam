<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MasterEnergyData;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;

#[Title('Config Data - Steel Plate')]
class ConfigurationDataSteelPlate extends Component
{
    // =========================================================================
    // URL STATE
    // =========================================================================

    #[Url]
    public string $periodYear = '';

    #[Url]
    public string $period = '';

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

    private function sumAcrossMonths(array $conditions): float
    {
        $months = $this->resolveMonths();
        if (empty($months)) return 0.0;

        return array_reduce(
            $months,
            fn(float $carry, string $month) => $carry + $this->sumEnergyData($conditions, $month),
            0.0
        );
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
    // COMPUTED – TABLE 1: Power Plate Mill
    // =========================================================================

    /**
     * Table 1 – Power Plate Mill
     * Power = Quantity / 1000 (kWh → MWh)
     */
    #[Computed]
    public function steelPlateTable1(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        $conditions = [
            ['plant_code' => 'ILA000', 'plant_name' => 'Plate plant', 'criteria' => 'CONSUMPTION', 'energy_name' => 'POWER'],
        ];

        $qty  = $this->sumAcrossMonths($conditions);
        $power = $qty / 1000; // kWh → MWh

        return collect([
            [
                'description' => 'Power Plate Mill',
                'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = ILA000, plant_name = Plate plant, criteria = CONSUMPTION, energy_name = POWER</li><li>Divided by 1000 to convert kWh → MWh</li></ul>',
                'power'       => $power,
                'unit'        => 'MWh',
            ],
        ]);
    }

    // =========================================================================
    // COMPUTED – TABLE 1.1: Emission from Table 1
    // =========================================================================

    /**
     * Table 1.1 – Emission from Table 1
     * EF = Table 2.1 Emission Factor (tCO2/MWh) [4] (blended EF from Table 2.1 total row)
     * Total Emission = Table 1 Power[0] * EF[0]
     */
    #[Computed]
    public function steelPlateTable11(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        $t1 = $this->steelPlateTable1;
        if ($t1->isEmpty()) return collect();

        $power = $t1->first()['power'] ?? 0.0;

        // EF from Table 2.1 last row (blended EF = Total Emission / Total Purchase MWh)
        $ef       = $this->computeTable21Ef4();
        $emission = $power * $ef;

        return collect([
            [
                'emission_factor' => $ef,
                'ef_tooltip'      => '<ul><li>Steel Plate Tab → Table 2.1 Emission Factor (tCO2/MWh) [4] — blended EF from total row</li></ul>',
                'total_emission'  => $emission,
                'em_tooltip'      => '<ul><li>Table 1 Power Plate Mill (MWh) × Emission Factor (tCO2/MWh) [0]</li><li>= ' . $power . ' × ' . $ef . '</li></ul>',
                'unit'            => 'tCO2',
            ],
        ]);
    }

    // =========================================================================
    // COMPUTED – TABLE 2: COG for Plate Mill
    // =========================================================================

    /**
     * Table 2 – COG for Plate Mill
     * Quantity from Master_energy_data where plant_code = ILA000, criteria = CONSUMPTION, energy_name = COG
     */
    #[Computed]
    public function steelPlateTable2(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        $conditions = [
            ['plant_code' => 'ILA000', 'plant_name' => 'Plate plant', 'criteria' => 'CONSUMPTION', 'energy_name' => 'COG'],
        ];

        $qty = $this->sumAcrossMonths($conditions);

        return collect([
            [
                'description' => 'COG for Plate Mill',
                'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = ILA000, plant_name = Plate plant, criteria = CONSUMPTION, energy_name = COG</li></ul>',
                'cog'         => $qty,
                'unit'        => 'Nm3',
            ],
        ]);
    }

    // =========================================================================
    // COMPUTED – TABLE 2.1: By Product Gas Conversion
    // =========================================================================

    /**
     * Table 2.1 – By Product Gas Conversion
     * Conversion = 0.000018410 (TJ/m3) for COG
     * By Product Gas = Table 2 COG[0] * Conversion[0]
     * Emission Factor = By Product Gas[0] / By Product Gas[0] (= Conversion itself)
     */
    #[Computed]
    public function steelPlateTable21(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        $t2 = $this->steelPlateTable2;
        if ($t2->isEmpty()) return collect();

        $convCog    = 0.000018410;
        $cog        = $t2->first()['cog'] ?? 0.0;
        $byproduct  = $cog * $convCog;

        return collect([
            [
                'conversion'  => $convCog,
                'conv_tooltip' => '<ul><li>Conversion factor (TJ/m3) for COG: 0.000018410</li></ul>',
                'byproduct'   => $byproduct,
                'bp_tooltip'  => '<ul><li>Table 2 COG for Plate Mill × Conversion (TJ/m3) [0]</li><li>= ' . $cog . ' × ' . $convCog . '</li></ul>',
                'unit'        => 'Tj',
            ],
        ]);
    }

    // =========================================================================
    // HELPER – Table 2.1 Blended EF (used by Table 1.1)
    // =========================================================================

    /**
     * Compute blended Emission Factor from Table 2.1.
     * This mirrors the logic of ConfigurationData::computeTable21Ef4()
     * but using Steel Plate's own power data.
     *
     * EF[4] = Total Emission / (Total Purchase Electricity / 1000)
     * For Steel Plate, Total Purchase = Power Plate Mill (only 1 source)
     * Total Emission = Power Plate Mill * EF_PLN (0.87) / 1000
     * So EF[4] = 0.87 (PLN EF, since it's the only electricity source)
     *
     * Note: adjust this formula if Steel Plate has multiple electricity sources.
     */
    private function computeTable21Ef4(): float
    {
        $slab = new ConfigurationData();
        $slab->periodYear = $this->periodYear;
        $slab->period     = $this->period;

        return (float) ($slab->emissionTableData21()->last()['emission_factor'] ?? 0.0);
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    public function render()
    {
        return view('livewire.configuration-data-steel-plate', [
            'availableYears'    => $this->availableYears,
            'steelPlateTable1'  => $this->steelPlateTable1,
            'steelPlateTable11' => $this->steelPlateTable11,
            'steelPlateTable2'  => $this->steelPlateTable2,
            'steelPlateTable21' => $this->steelPlateTable21,
        ]);
    }
}
