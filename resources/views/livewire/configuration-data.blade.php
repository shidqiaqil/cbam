<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs nav-fill" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="#tabs-steel-slab"
                                    class="nav-link {{ $activeTab === 'steel-slab' ? 'active' : '' }}"
                                    data-bs-toggle="tab" wire:click="$set('activeTab', 'steel-slab')">Steel Slab</a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-steel-plate"
                                    class="nav-link {{ $activeTab === 'steel-plate' ? 'active' : '' }}"
                                    data-bs-toggle="tab" wire:click="$set('activeTab', 'steel-plate')">Steel Plate</a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-steel-hrc"
                                    class="nav-link {{ $activeTab === 'steel-hrc' ? 'active' : '' }}"
                                    data-bs-toggle="tab" wire:click="$set('activeTab', 'steel-hrc')">Steel HRC</a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-chp" class="nav-link {{ $activeTab === 'chp' ? 'active' : '' }}"
                                    data-bs-toggle="tab" wire:click="$set('activeTab', 'chp')">CHP - Power Plant</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane {{ $activeTab === 'steel-slab' ? 'active show' : '' }}"
                                id="tabs-steel-slab">
                                <div class="row g-3">
                                    <!-- NEW: Page-wide filters before Data Energy/Emission -->
                                    <div class="col-12 mb-4"
                                        style="border: 0.5px solid #b5d4f4; border-radius: 8px; background: #f0f6ff; padding: 1rem 1.25rem;">
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                                                <path d="M2 4h12M4 8h8M6 12h4" stroke="#378ADD" stroke-width="1.5"
                                                    stroke-linecap="round" />
                                            </svg>
                                            <span
                                                style="font-size: 12px; font-weight: 500; color: #378ADD; letter-spacing: 0.05em; text-transform: uppercase;">Filter</span>
                                        </div>
                                        @if($availableYears->isNotEmpty())
                                        <div class="row g-2">
                                            <div class="col-md-5">
                                                <label class="form-label">Year</label>
                                                <select wire:model.live="periodYear" class="form-select">
                                                    <option value="">Select Year</option>
                                                    @foreach($availableYears as $year)
                                                    <option value="{{ $year }}">{{ $year }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label">Period</label>
                                                <select wire:model.live.debounce.300ms="period" class="form-select" {{
                                                    empty($periodYear) ? 'disabled' : '' }}>
                                                    <option value="">Select Period</option>
                                                    <optgroup label="Monthly">
                                                        <option value="jan">Jan</option>
                                                        <option value="feb">Feb</option>
                                                        <option value="mar">Mar</option>
                                                        <option value="apr">Apr</option>
                                                        <option value="may">May</option>
                                                        <option value="jun">Jun</option>
                                                        <option value="jul">Jul</option>
                                                        <option value="aug">Aug</option>
                                                        <option value="sep">Sep</option>
                                                        <option value="oct">Oct</option>
                                                        <option value="nov">Nov</option>
                                                        <option value="dec">Dec</option>
                                                    </optgroup>
                                                    <optgroup label="Quarterly">
                                                        <option value="q1">Q1</option>
                                                        <option value="q2">Q2</option>
                                                        <option value="q3">Q3</option>
                                                        <option value="q4">Q4</option>
                                                    </optgroup>
                                                    <option value="yearly">Yearly</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">&nbsp;</label>
                                                <div class="mt-1">
                                                    @if(empty($periodYear) || empty($period))
                                                    <small class="text-muted">Select year and period to filter data
                                                        below</small>
                                                    @else
                                                    <span class="badge bg-blue-lt">Data for {{ strtoupper($period) }} {{
                                                        $periodYear }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Data Energy</h5>
                                        <!-- NEW: Energy Table 1 -->
                                        <div class="table-responsive mb-4">
                                            <h6 class="mb-3 text-primary">Table 1</h6>
                                            @if($energyTableData->isEmpty())
                                            <div class="alert alert-info">
                                                <i class="ti ti-info-circle"></i> Select year & period or <a
                                                    href="/uploadfile">Upload Energy Data</a>.
                                            </div>
                                            @else
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Description</th>
                                                        <th>Power</th>
                                                        <th>Unit</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($energyTableData as $row)
                                                    <tr {{ $row['description']==='Total' ? 'class=table-active fw-bold'
                                                        : '' }}>
                                                        <td>
                                                            {{ $row['description'] }}
                                                            @if(isset($row['tooltip']))
                                                            <span class="badge badge-sm bg-light text-primary ms-1"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                                                title="{{ $row['tooltip'] }}">i</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-end">{{ number_format($row['power'], 2) }}</td>
                                                        <td class="text-end">kWh</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            @endif
                                        </div>
                                        <!-- Table 2 -->
                                        <div class="table-responsive mb-4">
                                            <h6 class="mb-3 text-primary">Table 2</h6>
                                            @if($energyTableDataTable2->isEmpty())
                                            <div class="alert alert-info">
                                                <i class="ti ti-info-circle"></i> Select year & period to see Table 2
                                                data.
                                            </div>
                                            @else
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Description</th>
                                                        <th>Power</th>
                                                        <th>Unit</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($energyTableDataTable2 as $row)
                                                    <tr {{ $row['description']==='Total Purchase Electricity'
                                                        ? 'class=table-active fw-bold' : '' }}>
                                                        <td>
                                                            {{ $row['description'] }}
                                                            @if(isset($row['tooltip']))
                                                            <span class="badge badge-sm bg-light text-primary ms-1"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                                                title="{{ $row['tooltip'] }}">i</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-end">{{ number_format($row['power'], 2) }}</td>
                                                        <td class="text-end">kWh</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            @endif
                                        </div>
                                        <!-- Table 3 -->
                                        <div class="table-responsive mb-4">
                                            <h6 class="mb-3 text-primary">Table 3</h6>
                                            @if($energyTableDataTable3->isEmpty())
                                            <div class="alert alert-info">
                                                <i class="ti ti-info-circle"></i> Select year & period to see Table 3
                                                data.
                                            </div>
                                            @else
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Description</th>
                                                        <th>Power</th>
                                                        <th>Unit</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($energyTableDataTable3 as $row)
                                                    <tr {{ $row['description']==='Total' ? 'class=table-active fw-bold'
                                                        : '' }}>
                                                        <td>
                                                            {{ $row['description'] }}
                                                            @if(isset($row['tooltip']))
                                                            <span class="badge badge-sm bg-light text-primary ms-1"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                                                title="{{ $row['tooltip'] }}">i</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-end">{{ number_format($row['power'], 2) }}</td>
                                                        <td class="text-end">Ton</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            @endif
                                        </div>
                                        <!-- NEW: Table 4 -->
                                        <div class="table-responsive mb-4">
                                            <h6 class="mb-3 text-primary">Table 4</h6>
                                            @if($energyTableDataTable4->isEmpty())
                                            <div class="alert alert-info">
                                                <i class="ti ti-info-circle"></i> Select year & period to see Table 4
                                                data.
                                            </div>
                                            @else
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Description</th>
                                                        <th>Power</th>
                                                        <th>Unit</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($energyTableDataTable4 as $row)
                                                    <tr {{ $row['description']==='Total Purchase Steam'
                                                        ? 'class=table-active fw-bold' : '' }}>
                                                        <td>
                                                            {{ $row['description'] }}
                                                            @if(isset($row['tooltip']))
                                                            <span class="badge badge-sm bg-light text-primary ms-1"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                                                title="{{ $row['tooltip'] }}">i</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-end">{{ number_format($row['power'], 2) }}</td>
                                                        <td class="text-end">Ton</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            @endif

                                        </div>

                                        <!-- NEW: Export Table -->
                                        <div class="table-responsive mb-4">
                                            <h6 class="mb-3 text-primary">Export</h6>
                                            @if($energyTableDataExport->isEmpty())
                                            <div class="alert alert-info">
                                                <i class="ti ti-info-circle"></i> Select year & period to see Export
                                                data.
                                            </div>
                                            @else
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Source</th>
                                                        <th>Quantity</th>
                                                        <th>Unit</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($energyTableDataExport as $row)
                                                    <tr>
                                                        <td>
                                                            {{ $row['description'] }}
                                                            @if(isset($row['tooltip']) && !empty($row['tooltip']))
                                                            <span class="badge badge-sm bg-light text-primary ms-1"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                                                title="{{ $row['tooltip'] }}">i</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-end">{{ number_format($row['quantity'], 2)
                                                            }}</td>
                                                        <td class="text-end">Nm3</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            @endif
                                        </div>

                                        <!-- NEW: Import Table -->
                                        <div class="table-responsive mb-4">
                                            <h6 class="mb-3 text-primary">Import</h6>
                                            @if($energyTableDataImport->isEmpty())
                                            <div class="alert alert-info">
                                                <i class="ti ti-info-circle"></i> Select year & period to see Import
                                                data.
                                            </div>
                                            @else
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Description</th>
                                                        <th>Quantity</th>
                                                        <th>Unit</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($energyTableDataImport as $row)
                                                    <tr>
                                                        <td>
                                                            {{ $row['description'] }}
                                                            @if(isset($row['tooltip']) && !empty($row['tooltip']))
                                                            <span class="badge badge-sm bg-light text-primary ms-1"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                                                title="{{ $row['tooltip'] }}">i</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-end">{{ number_format($row['quantity'], 2) }}
                                                        </td>
                                                        <td class="text-end">ton</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            @endif
                                        </div>

                                        <!-- NEW: Export Electricity Table -->
                                        <div class="table-responsive mb-4">
                                            <h6 class="mb-3 text-primary">Export Electricity</h6>
                                            @if($energyTableDataExportElectricity->isEmpty())
                                            <div class="alert alert-info">
                                                <i class="ti ti-info-circle"></i> Select year & period to see Export
                                                Electricity data.
                                            </div>
                                            @else
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Description</th>
                                                        <th>Quantity</th>
                                                        <th>Unit</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($energyTableDataExportElectricity as $row)
                                                    <tr>
                                                        <td>
                                                            {{ $row['description'] }}
                                                            @if(isset($row['tooltip']))
                                                            <span class="badge badge-sm bg-light text-primary ms-1"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                                                title="{{ $row['tooltip'] }}">i</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-end">{{ number_format($row['quantity'], 2) }}
                                                        </td>
                                                        <td class="text-end">{{ $loop->first ? 'kWh' : ($loop->last ?
                                                            'MWh' : 'kWh') }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h5 class="mb-3">Data Emission</h5>
                                        <div class="table-responsive mb-4">
                                            <h6 class="mb-3 text-primary">Table 1</h6>
                                            @if($energyTableData->isEmpty())
                                            <div class="alert alert-info">
                                                <i class="ti ti-info-circle"></i> Select year & period or <a
                                                    href="/uploadfile">Upload Energy Data</a>.
                                            </div>
                                            @else
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Description</th>
                                                        <th>Power</th>
                                                        <th>Unit</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($energyTableData as $row)
                                                    <tr {{ $row['description']==='Total' ? 'class=table-active fw-bold'
                                                        : '' }}>
                                                        <td>
                                                            {{ $row['description'] }}
                                                            @if(isset($row['tooltip']))
                                                            <span class="badge badge-sm bg-light text-primary ms-1"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                data-bs-html="true"
                                                                title="{{ $row['tooltip'] }}">i</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-end">{{ number_format($row['power'], 2) }}</td>
                                                        <td class="text-end">kWh</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            @endif
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <!-- Other tabs unchanged -->
                            <div class="tab-pane {{ $activeTab === 'steel-plate' ? 'active show' : '' }}"
                                id="tabs-steel-plate">
                                <h4>Steel Plate</h4>
                                <div>
                                    Konten untuk Steel Plate. Tambahkan form atau tabel konfigurasi di sini.
                                </div>
                            </div>
                            <div class="tab-pane {{ $activeTab === 'steel-hrc' ? 'active show' : '' }}"
                                id="tabs-steel-hrc">
                                <h4>Steel HRC</h4>
                                <div>
                                    Konten untuk Steel HRC. Tambahkan form atau tabel konfigurasi di sini.
                                </div>
                            </div>
                            <div class="tab-pane {{ $activeTab === 'chp' ? 'active show' : '' }}" id="tabs-chp">
                                <div class="row g-3">
                                    <!-- Filter section (same as Steel Slab) -->
                                    <div class="col-12 mb-4"
                                        style="border: 0.5px solid #b5d4f4; border-radius: 8px; background: #f0f6ff; padding: 1rem 1.25rem;">
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                                                <path d="M2 4h12M4 8h8M6 12h4" stroke="#378ADD" stroke-width="1.5"
                                                    stroke-linecap="round" />
                                            </svg>
                                            <span
                                                style="font-size: 12px; font-weight: 500; color: #378ADD; letter-spacing: 0.05em; text-transform: uppercase;">Filter</span>
                                        </div>
                                        @if($availableYears->isNotEmpty())
                                        <div class="row g-2">
                                            <div class="col-md-5">
                                                <label class="form-label">Year</label>
                                                <select wire:model.live="periodYear" class="form-select">
                                                    <option value="">Select Year</option>
                                                    @foreach($availableYears as $year)
                                                    <option value="{{ $year }}">{{ $year }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label">Period</label>
                                                <select wire:model.live.debounce.300ms="period" class="form-select" {{
                                                    empty($periodYear) ? 'disabled' : '' }}>
                                                    <option value="">Select Period</option>
                                                    <optgroup label="Monthly">
                                                        <option value="jan">Jan</option>
                                                        <option value="feb">Feb</option>
                                                        <option value="mar">Mar</option>
                                                        <option value="apr">Apr</option>
                                                        <option value="may">May</option>
                                                        <option value="jun">Jun</option>
                                                        <option value="jul">Jul</option>
                                                        <option value="aug">Aug</option>
                                                        <option value="sep">Sep</option>
                                                        <option value="oct">Oct</option>
                                                        <option value="nov">Nov</option>
                                                        <option value="dec">Dec</option>
                                                    </optgroup>
                                                    <optgroup label="Quarterly">
                                                        <option value="q1">Q1</option>
                                                        <option value="q2">Q2</option>
                                                        <option value="q3">Q3</option>
                                                        <option value="q4">Q4</option>
                                                    </optgroup>
                                                    <option value="yearly">Yearly</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">&nbsp;</label>
                                                <div class="mt-1">
                                                    @if(empty($periodYear) || empty($period))
                                                    <small class="text-muted">Select year and period to see CHP table
                                                        data below</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- NEW: Bootstrap tooltip initializer -->
                            <script>
                                document.addEventListener('livewire:load', function () {
        initTooltips();
    });
    document.addEventListener('livewire:update', function () {
        initTooltips();
    });
    function initTooltips() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                boundary: 'window',
                popperConfig: {
                    modifiers: [{
                        name: 'preventOverflow',
                        options: { boundary: 'window' }
                    }]
                }
            });
        });
    }
                            </script>
                        </div>