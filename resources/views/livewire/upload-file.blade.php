<div class="page-wrapper">
    <!-- BEGIN PAGE HEADER -->
    <div class="page-header d-print-none" aria-label="Page header">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Upload File Form</h2>
                </div>
            </div>
        </div>
    </div>
    <!-- END PAGE HEADER -->
    <!-- BEGIN PAGE BODY -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards row-cols-1 row-cols-md-1">
                <div class="col-12">
                    <div class="row row-cards">

                        <div class="col-12">


                            <!-- Upload Form -->
                            <form class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Upload File</h3>
                                </div>
                                <div class="card-body">
                                    @if (session()->has('message'))
                                    <div class="alert alert-success">
                                        {{ session('message') }}
                                    </div>
                                    @endif

                                    <div class="mb-3 row">
                                        <label class="col-3 col-form-label required">Plant</label>
                                        <div class="col">
                                            <select wire:model="plant" class="form-select">
                                                <option value="">Choose Plant</option>
                                                <option value="blast furnace">Blast Furnace</option>
                                                <option value="energy">Energy</option>
                                                <option value="pco">PCO</option>
                                                <option value="sinter">Sinter</option>
                                                <option value="steel making">Steel Making</option>
                                                <option value="byproduct">Byproduct</option>

                                            </select>
                                            @error('plant') <div class="text-danger">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="mb-3 row">
                                        <label class="col-3 col-form-label required">Month</label>
                                        <div class="col">
                                            <select wire:model="month" class="form-select">
                                                <option value="">Choose Month</option>
                                                <option value="january">January</option>
                                                <option value="february">February</option>
                                                <option value="march">March</option>
                                                <option value="april">April</option>
                                                <option value="may">May</option>
                                                <option value="june">June</option>
                                                <option value="july">July</option>
                                                <option value="august">August</option>
                                                <option value="september">September</option>
                                                <option value="october">October</option>
                                                <option value="november">November</option>
                                                <option value="december">December</option>
                                            </select>
                                            @error('month') <div class="text-danger">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="mb-3 row">
                                        <label class="col-3 col-form-label required">Year</label>
                                        <div class="col">
                                            <select wire:model="year" class="form-select">
                                                <option value="">Choose Year</option>
                                                <!-- Tahun Lalu -->
                                                <option value="{{ date('Y') - 1 }}">{{ date('Y') - 1 }}
                                                </option>
                                                <!-- Tahun Ini -->
                                                <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                                            </select>
                                            @error('year') <div class="text-danger">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="mb-3 row">
                                        <label class="col-3 col-form-label required">File</label>
                                        <div class="col">
                                            <input type="file" wire:model="file" class="form-control"
                                                accept=".xlsx,.xls,.csv" />
                                            <small class="form-hint">Upload Excel or CSV file (max 2MB)</small>
                                            @error('file') <div class="text-danger">{{ $message }}</div> @enderror
                                        </div>
                                    </div>



                                    {{-- Progress bar --}}
                                    <div wire:loading wire:target="submit" class="mb-3">
                                        <div class="progress mb-1" style="height: 8px;">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated w-100 bg-primary"
                                                role="progressbar">
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                            Sedang memproses file, mohon tunggu...
                                        </small>
                                    </div>
                                    <a href="#" wire:click.prevent="openTemplateModal">
                                        <i class="ti ti-download me-2"></i>
                                        Download Template Files
                                    </a>

                                    @if($showTemplateModal)
                                    <div class="modal fade show d-block" tabindex="-1" wire:ignore.self>
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">

                                                {{-- Header --}}
                                                <div class="modal-header border-bottom px-4 py-3">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="rounded-3 p-2 bg-primary bg-opacity-10 d-flex">
                                                            <i class="ti ti-files fs-5 text-primary"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0 fw-semibold">Template Plant</h6>
                                                            <small class="text-muted">Pilih template sesuai
                                                                kebutuhan</small>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn-close"
                                                        wire:click="closeTemplateModal"></button>
                                                </div>

                                                {{-- Body --}}
                                                <div class="modal-body px-4 py-3">
                                                    @if (session()->has('error'))
                                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                                    @endif

                                                    <div class="row g-3">
                                                        @foreach ([
                                                        ['key' => 'blast furnace', 'label' => 'Blast Furnace', 'file' =>
                                                        'blast_furnace.xlsx', 'icon' => 'ti-fire', 'color' =>
                                                        'primary'],
                                                        ['key' => 'energy', 'label' => 'Energy', 'file' =>
                                                        'energy.xlsx', 'icon' => 'ti-bolt', 'color' => 'success'],
                                                        ['key' => 'pco', 'label' => 'PCO', 'file' => 'pco.xlsx', 'icon'
                                                        => 'ti-settings', 'color' => 'danger'],
                                                        ['key' => 'sinter', 'label' => 'Sinter', 'file' =>
                                                        'sinter.xlsx', 'icon' => 'ti-layers', 'color' => 'warning'],
                                                        ['key' => 'steel making', 'label' => 'Steel Making', 'file' =>
                                                        'steel_making.xlsx', 'icon' => 'ti-hammer', 'color' => 'info'],
                                                        ['key' => 'byproduct', 'label' => 'Byproduct', 'file' =>
                                                        'byproduct.xlsx', 'icon' => 'ti-recycle', 'color' =>
                                                        'secondary'],
                                                        ] as $tpl)
                                                        <div class="col-md-4">
                                                            <a href="#"
                                                                wire:click.prevent="downloadTemplate('{{ $tpl['key'] }}')"
                                                                class="text-decoration-none d-block h-100 template-card">
                                                                <div
                                                                    class="card border h-100 text-center p-3 rounded-3 template-card-inner">
                                                                    <div class="rounded-3 p-2 mb-2 mx-auto d-flex align-items-center justify-content-center
                                            bg-{{ $tpl['color'] }} bg-opacity-10" style="width:48px;height:48px;">
                                                                        <i
                                                                            class="ti {{ $tpl['icon'] }} fs-4 text-{{ $tpl['color'] }}"></i>
                                                                    </div>
                                                                    <h6 class="mb-1 fw-semibold small">{{ $tpl['label']
                                                                        }}</h6>
                                                                    <small class="text-muted" style="font-size:11px;">{{
                                                                        $tpl['file'] }}</small>
                                                                    <div class="mt-2 d-flex align-items-center justify-content-center gap-1 text-primary"
                                                                        style="font-size:12px;">
                                                                        <i class="ti ti-download"
                                                                            style="font-size:13px;"></i>
                                                                        Unduh
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                {{-- Footer --}}
                                                <div
                                                    class="modal-footer border-top px-4 py-2 d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">6 template tersedia</small>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-secondary rounded-3"
                                                        wire:click="closeTemplateModal">Tutup</button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-backdrop fade show" wire:click="closeTemplateModal"></div>

                                    {{-- Hover style --}}
                                    <style>
                                        .template-card-inner {
                                            transition: border-color .15s, background-color .15s;
                                        }

                                        .template-card:hover .template-card-inner {
                                            border-color: var(--bs-primary) !important;
                                            background-color: rgba(var(--bs-primary-rgb), .04);
                                        }
                                    </style>
                                    @endif

                                    <div class="text-end">
                                        <button type="button" wire:click="submit" class="btn btn-primary"
                                            wire:loading.attr="disabled" wire:target="submit">
                                            <span wire:loading.remove wire:target="submit">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24"
                                                    height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                                    <polyline points="7 9 12 4 17 9" />
                                                    <line x1="12" y1="4" x2="12" y2="16" />
                                                </svg>
                                                Upload File
                                            </span>
                                            <span wire:loading wire:target="submit">
                                                <span class="spinner-border spinner-border-sm me-1"
                                                    role="status"></span>
                                                Uploading...
                                            </span>
                                        </button>
                                    </div>


                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END PAGE BODY -->

</div>