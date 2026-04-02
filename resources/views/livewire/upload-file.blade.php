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
                            <form class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Master Data</h3>
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