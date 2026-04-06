<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">BF Master Data</h3>

                        <div class="ms-auto d-flex align-items-center gap-2">
                            <div class="input-group input-group-flat" style="min-width: 200px;">
                                <span class="input-group-text">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="icon">
                                        <circle cx="10" cy="10" r="7"></circle>
                                        <line x1="21" y1="21" x2="15" y2="15"></line>
                                    </svg>
                                </span>
                                <input id="bf-search" wire:model.live.debounce.300ms="search" type="text"
                                    class="form-control" placeholder="Search..." />
                            </div>
                            <div class="input-group input-group-flat" style="min-width: 140px;">
                                <select wire:model.live="yearFilter" class="form-select">
                                    <option value="">All Years</option>
                                    @foreach($years as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button wire:click="openDeleteModal" class="btn btn-danger"
                                style="white-space: nowrap; min-width: 150px;">
                                <i class="ti ti-trash"></i> Delete Period
                            </button>
                        </div>

                        <!-- Delete Modal -->
                        <div class="modal fade" id="deleteModal" tabindex="-1" wire:model="showDeleteModal">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Hapus Data Periode</h5>
                                        <button type="button" class="btn-close" wire:click="closeDeleteModal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Tahun:</label>
                                            <select wire:model="deleteYear" class="form-select">
                                                <option value="">Pilih Tahun</option>
                                                @foreach($years as $year)
                                                <option value="{{ $year }}">{{ $year }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Bulan:</label>
                                            <select wire:model="deleteMonth" class="form-select">
                                                <option value="">Pilih Bulan</option>
                                                @foreach($months as $month)
                                                <option value="{{ $month }}">{{ $month }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="alert alert-warning">
                                            Ini akan menghapus SEMUA data untuk periode yang dipilih di tab aktif ({{
                                            ucfirst(str_replace('_', ' ', $activeTab)) }}). Tindakan ini tidak dapat
                                            dibatalkan!
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            wire:click="closeDeleteModal">Batal</button>
                                        <button type="button" class="btn btn-danger"
                                            wire:click="deleteCurrentFilterData"
                                            wire:confirm="Yakin hapus data {{ $deleteYear }} {{ $deleteMonth }}?">
                                            Hapus Data
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body border-0 p-0">
                        @if(session('message'))
                        <div class="alert alert-{{ session('message_type', 'success') }} alert-dismissible fade show m-3"
                            role="alert">
                            {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endif

                        <ul class="nav nav-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab === 'data' ? 'active' : '' }}"
                                    wire:click="setTab('data')" href="#" style="cursor: pointer;">
                                    BF Data
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab === 'pci' ? 'active' : '' }}"
                                    wire:click="setTab('pci')" href="#" style="cursor: pointer;">
                                    PCI Coal
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $activeTab === 'quality' ? 'active' : '' }}"
                                    wire:click="setTab('quality')" href="#" style="cursor: pointer;">
                                    Quality
                                </a>
                            </li>
                        </ul>

                        <div class="card-table">
                            @if($activeTab === 'data')
                            <div id="bf-data-table">
                                <div class="table-responsive">
                                    <table class="table table-vcenter table-selectable">
                                        <thead>
                                            <tr>
                                                <th class="w-1">No.</th>
                                                <th>Plant</th>
                                                <th>Year</th>
                                                <th>Classification</th>
                                                <th>Sub Class</th>
                                                <th>Sub Subclass</th>
                                                @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']
                                                as $m)
                                                <th>{{ $m }}</th>
                                                @endforeach
                                                <th class="text-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($bfData as $index => $row)
                                            @php
                                            $months =
                                            ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                                            $total = 0;
                                            foreach($months as $m) {
                                            $total += $row[$m] ?? 0;
                                            }
                                            @endphp
                                            <tr>
                                                <td>{{ ($bfData->currentPage() - 1) * $bfData->perPage() + $index + 1 }}
                                                </td>
                                                <td class="fw-bold">{{ $row['Plant'] ?? '-' }}</td>
                                                <td>{{ $row['Year'] }}</td>
                                                <td>{{ $row['Classification'] }}</td>
                                                <td>{{ $row['Sub Class'] }}</td>
                                                <td>{{ $row['Sub Subclass'] }}</td>
                                                @foreach($months as $m)
                                                <td class="text-end">{{ number_format($row[$m] ?? 0, 2) }}</td>
                                                @endforeach
                                                <td class="text-end fw-bold">{{ number_format($total, 2) }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="18" class="text-center py-4">No BF Data found matching
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
                                                {{ $bfData->perPage() }} records per page
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
                                                Showing {{ ($bfData->currentPage() - 1) * $bfData->perPage() + 1 }} to
                                                {{ min($bfData->total(), $bfData->currentPage() * $bfData->perPage()) }}
                                                of {{ $bfData->total() }} results
                                            </span>
                                            {{ $bfData->links('vendor.livewire.bootstrap') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @elseif($activeTab === 'pci')
                            <div id="bf-pci-table">
                                <div class="table-responsive">
                                    <table class="table table-vcenter table-selectable">
                                        <thead>
                                            <tr>
                                                <th class="w-1">No.</th>
                                                <th>Plant</th>
                                                <th>Year</th>
                                                <th>Item</th>
                                                <th>Brand</th>
                                                <th>Sub Brand</th>
                                                @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']
                                                as $m)
                                                <th>{{ $m }}</th>
                                                @endforeach
                                                <th class="text-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($pciCoal as $index => $row)
                                            @php
                                            $months =
                                            ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                                            $total = 0;
                                            foreach($months as $m) {
                                            $total += $row[$m] ?? 0;
                                            }
                                            @endphp
                                            <tr>
                                                <td>{{ ($pciCoal->currentPage() - 1) * $pciCoal->perPage() + $index + 1
                                                    }}</td>
                                                <td class="fw-bold">{{ $row['Plant'] ?? '-' }}</td>
                                                <td>{{ $row['Year'] }}</td>
                                                <td>{{ $row['Item'] }}</td>
                                                <td>{{ $row['Brand'] }}</td>
                                                <td>{{ $row['Sub Brand'] }}</td>
                                                @foreach($months as $m)
                                                <td class="text-end">{{ number_format($row[$m] ?? 0, 2) }}</td>
                                                @endforeach
                                                <td class="text-end fw-bold">{{ number_format($total, 2) }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="18" class="text-center py-4">No PCI Coal found matching
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
                                                {{ $pciCoal->perPage() }} records per page
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
                                                Showing {{ ($pciCoal->currentPage() - 1) * $pciCoal->perPage() + 1 }} to
                                                {{ min($pciCoal->total(), $pciCoal->currentPage() * $pciCoal->perPage())
                                                }}
                                                of {{ $pciCoal->total() }} results
                                            </span>
                                            {{ $pciCoal->links('vendor.livewire.bootstrap') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @elseif($activeTab === 'quality')
                            <div id="bf-quality-table">
                                <div class="table-responsive">
                                    <table class="table table-vcenter table-selectable">
                                        <thead>
                                            <tr>
                                                <th class="w-1">No.</th>
                                                <th>Plant</th>
                                                <th>Year</th>
                                                <th>Classification</th>
                                                <th>Sub Class</th>
                                                <th>Sub Subclass</th>
                                                @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']
                                                as $m)
                                                <th>{{ $m }}</th>
                                                @endforeach
                                                <th class="text-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($bfQuality as $index => $row)
                                            @php
                                            $months =
                                            ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                                            $total = 0;
                                            foreach($months as $m) {
                                            $total += $row[$m] ?? 0;
                                            }
                                            @endphp
                                            <tr>
                                                <td>{{ ($bfQuality->currentPage() - 1) * $bfQuality->perPage() + $index
                                                    + 1 }}</td>
                                                <td class="fw-bold">{{ $row['Plant'] ?? '-' }}</td>
                                                <td>{{ $row['Year'] }}</td>
                                                <td>{{ $row['Classification'] }}</td>
                                                <td>{{ $row['Sub Class'] }}</td>
                                                <td>{{ $row['Sub Subclass'] }}</td>
                                                @foreach($months as $m)
                                                <td class="text-end">{{ number_format($row[$m] ?? 0, 2) }}</td>
                                                @endforeach
                                                <td class="text-end fw-bold">{{ number_format($total, 2) }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="18" class="text-center py-4">No BF Quality found matching
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
                                                {{ $bfQuality->perPage() }} records per page
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
                                                Showing {{ ($bfQuality->currentPage() - 1) * $bfQuality->perPage() + 1
                                                }} to
                                                {{ min($bfQuality->total(), $bfQuality->currentPage() *
                                                $bfQuality->perPage()) }}
                                                of {{ $bfQuality->total() }} results
                                            </span>
                                            {{ $bfQuality->links('vendor.livewire.bootstrap') }}
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
    <script>
        document.addEventListener('livewire:initialized', () => {
        Livewire.on('open-delete-modal', () => {
            const oldBackdrop = document.getElementById('delete-modal-backdrop');
            if (oldBackdrop) oldBackdrop.remove();

            const modalEl = document.getElementById('deleteModal');
            modalEl.classList.add('show');
            modalEl.style.display = 'block';
            document.body.classList.add('modal-open');

            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.id = 'delete-modal-backdrop';
            document.body.appendChild(backdrop);
        });

        Livewire.on('close-delete-modal', () => {
            const modalEl = document.getElementById('deleteModal');
            modalEl.classList.remove('show');
            modalEl.style.display = 'none';
            document.body.classList.remove('modal-open');

            const backdrop = document.getElementById('delete-modal-backdrop');
            if (backdrop) backdrop.remove();
        });

        // tambah ini
        Livewire.on('notify', (data) => {
            console.log('notify fired:', data); // cek dulu di console
        });
    });
    </script>
</div>