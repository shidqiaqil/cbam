<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MasterPco;
use Livewire\Attributes\Url;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class PcoMaster extends Component
{
    use WithPagination;

    #[Url(as: 'tab')]
    public $activeTab = 'pco';

    public $search = '';
    public $yearFilter = '';
    public $perPage = 20;

    public $showDeleteModal = false;
    public $deleteYear = '';
    public $deleteMonth = '';

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

        $deletedCount = MasterPco::where('period_year', $this->deleteYear)
            ->whereRaw('LOWER(period_month) = ?', [$fullMonth])
            ->delete();


        session()->flash('message', "Berhasil hapus {$deletedCount} record(s) {$this->deleteYear} {$this->deleteMonth}.");
        session()->flash('message_type', 'success');
        $this->closeDeleteModal();
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedYearFilter()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    protected function paginateCollection($items, $perPage, $pageName = 'page')
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
        return MasterPco::distinct()
            ->orderByDesc('period_year')
            ->pluck('period_year');
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        $pcoData = $this->getPcoDataProperty();
        $years = $this->getYearsProperty();
        $months = $this->getMonthsProperty();

        return view('livewire.pco-master', [
            'pcoData' => $pcoData,
            'years' => $years,
            'months' => $months,
            'activeTab' => $this->activeTab
        ]);
    }

    public function getPcoDataProperty()
    {
        $rawData = MasterPco::query();

        if (!empty($this->search)) {
            $rawData->where(function ($q) {
                $q->where('plant', 'like', '%' . $this->search . '%')
                    ->orWhere('period_year', 'like', '%' . $this->search . '%')
                    ->orWhere('criteria', 'like', '%' . $this->search . '%')
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

        // Group by unique row identity (period_year, plant, criteria, unit)
        $grouped = $rawData->groupBy(function ($record) {
            return implode('|', [
                $record->period_year,
                $record->plant,
                $record->criteria,
                $record->unit,
            ]);
        });

        foreach ($grouped as $records) {
            $first = $records->first();

            $row = [
                'Plant' => $first->plant,
                'Year' => $first->period_year,
                'Criteria' => $first->criteria,
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
                str_contains(strtolower($row['Unit'] ?? ''), $search);
        });

        return $this->paginateCollection($filtered, $this->perPage);
    }
}
