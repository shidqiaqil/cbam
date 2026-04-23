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

    private function computeTable41Conv3(): float
    {
        $t4 = $this->energyTableDataTable4;
        if ($t4->isEmpty()) return 0.0;

        $smpGeneration = $t4[1]['power'] ?? 0.0;
        $purchaseKpe   = $t4[2]['power'] ?? 0.0;
        $totalPurchase = $t4->last()['power'] ?? 0.0;

        $chp = new ConfigurationDataCHP();
        $chp->periodYear = $this->periodYear;
        $chp->period     = $this->period;
        $conv2 = (float) ($chp->steamConversionTableData()->firstWhere('unit', 'tCO2/Tj')['steam'] ?? 0.0);

        $steam1 = $smpGeneration * 0.0;
        $steam2 = $purchaseKpe   * $conv2;

        $totalSteam = $steam1 + $steam2;

        return $totalPurchase > 0 ? ($totalSteam / $totalPurchase) : 0.0;
    }

    #[Computed]
    public function emissionTableData31(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        $t3 = $this->energyTableDataTable3;
        if ($t3->isEmpty()) return collect();

        $export = $t3[0]['power'] ?? 0.0; // Export to Coke Plant & Vendor
        $bf     = $t3[1]['power'] ?? 0.0; // BF
        $smp    = $t3[2]['power'] ?? 0.0; // SMP & CCP + Energy

        // Conv[0],[1],[2] = Table 4.1 Conv[3]
        $conv = $this->emissionTableData41->firstWhere('is_total', true)['conversion'] ?? 0.0;

        // CHP Table 2.2 conversion[0] = Tj/ton
        $chp = new ConfigurationDataCHP();
        $chp->periodYear = $this->periodYear;
        $chp->period     = $this->period;
        $chpConv0 = (float) ($chp->steamConversionTableData()->values()[0]['conversion'] ?? 0.0);

        $steam0 = $export * $conv;
        $steam1 = $bf     * $conv;
        $steam2 = $smp    * $conv;
        $steam3 = $steam1 + $steam2; // Total
        $steam4 = $export * $chpConv0;          // Energy
        $steam5 = $steam4 > 0 ? ($steam0 / $steam4) : 0.0; // EF Steam

        return collect([
            [
                'description' => 'Export to Coke Plant & Vendor',
                'conversion'  => $conv,
                'conv_tooltip' => '<ul><li>Table 4.1 Conversion (tCO2/Ton) [3]</li></ul>',
                'steam'       => $steam0,
                'st_tooltip'  => '<ul><li>Table 3 Export to Coke Plant & Vendor × Conv[0]</li><li>= ' . $export . ' × ' . $conv . '</li></ul>',
                'unit'        => 'tCO2',
            ],
            [
                'description' => 'BF',
                'conversion'  => $conv,
                'conv_tooltip' => '<ul><li>Table 4.1 Conversion (tCO2/Ton) [3]</li></ul>',
                'steam'       => $steam1,
                'st_tooltip'  => '<ul><li>Table 3 BF × Conv[1]</li><li>= ' . $bf . ' × ' . $conv . '</li></ul>',
                'unit'        => 'tCO2',
            ],
            [
                'description' => 'SMP & CCP + Energy',
                'conversion'  => $conv,
                'conv_tooltip' => '<ul><li>Table 4.1 Conversion (tCO2/Ton) [3]</li></ul>',
                'steam'       => $steam2,
                'st_tooltip'  => '<ul><li>Table 3 SMP & CCP + Energy × Conv[2]</li><li>= ' . $smp . ' × ' . $conv . '</li></ul>',
                'unit'        => 'tCO2',
            ],
            [
                'description' => 'Total',
                'conversion'  => null,
                'conv_tooltip' => '',
                'steam'       => $steam3,
                'st_tooltip'  => '<ul><li>Total = Steam[0] + Steam[1] + Steam[2]</li><li>= ' . $steam0 . ' + ' . $steam1 . ' + ' . $steam2 . '</li></ul>',
                'unit'        => 'tCO2',
                'is_total'    => true,
            ],
            [
                'description' => 'Energy',
                'conversion'  => 'Energy',
                'conv_tooltip' => '<ul><li>CHP Power Plant Tab → Table 2.2 conversion[0] (Tj/ton)</li></ul>',
                'steam'       => $steam4,
                'st_tooltip'  => '<ul><li>Table 3 Export to Coke Plant & Vendor × CHP Table 2.2 conversion[0]</li><li>= ' . $export . ' × ' . $chpConv0 . '</li></ul>',
                'unit'        => 'Tj',
            ],
            [
                'description' => 'EF Steam',
                'conversion'  => 'EF Steam',
                'conv_tooltip' => '<ul><li>Steam[0] / Steam[4] (Energy)</li><li>= ' . $steam0 . ' / ' . $steam4 . '</li></ul>',
                'steam'       => $steam5,
                'st_tooltip'  => '<ul><li>Steam[0] / Steam[4] (Energy)</li><li>= ' . $steam0 . ' / ' . $steam4 . '</li></ul>',
                'unit'        => 'tCO2/Tj',
            ],
        ]);
    }

    #[Computed]
    public function emissionTableData41(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        $t4 = $this->energyTableDataTable4;
        if ($t4->isEmpty()) return collect();

        // Table 4 quantities (Ton)
        $operational   = $t4[0]['power'] ?? 0.0; // Operational usage for Slab Production → row [0]
        $smpGeneration = $t4[1]['power'] ?? 0.0; // SMP Generation                        → row [1]
        $purchaseKpe   = $t4[2]['power'] ?? 0.0; // Purchase KPE                          → row [2]
        $totalPurchase = $t4->last()['power'] ?? 0.0; // Total Purchase Steam

        // CHP Table 2.2
        $chp = new ConfigurationDataCHP();
        $chp->periodYear = $this->periodYear;
        $chp->period     = $this->period;
        $steamConvData      = $chp->steamConversionTableData()->values();
        $chpConv0           = (float) ($steamConvData[0]['conversion'] ?? 0.0); // Tj/ton
        // $conv2              = (float) ($steamConvData[1]['conversion'] ?? 0.0); // EF Steam (tCO2/Tj)
        $conv2 = (float) ($steamConvData[1]['steam'] ?? 0.0);

        // Conv[1] = 0 (fixed)
        $conv1 = 0.0;

        // Steam calculations — compute [1] and [2] first to derive Conv[3]
        $steam1 = $smpGeneration * $conv1; // = 0
        $steam2 = $purchaseKpe   * $conv2;

        // Conv[3] = Steam[1]+[2] / Total Purchase Steam  (blended EF)
        $conv3  = $totalPurchase > 0 ? (($steam1 + $steam2) / $totalPurchase) : 0.0;

        // Conv[0] = Conv[3]
        $conv0  = $conv3;
        $steam0 = $operational * $conv0;

        // Steam[3] = Total = Steam[1] + Steam[2]
        $steam3 = $steam1 + $steam2;

        // Steam[4] = Energy = Purchase KPE * CHP Table 2.2 conversion[0]
        $steam4 = $purchaseKpe * $chpConv0;

        // Steam[5] = EF Steam = Steam[2] / Steam[4]
        $steam5 = $steam4 > 0 ? ($steam2 / $steam4) : 0.0;

        return collect([
            [
                'description' => 'Operational usage for Slab Production',
                'conversion'  => $conv3,  // Conv[3] shown in conversion column
                'conv_tooltip' => '<ul><li>Conversion (tCO2/Ton) [3] = Steam[1+2] / Table 4 Total Purchase Steam</li><li>= (' . $steam1 . ' + ' . $steam2 . ') / ' . $totalPurchase . '</li></ul>',
                'steam'       => $steam0,
                'st_tooltip'  => '<ul><li>Table 4 Operational usage × Conv[0]</li><li>= ' . $operational . ' × ' . $conv0 . '</li></ul>',
                'unit'        => 'tCO2',
            ],
            [
                'description' => 'SMP Generation',
                'conversion'  => $conv1,  // 0 → blade akan tampilkan "-"
                'conv_tooltip' => '<ul><li>Fixed value: 0</li></ul>',
                'steam'       => $steam1,
                'st_tooltip'  => '<ul><li>Table 4 SMP Generation × Conv[1] = 0</li></ul>',
                'unit'        => 'tCO2',
            ],
            [
                'description' => 'Purchase KPE',
                'conversion'  => $conv2,  // CHP Table 2.2 conversion[1]
                'conv_tooltip' => '<ul><li>CHP Power Plant Tab → Table 2.2 conversion[1] (EF Steam tCO2/Tj)</li></ul>',
                'steam'       => $steam2,
                'st_tooltip'  => '<ul><li>Table 4 Purchase KPE × Conv[2]</li><li>= ' . $purchaseKpe . ' × ' . $conv2 . '</li></ul>',
                'unit'        => 'tCO2',
            ],
            [
                'description' => 'Total',
                'conversion'  => $conv3,  // Steam[3] / Total Purchase Steam
                'conv_tooltip' => '<ul><li>Steam[1+2] / Table 4 Total Purchase Steam</li><li>= ' . ($steam1 + $steam2) . ' / ' . $totalPurchase . '</li></ul>',
                'steam'       => $steam3,
                'st_tooltip'  => '<ul><li>Total = Steam[1] + Steam[2]</li><li>= ' . $steam1 . ' + ' . $steam2 . '</li></ul>',
                'unit'        => 'tCO2',
                'is_total'    => true,
            ],
            [
                'description' => 'Energy',
                'conversion'  => null,  // teks "Energy" tampil di blade
                'conv_tooltip' => '<ul><li>CHP Power Plant Tab → Table 2.2 conversion[0] (Tj/ton)</li></ul>',
                'steam'       => $steam4,
                'st_tooltip'  => '<ul><li>Table 4 Purchase KPE × CHP Table 2.2 conversion[0]</li><li>= ' . $purchaseKpe . ' × ' . $chpConv0 . '</li></ul>',
                'unit'        => 'Tj',
            ],
            [
                'description' => 'EF Steam',
                'conversion'  => 'EF Steam',
                'conv_tooltip' => '<ul><li>Steam[2] / Steam[4] (Energy)</li><li>= ' . $steam2 . ' / ' . $steam4 . '</li></ul>',
                'steam'       => $steam5,
                'st_tooltip'  => '<ul><li>Steam[2] / Steam[4] (Energy)</li><li>= ' . $steam2 . ' / ' . $steam4 . '</li></ul>',
                'unit'        => 'tCO2/Tj',
            ],
        ]);
    }

    #[Computed]
    public function emissionTableData51(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        $export = $this->energyTableDataExport;
        if ($export->isEmpty()) return collect();

        $convCog = 0.000018410; // COG
        $convBfg = 0.000003138; // BFG 
        $convLdg = 0.000008368; // LDG

        $conversions = [
            $convCog, // [0] COG for Plate Mill
            $convCog, // [1] COG for HRP
            $convCog, // [2] Total COG Sales
            $convBfg, // [3] BFG for KPE
            $convLdg, // [4] LDG for KPE
            $convBfg, // [5] BFG for COP
        ];

        $rows    = collect();
        $grandTotal = 0.0;

        foreach ($export as $index => $row) {
            $conv     = $conversions[$index] ?? 0.0;
            $byproduct = $row['quantity'] * $conv;
            $grandTotal += $byproduct;

            $rows->push([
                'description' => $row['description'],
                'conversion'  => $conv,
                'conv_tooltip' => '<ul><li>Conversion factor (TJ/m3) for ' . $row['description'] . '</li></ul>',
                'byproduct'   => $byproduct,
                'bp_tooltip'  => '<ul><li>Export ' . $row['description'] . ' × Conversion (TJ/m3)</li><li>= ' . $row['quantity'] . ' × ' . $conv . '</li></ul>',
                'unit'        => 'Tj',
            ]);
        }

        $rows->push([
            'description' => 'Grand Total',
            'conversion'  => 'Grand Total',
            'conv_tooltip' => '',
            'byproduct'   => $grandTotal,
            'bp_tooltip'  => '<ul><li>Total By Product Gas [0]:[5]</li></ul>',
            'unit'        => 'Tj',
            'is_total'    => true,
        ]);

        return $rows;
    }
    #[Computed]
    public function emissionTableData61(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        $import = $this->energyTableDataImport;
        if ($import->isEmpty()) return collect();

        $convCog = 0.000018410;

        $row = $import->first();

        return collect([
            [
                'description' => $row['description'],
                'conversion'  => $convCog,
                'conv_tooltip' => '<ul><li>Conversion factor (TJ/m3) for COG</li></ul>',
                'cog'         => $row['quantity'] * $convCog,
                'cog_tooltip' => '<ul><li>Table 6 ' . $row['description'] . ' × Conversion (TJ/m3)</li><li>= ' . $row['quantity'] . ' × ' . $convCog . '</li></ul>',
                'unit'        => 'Tj',
            ],
        ]);
    }

    #[Computed]
    public function emissionTableData71(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        $exportElec = $this->energyTableDataExportElectricity;
        if ($exportElec->isEmpty()) return collect();

        // EF from Table 2.1 last row (Total) emission_factor
        $ef = $this->emissionTableData21->last()['emission_factor'] ?? 0.0;

        // Only row [0] Reverse Power and row [1] Export to Coke Plant
        $rows = collect();
        foreach ([0, 1] as $index) {
            $row    = $exportElec->values()[$index] ?? null;
            if (!$row) continue;

            $emission = $ef * $row['quantity'];

            $rows->push([
                'description' => $row['description'],
                'emission_factor' => $ef,
                'ef_tooltip'  => '<ul><li>Table 2.1 Emission Factor (tCO2/MWh) [4] — blended EF</li></ul>',
                'total_emission'  => $emission,
                'em_tooltip'  => '<ul><li>Emission Factor (tCO2/MWh) [0] × Table 7 ' . $row['description'] . ' / 1000</li><li>= ' . $ef . ' × ' . $row['quantity'] . ' / 1000</li></ul>',
                'unit'        => 'tCO2',
            ]);
        }

        return $rows;
    }

    #[Computed]
    public function emissionTableData52(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        $t51 = $this->emissionTableData51;
        if ($t51->isEmpty()) return collect();

        $factors = [44.4, 44.4, 44.4, 260.0, 182.0, 260.0];

        $rows        = collect();
        $grandTotal  = 0.0;

        foreach ($factors as $index => $factor) {
            $byproduct = $t51->values()[$index]['byproduct'] ?? 0.0;
            $emission  = $factor * $byproduct;
            $grandTotal += $emission;

            $rows->push([
                'description'    => $t51->values()[$index]['description'] ?? '',
                'emission_factor' => $factor,
                'ef_tooltip'     => '<ul><li>Emission Factor (tCO2/Tj) [' . $index . ']</li></ul>',
                'total_emission' => $emission,
                'em_tooltip'     => '<ul><li>Emission Factor (tCO2/Tj) [' . $index . '] × Table 5.1 By Product Gas [' . $index . ']</li><li>= ' . $factor . ' × ' . $byproduct . '</li></ul>',
                'unit'           => 'tCO2',
            ]);
        }

        // Grand Total row
        $rows->push([
            'description'    => 'Grand Total',
            'emission_factor' => 'Grand Total',
            'ef_tooltip'     => '',
            'total_emission' => $grandTotal,
            'em_tooltip'     => '<ul><li>Total Emission [0:5]</li></ul>',
            'unit'           => 'tCO2',
            'is_total'       => true,
        ]);

        // Emission Factor row = Grand Total / Table 5.1 Grand Total (byproduct[6])
        $grandTotalBp = $t51->last()['byproduct'] ?? 0.0;
        $efRow        = $grandTotalBp > 0 ? ($grandTotal / $grandTotalBp) : 0.0;

        $rows->push([
            'description'    => 'Emission Factor',
            'emission_factor' => 'Emission Factor',
            'ef_tooltip'     => '',
            'total_emission' => $efRow,
            'em_tooltip'     => '<ul><li>Total Emission [0:5] / Table 5.1 Grand Total By Product Gas</li><li>= ' . $grandTotal . ' / ' . $grandTotalBp . '</li></ul>',
            'unit'           => 'tCO2/Tj',
        ]);

        return $rows;
    }

    #[Computed]
    public function emissionTableData62(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        $t61 = $this->emissionTableData61;
        if ($t61->isEmpty()) return collect();

        $factor  = 44.4;
        $cog0    = $t61->first()['cog'] ?? 0.0;
        $emission = $factor * $cog0;
        $ef       = $cog0 > 0 ? ($emission / $cog0) : 0.0;

        return collect([
            [
                'emission_factor' => $factor,
                'ef_tooltip'      => '<ul><li>Emission Factor (tCO2/Tj) [0]</li></ul>',
                'total_emission'  => $emission,
                'em_tooltip'      => '<ul><li>Emission Factor (tCO2/Tj) [0] × Table 6.1 COG [0]</li><li>= ' . $factor . ' × ' . $cog0 . '</li></ul>',
                'unit'            => 'tCO2',
            ],
            [
                'emission_factor' => 'Emission Factor',
                'ef_tooltip'      => '',
                'total_emission'  => $ef,
                'em_tooltip'      => '<ul><li>Total Emission [0] / Table 6.1 COG [0]</li><li>= ' . $emission . ' / ' . $cog0 . '</li></ul>',
                'unit'            => 'tCO2/Tj',
            ],
        ]);
    }

    #[Computed]
    public function emissionTableData8(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        // Table 6 source[0] = Export Electricity row[0] (Reverse Power) / 1000
        $exportElec  = $this->energyTableDataTable2;
        $source0     = ($exportElec->values()[0]['power'] ?? 0.0) / 1000;

        // Table 2.1 total emission [0] = emissionTableData21 row[0] total_emission
        $t21Em0 = $this->emissionTableData21->values()[0]['total_emission'] ?? 0.0;

        // Table 4.1 steam [0] = emissionTableData41 row[0] steam
        $t41Steam0 = $this->emissionTableData41->values()[0]['steam'] ?? 0.0;

        // Steel HRC Table 7 TCO2[3] = hrcTable7Data row[3] tco2
        $hrc = new ConfigurationDataHRC();
        $hrc->periodYear = $this->periodYear;
        $hrc->period     = $this->period;
        $hrcTco23 = $hrc->hrcTable7Data->values()[3]['tco2'] ?? 0.0;

        return collect([
            [
                'description' => 'Operational usage for Slab Production',
                'tooltip'     => '<ul><li>Table 6 Source[0] (Reverse Power) / 1000</li><li>= ' . ($exportElec->values()[0]['quantity'] ?? 0.0) . ' / 1000</li></ul>',
                'quantity'    => $source0,
                'unit'        => '',
            ],
            [
                'description' => 'Indirect slab',
                'tooltip'     => '<ul><li>Table 2.1 Total Emission [0] + Table 4.1 Steam [0]</li><li>= ' . $t21Em0 . ' + ' . $t41Steam0 . '</li></ul>',
                'quantity'    => $t21Em0 + $t41Steam0,
                'unit'        => 'tCO2',
            ],
            [
                'description' => 'Indirect total (masih salah formula)',
                'tooltip'     => '<ul><li>Table 4.1 Steam [0] + Steel HRC Tab → Table 2.1 By Product Gas [0]</li><li>= ' . $t41Steam0 . ' + ' . $hrcTco23 . '</li></ul>',
                'quantity'    => $t41Steam0 + $hrcTco23,
                'unit'        => 'tCO2',
            ],
        ]);
    }

    #[Computed]
    public function emissionTableData9(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) return collect();

        // Table 6.1 COG [0]
        $cog61 = $this->emissionTableData61->first()['cog'] ?? 0.0;

        // Table 6.2 Emission Factor (tCO2/Tj) [0]
        $ef62 = $this->emissionTableData62->first()['emission_factor'] ?? 0.0;

        // Steel HRC instances
        $hrc = new ConfigurationDataHRC();
        $hrc->periodYear = $this->periodYear;
        $hrc->period     = $this->period;

        // HRC Table 4 Natural Gas [0]
        $hrcNg4 = $hrc->hrcTable4Data->first()['natural_gas'] ?? 0.0;

        // HRC Table 5 MWh [0]
        $hrcMwh5 = $hrc->hrcTable5Data->values()[0]['mwh'] ?? 0.0;

        // HRC Table 7 EF [3]
        $hrcEf7 = $hrc->hrcTable7Data->values()[3]['ef'] ?? 0.0;

        // Table 8 quantity [0]
        $t8Qty0 = $this->emissionTableData8->values()[0]['quantity'] ?? 0.0;

        // Row 0 — Waste gas Imported
        $wasteGasImported = $cog61 + $hrcNg4;

        // Row 1 — Emission Factors Imported
        $efImported = $wasteGasImported > 0
            ? (($cog61 * $ef62) + ($hrcNg4 * 44.4)) / $wasteGasImported
            : 0.0;

        // Row 2 — Electricity Imported
        $elecImported = $t8Qty0 + $hrcMwh5;

        // Row 3 — Emission Factors Electricity
        $efElec = $hrcEf7;

        return collect([
            [
                'description' => 'Waste gas',
                'tooltip'     => '<ul><li>Table 6.1 COG [0] + Steel HRC Tab → Table 4 Natural Gas [0]</li><li>= ' . $cog61 . ' + ' . $hrcNg4 . '</li></ul>',
                'imported'    => $wasteGasImported,
                'im_tooltip'  => '',
                'exported'    => null,
                'ex_tooltip'  => '',
                'unit'        => 'TJ',
            ],
            [
                'description' => 'Emission Factors',
                'tooltip'     => '<ul><li>((Table 6.1 COG [0] × Table 6.2 EF [0]) + (HRC Table 4 NG [0] × 44.4)) / Imported[0]</li><li>= ((' . $cog61 . ' × ' . $ef62 . ') + (' . $hrcNg4 . ' × 44.4)) / ' . $wasteGasImported . '</li></ul>',
                'imported'    => $efImported,
                'im_tooltip'  => '',
                'exported'    => null,
                'ex_tooltip'  => '',
                'unit'        => 'tCO2/TJ',
            ],
            [
                'description' => 'Electricity',
                'tooltip'     => '<ul><li>Table 8 Quantity[0] + Steel HRC Tab → Table 5 MWh[0]</li><li>= ' . $t8Qty0 . ' + ' . $hrcMwh5 . '</li></ul>',
                'imported'    => $elecImported,
                'im_tooltip'  => '',
                'exported'    => null,
                'ex_tooltip'  => '',
                'unit'        => 'MWh',
            ],
            [
                'description' => 'Emission Factors',
                'tooltip'     => '<ul><li>Steel HRC Tab → Table 7 EF[3]</li></ul>',
                'imported'    => $efElec,
                'im_tooltip'  => '',
                'exported'    => null,
                'ex_tooltip'  => '',
                'unit'        => 'tCO2/MWh',
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
            'emissionTableData31'              => $this->emissionTableData31,
            'emissionTableData41'              => $this->emissionTableData41,
            'emissionTableData51'              => $this->emissionTableData51,
            'emissionTableData61'              => $this->emissionTableData61,
            'emissionTableData71'              => $this->emissionTableData71,
            'emissionTableData52' => $this->emissionTableData52,
            'emissionTableData62' => $this->emissionTableData62,
            'emissionTableData8' => $this->emissionTableData8,
            'emissionTableData9' => $this->emissionTableData9,
        ]);
    }
}
