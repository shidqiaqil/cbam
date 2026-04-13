<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MasterEnergyData;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

#[Layout('layouts.app')]
#[Title('Config Data')]
class ConfigurationData extends Component
{
    #[Url]
    public $activeTab = 'steel-slab';

    #[Url]
    public $periodYear = '';

    #[Url]
    public $period = ''; // jan, feb, ..., q1, q2, q3, q4, yearly

    // -------------------------------------------------------------------------
    // Row definitions
    // -------------------------------------------------------------------------

    protected array $energyRows = [
        [
            'description' => 'BF Generation',
            'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = IBN000, plant_name = Blast Furnace Plant, criteria= PRODUCTION, energy_name = AIR BLAST</li></ul>',
            'conditions'  => [
                ['plant_code' => 'IBN000', 'plant_name' => 'Blast Furnace Plant', 'criteria' => 'PRODUCTION', 'energy_name' => 'AIR BLAST']
            ]
        ],
        [
            'description' => 'Purchase Electricity from PLN',
            'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = I01200, plant_name = Purchase (PLN),criteria= PRODUCTION, energy_name = POWER</li></ul>',
            'conditions'  => [
                ['plant_code' => 'I01200', 'plant_name' => 'Purchase (PLN)', 'criteria' => 'PRODUCTION', 'energy_name' => 'POWER']
            ]
        ],
        [
            'description' => 'Purchase (KPE Power)',
            'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = I01200, plant_name = Purchase (KPE), criteria = PRODUCTION, energy_name = POWER</li></ul>',
            'conditions'  => [
                ['plant_code' => 'I01200', 'plant_name' => 'Purchase (KPE)', 'criteria' => 'PRODUCTION', 'energy_name' => 'POWER']
            ]
        ],
        [
            'description' => 'Plate Mill Electricity Consumption',
            'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = ILA000, plant_name = Plate plant,criteria= CONSUMPTION, energy_name = POWER</li></ul>',
            'conditions'  => [
                ['plant_code' => 'ILA000', 'plant_name' => 'Plate plant', 'criteria' => 'CONSUMPTION', 'energy_name' => 'POWER']
            ]
        ],
        [
            'description' => 'COP Electricity Consumption',
            'tooltip'     => '<ul><li>Sum(Quantity) from Master_energy_data where plant_code = ICA000, plant_name = Cokes plant, criteria = CONSUMPTION, energy_name = POWER</li><li>plant_code = ICK000, plant_name = Cokes plant, criteria = CONSUMPTION, energy_name = Cokes by product plant</li></ul>',
            'conditions'  => [
                ['plant_code' => 'ICA000', 'plant_name' => 'Cokes plant',            'criteria' => 'CONSUMPTION', 'energy_name' => 'POWER'],
                ['plant_code' => 'ICK000', 'plant_name' => 'Cokes by product plant', 'criteria' => 'CONSUMPTION', 'energy_name' => 'POWER']
            ]
        ]
    ];

    protected array $energyRowsTable2 = [
        [
            'description' => 'Operational usage for Slab Production',
            'tooltip'     => '<ul><li>Sum(Quantity) from Master_energy_data where multiple plant_codes for POWER CONSUMPTION</li></ul>',
            'conditions'  => [
                ['plant_code' => 'I02210', 'plant_name' => 'HQ',                                                  'energy_name' => 'POWER', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'IBA000', 'plant_name' => 'Raw Material',                                        'energy_name' => 'POWER', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'IBB000', 'plant_name' => 'Sinter Plant',                                        'energy_name' => 'POWER', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'IBN000', 'plant_name' => 'Blast Furnace Plant',                                 'energy_name' => 'POWER', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'IEA000', 'plant_name' => 'Steel making',                                        'energy_name' => 'POWER', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'IEE000', 'plant_name' => 'Continuous Casting',                                  'energy_name' => 'POWER', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'ITC110', 'plant_name' => 'Water-Fresh water from KTI',                          'energy_name' => 'POWER', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'ITC120', 'plant_name' => 'Water-Fresh water from recycling facility',           'energy_name' => 'POWER', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'ITD120', 'plant_name' => 'Utility- By Product Gas distribution',                'energy_name' => 'POWER', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'ITB110', 'plant_name' => 'Electric Power System-incoming and distribution',    'energy_name' => 'POWER', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'ITZ990', 'plant_name' => 'Energy Dept Common',                                  'energy_name' => 'POWER', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'IUA000', 'plant_name' => 'Facility Technology Department',                      'energy_name' => 'POWER', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'IVA000', 'plant_name' => 'Production Technology Departement',                   'energy_name' => 'POWER', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'IWA000', 'plant_name' => 'Technology and Business Development Division',        'energy_name' => 'POWER', 'criteria' => 'CONSUMPTION'],
            ]
        ],
        [
            'description' => 'BF Generation',
            'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = IBN000, plant_name = Blast Furnace Plant, criteria=PRODUCTION , energy_name = POWER</li></ul>',
            'conditions'  => [
                ['plant_code' => 'IBN000', 'plant_name' => 'Blast Furnace Plant', 'energy_name' => 'POWER', 'criteria' => 'PRODUCTION']
            ]
        ],
        [
            'description' => 'Purchase (KPE Power)',
            'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = I01200, plant_name = Purchase (KPE), criteria=PRODUCTION , energy_name = POWER</li></ul>',
            'conditions'  => [
                ['plant_code' => 'I01200', 'plant_name' => 'Purchase (KPE)', 'energy_name' => 'POWER', 'criteria' => 'PRODUCTION']
            ]
        ],
        [
            'description' => 'Purchase Electricity from PLN',
            'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = I01200, plant_name = Purchase (PLN),criteria=PRODUCTION ,energy_name = POWER</li></ul>',
            'conditions'  => [
                ['plant_code' => 'I01200', 'plant_name' => 'Purchase (PLN)', 'energy_name' => 'POWER', 'criteria' => 'PRODUCTION']
            ]
        ]
    ];

    protected array $energyRowsTable3 = [
        [
            'description' => 'Export to Coke Plant & Vendor',
            'tooltip'     => '<ul><li>Sum(Quantity) from Master_energy_data for STEAM CONSUMPTION across multiple plants</li></ul>',
            'conditions'  => [
                ['plant_code' => 'ICK000',  'plant_name' => 'Cokes by product plant',       'energy_name' => 'STEAM', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'I022B0',  'plant_name' => 'Vendor',                        'energy_name' => 'STEAM', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'I022CA',  'plant_name' => 'Sales (KPCC, Lime Calcining)',  'energy_name' => 'STEAM', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'I022CC',  'plant_name' => 'Sales (Linde, Oxygen Plant)',   'energy_name' => 'STEAM', 'criteria' => 'CONSUMPTION']
            ]
        ],
        [
            'description' => 'BF',
            'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = IBN000, plant_name = Blast Furnace Plant, criteria=CONSUMPTION , energy_name = STEAM</li></ul>',
            'conditions'  => [
                ['plant_code' => 'IBN000', 'plant_name' => 'Blast Furnace Plant', 'energy_name' => 'STEAM', 'criteria' => 'CONSUMPTION']
            ]
        ],
        [
            'description' => 'SMP & CCP + Energy',
            'tooltip'     => '<ul><li>Sum(Quantity) for STEAM CONSUMPTION at Steel making and Utility- By Product Gas distribution</li></ul>',
            'conditions'  => [
                ['plant_code' => 'IEA000', 'plant_name' => 'Steel making',                       'energy_name' => 'STEAM', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'ITD120', 'plant_name' => 'Utility- By Product Gas distribution', 'energy_name' => 'STEAM', 'criteria' => 'CONSUMPTION']
            ]
        ]
    ];

    protected array $energyRowsTable4 = [
        [
            'description' => 'Operational usage for Slab Production',
            'tooltip'     => '<ul><li>Sum(Quantity) from Master_energy_data for STEAM CONSUMPTION across multiple plants</li></ul>',
            'conditions'  => [
                ['plant_code' => 'I02400', 'plant_name' => 'Loss',                                'energy_name' => 'STEAM', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'IBN000', 'plant_name' => 'Blast Furnace Plant',                 'energy_name' => 'STEAM', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'IEA000', 'plant_name' => 'Steel making',                        'energy_name' => 'STEAM', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'IEE000', 'plant_name' => 'Continuous Casting',                  'energy_name' => 'STEAM', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'IHA000', 'plant_name' => 'Hot Strip Mill',                      'energy_name' => 'STEAM', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'ITD120', 'plant_name' => 'Utility- By Product Gas distribution', 'energy_name' => 'STEAM', 'criteria' => 'CONSUMPTION']
            ]
        ],
        [
            'description' => 'SMP Generation',
            'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = IEA000, plant_name =Steel making, criteria=PRODUCTION, energy_name = STEAM</li></ul>',
            'conditions'  => [
                ['plant_code' => 'IEA000', 'plant_name' => 'Steel making', 'energy_name' => 'STEAM', 'criteria' => 'PRODUCTION']
            ]
        ],
        [
            'description' => 'Purchase KPE',
            'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = I01200, plant_name =Purchase, criteria=PRODUCTION,energy_name = STEAM</li></ul>',
            'conditions'  => [
                ['plant_code' => 'I01200', 'plant_name' => 'Purchase', 'energy_name' => 'STEAM', 'criteria' => 'PRODUCTION']
            ]
        ]
    ];

    protected array $energyRowsExport = [
        [
            'description' => 'COG for Plate Mill',
            'tooltip'     => '<ul><li>-</li></ul>',
            'conditions'  => [],
            'fixedValue'  => 0
        ],
        [
            'description' => 'COG for HRP',
            'tooltip'     => '<ul><li>-</li></ul>',
            'conditions'  => [],
            'fixedValue'  => 0
        ],
        [
            'description' => 'Total COG Sales',
            'tooltip'     => '<ul><li>-</li></ul>',
            'conditions'  => [],
            'fixedValue'  => 0
        ],
        [
            'description' => 'BFG for KPE',
            'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = I022CB, plant_name = Sales (KPE, Power generation), criteria = CONSUMPTION, energy_name = BFG</li></ul>',
            'conditions'  => [
                ['plant_code' => 'I022CB', 'plant_name' => 'Sales (KPE, Power generation)', 'criteria' => 'CONSUMPTION', 'energy_name' => 'BFG']
            ]
        ],
        [
            'description' => 'LDG for KPE',
            'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = I022CB, plant_name = Sales (KPE, Power generation), criteria = CONSUMPTION, energy_name = LDG</li></ul>',
            'conditions'  => [
                ['plant_code' => 'I022CB', 'plant_name' => 'Sales (KPE, Power generation)', 'criteria' => 'CONSUMPTION', 'energy_name' => 'LDG']
            ]
        ],
        [
            'description' => 'BFG for COP',
            'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = ICA000, plant_name = Cokes plant, criteria = CONSUMPTION, energy_name = BFG</li></ul>',
            'conditions'  => [
                ['plant_code' => 'ICA000', 'plant_name' => 'Cokes plant', 'criteria' => 'CONSUMPTION', 'energy_name' => 'BFG']
            ]
        ]
    ];

    protected array $energyRowsImport = [
        [
            'description' => 'Operational usage for Slab Production',
            'tooltip'     => '<ul><li>Sum(Quantity) from Master_energy_data for COG CONSUMPTION across multiple plants</li></ul>',
            'conditions'  => [
                ['plant_code' => 'I02500', 'plant_name' => 'Diffusion',           'energy_name' => 'COG', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'IBB000', 'plant_name' => 'Sinter Plant',        'energy_name' => 'COG', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'IBN000', 'plant_name' => 'Blast Furnace Plant', 'energy_name' => 'COG', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'IEA000', 'plant_name' => 'Steel making',        'energy_name' => 'COG', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'IEE000', 'plant_name' => 'Continuous Casting',  'energy_name' => 'COG', 'criteria' => 'CONSUMPTION']
            ]
        ]
    ];

    protected array $energyRowsExportElectricity = [
        [
            'description' => 'Reverse Power',
            'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = I02300, plant_name = Reverse Power, criteria = CONSUMPTION, energy_name = POWER</li></ul>',
            'conditions'  => [
                ['plant_code' => 'I02300', 'plant_name' => 'Reverse Power', 'criteria' => 'CONSUMPTION', 'energy_name' => 'POWER']
            ]
        ],
        [
            'description' => 'Export to Coke Plant',
            'tooltip'     => '<ul><li>Sum(Quantity) from Master_energy_data for POWER CONSUMPTION at Cokes plant and Cokes by product plant</li></ul>',
            'conditions'  => [
                ['plant_code' => 'ICA000', 'plant_name' => 'Cokes plant',            'criteria' => 'CONSUMPTION', 'energy_name' => 'POWER'],
                ['plant_code' => 'ICK000', 'plant_name' => 'Cokes by product plant', 'criteria' => 'CONSUMPTION', 'energy_name' => 'POWER']
            ]
        ],
        [
            'description' => 'Total Power Sales to tenant',
            'tooltip'     => '<ul><li>Sum(Quantity) from Master_energy_data for POWER CONSUMPTION across Vendor, KPCC, Linde, KDL, and Other</li></ul>',
            'conditions'  => [
                ['plant_code' => 'I022B0', 'plant_name' => 'Vendor',                          'criteria' => 'CONSUMPTION', 'energy_name' => 'POWER'],
                ['plant_code' => 'I022CA', 'plant_name' => 'Sales (KPCC, Lime Calcining)',     'criteria' => 'CONSUMPTION', 'energy_name' => 'POWER'],
                ['plant_code' => 'I022CC', 'plant_name' => 'Sales (Linde, Oxygen Plant)',      'criteria' => 'CONSUMPTION', 'energy_name' => 'POWER'],
                ['plant_code' => 'I022CD', 'plant_name' => 'Sales (KDL for Excess Power)',     'criteria' => 'CONSUMPTION', 'energy_name' => 'POWER'],
                ['plant_code' => 'I022D0', 'plant_name' => 'Other (free incharge)',            'criteria' => 'CONSUMPTION', 'energy_name' => 'POWER']
            ]
        ]
    ];

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Resolve the list of month names for the selected period.
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

    /**
     * Sum quantity from DB for a set of conditions across the given months.
     */
    private function getRowQuantity(array $conditions, string $month): float
    {
        if (empty($conditions)) return 0;

        return (float) MasterEnergyData::where('period_year', $this->periodYear)
            ->whereRaw('LOWER(TRIM(period_month)) = ?', [strtolower(trim($month))])
            ->where(function ($q) use ($conditions) {
                foreach ($conditions as $cond) {
                    $q->orWhere(function ($sub) use ($cond) {
                        $sub->where('plant_code',   $cond['plant_code'])
                            ->where('plant_name',   $cond['plant_name'])
                            ->where('energy_name',  $cond['energy_name']);
                        if (isset($cond['criteria'])) {
                            $sub->where('criteria', $cond['criteria']);
                        }
                    });
                }
            })
            ->sum('quantity');
    }

    /**
     * Generic builder: loops over row definitions, sums quantity per month,
     * and returns a Collection of ['description', 'tooltip', 'power'|'quantity'].
     */
    private function buildTableData(array $rowDefs, string $valueKey = 'power'): Collection
    {
        $months = $this->resolveMonths();
        if (empty($months)) return collect();

        return collect($rowDefs)->map(function ($rowData) use ($months, $valueKey) {
            $value = 0;

            if (!empty($rowData['conditions'])) {
                foreach ($months as $month) {
                    $value += $this->getRowQuantity($rowData['conditions'], $month);
                }
            } elseif (isset($rowData['fixedValue'])) {
                $value = $rowData['fixedValue'];
            }

            return [
                'description' => $rowData['description'],
                'tooltip'     => $rowData['tooltip'] ?? '',
                $valueKey     => round($value, 2),
            ];
        });
    }

    // -------------------------------------------------------------------------
    // Livewire hooks
    // -------------------------------------------------------------------------

    public function updatedPeriodYear(): void
    {
        if (empty($this->periodYear)) {
            $this->period = '';
        }
    }

    // -------------------------------------------------------------------------
    // Computed properties  ← #[Computed] makes them reactive in Livewire 3
    // -------------------------------------------------------------------------

    #[Computed]
    public function availableYears()
    {
        return MasterEnergyData::distinct()
            ->orderByDesc('period_year')
            ->pluck('period_year')
            ->values();
    }

    #[Computed]
    public function energyTableData(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        $data = $this->buildTableData($this->energyRows, 'power');

        // Append Total row
        $data->push([
            'description' => 'Total',
            'tooltip'     => 'Sum of all rows above',
            'power'       => round($data->sum('power'), 2),
        ]);

        return $data;
    }

    #[Computed]
    public function energyTableDataTable2(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        $data = $this->buildTableData($this->energyRowsTable2, 'power');

        // Total Purchase Electricity = BF Generation (idx 1) + Purchase KPE (idx 2) + PLN (idx 3)
        $total = 0;
        if ($data->count() >= 4) {
            $total = $data[1]['power'] + $data[2]['power'] + $data[3]['power'];
        }
        $data->push([
            'description' => 'Total Purchase Electricity',
            'tooltip'     => 'BF Generation + Purchase (KPE Power) + Purchase Electricity from PLN',
            'power'       => round($total, 2),
        ]);

        return $data;
    }

    #[Computed]
    public function energyTableDataTable3(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        $data = $this->buildTableData($this->energyRowsTable3, 'power');

        // Total = BF (idx 1) + SMP & CCP + Energy (idx 2)
        $total = 0;
        if ($data->count() >= 3) {
            $total = $data[1]['power'] + $data[2]['power'];
        }
        $data->push([
            'description' => 'Total',
            'tooltip'     => 'BF + SMP & CCP + Energy',
            'power'       => round($total, 2),
        ]);

        return $data;
    }

    #[Computed]
    public function energyTableDataTable4(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        $data = $this->buildTableData($this->energyRowsTable4, 'power');

        // Total Purchase Steam = SMP Generation (idx 1) + Purchase KPE (idx 2)
        $total = 0;
        if ($data->count() >= 3) {
            $total = $data[1]['power'] + $data[2]['power'];
        }
        $data->push([
            'description' => 'Total Purchase Steam',
            'tooltip'     => 'SMP Generation + Purchase KPE',
            'power'       => round($total, 2),
        ]);

        return $data;
    }

    #[Computed]
    public function energyTableDataExport(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        return $this->buildTableData($this->energyRowsExport, 'quantity');
    }

    #[Computed]
    public function energyTableDataImport(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        return $this->buildTableData($this->energyRowsImport, 'quantity');
    }

    #[Computed]
    public function energyTableDataExportElectricity(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        $months = $this->resolveMonths();
        if (empty($months)) return collect();

        $data = collect();

        // Row 1 — Reverse Power
        $reversePower = 0;
        foreach ($months as $month) {
            $reversePower += $this->getRowQuantity($this->energyRowsExportElectricity[0]['conditions'], $month);
        }
        $data->push([
            'description' => 'Reverse Power',
            'tooltip'     => $this->energyRowsExportElectricity[0]['tooltip'],
            'quantity'    => round($reversePower, 2),
        ]);

        // Row 2 — Export to Coke Plant
        $qty2 = 0;
        foreach ($months as $month) {
            $qty2 += $this->getRowQuantity($this->energyRowsExportElectricity[1]['conditions'], $month);
        }
        $data->push([
            'description' => 'Export to Coke Plant',
            'tooltip'     => $this->energyRowsExportElectricity[1]['tooltip'],
            'quantity'    => round($qty2, 2),
        ]);

        // Row 3 — Total Power Sales to tenant
        $qty3 = 0;
        foreach ($months as $month) {
            $qty3 += $this->getRowQuantity($this->energyRowsExportElectricity[2]['conditions'], $month);
        }
        $data->push([
            'description' => 'Total Power Sales to tenant',
            'tooltip'     => $this->energyRowsExportElectricity[2]['tooltip'],
            'quantity'    => round($qty3, 2),
        ]);

        // Row 4 — Reverse Power / 1000
        $data->push([
            'description' => 'Reverse Power/1000',
            'tooltip'     => 'Reverse Power value / 1000',
            'quantity'    => round($reversePower / 1000, 2),
        ]);

        return $data;
    }

    // -------------------------------------------------------------------------
    // Render
    // -------------------------------------------------------------------------

    public function render()
    {
        return view('livewire.configuration-data', [
            'availableYears'                   => $this->availableYears,
            'energyTableData'                  => $this->energyTableData,
            'energyTableDataTable2'            => $this->energyTableDataTable2,
            'energyTableDataTable3'            => $this->energyTableDataTable3,
            'energyTableDataTable4'            => $this->energyTableDataTable4,
            'energyTableDataExport'            => $this->energyTableDataExport,
            'energyTableDataImport'            => $this->energyTableDataImport,
            'energyTableDataExportElectricity' => $this->energyTableDataExportElectricity,
        ]);
    }
}
