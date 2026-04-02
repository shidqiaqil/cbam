<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MasterSinter;
use Illuminate\Pagination\LengthAwarePaginator;

class SinterMaster extends Component
{
    use WithPagination;

    public $search = '';
    public $yearFilter = '';
    public $perPage = 20;

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

        return view('livewire.sinter-master', [
            'sinterData' => $sinterData,
            'years' => $years
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
