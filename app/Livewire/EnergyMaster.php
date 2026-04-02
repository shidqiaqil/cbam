<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MasterEnergyData;
use App\Models\MasterEnergySales;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Url;

class EnergyMaster extends Component
{
    use WithPagination;

    #[Url]
    public $activeTab = 'data';
    #[Url]
    public $search = '';
    #[Url]
    public $yearFilter = '';
    #[Url]
    public $perPage = 20;

    public function updatedSearch()
    {
        $this->resetPage('page_data');
        $this->resetPage('page_sales');
    }

    public function updatedYearFilter()
    {
        $this->resetPage('page_data');
        $this->resetPage('page_sales');
    }

    public function updatedPerPage()
    {
        $this->resetPage('page_data');
        $this->resetPage('page_sales');
    }

    protected function paginateCollection($items, $perPage, $pageName)
    {
        $page = LengthAwarePaginator::resolveCurrentPage($pageName);
        $results = $items->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $results,
            $items->count(),
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => $pageName
            ]
        );
    }

    public function getYearsProperty()
    {
        $years = collect();
        $years = $years->merge(MasterEnergyData::distinct()->orderByDesc('period_year')->pluck('period_year'));
        $years = $years->merge(MasterEnergySales::distinct()->orderByDesc('period_year')->pluck('period_year'));
        return $years->unique()->values()->sortDesc()->values();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        $energyData = $this->getEnergyDataProperty();
        $energySales = $this->getEnergySalesProperty();
        $years = $this->getYearsProperty();

        return view('livewire.energy-master', [
            'energyData' => $energyData,
            'energySales' => $energySales,
            'years' => $years,
            'activeTab' => $this->activeTab
        ]);
    }

    public function getEnergyDataProperty()
    {
        $rawData = MasterEnergyData::query();

        if (!empty($this->search)) {
            $rawData->where(function ($q) {
                $q->where('plant', 'like', '%' . $this->search . '%')
                    ->orWhere('period_year', 'like', '%' . $this->search . '%')
                    ->orWhere('criteria', 'like', '%' . $this->search . '%')
                    ->orWhere('energy_name', 'like', '%' . $this->search . '%')
                    ->orWhere('unit', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->yearFilter)) {
            $rawData->where('period_year', $this->yearFilter);
        }

        $rawData = $rawData->get();

        $pivoted = [];

        $monthMap = [
            'january' => 'Jan',
            'february' => 'Feb',
            'march' => 'Mar',
            'april' => 'Apr',
            'may' => 'May',
            'june' => 'Jun',
            'july' => 'Jul',
            'august' => 'Aug',
            'september' => 'Sep',
            'october' => 'Oct',
            'november' => 'Nov',
            'december' => 'Dec',
        ];

        $grouped = $rawData->groupBy(function ($record) {
            return implode('|', [
                $record->period_year,
                $record->plant,
                $record->criteria,
                $record->energy_name,
                $record->unit,
            ]);
        });

        foreach ($grouped as $records) {
            $first = $records->first();

            $row = [
                'Plant' => $first->plant,
                'Year' => $first->period_year,
                'Criteria' => $first->criteria,
                'EnergyName' => $first->energy_name,
                'Unit' => $first->unit,
            ];

            foreach (array_values($monthMap) as $abbr) {
                $row[$abbr] = 0;
            }

            foreach ($records as $record) {
                $monthKey = strtolower($record->period_month);
                if (isset($monthMap[$monthKey])) {
                    $row[$monthMap[$monthKey]] = $record->quantity;
                }
            }

            $pivoted[] = $row;
        }

        $filtered = collect($pivoted)->filter(function ($row) {
            $search = strtolower($this->search);
            return empty($search) ||
                str_contains(strtolower($row['Plant'] ?? ''), $search) ||
                str_contains(strtolower($row['Year'] ?? ''), $search) ||
                str_contains(strtolower($row['Criteria'] ?? ''), $search) ||
                str_contains(strtolower($row['EnergyName'] ?? ''), $search) ||
                str_contains(strtolower($row['Unit'] ?? ''), $search);
        });

        return $this->paginateCollection($filtered, $this->perPage, 'page_data');
    }

    public function getEnergySalesProperty()
    {
        $rawData = MasterEnergySales::query();

        if (!empty($this->search)) {
            $rawData->where(function ($q) {
                $q->where('plant', 'like', '%' . $this->search . '%')
                    ->orWhere('period_year', 'like', '%' . $this->search . '%')
                    ->orWhere('en_name', 'like', '%' . $this->search . '%')
                    ->orWhere('use_product', 'like', '%' . $this->search . '%')
                    ->orWhere('uom', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->yearFilter)) {
            $rawData->where('period_year', $this->yearFilter);
        }

        $rawData = $rawData->get();

        $pivoted = [];

        $monthMap = [
            'january' => 'Jan',
            'february' => 'Feb',
            'march' => 'Mar',
            'april' => 'Apr',
            'may' => 'May',
            'june' => 'Jun',
            'july' => 'Jul',
            'august' => 'Aug',
            'september' => 'Sep',
            'october' => 'Oct',
            'november' => 'Nov',
            'december' => 'Dec',
        ];

        $grouped = $rawData->groupBy(function ($record) {
            return implode('|', [
                $record->period_year,
                $record->plant,
                $record->en_name,
                $record->use_product,
                $record->uom,
            ]);
        });

        foreach ($grouped as $records) {
            $first = $records->first();

            $row = [
                'Plant' => $first->plant,
                'Year' => $first->period_year,
                'Month' => '-',
                'EnName' => $first->en_name,
                'UseProduct' => $first->use_product,
                'UOM' => $first->uom,
            ];

            foreach (array_values($monthMap) as $abbr) {
                $row[$abbr] = 0;
            }

            foreach ($records as $record) {
                $monthKey = strtolower($record->period_month);
                if (isset($monthMap[$monthKey])) {
                    $row[$monthMap[$monthKey]] = $record->quantity;
                }
            }

            $pivoted[] = $row;
        }

        $filtered = collect($pivoted)->filter(function ($row) {
            $search = strtolower($this->search);
            return empty($search) ||
                str_contains(strtolower($row['Plant'] ?? ''), $search) ||
                str_contains(strtolower($row['Year'] ?? ''), $search) ||
                str_contains(strtolower($row['EnName'] ?? ''), $search) ||
                str_contains(strtolower($row['UseProduct'] ?? ''), $search) ||
                str_contains(strtolower($row['UOM'] ?? ''), $search);
        });

        return $this->paginateCollection($filtered, $this->perPage, 'page_sales');
    }
}
