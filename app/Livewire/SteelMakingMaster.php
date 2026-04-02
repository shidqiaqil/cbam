<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MasterSmpSubmaterial;
use App\Models\MasterSmpScrap;
use Illuminate\Pagination\LengthAwarePaginator;

class SteelMakingMaster extends Component
{
    use WithPagination;

    public $activeTab = 'submaterial';
    public $search = '';
    public $yearFilter = '';
    public $perPage = 20;

    public function updatedSearch()
    {
        $this->resetPage('page_submaterial');
        $this->resetPage('page_scrap');
    }

    public function updatedYearFilter()
    {
        $this->resetPage('page_submaterial');
        $this->resetPage('page_scrap');
    }

    public function updatedPerPage()
    {
        $this->resetPage('page_submaterial');
        $this->resetPage('page_scrap');
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
        $years = $years->merge(MasterSmpSubmaterial::distinct()->orderByDesc('period_year')->pluck('period_year'));
        $years = $years->merge(MasterSmpScrap::distinct()->orderByDesc('period_year')->pluck('period_year'));
        return $years->unique()->values()->sortDesc()->values();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        $submaterialData = $this->getSubmaterialDataProperty();
        $scrapData = $this->getScrapDataProperty();
        $years = $this->getYearsProperty();

        return view('livewire.steel-making-master', [
            'submaterialData' => $submaterialData,
            'scrapData' => $scrapData,
            'years' => $years,
            'activeTab' => $this->activeTab
        ]);
    }

    public function getSubmaterialDataProperty()
    {
        $rawData = MasterSmpSubmaterial::query();

        if (!empty($this->search)) {
            $rawData->where(function ($q) {
                $q->where('plant', 'like', '%' . $this->search . '%')
                    ->orWhere('period_year', 'like', '%' . $this->search . '%')
                    ->orWhere('classification', 'like', '%' . $this->search . '%')
                    ->orWhere('sub_class', 'like', '%' . $this->search . '%')
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
                $record->classification,
                $record->sub_class,
                $record->unit,
            ]);
        });

        foreach ($grouped as $records) {
            $first = $records->first();

            $row = [
                'Plant' => $first->plant,
                'Year' => $first->period_year,
                'Classification' => $first->classification,
                'SubClass' => $first->sub_class,
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
                str_contains(strtolower($row['Classification'] ?? ''), $search) ||
                str_contains(strtolower($row['SubClass'] ?? ''), $search) ||
                str_contains(strtolower($row['Unit'] ?? ''), $search);
        });

        return $this->paginateCollection($filtered, $this->perPage, 'page_submaterial');
    }

    public function getScrapDataProperty()
    {
        $rawData = MasterSmpScrap::query();

        if (!empty($this->search)) {
            $rawData->where(function ($q) {
                $q->where('plant', 'like', '%' . $this->search . '%')
                    ->orWhere('period_year', 'like', '%' . $this->search . '%')
                    ->orWhere('category', 'like', '%' . $this->search . '%')
                    ->orWhere('sub_category', 'like', '%' . $this->search . '%');
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

        // group by category / sub_category / unit so we get one pivot row per scrap category
        $grouped = $rawData->groupBy(function ($record) {
            return implode('|', [
                $record->period_year,
                $record->plant,
                $record->category,
                $record->sub_category,
                $record->unit,
            ]);
        });

        foreach ($grouped as $records) {
            $first = $records->first();

            $row = [
                'Plant' => $first->plant,
                'Year' => $first->period_year,
                'Category' => $first->category,
                'SubCategory' => $first->sub_category,
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
                str_contains(strtolower($row['Category'] ?? ''), $search) ||
                str_contains(strtolower($row['SubCategory'] ?? ''), $search) ||
                str_contains(strtolower($row['Unit'] ?? ''), $search);
        });

        return $this->paginateCollection($filtered, $this->perPage, 'page_scrap');
    }
}
