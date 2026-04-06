<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MasterBf;
use App\Models\MasterBfPciCoal;
use App\Models\MasterBfQuality;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;

class BfMaster extends Component
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

    public function updatedSearch()
    {
        $this->resetPage('page_data');
        $this->resetPage('page_pci');
        $this->resetPage('page_quality');
    }

    public function updatedYearFilter()
    {
        $this->resetPage('page_data');
        $this->resetPage('page_pci');
        $this->resetPage('page_quality');
    }

    public function updatedPerPage()
    {
        $this->resetPage('page_data');
        $this->resetPage('page_pci');
        $this->resetPage('page_quality');
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
                $deletedCount = MasterBf::where('period_year', $this->deleteYear)
                    ->whereRaw('LOWER(period_month) = ?', [$fullMonth])
                    ->delete();
                break;
            case 'pci':
                $deletedCount = MasterBfPciCoal::where('period_year', $this->deleteYear)
                    ->whereRaw('LOWER(period_month) = ?', [$fullMonth])
                    ->delete();
                break;
            case 'quality':
                $deletedCount = MasterBfQuality::where('period_year', $this->deleteYear)
                    ->whereRaw('LOWER(period_month) = ?', [$fullMonth])
                    ->delete();
                break;
        }

        session()->flash('message', "Berhasil hapus {$deletedCount} record(s) {$this->deleteYear} {$this->deleteMonth}.");
        session()->flash('message_type', 'success');

        $this->closeDeleteModal();
        $this->resetPage("page_{$this->activeTab}");
    }

    public function openDeleteModal()
    {
        $this->showDeleteModal = true;
        $this->deleteYear = $this->yearFilter ?: ($this->getYearsProperty()->first() ?? '');
        $this->deleteMonth = '';
        $this->dispatch('open-delete-modal'); // tambah ini
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deleteYear = '';
        $this->deleteMonth = '';
        $this->dispatch('close-delete-modal'); // tambah ini
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
        $years = $years->merge(MasterBf::distinct()->orderByDesc('period_year')->pluck('period_year'));
        $years = $years->merge(MasterBfPciCoal::distinct()->orderByDesc('period_year')->pluck('period_year'));
        $years = $years->merge(MasterBfQuality::distinct()->orderByDesc('period_year')->pluck('period_year'));
        return $years->unique()->values()->sortDesc()->values();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        $bfData = $this->getBfDataProperty();
        $pciCoal = $this->getPciCoalProperty();
        $bfQuality = $this->getBfQualityProperty();
        $years = $this->getYearsProperty();
        $months = $this->getMonthsProperty();

        return view('livewire.bf-master', [
            'bfData' => $bfData,
            'pciCoal' => $pciCoal,
            'bfQuality' => $bfQuality,
            'years' => $years,
            'months' => $months,
            'activeTab' => $this->activeTab
        ]);
    }

    public function getBfDataProperty()
    {
        $rawData = MasterBf::query();

        if (!empty($this->search)) {
            $rawData->where(function ($q) {
                $q->where('plant', 'like', '%' . $this->search . '%')
                    ->orWhere('period_year', 'like', '%' . $this->search . '%')
                    ->orWhere('classification', 'like', '%' . $this->search . '%')
                    ->orWhere('sub_class', 'like', '%' . $this->search . '%')
                    ->orWhere('sub_subclass', 'like', '%' . $this->search . '%');
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
                $record->classification,
                $record->sub_class,
                $record->sub_subclass,
            ]);
        });

        foreach ($grouped as $records) {
            $first = $records->first();

            $row = [
                'Plant' => $first->plant,
                'Year' => $first->period_year,
                'Classification' => $first->classification,
                'Sub Class' => $first->sub_class,
                'Sub Subclass' => $first->sub_subclass,
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
                str_contains(strtolower($row['Classification'] ?? ''), $search) ||
                str_contains(strtolower($row['Sub Class'] ?? ''), $search) ||
                str_contains(strtolower($row['Sub Subclass'] ?? ''), $search);
        });

        return $this->paginateCollection($filtered, $this->perPage, 'page_data');
    }

    public function getPciCoalProperty()
    {
        $rawData = MasterBfPciCoal::query();

        if (!empty($this->search)) {
            $rawData->where(function ($q) {
                $q->where('plant', 'like', '%' . $this->search . '%')
                    ->orWhere('period_year', 'like', '%' . $this->search . '%')
                    ->orWhere('item', 'like', '%' . $this->search . '%')
                    ->orWhere('brand', 'like', '%' . $this->search . '%')
                    ->orWhere('sub_brand', 'like', '%' . $this->search . '%');
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
                $record->item,
                $record->brand,
                $record->sub_brand,
            ]);
        });

        foreach ($grouped as $records) {
            $first = $records->first();

            $row = [
                'Plant' => $first->plant,
                'Year' => $first->period_year,
                'Item' => $first->item,
                'Brand' => $first->brand,
                'Sub Brand' => $first->sub_brand,
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
                str_contains(strtolower($row['Item'] ?? ''), $search) ||
                str_contains(strtolower($row['Brand'] ?? ''), $search) ||
                str_contains(strtolower($row['Sub Brand'] ?? ''), $search);
        });

        return $this->paginateCollection($filtered, $this->perPage, 'page_pci');
    }

    public function getBfQualityProperty()
    {
        $rawData = MasterBfQuality::query();

        if (!empty($this->search)) {
            $rawData->where(function ($q) {
                $q->where('plant', 'like', '%' . $this->search . '%')
                    ->orWhere('period_year', 'like', '%' . $this->search . '%')
                    ->orWhere('classification', 'like', '%' . $this->search . '%')
                    ->orWhere('sub_class', 'like', '%' . $this->search . '%')
                    ->orWhere('sub_subclass', 'like', '%' . $this->search . '%');
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
                $record->classification,
                $record->sub_class,
                $record->sub_subclass,
            ]);
        });

        foreach ($grouped as $records) {
            $first = $records->first();

            $row = [
                'Plant' => $first->plant,
                'Year' => $first->period_year,
                'Classification' => $first->classification,
                'Sub Class' => $first->sub_class,
                'Sub Subclass' => $first->sub_subclass,
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
                str_contains(strtolower($row['Classification'] ?? ''), $search) ||
                str_contains(strtolower($row['Sub Class'] ?? ''), $search) ||
                str_contains(strtolower($row['Sub Subclass'] ?? ''), $search);
        });

        return $this->paginateCollection($filtered, $this->perPage, 'page_quality');
    }
}
