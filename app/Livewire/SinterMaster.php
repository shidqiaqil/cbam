<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MasterSinter;
use Livewire\Attributes\Url;
use Illuminate\Pagination\LengthAwarePaginator;

class SinterMaster extends Component
{
    use WithPagination;

    #[Url(as: 'tab')]
    public $activeTab = 'sinter';

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

        $deletedCount = MasterSinter::where('period_year', $this->deleteYear)
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
        return MasterSinter::distinct()
            ->orderByDesc('period_year')
            ->pluck('period_year');
    }

    public function render()
    {
        $sinterData = $this->getSinterDataProperty();
        $years = $this->getYearsProperty();
        $months = $this->getMonthsProperty();

        return view('livewire.sinter-master', [
            'sinterData' => $sinterData,
            'years' => $years,
            'months' => $months
        ]);
    }

    public function getSinterDataProperty()
    {
        $rawData = MasterSinter::query();

        if (!empty($this->search)) {
            $rawData->where(function ($q) {
                $q->where('plant', 'like', '%' . $this->search . '%')
                    ->orWhere('period_year', 'like', '%' . $this->search . '%')
                    ->orWhere('classification', 'like', '%' . $this->search . '%')
                    ->orWhere('sub_class', 'like', '%' . $this->search . '%');
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
            ]);
        });

        foreach ($grouped as $records) {
            $first = $records->first();

            $row = [
                'Plant' => $first->plant,
                'Year' => $first->period_year,
                'Classification' => $first->classification,
                'SubClass' => $first->sub_class,
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
                str_contains(strtolower($row['SubClass'] ?? ''), $search);
        });

        return $this->paginateCollection($filtered, $this->perPage);
    }
}
