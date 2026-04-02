<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Energy Master Data</h3>
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
                                <input id="energy-search" wire:model.live.debounce.300ms="search" type="text"
                                    class="form-control" placeholder="Search..." />
                            </div>
                            <div class="input-group input-group-flat me-2">
                                <select wire:model.live="yearFilter" class="form-select">
                                    <option value="">All Years</option>
                                    @foreach($years as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- <div class="btn-list">
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

                    <div class="card-body border-0 p-0">
                        <ul class="nav nav-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab === 'data' ? 'active' : '' }}"
                                    wire:click="setTab('data')" href="#" style="cursor: pointer;">
                                    Energy Data
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab === 'sales' ? 'active' : '' }}"
                                    wire:click="setTab('sales')" href="#" style="cursor: pointer;">
                                    Energy Sales
                                </a>
                            </li>
                        </ul>

                        <div class="card-table">
                            @if($activeTab === 'data')
                            <div id="energy-data-table">
                                <div class="table-responsive">
                                    <table class="table table-vcenter table-selectable">
                                        <thead>
                                            <tr>
                                                <th class="w-1">No.</th>
                                                <th>Plant</th>
                                                <th>Year</th>
                                                <th>Criteria</th>
                                                <th>Energy Name</th>
                                                <th>Unit</th>
                                                @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']
                                                as $m)
                                                <th>{{ $m }}</th>
                                                @endforeach
                                                <th class="text-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($energyData as $index => $row)
                                            @php
                                            $months =
                                            ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                                            $total = 0;
                                            foreach($months as $m) {
                                            $total += $row[$m] ?? 0;
                                            }
                                            @endphp
                                            <tr>
                                                <td>{{ ($energyData->currentPage() - 1) * $energyData->perPage() +
                                                    $index + 1 }}</td>
                                                <td class="fw-bold">{{ $row['Plant'] ?? '-' }}</td>
                                                <td>{{ $row['Year'] }}</td>
                                                <td>{{ $row['Criteria'] }}</td>
                                                <td>{{ $row['EnergyName'] }}</td>
                                                <td>{{ $row['Unit'] }}</td>
                                                @foreach($months as $m)
                                                <td class="text-end">{{ number_format($row[$m] ?? 0, 2) }}</td>
                                                @endforeach
                                                <td class="text-end fw-bold">{{ number_format($total, 2) }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="19" class="text-center py-4">No Energy Data found matching
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
                                                {{ $energyData->perPage() }} records per page
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
                                                Showing {{ ($energyData->currentPage() - 1) * $energyData->perPage() + 1
                                                }} to
                                                {{ min($energyData->total(), $energyData->currentPage() *
                                                $energyData->perPage()) }}
                                                of {{ $energyData->total() }} results
                                            </span>
                                            {{ $energySales->links('vendor.livewire.bootstrap') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @elseif($activeTab === 'sales')
                            <div id="energy-sales-table">
                                <div class="table-responsive">
                                    <table class="table table-vcenter table-selectable">
                                        <thead>
                                            <tr>
                                                <th class="w-1">No.</th>
                                                <th>Plant</th>
                                                <th>Year</th>

                                                <th>En Name</th>
                                                <th>Use Product</th>
                                                <th>UOM</th>
                                                @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']
                                                as $m)
                                                <th>{{ $m }}</th>
                                                @endforeach
                                                <th class="text-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($energySales as $index => $row)
                                            @php
                                            $months =
                                            ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                                            $total = 0;
                                            foreach($months as $m) {
                                            $total += $row[$m] ?? 0;
                                            }
                                            @endphp
                                            <tr>
                                                <td>{{ ($energySales->currentPage() - 1) * $energySales->perPage() +
                                                    $index + 1 }}</td>
                                                <td class="fw-bold">{{ $row['Plant'] ?? '-' }}</td>
                                                <td>{{ $row['Year'] }}</td>

                                                <td>{{ $row['EnName'] }}</td>
                                                <td>{{ $row['UseProduct'] }}</td>
                                                <td>{{ $row['UOM'] }}</td>
                                                @foreach($months as $m)
                                                <td class="text-end">{{ number_format($row[$m] ?? 0, 2) }}</td>
                                                @endforeach
                                                <td class="text-end fw-bold">{{ number_format($total, 2) }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="20" class="text-center py-4">No Energy Sales found matching
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
                                                {{ $energySales->perPage() }} records per page
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
                                            {{ $energySales->links('vendor.livewire.bootstrap') }}
                                        </div>
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