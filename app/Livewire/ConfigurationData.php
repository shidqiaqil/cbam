<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MasterEnergyData;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
#[Title('Config Data')]
class ConfigurationData extends Component
{
    #[Url]
    public $activeTab = 'steel-slab';

    // NEW: Filters for Energy Table 1
    #[Url]
    public $periodYear = '';
    #[Url]
    public $period = ''; // jan, feb, ..., q1, q2, q3, q4, yearly

    // NEW: Hardcoded Energy Table 1 rows with query conditions
    protected array $energyRows = [
        [
            'description' => 'BF Generation',
            'tooltip' => '<ul><li>Quantity from Master_energy_data where plant_code = IBN000, plant_name = Blast Furnace Plant, criteria= PRODUCTION, energy_name = AIR BLAST</li></ul>',
            'conditions' => [
                ['plant_code' => 'IBN000', 'plant_name' => 'Blast Furnace Plant', 'criteria' => 'PRODUCTION', 'energy_name' => 'AIR BLAST']
            ]
        ],
        [
            'description' => 'Purchase Electricity from PLN',
            'tooltip' => '<ul><li>Quantity from Master_energy_data where plant_code = I01200, plant_name = Purchase (PLN),criteria= PRODUCTION, energy_name = POWER</li></ul>',
            'conditions' => [
                ['plant_code' => 'I01200', 'plant_name' => 'Purchase (PLN)', 'criteria' => 'PRODUCTION', 'energy_name' => 'POWER']
            ]
        ],
        [
            'description' => 'Purchase (KPE Power)',
            'tooltip' => '<ul><li>Quantity from Master_energy_data where plant_code = I01200, plant_name = Purchase (KPE), criteria = PRODUCTION, energy_name = POWER</li></ul>',
            'conditions' => [
                ['plant_code' => 'I01200', 'plant_name' => 'Purchase (KPE)', 'criteria' => 'PRODUCTION', 'energy_name' => 'POWER']
            ]
        ],
        [
            'description' => 'Plate Mill Electricity Consumption',
            'tooltip' => '<ul><li>Quantity from Master_energy_data where plant_code = ILA000, plant_name = Plate plant,criteria= CONSUMPTION, energy_name = POWER</li></ul>',
            'conditions' => [
                ['plant_code' => 'ILA000', 'plant_name' => 'Plate plant', 'criteria' => 'CONSUMPTION', 'energy_name' => 'POWER']
            ]
        ],
        [
            'description' => 'COP Electricity Consumption',
            'tooltip' => '<ul><li>Sum(Quantity) from Master_energy_data where plant_code = ICA000, plant_name = Cokes plant, criteria = CONSUMPTION, energy_name = POWER</li><li>plant_code = ICK000, plant_name = Cokes plant, criteria = CONSUMPTION, energy_name = Cokes by product plant</li></ul>',
            'conditions' => [
                ['plant_code' => 'ICA000', 'plant_name' => 'Cokes plant', 'criteria' => 'CONSUMPTION', 'energy_name' => 'POWER'],
                ['plant_code' => 'ICK000', 'plant_name' => 'Cokes plant', 'criteria' => 'CONSUMPTION', 'energy_name' => 'Cokes by product plant']
            ]
        ]
    ];

    // NEW: Hardcoded Energy Table 2 rows for Steel Slab (below Table 1)
    protected array $energyRowsTable2 = [
        [
            'description' => 'Operational usage for Slab Production',
            'tooltip' => '<ul><li>Sum(Quantity) from Master_energy_data where:<ul>
        <li>plant_code = I02210, plant_name = HQ, energy_name = POWER, criteria = CONSUMPTION</li>
        <li>plant_code = IBA000, plant_name = Raw Material, energy_name = POWER, criteria = CONSUMPTION</li>
        <li>plant_code = IBB000, plant_name = Sinter Plant, energy_name = POWER, criteria = CONSUMPTION</li>
        <li>plant_code = IBN000, plant_name = Blast Furnace Plant, energy_name = POWER, criteria = CONSUMPTION</li>
        <li>plant_code = IEA000, plant_name = Steel making, energy_name = POWER, criteria = CONSUMPTION</li>
        <li>plant_code = IEE000, plant_name = Continuous Casting, energy_name = POWER, criteria = CONSUMPTION</li>
        <li>plant_code = ITC110, plant_name = Water-Fresh water from KTI, energy_name = POWER, criteria = CONSUMPTION</li>
        <li>plant_code = ITC120, plant_name = Water-Fresh water from recycling facility, energy_name = POWER, criteria = CONSUMPTION</li>
        <li>plant_code = ITD120, plant_name = Utility- By Product Gas distribution, energy_name = POWER, criteria = CONSUMPTION</li>
        <li>plant_code = ITB110, plant_name = Electric Power System-incoming and distribution, energy_name = POWER, criteria = CONSUMPTION</li>
        <li>plant_code = ITZ990, plant_name = Energy Dept Common, energy_name = POWER, criteria = CONSUMPTION</li>
        <li>plant_code = IUA000, plant_name = Facility Technology Department, energy_name = POWER, criteria = CONSUMPTION</li>
        <li>plant_code = IVA000, plant_name = Production Technology Departement, energy_name = POWER, criteria = CONSUMPTION</li>
        <li>plant_code = IWA000, plant_name = Technology and Business Development Division, energy_name = POWER, criteria = CONSUMPTION</li>
    </ul></li></ul>',
            'conditions' => [
                ['plant_code' => 'I02210', 'plant_name' => 'HQ', 'energy_name' => 'POWER', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'IBA000', 'plant_name' => 'Raw Material', 'energy_name' => 'POWER', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'IBB000', 'plant_name' => 'Sinter Plant', 'energy_name' => 'POWER', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'IBN000', 'plant_name' => 'Blast Furnace Plant', 'energy_name' => 'POWER', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'IEA000', 'plant_name' => 'Steel making', 'energy_name' => 'POWER', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'IEE000', 'plant_name' => 'Continuous Casting', 'energy_name' => 'POWER', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'ITC110', 'plant_name' => 'Water-Fresh water from KTI', 'energy_name' => 'POWER', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'ITC120', 'plant_name' => 'Water-Fresh water from recycling facility', 'energy_name' => 'POWER', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'ITD120', 'plant_name' => 'Utility- By Product Gas distribution', 'energy_name' => 'POWER', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'ITB110', 'plant_name' => 'Electric Power System-incoming and distribution', 'energy_name' => 'POWER', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'ITZ990', 'plant_name' => 'Energy Dept Common', 'energy_name' => 'POWER', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'IUA000', 'plant_name' => 'Facility Technology Department', 'energy_name' => 'POWER', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'IVA000', 'plant_name' => 'Production Technology Departement', 'energy_name' => 'POWER', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'IWA000', 'plant_name' => 'Technology and Business Development Division', 'energy_name' => 'POWER', 'criteria' => 'CONSUMPTION'],
            ]
        ],
        [
            'description' => 'BF Generation',
            'tooltip' => '<ul><li>Quantity from Master_energy_data where plant_code = IBN000, plant_name = Blast Furnace Plant, criteria=PRODUCTION , energy_name = POWER</li></ul>',
            'conditions' => [
                ['plant_code' => 'IBN000', 'plant_name' => 'Blast Furnace Plant', 'energy_name' => 'POWER', 'criteria' => 'PRODUCTION']
            ]
        ],
        [
            'description' => 'Purchase (KPE Power)',
            'tooltip' => '<ul><li>Quantity from Master_energy_data where plant_code = I01200, plant_name = Purchase (KPE), criteria=PRODUCTION , energy_name = POWER</li></ul>',
            'conditions' => [
                ['plant_code' => 'I01200', 'plant_name' => 'Purchase (KPE)', 'energy_name' => 'POWER', 'criteria' => 'PRODUCTION']
            ]
        ],
        [
            'description' => 'Purchase Electricity from PLN',
            'tooltip' => '<ul><li>Quantity from Master_energy_data where plant_code = I01200, plant_name = Purchase (PLN),criteria=PRODUCTION ,energy_name = POWER</li></ul>',
            'conditions' => [
                ['plant_code' => 'I01200', 'plant_name' => 'Purchase (PLN)', 'energy_name' => 'POWER', 'criteria' => 'PRODUCTION']
            ]
        ]
    ];

    // NEW: Hardcoded Energy Table 3 rows for Steel Slab STEAM (Table 3)
    protected array $energyRowsTable3 = [
        [
            'description' => 'Export to Coke Plant & Vendor',
            'tooltip' => '<ul><li>Sum(Quantity) from Master_energy_data where:<ul><li>plant_code = ICK000, plant_name = Cokes by product plant,criteria=CONSUMPTION ,energy_name = STEAM</li><li>plant_code = I022B0, plant_name = Vendor, energy_name = STEAM</li><li>plant_code = I022CA, plant_name = Sales (KPCC, Lime Calcining), energy_name = STEAM</li><li>plant_code = I022CC, plant_name = Sales (Linde, Oxygen Plant), energy_name = STEAM</li></ul></li></ul>',
            'conditions' => [
                ['plant_code' => 'ICK000', 'plant_name' => 'Cokes by product plant', 'energy_name' => 'STEAM', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'I022B0', 'plant_name' => 'Vendor', 'energy_name' => 'STEAM', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'I022CA', 'plant_name' => 'Sales (KPCC, Lime Calcining)', 'energy_name' => 'STEAM', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'I022CC', 'plant_name' => 'Sales (Linde, Oxygen Plant)', 'energy_name' => 'STEAM', 'criteria' => 'CONSUMPTION']
            ]
        ],
        [
            'description' => 'BF',
            'tooltip' => '<ul><li>Quantity from Master_energy_data where plant_code = IBN000, plant_name = Blast Furnace Plant, criteria=CONSUMPTION , energy_name = STEAM</li></ul>',
            'conditions' => [
                ['plant_code' => 'IBN000', 'plant_name' => 'Blast Furnace Plant', 'energy_name' => 'STEAM', 'criteria' => 'CONSUMPTION']
            ]
        ],
        [
            'description' => 'SMP & CCP + Energy',
            'tooltip' => '<ul><li>Sum(Quantity) from Master_energy_data where:<ul><li>plant_code = IEA000, plant_name = Steel making, criteria=CONSUMPTION , energy_name = STEAM</li><li>plant_code = ITD120, plant_name = Utility- By Product Gas distribution, criteria=CONSUMPTION , energy_name = STEAM</li></ul></li></ul>',
            'conditions' => [
                ['plant_code' => 'IEA000', 'plant_name' => 'Steel making', 'energy_name' => 'STEAM', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'ITD120', 'plant_name' => 'Utility- By Product Gas distribution', 'energy_name' => 'STEAM', 'criteria' => 'CONSUMPTION']
            ]
        ]
    ];

    // NEW: Hardcoded Energy Table 4 rows for Slab Production STEAM (Table 4)
    protected array $energyRowsTable4 = [
        [
            'description' => 'Operational usage for Slab Production',
            'tooltip' => '<ul><li>Sum(Quantity) from Master_energy_data where plant_code = I02400, plant_name = Loss, criteria=CONSUMPTION,energy_name = STEAM
and 
 plant_code = IBN000, plant_name =Blast Furnace Plant, criteria=CONSUMPTION, energy_name = STEAM
	And
	 plant_code = IEA000, plant_name =Steel making, criteria=CONSUMPTION, energy_name = STEAM
	And
	 plant_code = IEE000, plant_name =Continuous Casting, criteria=CONSUMPTION, energy_name = STEAM
	And
	 plant_code = IHA000, plant_name =Hot Strip Mill, criteria=CONSUMPTION, energy_name = STEAM
	
	And
	 plant_code = ITD120, plant_name =Utility- By Product Gas distribution, criteria=CONSUMPTION, energy_name = STEAM</li></ul>',
            'conditions' => [
                ['plant_code' => 'I02400', 'plant_name' => 'Loss', 'energy_name' => 'STEAM', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'IBN000', 'plant_name' => 'Blast Furnace Plant', 'energy_name' => 'STEAM', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'IEA000', 'plant_name' => 'Steel making', 'energy_name' => 'STEAM', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'IEE000', 'plant_name' => 'Continuous Casting', 'energy_name' => 'STEAM', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'IHA000', 'plant_name' => 'Hot Strip Mill', 'energy_name' => 'STEAM', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'ITD120', 'plant_name' => 'Utility- By Product Gas distribution', 'energy_name' => 'STEAM', 'criteria' => 'CONSUMPTION']
            ]
        ],
        [
            'description' => 'SMP Generation',
            'tooltip' => '<ul><li>Quantity from Master_energy_data where plant_code = IEA000, plant_name =Steel making, criteria=PRODUCTION, energy_name = STEAM</li></ul>',
            'conditions' => [
                ['plant_code' => 'IEA000', 'plant_name' => 'Steel making', 'energy_name' => 'STEAM', 'criteria' => 'PRODUCTION']
            ]
        ],
        [
            'description' => 'Purchase KPE',
            'tooltip' => '<ul><li>Quantity from Master_energy_data where plant_code = I01200, plant_name =Purchase, criteria=PRODUCTION,energy_name = STEAM</li></ul>',
            'conditions' => [
                ['plant_code' => 'I01200', 'plant_name' => 'Purchase', 'energy_name' => 'STEAM', 'criteria' => 'PRODUCTION']
            ]
        ]
    ];

    // NEW: Export table rows
    protected array $energyRowsExport = [
        [
            'description' => 'COG for Plate Mill',
            'tooltip' => '<ul><li>-</li></ul>',
            'conditions' => [],
            'fixedValue' => 0
        ],
        [
            'description' => 'COG for HRP',
            'tooltip' => '<ul><li>-</li></ul>',
            'conditions' => [],
            'fixedValue' => 0
        ],
        [
            'description' => 'Total COG Sales',
            'tooltip' => '<ul><li>-</li></ul>',
            'conditions' => [],
            'fixedValue' => 0
        ],
        [
            'description' => 'BFG for KPE',
            'tooltip' => '<ul><li>Quantity from Master_energy_data where plant_code = I022CB, plant_name = Sales (KPE, Power generation), criteria = CONSUMPTION, energy_name = BFG</li></ul>',
            'conditions' => [
                ['plant_code' => 'I022CB', 'plant_name' => 'Sales (KPE, Power generation)', 'criteria' => 'CONSUMPTION', 'energy_name' => 'BFG']
            ]
        ],
        [
            'description' => 'LDG for KPE',
            'tooltip' => '<ul><li>Quantity from Master_energy_data where plant_code = I022CB, plant_name = Sales (KPE, Power generation), criteria = CONSUMPTION, energy_name = LDG</li></ul>',
            'conditions' => [
                ['plant_code' => 'I022CB', 'plant_name' => 'Sales (KPE, Power generation)', 'criteria' => 'CONSUMPTION', 'energy_name' => 'LDG']
            ]
        ],
        [
            'description' => 'BFG for COP',
            'tooltip' => '<ul><li>Quantity from Master_energy_data where plant_code = ICA000, plant_name = Cokes plant, criteria = CONSUMPTION, energy_name = BFG</li></ul>',
            'conditions' => [
                ['plant_code' => 'ICA000', 'plant_name' => 'Cokes plant', 'criteria' => 'CONSUMPTION', 'energy_name' => 'BFG']
            ]
        ]
    ];

    // NEW: Import table rows - Operational usage for Slab Production COG
    protected array $energyRowsImport = [
        [
            'description' => 'Operational usage for Slab Production',
            'tooltip' => '<ul><li>Sum(Quantity) from Master_energy_data where:<ul><li>plant_code = I02500, plant_name = Diffusion, energy_name = COG, criteria=CONSUMPTION</li><li>plant_code = IBB000, plant_name = Sinter Plant, energy_name = COG, criteria=CONSUMPTION</li><li>plant_code = IBN000, plant_name = Blast Furnace Plant, energy_name = COG, criteria=CONSUMPTION</li><li>plant_code = IEA000, plant_name = Steel making, energy_name = COG, criteria=CONSUMPTION</li></ul></li></ul>',
            'conditions' => [
                ['plant_code' => 'I02500', 'plant_name' => 'Diffusion', 'energy_name' => 'COG', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'IBB000', 'plant_name' => 'Sinter Plant', 'energy_name' => 'COG', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'IBN000', 'plant_name' => 'Blast Furnace Plant', 'energy_name' => 'COG', 'criteria' => 'CONSUMPTION'],
                ['plant_code' => 'IEA000', 'plant_name' => 'Steel making', 'energy_name' => 'COG', 'criteria' => 'CONSUMPTION'],

                ['plant_code' => 'IEE000', 'plant_name' => 'Continuous Casting', 'energy_name' => 'COG', 'criteria' => 'CONSUMPTION']
            ]
        ]
    ];

    public function getEnergyTableDataImportProperty(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) {
            return collect();
        }

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
            'dec' => 'december'
        ];

        $quarterMonths = [
            'q1' => ['january', 'february', 'march'],
            'q2' => ['april', 'may', 'june'],
            'q3' => ['july', 'august', 'september'],
            'q4' => ['october', 'november', 'december']
        ];

        $monthsForPeriod = match (true) {
            $this->period === 'yearly' => array_values($monthMap),
            isset($quarterMonths[$this->period]) => $quarterMonths[$this->period],
            default => [$monthMap[$this->period] ?? '']
        };

        if (empty($monthsForPeriod)) return collect();

        $tableData = collect();

        foreach ($this->energyRowsImport as $rowData) {
            $quantity = 0;
            foreach ($monthsForPeriod as $month) {
                $quantity += $this->getRowQuantity($rowData['conditions'], $month);
            }
            $row = [
                'description' => $rowData['description'],
                'tooltip' => $rowData['tooltip'],
                'quantity' => round($quantity, 2)
            ];
            $tableData->push($row);
        }

        return $tableData;
    }

    // NEW: Get available years from DB for filter dropdown
    public function getAvailableYearsProperty()
    {
        return MasterEnergyData::distinct()
            ->orderByDesc('period_year')
            ->pluck('period_year')
            ->values();
    }

    // NEW: React to filter changes - reset if no year selected
    public function updatedPeriodYear()
    {
        if (empty($this->periodYear)) {
            $this->period = '';
        }
    }

    public function getEnergyTableDataExportProperty(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) {
            return collect();
        }

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
            'dec' => 'december'
        ];

        $quarterMonths = [
            'q1' => ['january', 'february', 'march'],
            'q2' => ['april', 'may', 'june'],
            'q3' => ['july', 'august', 'september'],
            'q4' => ['october', 'november', 'december']
        ];

        $monthsForPeriod = match (true) {
            $this->period === 'yearly' => array_values($monthMap),
            isset($quarterMonths[$this->period]) => $quarterMonths[$this->period],
            default => [$monthMap[$this->period] ?? '']
        };

        if (empty($monthsForPeriod)) return collect();

        $tableData = collect();

        foreach ($this->energyRowsExport as $rowData) {
            $quantity = 0;
            foreach ($monthsForPeriod as $month) {
                $quantity += $this->getRowQuantity($rowData['conditions'], $month);
            }
            $row = [
                'description' => $rowData['description'],
                'tooltip' => $rowData['tooltip'],
                'quantity' => isset($rowData['fixedValue']) ? $rowData['fixedValue'] : round($quantity, 2)
            ];
            $tableData->push($row);
        }

        return $tableData;
    }

    // NEW: Compute fixed table data for Energy Table 4 (Slab Production STEAM Table 4)
    public function getEnergyTableDataTable4Property(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) {
            return collect();
        }

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
            'dec' => 'december'
        ];

        $quarterMonths = [
            'q1' => ['january', 'february', 'march'],
            'q2' => ['april', 'may', 'june'],
            'q3' => ['july', 'august', 'september'],
            'q4' => ['october', 'november', 'december']
        ];

        $monthsForPeriod = match (true) {
            $this->period === 'yearly' => array_values($monthMap),
            isset($quarterMonths[$this->period]) => $quarterMonths[$this->period],
            default => [$monthMap[$this->period] ?? '']
        };

        if (empty($monthsForPeriod)) return collect();

        $tableData = collect();

        foreach ($this->energyRowsTable4 as $rowData) {
            $power = 0;
            foreach ($monthsForPeriod as $month) {
                $power += $this->getRowQuantity($rowData['conditions'], $month);
            }
            $row = [
                'description' => $rowData['description'],
                'tooltip' => $rowData['tooltip'],
                'power' => round($power, 2)
            ];
            $tableData->push($row);
        }

        // NEW: Total Purchase Steam = SMP Generation (row 1) + Purchase KPE (row 2)
        $totalPower = 0;
        if ($tableData->count() >= 3) {
            $totalPower = $tableData[1]['power'] + $tableData[2]['power'];
        }
        $tableData->push(['description' => 'Total Purchase Steam', 'tooltip' => 'SMP Generation + Purchase KPE', 'power' => round($totalPower, 2)]);

        return $tableData;
    }

    public function getEnergyTableDataTable3Property(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) {
            return collect();
        }

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
            'dec' => 'december'
        ];

        $quarterMonths = [
            'q1' => ['january', 'february', 'march'],
            'q2' => ['april', 'may', 'june'],
            'q3' => ['july', 'august', 'september'],
            'q4' => ['october', 'november', 'december']
        ];

        $monthsForPeriod = match (true) {
            $this->period === 'yearly' => array_values($monthMap),
            isset($quarterMonths[$this->period]) => $quarterMonths[$this->period],
            default => [$monthMap[$this->period] ?? '']
        };

        if (empty($monthsForPeriod)) return collect();

        $tableData = collect();

        foreach ($this->energyRowsTable3 as $rowData) {
            $power = 0;
            foreach ($monthsForPeriod as $month) {
                $power += $this->getRowQuantity($rowData['conditions'], $month);
            }
            $row = [
                'description' => $rowData['description'],
                'tooltip' => $rowData['tooltip'],
                'power' => round($power, 2)
            ];
            $tableData->push($row);
        }

        // NEW: Total = BF + SMP & CCP + Energy (rows 1+2 0-index)
        $totalPower = 0;
        if ($tableData->count() >= 3) {
            $totalPower = $tableData[1]['power'] + $tableData[2]['power'];
        }
        $tableData->push(['description' => 'Total', 'tooltip' => 'BF + SMP & CCP + Energy', 'power' => round($totalPower, 2)]);

        return $tableData;
    }

    // NEW: Compute fixed table data for Energy Table 2 (Steel Slab Table 2 below Table 1)
    public function getEnergyTableDataTable2Property(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) {
            return collect();
        }

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
            'dec' => 'december'
        ];

        $quarterMonths = [
            'q1' => ['january', 'february', 'march'],
            'q2' => ['april', 'may', 'june'],
            'q3' => ['july', 'august', 'september'],
            'q4' => ['october', 'november', 'december']
        ];

        $monthsForPeriod = match (true) {
            $this->period === 'yearly' => array_values($monthMap),
            isset($quarterMonths[$this->period]) => $quarterMonths[$this->period],
            default => [$monthMap[$this->period] ?? '']
        };

        if (empty($monthsForPeriod)) return collect();

        $tableData = collect();

        foreach ($this->energyRowsTable2 as $rowData) {
            $power = 0;
            foreach ($monthsForPeriod as $month) {
                $power += $this->getRowQuantity($rowData['conditions'], $month);
            }
            $row = [
                'description' => $rowData['description'],
                'tooltip' => $rowData['tooltip'],
                'power' => round($power, 2)
            ];
            $tableData->push($row);
        }

        // NEW: Total Purchase Electricity = BF Generation + Purchase KPE + PLN (indices 1,2,3)
        $totalPurchase = 0;
        if ($tableData->count() >= 4) {
            $totalPurchase = $tableData[1]['power'] + $tableData[2]['power'] + $tableData[3]['power'];
        }
        $tableData->push(['description' => 'Total Purchase Electricity', 'tooltip' => 'BF Generation + Purchase (KPE Power) + Purchase Electricity from PLN', 'power' => round($totalPurchase, 2)]);

        return $tableData;
    }

    // NEW: Compute fixed table data for Energy Table 1 (Description | Power | Unit) based on selected period/year
    public function getEnergyTableDataProperty(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) {
            return collect();
        }

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
            'dec' => 'december'
        ];

        $quarterMonths = [
            'q1' => ['january', 'february', 'march'],
            'q2' => ['april', 'may', 'june'],
            'q3' => ['july', 'august', 'september'],
            'q4' => ['october', 'november', 'december']
        ];

        $monthsForPeriod = match (true) {
            $this->period === 'yearly' => array_values($monthMap),
            isset($quarterMonths[$this->period]) => $quarterMonths[$this->period],
            default => [$monthMap[$this->period] ?? '']
        };

        if (empty($monthsForPeriod)) return collect();

        $tableData = collect();

        foreach ($this->energyRows as $rowData) {
            $power = 0;
            foreach ($monthsForPeriod as $month) {
                $power += $this->getRowQuantity($rowData['conditions'], $month);
            }
            $row = [
                'description' => $rowData['description'],
                'tooltip' => $rowData['tooltip'],
                'power' => round($power, 2)
            ];
            $tableData->push($row);
        }

        // NEW: Total row sum of all powers
        $totalPower = $tableData->sum('power');
        $tableData->push(['description' => 'Total', 'tooltip' => 'Sum data 1 tabel ini', 'power' => round($totalPower, 2)]);

        return $tableData;
    }

    // NEW: Helper to get sum(quantity) for row conditions in specific month/year
    private function getRowQuantity(array $conditions, string $month): float
    {
        $query = MasterEnergyData::where('period_year', $this->periodYear)
            ->whereRaw('LOWER(TRIM(period_month)) = ?', [strtolower(trim($month))]);

        $query->where(function ($q) use ($conditions) {
            foreach ($conditions as $cond) {
                $q->orWhere(function ($subQ) use ($cond) {
                    $subQ->where('plant_code', $cond['plant_code'])
                        ->where('plant_name', $cond['plant_name'])
                        ->where('energy_name', $cond['energy_name']);
                    if (isset($cond['criteria'])) {
                        $subQ->where('criteria', $cond['criteria']);
                    }
                });
            }
        });

        return $query->sum('quantity');
    }

    // NEW: Export Electricity rows
    protected array $energyRowsExportElectricity = [
        [
            'description' => 'Reverse Power',
            'tooltip' => '<ul><li>Quantity from Master_energy_data where plant_code = I02300, plant_name = Reverse Power, criteria = CONSUMPTION, energy_name = POWER</li></ul>',
            'conditions' => [
                ['plant_code' => 'I02300', 'plant_name' => 'Reverse Power', 'criteria' => 'CONSUMPTION', 'energy_name' => 'POWER']
            ]
        ],
        [
            'description' => 'Export to Coke Plant',
            'tooltip' => '<ul><li>Sum(Quantity) from Master_energy_data where <ul><li> plant_code = ICA000, plant_name = Cokes plant, energy_name = POWER, criteria = CONSUMPTION </li><li> plant_code = ICK000, plant_name = Cokes by product plant, energy_name = POWER, criteria = CONSUMPTION</li></ul></ul>',
            'conditions' => [
                ['plant_code' => 'ICA000', 'plant_name' => 'Cokes plant', 'criteria' => 'CONSUMPTION', 'energy_name' => 'POWER'],
                ['plant_code' => 'ICK000', 'plant_name' => 'Cokes by product plant', 'criteria' => 'CONSUMPTION', 'energy_name' => 'POWER']
            ]
        ],
        [
            'description' => 'Total Power Sales to tenant',
            'tooltip' => '<ul><li>Sum(Quantity) from Master_energy_data where <ul><li> plant_code = I022B0, plant_name = Vendor, energy_name = POWER, criteria = CONSUMPTION </li><li> plant_code = I022CA, plant_name = Sales (KPCC, Lime Calcining), energy_name = POWER, criteria = CONSUMPTION</li><li> plant_code = I022CC, plant_name = Sales (Linde, Oxygen Plant), energy_name = POWER, criteria = CONSUMPTION</li><li> plant_code = I022CD, plant_name = Sales (KDL for Excess Power), energy_name = POWER, criteria = CONSUMPTION</li></ul></ul>',
            'conditions' => [
                ['plant_code' => 'I022B0', 'plant_name' => 'Vendor', 'criteria' => 'CONSUMPTION', 'energy_name' => 'POWER'],
                ['plant_code' => 'I022CA', 'plant_name' => 'Sales (KPCC, Lime Calcining)', 'criteria' => 'CONSUMPTION', 'energy_name' => 'POWER'],
                ['plant_code' => 'I022CC', 'plant_name' => 'Sales (Linde, Oxygen Plant)', 'criteria' => 'CONSUMPTION', 'energy_name' => 'POWER'],
                ['plant_code' => 'I022CD', 'plant_name' => 'Sales (KDL for Excess Power)', 'criteria' => 'CONSUMPTION', 'energy_name' => 'POWER'],
                ['plant_code' => 'I022D0', 'plant_name' => 'Other (free incharge)', 'criteria' => 'CONSUMPTION', 'energy_name' => 'POWER']
            ]
        ]
    ];

    public function getEnergyTableDataExportElectricityProperty(): Collection
    {
        if (empty($this->periodYear) || empty($this->period)) {
            return collect();
        }

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
            'dec' => 'december'
        ];

        $quarterMonths = [
            'q1' => ['january', 'february', 'march'],
            'q2' => ['april', 'may', 'june'],
            'q3' => ['july', 'august', 'september'],
            'q4' => ['october', 'november', 'december']
        ];

        $monthsForPeriod = match (true) {
            $this->period === 'yearly' => array_values($monthMap),
            isset($quarterMonths[$this->period]) => $quarterMonths[$this->period],
            default => [$monthMap[$this->period] ?? '']
        };

        if (empty($monthsForPeriod)) return collect();

        $tableData = collect();

        $reversePower = 0;
        foreach ($this->energyRowsExportElectricity[0]['conditions'] as $cond) {
            foreach ($monthsForPeriod as $month) {
                $reversePower += $this->getRowQuantity([$cond], $month);
            }
        }

        // Row 1
        $row1 = [
            'description' => 'Reverse Power',
            'tooltip' => $this->energyRowsExportElectricity[0]['tooltip'],
            'quantity' => round($reversePower, 2)
        ];
        $tableData->push($row1);

        // Row 2
        $quantity2 = 0;
        foreach ($monthsForPeriod as $month) {
            $quantity2 += $this->getRowQuantity($this->energyRowsExportElectricity[1]['conditions'], $month);
        }
        $row2 = [
            'description' => 'Export to Coke Plant',
            'tooltip' => $this->energyRowsExportElectricity[1]['tooltip'],
            'quantity' => round($quantity2, 2)
        ];
        $tableData->push($row2);

        // Row 3
        $quantity3 = 0;
        foreach ($monthsForPeriod as $month) {
            $quantity3 += $this->getRowQuantity($this->energyRowsExportElectricity[2]['conditions'], $month);
        }
        $row3 = [
            'description' => 'Total Power Sales to tenant',
            'tooltip' => $this->energyRowsExportElectricity[2]['tooltip'],
            'quantity' => round($quantity3, 2)
        ];
        $tableData->push($row3);

        // Row 4: Reverse Power/1000
        $row4 = [
            'description' => 'Reverse Power/1000',
            'tooltip' => 'Reverse Power value / 1000',
            'quantity' => round($reversePower / 1000, 2)
        ];
        $tableData->push($row4);

        return $tableData;
    }

    public function render()
    {
        return view('livewire.configuration-data', [
            'availableYears' => $this->availableYears,
            'energyTableData' => $this->energyTableData,
            'energyTableDataTable2' => $this->energyTableDataTable2,
            'energyTableDataTable3' => $this->energyTableDataTable3,
            'energyTableDataTable4' => $this->energyTableDataTable4,
            'energyTableDataExport' => $this->energyTableDataExport,
            'energyTableDataImport' => $this->energyTableDataImport,
            'energyTableDataExportElectricity' => $this->energyTableDataExportElectricity
        ]);
    }
}
