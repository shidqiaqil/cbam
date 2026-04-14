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

    public $showDeleteModal = false;
    public $deleteYear = '';
    public $deleteMonth = '';

    public function openDeleteModal()
    {
        $this->showDeleteModal = true;
        $this->deleteYear = $this->yearFilter ?: ($this->getYearsProperty()->first() ?? '');
        $this->deleteMonth = '';
        $this->dispatch('open-delete-modal'); // align with BfMaster
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deleteYear = '';
        $this->deleteMonth = '';
        $this->dispatch('close-delete-modal'); // align with BfMaster
    }

    public function deleteCurrentFilterData()
    {
        if (empty($this->deleteYear) || empty($this->deleteMonth)) {
            session()->flash('message', 'Pilih tahun dan bulan terlebih dahulu.');
            session()->flash('message_type', 'danger');
            return;
        }

        $monthMap = [
            'Jan' => 'january',
            'Feb' => 'february',
            'Mar' => 'march',
            'Apr' => 'april',
            'May' => 'may',
            'Jun' => 'june',
            'Jul' => 'july',
            'Aug' => 'august',
            'Sep' => 'september',
            'Oct' => 'october',
            'Nov' => 'november',
            'Dec' => 'december',
        ];

        $fullMonth = strtolower($monthMap[$this->deleteMonth] ?? $this->deleteMonth);

        $deletedCount = 0;

        switch ($this->activeTab) {
            case 'data':
                $deletedCount = MasterEnergyData::where('period_year', $this->deleteYear)
                    ->whereRaw('LOWER(period_month) = ?', [$fullMonth])
                    ->delete();
                break;
            case 'sales':
                $deletedCount = MasterEnergySales::where('period_year', $this->deleteYear)
                    ->whereRaw('LOWER(period_month) = ?', [$fullMonth])
                    ->delete();
                break;
        }

        session()->flash('message', "Berhasil hapus {$deletedCount} record(s) {$this->deleteYear} {$this->deleteMonth}.");
        session()->flash('message_type', 'success');

        $this->closeDeleteModal();
        $this->resetPage("page_{$this->activeTab}");
    }

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
    public function getMonthsProperty()
    {
        return collect(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']);
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
        $months = $this->getMonthsProperty();

        return view('livewire.energy-master', [
            'energyData' => $energyData,
            'energySales' => $energySales,
            'years' => $years,
            'months' => $months,
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
                $record->plant_code,
                $record->plant_name,
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
                'PlantCode' => $first->plant_code ?? '',
                'PlantName' => $first->plant_name ?? '',
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
