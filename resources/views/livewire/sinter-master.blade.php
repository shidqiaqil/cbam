<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Sinter Master Data</h3>
                        <div class="ms-auto d-flex">
                            <div class="input-group input-group-flat me-2">
                                <span class="input-group-text">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="icon">
                                        <circle cx="10" cy="10" r="7"></circle>
                                        <line x1="21" y1="21" x2="15" y2="15"></line>
                                    </svg>
                                </span>
                                <input id="sinter-search" wire:model.live.debounce.300ms="search" type="text"
                                    class="form-control" placeholder="Search..." />
                            </div>
                            <div class="input-group input-group-flat">
                                <select wire:model.live="yearFilter" class="form-select">
                                    <option value="">All Years</option>
                                    @foreach($years as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- <div class="btn-list ms-2">
                                <div class="dropdown">
                                    <button class="btn dropdown-toggle" data-bs-toggle="dropdown">Download</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#">CSV</a>
                                        <a class="dropdown-item" href="#">Excel</a>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                    <div class="card-table">
                        <div id="sinter-table">
                            <div class="table-responsive">
                                <table class="table table-vcenter table-selectable">
                                    <thead>
                                        <tr>
                                            <th class="w-1">No.</th>
                                            <th>Plant</th>
                                            <th>Year</th>
                                            <th>Classification</th>
                                            <th>Sub Class</th>
                                            @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep',
                                            'Oct', 'Nov', 'Dec'] as $month)
                                            <th>{{ $month }}</th>
                                            @endforeach
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($sinterData as $index => $row)
                                        @php
                                        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct',
                                        'Nov', 'Dec'];
                                        $total = 0;
                                        foreach($months as $m) {
                                        $total += $row[$m] ?? 0;
                                        }
                                        @endphp
                                        <tr>
                                            <td>{{ ($sinterData->currentPage() - 1) * $sinterData->perPage() + $index +
                                                1 }}</td>
                                            <td class="fw-bold">{{ $row['Plant'] ?? '-' }}</td>
                                            <td>{{ $row['Year'] }}</td>
                                            <td>{{ $row['Classification'] }}</td>
                                            <td>{{ $row['SubClass'] }}</td>
                                            @foreach($months as $month)
                                            <td class="text-end">{{ number_format($row[$month] ?? 0, 2) }}</td>
                                            @endforeach
                                            <td class="text-end fw-bold">{{ number_format($total, 2) }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="18" class="text-center py-4">No Sinter data matching filters.
                                                Upload via Upload File.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex">
                                    <div class="dropdown">
                                        <button class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                            data-bs-auto-close="outside">
                                            {{ $sinterData->perPage() }} records per page
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#"
                                                wire:click.prevent="$set('perPage', 10)">10</a>
                                            <a class="dropdown-item" href="#"
                                                wire:click.prevent="$set('perPage', 20)">20</a>
                                            <a class="dropdown-item" href="#"
                                                wire:click.prevent="$set('perPage', 50)">50</a>
                                            <a class="dropdown-item" href="#"
                                                wire:click.prevent="$set('perPage', 100)">100</a>
                                        </div>
                                    </div>
                                    <div class="ms-auto d-flex align-items-center">
                                        <span class="text-muted me-3">
                                            Showing {{ ($sinterData->currentPage() - 1) * $sinterData->perPage() + 1 }}
                                            to
                                            {{ min($sinterData->total(), $sinterData->currentPage() *
                                            $sinterData->perPage()) }}
                                            of {{ $sinterData->total() }} results
                                        </span>
                                        {{ $sinterData->links('vendor.livewire.bootstrap') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>