<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MasterEnergyData;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

#[Title('CHP - Power Plant')]
class ConfigurationDataCHP extends Component
{
    #[Url]
    public $periodYear = '';

    #[Url]
    public $period = ''; // jan, feb, ..., q1, q2, q3, q4, yearly

    protected array $electricityConversion = [0.00000933032]; // Tj/kWh

    protected array $electricityRows = [
        [
            'description' => 'Electricity Output',
            'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = I01200, plant_name = Purchase (KPE), energy_name = POWER, criteria= PRODUCTION</li></ul>',
            'conditions'  => [
                ['plant_code' => 'I01200', 'plant_name' => 'Purchase (KPE)', 'energy_name' => 'POWER', 'criteria' => 'PRODUCTION']
            ]
        ]
    ];

    protected array $chpRows = [
        [
            'description' => 'BFG for KPE',
            'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = I022CB, plant_name = Sales (KPE, Power generation), criteria=CONSUMPTION, energy_name = BFG</li></ul>',
            'conditions'  => [
                ['plant_code' => 'I022CB', 'plant_name' => 'Sales (KPE, Power generation)', 'criteria' => 'CONSUMPTION', 'energy_name' => 'BFG']
            ]
        ],
        [
            'description' => 'LDG for KPE',
            'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = I022CB, plant_name = Sales (KPE, Power generation), criteria=CONSUMPTION, energy_name = LDG</li></ul>',
            'conditions'  => [
                ['plant_code' => 'I022CB', 'plant_name' => 'Sales (KPE, Power generation)', 'criteria' => 'CONSUMPTION', 'energy_name' => 'LDG']
            ]
        ],
        [
            'description' => 'COG for KPE',
            'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = I022CB, plant_name = Sales (KPE, Power generation), criteria=CONSUMPTION, energy_name = COG</li></ul>',
            'conditions'  => [
                ['plant_code' => 'I022CB', 'plant_name' => 'Sales (KPE, Power generation)', 'criteria' => 'CONSUMPTION', 'energy_name' => 'COG']
            ],

        ],
        [
            'description' => 'HSD for KPE',
            'tooltip'     => '<ul><li>(Quantity from Master_energy_data where plant_code = I022CB, plant_name = Sales (KPE, Power generation), criteria=CONSUMPTION, energy_name = Heavy Oil) / 850 * 0.65</li></ul>',
            'conditions'  => [
                ['plant_code' => 'I022CB', 'plant_name' => 'Sales (KPE, Power generation)', 'criteria' => 'CONSUMPTION', 'energy_name' => 'Heavy Oil']
            ],
            'formula' => '/850*0.65'
        ]
    ];

    protected array $steamRows = [
        [
            'description' => 'Steam Output',
            'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = I01200, plant_name =Purchase, energy_name = STEAM, criteria=PRODUCTION</li></ul>',
            'conditions'  => [
                ['plant_code' => 'I01200', 'plant_name' => 'Purchase', 'energy_name' => 'STEAM', 'criteria' => 'PRODUCTION']
            ]
        ]
    ];

    protected array $steamConversion = [3.18 / 1000]; // Steam conversion Tj/ton
    protected float $steamEf = 0.195; // tCO2/ton

    protected array $emissionConversion = [
        0.0000031380,  // BFG [0] 3.138/1000000
        0.0000083680,  // LDG [1] 8.368/1000000
        0.0000184100,  // COG [2] 18.41/1000000
        0.0000377000,  // HSD [3] 37.7/1000000
    ];

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
            $this->period === 'yearly'              => array_values($monthMap),
            isset($quarterMonths[$this->period])    => $quarterMonths[$this->period],
            isset($monthMap[$this->period])         => [$monthMap[$this->period]],
            default                                 => []
        };
    }

    private function getRowQuantity(array $conditions, string $month): float
    {
        if (empty($conditions)) return 0;

        return (float) MasterEnergyData::where('period_year', $this->periodYear)
            ->whereRaw('LOWER(TRIM(period_month)) = ?', [strtolower(trim($month))])
            ->where(function ($q) use ($conditions) {
                foreach ($conditions as $cond) {
                    $q->orWhere(function ($sub) use ($cond) {
                        $sub->where('plant_code', $cond['plant_code'])
                            ->where('plant_name', $cond['plant_name'])
                            ->where('energy_name', $cond['energy_name']);
                        if (isset($cond['criteria'])) {
                            $sub->where('criteria', $cond['criteria']);
                        }
                    });
                }
            })
            ->sum('quantity');
    }

    private function buildTableData(array $rowDefs): Collection
    {
        $months = $this->resolveMonths();
        if (empty($months)) return collect();

        return collect($rowDefs)->map(function ($rowData) use ($months) {
            $rawValue = 0;
            foreach ($months as $month) {
                $rawValue += $this->getRowQuantity($rowData['conditions'], $month);
            }

            $value = $rawValue;
            if (isset($rowData['formula'])) {
                $rawStr = (string)$rawValue;
                $fullFormula = $rawStr . $rowData['formula'];
                $value = eval('return ' . $fullFormula . ';');
            }

            return [
                'description' => $rowData['description'],
                'tooltip'     => $rowData['tooltip'],
                'quantity'    => round($value, 2),
            ];
        });
    }

    public function updatedPeriodYear(): void
    {
        if (empty($this->periodYear)) {
            $this->period = '';
        }
    }

    #[Computed]
    public function availableYears()
    {
        return MasterEnergyData::distinct()
            ->orderByDesc('period_year')
            ->pluck('period_year')
            ->values();
    }

    #[Computed]
    public function electricityConversionTableData(): Collection
    {
        $elecQty = $this->electricityTableData[0]['quantity'] ?? 0;
        $elecTj = round($elecQty * $this->electricityConversion[0], 10);

        return collect([
            [
                'conversion' => number_format($this->electricityConversion[0], 11),
                'electricity' => $elecTj,
                'unit' => 'Tj',
                'tooltip' => '<ul><li>Electricity Output Table 3 * Conversion (Tj/kWh)[0]</li></ul>'
            ]
        ]);
    }

    #[Computed]
    public function electricityTableData(): Collection
    {
        $data = $this->buildTableData($this->electricityRows);
        $row = $data->first();
        if ($row) {
            $row['quantity_mwh'] = round($row['quantity'] / 1000, 2);
            $row['tooltip_mwh'] = $row['tooltip'] . ' / 1000';
        }
        return collect([$row ?? ['description' => 'No data', 'quantity' => 0, 'quantity_mwh' => 0]]);
    }

    #[Computed]
    public function chpTableData(): Collection
    {
        return $this->buildTableData($this->chpRows);
    }

    #[Computed]
    public function steamTableData(): Collection
    {
        return $this->buildTableData($this->steamRows);
    }

    #[Computed]
    public function steamConversionTableData(): Collection
    {
        $steamQty = $this->steamTableData[0]['quantity'] ?? 0;
        $steamTjRaw = $steamQty * $this->steamConversion[0]; // 
        $steamTj = round($steamTjRaw, 2); // hanya untuk display
        $emission = round($steamQty * $this->steamEf, 2);
        $efSteam = $steamTjRaw > 0 ? round($emission / $steamTjRaw, 4) : 0; // pakai raw

        return collect([
            [
                'conversion' => $this->steamConversion[0],
                'steam' => $steamTj,
                'unit' => 'Tj',
                'tooltip' => '<ul><li>Quantity from Master_energy_data where plant_code = I01200, plant_name =Purchase, energy_name = STEAM, criteria=PRODUCTION * conversion(tj/ton)[0]</li></ul>'
            ],
            [
                'conversion' => 'EF Steam',
                'steam' => $this->steamEf,
                'unit' => 'tCo2/ton',
                'tooltip' => '<ul><li>0.195</li></ul>'

            ],
            [
                'conversion' => 'Emission',
                'steam' => $emission,
                'unit' => 'tCO2',
                'tooltip' => '<ul><li>Quantity from Master_energy_data where plant_code = I01200, plant_name =Purchase, energy_name = STEAM, criteria=PRODUCTION * EF Steam</li></ul>'
            ],
            [
                'conversion' => 'EF Steam',
                'steam' => $efSteam,
                'unit' => 'tCO2/Tj',
                'tooltip' => '<ul><li>Emission / Steam[0]</li></ul>'
            ]
        ]);
    }

    #[Computed]
    public function emissionTableData(): Collection
    {
        $data = $this->chpTableData()->values();
        $total = 0;
        $tot = 'Total';

        $result = $data->map(function ($row, $index) use (&$total) {
            $conv = $this->emissionConversion[$index] ?? 0;
            $tj   = $row['quantity'] * $conv;


            // row[3] dikali 1000
            if ($index === 3) {
                $tj = $tj * 1000;
            }

            $total += $tj;

            return array_merge($row, [
                'tj'         => round($tj, 2),
                'conversion' => $conv,
            ]);
        });

        // row[4] = sum row[0] sampai row[3]
        $result->push([
            'description' => 'Total',
            'conversion'  => 'Total', // ✅ ubah dari '' ke 'Total'
            'tj'          => round($total, 2), // ✅ 2 desimal
        ]);

        return $result;
    }

    public function render()
    {
        return view('livewire.configuration-data-chp', [
            'availableYears'              => $this->availableYears,
            'chpTableData'                => $this->chpTableData,
            'electricityTableData'        => $this->electricityTableData,
            'electricityConversionTableData' => $this->electricityConversionTableData,
            'steamTableData'              => $this->steamTableData,
            'emissionTableData'           => $this->emissionTableData,
            'steamConversionTableData'    => $this->steamConversionTableData,
            'emissionConversion'          => $this->emissionConversion, // ✅ tambah ini
        ]);
    }
}
