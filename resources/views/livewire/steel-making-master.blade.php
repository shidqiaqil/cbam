<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Steel Making Master Data</h3>
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
                                <input id="steelmaking-search" wire:model.live.debounce.300ms="search" type="text"
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
                            <div class="btn-list ms-2">
                                <div class="dropdown">
                                    <button class="btn dropdown-toggle" data-bs-toggle="dropdown">Download</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#">CSV</a>
                                        <a class="dropdown-item" href="#">Excel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body border-0 p-0">
                        <ul class="nav nav-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab === 'submaterial' ? 'active' : '' }}"
                                    wire:click="setTab('submaterial')" href="#" style="cursor: pointer;">
                                    Submaterial
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab === 'scrap' ? 'active' : '' }}"
                                    wire:click="setTab('scrap')" href="#" style="cursor: pointer;">
                                    Scrap
                                </a>
                            </li>
                        </ul>

                        <div class="card-table">
                            @if($activeTab === 'submaterial')
                            <div class="table-responsive">
                                <table class="table table-vcenter table-selectable">
                                    <thead>
                                        <tr>
                                            <th class="w-1">No.</th>
                                            <th>Plant</th>
                                            <th>Year</th>
                                            <th>Classification</th>
                                            <th>Sub Class</th>
                                            <th>Unit</th>

                                            @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep',
                                            'Oct', 'Nov', 'Dec'] as $month)
                                            <th>{{ $month }}</th>
                                            @endforeach
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($submaterialData as $index => $row)
                                        @php
                                        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct',
                                        'Nov', 'Dec'];
                                        $total = 0;
                                        foreach($months as $m) {
                                        $total += $row[$m] ?? 0;
                                        }
                                        @endphp
                                        <tr>
                                            <td>{{ ($submaterialData->currentPage() - 1) * $submaterialData->perPage() +
                                                $index + 1 }}</td>
                                            <td class="fw-bold">{{ $row['Plant'] ?? '-' }}</td>
                                            <td>{{ $row['Year'] ?? '-' }}</td>
                                            <td>{{ $row['Classification'] ?? '-' }}</td>
                                            <td>{{ $row['SubClass'] ?? '-' }}</td>
                                            <td>{{ $row['Unit'] ?? '-' }}</td>

                                            @foreach($months as $month)
                                            <td class="text-end">{{ number_format($row[$month] ?? 0, 2) }}</td>
                                            @endforeach
                                            <td class="text-end fw-bold">{{ number_format($total, 2) }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="20" class="text-center py-4">No Submaterial data matching
                                                filters. Upload via Upload File.</td>
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
                                            {{ $submaterialData->perPage() }} records per page
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
                                            Showing {{ ($submaterialData->currentPage() - 1) *
                                            $submaterialData->perPage() + 1 }} to
                                            {{ min($submaterialData->total(), $submaterialData->currentPage() *
                                            $submaterialData->perPage()) }}
                                            of {{ $submaterialData->total() }} results
                                        </span>
                                        {{ $submaterialData->links('vendor.livewire.bootstrap') }}
                                    </div>
                                </div>
                            </div>
                            @elseif($activeTab === 'scrap')
                            <div class="table-responsive">
                                <table class="table table-vcenter table-selectable">
                                    <thead>
                                        <tr>
                                            <th class="w-1">No.</th>
                                            <th>Plant</th>
                                            <th>Year</th>
                                            <th>Category</th>
                                            <th>Sub Category</th>
                                            @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep',
                                            'Oct', 'Nov', 'Dec'] as $month)
                                            <th>{{ $month }}</th>
                                            @endforeach
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($scrapData as $index => $row)
                                        @php
                                        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct',
                                        'Nov', 'Dec'];
                                        $total = 0;
                                        foreach($months as $m) {
                                        $total += $row[$m] ?? 0;
                                        }
                                        @endphp
                                        <tr>
                                            <td>{{ ($scrapData->currentPage() - 1) * $scrapData->perPage() + $index + 1
                                                }}</td>
                                            <td class="fw-bold">{{ $row['Plant'] ?? '-' }}</td>
                                            <td>{{ $row['Year'] ?? '-' }}</td>
                                            <td>{{ $row['Category'] ?? '-' }}</td>
                                            <td>{{ $row['SubCategory'] ?? '-' }}</td>
                                            @foreach($months as $month)
                                            <td class="text-end">{{ number_format($row[$month] ?? 0, 2) }}</td>
                                            @endforeach
                                            <td class="text-end fw-bold">{{ number_format($total, 2) }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="19" class="text-center py-4">No Scrap data matching filters.
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
                                            {{ $scrapData->perPage() }} records per page
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
                                            Showing {{ ($scrapData->currentPage() - 1) * $scrapData->perPage() + 1 }} to
                                            {{ min($scrapData->total(), $scrapData->currentPage() *
                                            $scrapData->perPage()) }}
                                            of {{ $scrapData->total() }} results
                                        </span>
                                        {{ $scrapData->links('vendor.livewire.bootstrap') }}
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>