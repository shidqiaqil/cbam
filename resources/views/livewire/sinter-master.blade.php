<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Sinter Master Data</h3>
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
                    @if(session('message'))
                    <div class="alert alert-{{ session('message_type', 'success') }} alert-dismissible fade show m-3"
                        role="alert">
                        {{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

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
                                        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep',
                                        'Oct',
                                        'Nov', 'Dec'];
                                        $total = 0;
                                        foreach($months as $m) {
                                        $total += $row[$m] ?? 0;
                                        }
                                        @endphp
                                        <tr>
                                            <td>{{ ($sinterData->currentPage() - 1) * $sinterData->perPage() +
                                                $index +
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
                                            <td colspan="18" class="text-center py-4">No Sinter data matching
                                                filters.
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
                                            Showing {{ ($sinterData->currentPage() - 1) * $sinterData->perPage() + 1
                                            }}
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