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
            'tooltip'     => '<ul><li>Quantity from Master_energy_data where plant_code = IBN000, plant_name = Blast Furnace Plant, criteria= PRODUCTION, energy_name = POWER</li></ul>',
            'conditions'  => [
                ['plant_code' => 'IBN000', 'plant_name' => 'Blast Furnace Plant', 'criteria' => 'PRODUCTION', 'energy_name' => 'POWER']
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
                ['plant_code' => 'ITB110', 'plant_name' => 'Electric Power System-incoming and distribution',     'energy_name' => 'POWER', 'criteria' => 'CONSUMPTION'],
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
                ['plant_code' => 'IEA000', 'plant_name' => 'Steel making', 'energy_name' => 'STEAM', 'criteria' => 'CONSUMPTION'],
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
                $valueKey     => (float) $value,
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
    // Computed properties
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

        $bf    = $data[0]['power'] ?? 0;
        $pln   = $data[1]['power'] ?? 0;
        $kpe   = $data[2]['power'] ?? 0;
        $plate = $data[3]['power'] ?? 0;
        $cop   = $data[4]['power'] ?? 0;
        $total = $bf + $pln + $kpe - $plate - $cop;

        $data->push([
            'description' => 'Total',
            'tooltip'     => 'BF Generation + Purchase Electricity from PLN + Purchase (KPE Power) - Plate Mill Electricity Consumption - COP Electricity Consumption',
            'power'       => $total,
        ]);

        return $data;
    }

    #[Computed]
    public function energyTableDataTable2(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        $data = $this->buildTableData($this->energyRowsTable2, 'power');

        $total = 0;
        if ($data->count() >= 4) {
            $total = $data[1]['power'] + $data[2]['power'] + $data[3]['power'];
        }
        $data->push([
            'description' => 'Total Purchase Electricity',
            'tooltip'     => 'BF Generation + Purchase (KPE Power) + Purchase Electricity from PLN',
            'power'       => $total,
        ]);

        return $data;
    }

    #[Computed]
    public function energyTableDataTable3(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        $data = $this->buildTableData($this->energyRowsTable3, 'power');

        $total = 0;
        if ($data->count() >= 3) {
            $total = $data[1]['power'] + $data[2]['power'];
        }
        $data->push([
            'description' => 'Total',
            'tooltip'     => 'BF + SMP & CCP + Energy',
            'power'       => $total,
        ]);

        return $data;
    }

    #[Computed]
    public function energyTableDataTable4(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        $data = $this->buildTableData($this->energyRowsTable4, 'power');

        $total = 0;
        if ($data->count() >= 3) {
            $total = $data[1]['power'] + $data[2]['power'];
        }
        $data->push([
            'description' => 'Total Purchase Steam',
            'tooltip'     => 'SMP Generation + Purchase KPE',
            'power'       => $total,
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

        $reversePower = 0;
        foreach ($months as $month) {
            $reversePower += $this->getRowQuantity($this->energyRowsExportElectricity[0]['conditions'], $month);
        }
        $data->push([
            'description' => 'Reverse Power',
            'tooltip'     => $this->energyRowsExportElectricity[0]['tooltip'],
            'quantity'    => $reversePower,
        ]);

        $qty2 = 0;
        foreach ($months as $month) {
            $qty2 += $this->getRowQuantity($this->energyRowsExportElectricity[1]['conditions'], $month);
        }
        $data->push([
            'description' => 'Export to Coke Plant',
            'tooltip'     => $this->energyRowsExportElectricity[1]['tooltip'],
            'quantity'    => $qty2,
        ]);

        $qty3 = 0;
        foreach ($months as $month) {
            $qty3 += $this->getRowQuantity($this->energyRowsExportElectricity[2]['conditions'], $month);
        }
        $data->push([
            'description' => 'Total Power Sales to tenant',
            'tooltip'     => $this->energyRowsExportElectricity[2]['tooltip'],
            'quantity'    => $qty3,
        ]);

        $data->push([
            'description' => 'Reverse Power/1000',
            'tooltip'     => 'Reverse Power value / 1000',
            'quantity'    => $reversePower / 1000,
        ]);

        return $data;
    }

    // -------------------------------------------------------------------------
    // Computed – Fetch CHP emission factors from ConfigurationDataCHP
    // -------------------------------------------------------------------------

    /**
     * Retrieve the CHP Table 6 row value by its 'factor' label.
     * This instantiates ConfigurationDataCHP using the same period/year
     * so we can read its computed emission factors directly.
     */
    private function getChpSteamEmissionFactorByLabel(string $label): float
    {
        if (empty($this->periodYear) || empty($this->period)) return 0.0;

        // Instantiate CHP component with same period context
        $chp = new ConfigurationDataCHP();
        $chp->periodYear = $this->periodYear;
        $chp->period     = $this->period;

        // Find the row in steamEmissionKpeData matching the label
        $row = $chp->steamEmissionKpeData()->firstWhere('factor', $label);

        return (float) ($row['total_emission'] ?? 0.0);
    }

    // -------------------------------------------------------------------------
    // Computed – TABLE 1.1: Emission from Table 1 Energy Data
    // -------------------------------------------------------------------------

    /**
     * Table 1.1 – Emission calculation for each Table 1 row.
     *
     * Emission Factors:
     *   EF[0] = 0                            (BF Generation – internal, no grid EF)
     *   EF[1] = 0.87                         (PLN – national grid emission factor)
     *   EF[2] = CHP Table 6 "Total / (Electricity Output/1000) (tCO2/MWh)"
     *   EF[3] = Table 2.1 EF[4]             (Plate Mill – same EF as Table 2.1 row 4/total)
     *   EF[4] = Table 2.1 EF[4]             (COP – same EF as Table 2.1 row 4/total)
     *
     * Total Emission = Emission[1] + Emission[2] - Emission[3] - Emission[4]
     *
     * Note: EF[3] and EF[4] depend on Table 2.1 EF[4]. Table 2.1 EF[4] is
     * computed as: TotalEmission_Table2.1 / (Table2 Total Purchase Electricity / 1000)
     * which does NOT depend on Table 1.1, so there is no circular dependency.
     */
    #[Computed]
    public function emissionTableData11(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        // Get Table 1 energy quantities (power values in kWh)
        $t1 = $this->energyTableData;
        if ($t1->isEmpty()) return collect();

        $bf_kwh    = $t1[0]['power'] ?? 0; // BF Generation
        $pln_kwh   = $t1[1]['power'] ?? 0; // Purchase Electricity from PLN
        $kpe_kwh   = $t1[2]['power'] ?? 0; // Purchase (KPE Power)
        $plate_kwh = $t1[3]['power'] ?? 0; // Plate Mill Electricity Consumption
        $cop_kwh   = $t1[4]['power'] ?? 0; // COP Electricity Consumption

        // Emission factors
        $ef0 = 0.0;   // BF Generation
        $ef1 = 0.87;  // PLN

        // EF[2] from CHP Table 6: "Total / ( Electricity Output/1000 ) (tCO2/MWh)"
        $ef2 = $this->getChpSteamEmissionFactorByLabel('Total / ( Electricity Output/1000 ) (tCO2/MWh)');

        // EF[3] and EF[4] come from Table 2.1 EF[4] (the "per MWh" factor for the total)
        // We need Table 2.1 data – compute it here to avoid double-computation issues.
        // Table 2.1 EF[4] = TotalEmission_Table2.1 / (Table2 Total Purchase Electricity / 1000)
        $table21EF4 = $this->computeTable21Ef4();
        $ef3        = $table21EF4;
        $ef4        = $table21EF4;

        // Emission calculations (kWh → MWh: divide by 1000)
        $em0 = $bf_kwh    * $ef0 / 1000;
        $em1 = $pln_kwh   * $ef1 / 1000;
        $em2 = $kpe_kwh   * $ef2 / 1000;
        $em3 = $plate_kwh * $ef3 / 1000;
        $em4 = $cop_kwh   * $ef4 / 1000;

        // Total = em[1] + em[2] - em[3] - em[4]
        $total = $em1 + $em2 - $em3 - $em4;

        return collect([
            [
                'description'      => 'BF Generation',
                'emission_factor'  => $ef0,
                'ef_tooltip'       => '<ul><li>Fixed value: 0 (BF Generation is internal generation, no grid emission factor applied)</li></ul>',
                'total_emission'   => $em0,
                'em_tooltip'       => '<ul><li>Table 1 BF Generation (kWh) × Emission Factor (tCO2/MWh) [0] / 1000</li><li>= ' . number_format($bf_kwh, 2) . ' × ' . $ef0 . ' / 1000</li></ul>',
                'unit'             => 'tCO2',
            ],
            [
                'description'      => 'Purchase Electricity from PLN',
                'emission_factor'  => $ef1,
                'ef_tooltip'       => '<ul><li>Fixed value: 0.8700 tCO2/MWh (PLN national grid emission factor)</li></ul>',
                'total_emission'   => $em1,
                'em_tooltip'       => '<ul><li>Table 1 Purchase Electricity from PLN (kWh) × Emission Factor (tCO2/MWh) [1] / 1000</li><li>= ' . number_format($pln_kwh, 2) . ' × ' . $ef1 . ' / 1000</li></ul>',
                'unit'             => 'tCO2',
            ],
            [
                'description'      => 'Purchase (KPE Power)',
                'emission_factor'  => $ef2,
                'ef_tooltip'       => '<ul><li>From CHP Power Plant Tab → Table 6, row: "Total / ( Electricity Output/1000 ) (tCO2/MWh)"</li></ul>',
                'total_emission'   => $em2,
                'em_tooltip'       => '<ul><li>Table 1 Purchase (KPE Power) (kWh) × Emission Factor (tCO2/MWh) [2] / 1000</li><li>= ' . number_format($kpe_kwh, 2) . ' × ' . $ef2 . ' / 1000</li></ul>',
                'unit'             => 'tCO2',
            ],
            [
                'description'      => 'Plate Mill Electricity Consumption',
                'emission_factor'  => $ef3,
                'ef_tooltip'       => '<ul><li>From Table 2.1 Emission Factor (tCO2/MWh) [4] — computed as Total Emission Table 2.1 / (Table 2 Total Purchase Electricity / 1000)</li></ul>',
                'total_emission'   => $em3,
                'em_tooltip'       => '<ul><li>Table 1 Plate Mill Electricity Consumption (kWh) × Emission Factor (tCO2/MWh) [3] / 1000</li><li>= ' . number_format($plate_kwh, 2) . ' × ' . $ef3 . ' / 1000</li></ul>',
                'unit'             => 'tCO2',
            ],
            [
                'description'      => 'COP Electricity Consumption',
                'emission_factor'  => $ef4,
                'ef_tooltip'       => '<ul><li>From Table 2.1 Emission Factor (tCO2/MWh) [4] — computed as Total Emission Table 2.1 / (Table 2 Total Purchase Electricity / 1000)</li></ul>',
                'total_emission'   => $em4,
                'em_tooltip'       => '<ul><li>Table 1 COP Electricity Consumption (kWh) × Emission Factor (tCO2/MWh) [4] / 1000</li><li>= ' . number_format($cop_kwh, 2) . ' × ' . $ef4 . ' / 1000</li></ul>',
                'unit'             => 'tCO2',
            ],
            [
                'description'      => 'Total',
                'emission_factor'  => 0,
                'ef_tooltip'       => '<ul><li>Fixed: 0</li></ul>',
                'total_emission'   => $total,
                'em_tooltip'       => '<ul><li>Total Emission = Emission[PLN] + Emission[KPE] − Emission[Plate Mill] − Emission[COP]</li><li>= ' . $em1 . ' + ' . $em2 . ' − ' . $em3 . ' − ' . $em4 . '</li></ul>',
                'unit'             => 'tCO2',
                'is_total'         => true,
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // Computed – TABLE 2.1: Emission from Table 2 Energy Data
    // -------------------------------------------------------------------------

    /**
     * Helper: compute Table 2.1 EF[4] (the blended emission factor for total purchase electricity).
     * This is: (Emission[1] + Emission[2] + Emission[3]) / (Table2 Total Purchase Electricity / 1000)
     *
     * Where:
     *   Emission[1] = Table2 BF Generation * EF[1]=0 / 1000          → always 0
     *   Emission[2] = Table2 KPE Power     * EF[2]=CHP Table6 / 1000
     *   Emission[3] = Table2 PLN           * EF[3]=Table1.1 EF[1]=0.87 / 1000
     */
    private function computeTable21Ef4(): float
    {
        $t2 = $this->energyTableDataTable2;
        if ($t2->isEmpty()) return 0.0;

        $bf_kwh  = $t2[1]['power'] ?? 0; // BF Generation
        $kpe_kwh = $t2[2]['power'] ?? 0; // Purchase (KPE Power)
        $pln_kwh = $t2[3]['power'] ?? 0; // Purchase Electricity from PLN

        // Total Purchase Electricity (last row pushed to collection)
        $totalPurchase = $t2->last()['power'] ?? 0;

        $ef1_t2 = 0.0;   // BF Generation EF = 0
        $ef2_t2 = $this->getChpSteamEmissionFactorByLabel('Net Emission / ( Electricity Output/1000 ) (tCO2/MWh)');
        $ef3_t2 = 0.87;  // PLN EF (same as Table 1.1 EF[1])

        $em1 = $bf_kwh  * $ef1_t2 / 1000;
        $em2 = $kpe_kwh * $ef2_t2 / 1000;
        $em3 = $pln_kwh * $ef3_t2 / 1000;

        $totalEmission  = $em1 + $em2 + $em3;
        $totalPurchaseMwh = $totalPurchase / 1000;

        return $totalPurchaseMwh > 0 ? ($totalEmission / $totalPurchaseMwh) : 0.0;
    }

    /**
     * Table 2.1 – Emission calculation for each Table 2 row.
     *
     * Emission Factors:
     *   EF[0] = Table 2.1 EF[4]  (Operational – uses blended factor)
     *   EF[1] = 0                (BF Generation – internal, no grid EF)
     *   EF[2] = CHP Table 6 "Net Emission / ( Electricity Output/1000 ) (tCO2/MWh)"
     *   EF[3] = 0.87             (PLN – same as Table 1.1 EF[1])
     *
     * EF[4] (shown in last/total row) = Total Emission [1]+[2]+[3] / (Table2 Total Purchase Electricity / 1000)
     * Total Emission = Emission[1] + Emission[2] + Emission[3]
     */
    #[Computed]
    public function emissionTableData21(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        $t2 = $this->energyTableDataTable2;
        if ($t2->isEmpty()) return collect();

        // Table 2 quantities (kWh)
        $operational_kwh = $t2[0]['power'] ?? 0; // Operational usage for Slab Production
        $bf_kwh          = $t2[1]['power'] ?? 0; // BF Generation
        $kpe_kwh         = $t2[2]['power'] ?? 0; // Purchase (KPE Power)
        $pln_kwh         = $t2[3]['power'] ?? 0; // Purchase Electricity from PLN

        // Total Purchase Electricity from Table 2 (last row)
        $totalPurchaseKwh = $t2->last()['power'] ?? 0;

        // Emission Factors
        $ef1_t2 = 0.0;   // BF Generation
        $ef2_t2 = $this->getChpSteamEmissionFactorByLabel('Net Emission / ( Electricity Output/1000 ) (tCO2/MWh)');
        $ef3_t2 = 0.87;  // PLN

        // EF[4] = blended factor (computed from em[1]+em[2]+em[3] / totalPurchase MWh)
        $ef4_t2 = $this->computeTable21Ef4();

        // EF[0] for Operational = EF[4] (blended)
        $ef0_t2 = $ef4_t2;

        // Emission calculations
        $em0 = $operational_kwh * $ef0_t2 / 1000;
        $em1 = $bf_kwh          * $ef1_t2 / 1000;
        $em2 = $kpe_kwh         * $ef2_t2 / 1000;
        $em3 = $pln_kwh         * $ef3_t2 / 1000;

        // Total Emission = em[1] + em[2] + em[3]
        $totalEmission    = $em1 + $em2 + $em3;
        $totalPurchaseMwh = $totalPurchaseKwh / 1000;
        $ef4_display      = $totalPurchaseMwh > 0 ? ($totalEmission / $totalPurchaseMwh) : 0.0;

        return collect([
            [
                'description'      => 'Operational usage for Slab Production',
                'emission_factor'  => $ef0_t2,
                'ef_tooltip'       => '<ul><li>Table 2.1 Emission Factor (tCO2/MWh) [4] — blended factor: Total Emission [1+2+3] / (Table 2 Total Purchase Electricity / 1000)</li></ul>',
                'total_emission'   => $em0,
                'em_tooltip'       => '<ul><li>Table 2 Operational usage for Slab Production (kWh) × Emission Factor (tCO2/MWh) [0] / 1000</li><li>= ' . number_format($operational_kwh, 2) . ' × ' . $ef0_t2 . ' / 1000</li></ul>',
                'unit'             => 'tCO2',
            ],
            [
                'description'      => 'BF Generation',
                'emission_factor'  => $ef1_t2,
                'ef_tooltip'       => '<ul><li>Fixed value: 0 (BF Generation is internal generation, no grid emission factor applied)</li></ul>',
                'total_emission'   => $em1,
                'em_tooltip'       => '<ul><li>Table 2 BF Generation (kWh) × Emission Factor (tCO2/MWh) [1] / 1000</li><li>= ' . number_format($bf_kwh, 2) . ' × ' . $ef1_t2 . ' / 1000</li></ul>',
                'unit'             => 'tCO2',
            ],
            [
                'description'      => 'Purchase (KPE Power)',
                'emission_factor'  => $ef2_t2,
                'ef_tooltip'       => '<ul><li>From CHP Power Plant Tab → Table 6, row: "Net Emission / ( Electricity Output/1000 ) (tCO2/MWh)"</li></ul>',
                'total_emission'   => $em2,
                'em_tooltip'       => '<ul><li>Table 2 Purchase (KPE Power) (kWh) × Emission Factor (tCO2/MWh) [2] / 1000</li><li>= ' . number_format($kpe_kwh, 2) . ' × ' . $ef2_t2 . ' / 1000</li></ul>',
                'unit'             => 'tCO2',
            ],
            [
                'description'      => 'Purchase Electricity from PLN',
                'emission_factor'  => $ef3_t2,
                'ef_tooltip'       => '<ul><li>Table 1.1 Emission Factor (tCO2/MWh) [1] — PLN national grid emission factor: 0.87</li></ul>',
                'total_emission'   => $em3,
                'em_tooltip'       => '<ul><li>Table 2 Purchase Electricity from PLN (kWh) × Emission Factor (tCO2/MWh) [3] / 1000</li><li>= ' . number_format($pln_kwh, 2) . ' × ' . $ef3_t2 . ' / 1000</li></ul>',
                'unit'             => 'tCO2',
            ],
            [
                'description'      => 'Total',
                'emission_factor'  => $ef4_display,
                'ef_tooltip'       => '<ul><li>Total Emission [1+2+3] / (Table 2 Total Purchase Electricity / 1000)</li><li>= ' . $totalEmission . ' / ' . $totalPurchaseMwh . '</li></ul>',
                'total_emission'   => $totalEmission,
                'em_tooltip'       => '<ul><li>Total Emission = Emission[BF] + Emission[KPE] + Emission[PLN]</li><li>= ' . $em1 . ' + ' . $em2 . ' + ' . $em3 . '</li></ul>',
                'unit'             => 'tCO2',
                'is_total'         => true,
            ],
        ]);
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
            'emissionTableData11'              => $this->emissionTableData11,
            'emissionTableData21'              => $this->emissionTableData21,
        ]);
    }
}
