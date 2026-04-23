<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MasterPco;
use App\Models\MasterPcoCoil;
use App\Models\MasterPcoPlate;
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

        $modelClass = $this->getModelClass();
        $deletedCount = $modelClass::where('period_year', $this->deleteYear)
            ->whereRaw('LOWER(period_month) = ?', [$fullMonth])
            ->delete();

        $tabName = ucfirst(str_replace('_', ' ', $this->activeTab));
        session()->flash('message', "Berhasil hapus {$deletedCount} record(s) {$tabName} {$this->deleteYear} {$this->deleteMonth}.");
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

    private function getModelClass(): string
    {
        return match ($this->activeTab) {
            'pco_coil' => MasterPcoCoil::class,
            'pco_plate' => MasterPcoPlate::class,
            default => MasterPco::class,
        };
    }

    public function getYearsProperty()
    {
        $modelClass = $this->getModelClass();

        return $modelClass::distinct()
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
        $modelClass = $this->getModelClass();
        $rawData = $modelClass::query();

        if (!empty($this->search)) {
            $rawData->where(function ($q) {
                $q->where('plant', 'like', '%' . $this->search . '%')
                    ->orWhere('period_year', 'like', '%' . $this->search . '%');
                if ($this->activeTab === 'pco') {
                    $q->orWhere('criteria', 'like', '%' . $this->search . '%')
                        ->orWhere('unit', 'like', '%' . $this->search . '%');
                } else {
                    $q->orWhere('class', 'like', '%' . $this->search . '%');
                }
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

        // Group by unique row identity
        $groupFields = $this->activeTab === 'pco' ? ['period_year', 'plant', 'criteria', 'unit'] : ['period_year', 'plant', 'class'];
        $grouped = $rawData->groupBy(function ($record) use ($groupFields) {
            $fields = [];
            foreach ($groupFields as $field) {
                $fields[] = $record->{$field} ?? '';
            }
            return implode('|', $fields);
        });

        foreach ($grouped as $records) {
            $first = $records->first();

            $row = [
                'Plant' => $first->plant,
                'Year' => $first->period_year,
            ];
            if ($this->activeTab === 'pco') {
                $row['Criteria'] = $first->criteria;
                $row['Unit'] = $first->unit;
            } else {
                $row['Class'] = $first->class;
            }

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
                str_contains(strtolower($row['Criteria'] ?? $row['Class'] ?? ''), $search) ||
                ($this->activeTab === 'pco' ? str_contains(strtolower($row['Unit'] ?? ''), $search) : true);
        });

        return $this->paginateCollection($filtered, $this->perPage);
    }
}
