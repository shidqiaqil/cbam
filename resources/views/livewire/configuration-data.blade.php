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
                    </div>{{-- /card-header --}}

                    <div class="card-body">
                        <div class="tab-content">

                            {{-- ====================================================== --}}
                            {{-- TAB: STEEL SLAB --}}
                            {{-- ====================================================== --}}
                            <div class="tab-pane {{ $activeTab === 'steel-slab' ? 'active show' : '' }}"
                                id="tabs-steel-slab">
                                <div class="row g-3">

                                    {{-- FILTER --}}
                                    <div class="col-12 mb-4 filter-section">
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                                                <path d="M2 4h12M4 8h8M6 12h4" stroke="#378ADD" stroke-width="1.5"
                                                    stroke-linecap="round" />
                                            </svg>
                                            <span class="filter-label">Filter</span>
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
                                                <select wire:model.live="period" class="form-select" {{
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
                                    </div>{{-- /filter --}}

                                    {{-- LEFT COLUMN: DATA ENERGY --}}
                                    <div class="col-12 col-lg-6">
                                        <h5 class="mb-3">Data Energy</h5>

                                        {{-- Table 1 --}}
                                        <div class="table-responsive mb-4">
                                            <h6 class="mb-3 text-primary">Table 1</h6>
                                            @if($energyTableData->isEmpty())
                                            <div class="alert alert-info">
                                                <i class="ti ti-info-circle"></i> Select year &amp; period or <a
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
                                                    <tr @class(['table-active fw-bold'=> $row['description'] ===
                                                        'Total'])>
                                                        <td>
                                                            {{ $row['description'] }}
                                                            @if(!empty($row['tooltip']))
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
                                        </div>{{-- /table-1 --}}

                                        {{-- Table 2 --}}
                                        <div class="table-responsive mb-4">
                                            <h6 class="mb-3 text-primary">Table 2</h6>
                                            @if($energyTableDataTable2->isEmpty())
                                            <div class="alert alert-info">
                                                <i class="ti ti-info-circle"></i> Select year &amp; period to see Table
                                                2 data.
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
                                                    <tr @class(['table-active fw-bold'=> $row['description'] === 'Total
                                                        Purchase Electricity'])>
                                                        <td>
                                                            {{ $row['description'] }}
                                                            @if(!empty($row['tooltip']))
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
                                        </div>{{-- /table-2 --}}

                                        {{-- Table 3 --}}
                                        <div class="table-responsive mb-4">
                                            <h6 class="mb-3 text-primary">Table 3</h6>
                                            @if($energyTableDataTable3->isEmpty())
                                            <div class="alert alert-info">
                                                <i class="ti ti-info-circle"></i> Select year &amp; period to see Table
                                                3 data.
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
                                                    <tr @class(['table-active fw-bold'=> $row['description'] ===
                                                        'Total'])>
                                                        <td>
                                                            {{ $row['description'] }}
                                                            @if(!empty($row['tooltip']))
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
                                        </div>{{-- /table-3 --}}

                                        {{-- Table 4 --}}
                                        <div class="table-responsive mb-4">
                                            <h6 class="mb-3 text-primary">Table 4</h6>
                                            @if($energyTableDataTable4->isEmpty())
                                            <div class="alert alert-info">
                                                <i class="ti ti-info-circle"></i> Select year &amp; period to see Table
                                                4 data.
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
                                                    <tr @class(['table-active fw-bold'=> $row['description'] === 'Total
                                                        Purchase Steam'])>
                                                        <td>
                                                            {{ $row['description'] }}
                                                            @if(!empty($row['tooltip']))
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
                                        </div>{{-- /table-4 --}}

                                        {{-- Export --}}
                                        <div class="table-responsive mb-4">
                                            <h6 class="mb-3 text-primary">Export</h6>
                                            @if($energyTableDataExport->isEmpty())
                                            <div class="alert alert-info">
                                                <i class="ti ti-info-circle"></i> Select year &amp; period to see Export
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
                                                            @if(!empty($row['tooltip']))
                                                            <span class="badge badge-sm bg-light text-primary ms-1"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                                                title="{{ $row['tooltip'] }}">i</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-end">{{ number_format($row['quantity'], 2) }}
                                                        </td>
                                                        <td class="text-end">Nm3</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            @endif
                                        </div>{{-- /export --}}

                                        {{-- Import --}}
                                        <div class="table-responsive mb-4">
                                            <h6 class="mb-3 text-primary">Import</h6>
                                            @if($energyTableDataImport->isEmpty())
                                            <div class="alert alert-info">
                                                <i class="ti ti-info-circle"></i> Select year &amp; period to see Import
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
                                                            @if(!empty($row['tooltip']))
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
                                        </div>{{-- /import --}}

                                        {{-- Export Electricity --}}
                                        <div class="table-responsive mb-4">
                                            <h6 class="mb-3 text-primary">Export Electricity</h6>
                                            @if($energyTableDataExportElectricity->isEmpty())
                                            <div class="alert alert-info">
                                                <i class="ti ti-info-circle"></i> Select year &amp; period to see Export
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
                                                            @if(!empty($row['tooltip']))
                                                            <span class="badge badge-sm bg-light text-primary ms-1"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                                                title="{{ $row['tooltip'] }}">i</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-end">{{ number_format($row['quantity'], 2) }}
                                                        </td>
                                                        <td class="text-end">
                                                            {{ $row['description'] === 'Reverse Power/1000' ? 'MWh' :
                                                            'kWh' }}
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            @endif
                                        </div>{{-- /export-electricity --}}

                                    </div>{{-- /col data energy --}}

                                    {{-- RIGHT COLUMN: DATA EMISSION --}}
                                    <div class="col-12 col-lg-6">
                                        <h5 class="mb-3">Data Emission (CO2e)</h5>

                                        {{-- ============================================ --}}
                                        {{-- TABLE 1.1 – Electricity Emission (Table 1) --}}
                                        {{-- ============================================ --}}
                                        <div class="table-responsive mb-4">
                                            <h6 class="mb-3 text-primary">Table 1.1 – Electricity Emission</h6>
                                            @if($emissionTableData11->isEmpty())
                                            <div class="alert alert-info">
                                                <i class="ti ti-info-circle"></i> Select year &amp; period to see Table
                                                1.1 data.
                                            </div>
                                            @else
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-light">
                                                    <tr>

                                                        <th class="text-end">
                                                            Emission Factor
                                                            <br><small class="text-muted">(tCO2/MWh)</small>
                                                        </th>
                                                        <th class="text-end">
                                                            Total Emission
                                                        </th>
                                                        <th class="text-end">Unit</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($emissionTableData11 as $row)
                                                    <tr @class(['table-active fw-bold'=> !empty($row['is_total'])])>


                                                        {{-- Emission Factor column --}}
                                                        <td class="text-end">
                                                            @if(!empty($row['is_total']))
                                                            {{-- Total row: no EF displayed --}}
                                                            <span class="text-muted">—</span>
                                                            @else
                                                            {{ number_format($row['emission_factor'], 4) }}
                                                            @if(!empty($row['ef_tooltip']))
                                                            <span class="badge badge-sm bg-light text-primary ms-1"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                                                title="{{ $row['ef_tooltip'] }}">i</span>
                                                            @endif
                                                            @endif
                                                        </td>

                                                        {{-- Total Emission column --}}
                                                        <td class="text-end">
                                                            {{ number_format($row['total_emission']) }}
                                                            @if(!empty($row['em_tooltip']))
                                                            <span class="badge badge-sm bg-light text-primary ms-1"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                                                title="{{ $row['em_tooltip'] }}">i</span>
                                                            @endif
                                                        </td>

                                                        <td class="text-end">{{ $row['unit'] }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            {{-- <small class="text-muted">
                                                EF[2] sourced from CHP Tab → Table 6 (Total/Elec Output).
                                                EF[3] &amp; EF[4] sourced from Table 2.1 blended EF.
                                            </small> --}}
                                            @endif
                                        </div>{{-- /table-1.1 --}}

                                        {{-- ============================================ --}}
                                        {{-- TABLE 2.1 – Electricity Emission (Table 2) --}}
                                        {{-- ============================================ --}}
                                        <div class="table-responsive mb-4">
                                            <h6 class="mb-3 text-primary">Table 2.1 – Electricity Emission</h6>
                                            @if($emissionTableData21->isEmpty())
                                            <div class="alert alert-info">
                                                <i class="ti ti-info-circle"></i> Select year &amp; period to see Table
                                                2.1 data.
                                            </div>
                                            @else
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-light">
                                                    <tr>

                                                        <th class="text-end">
                                                            Emission Factor
                                                            <br><small class="text-muted">(tCO2/MWh)</small>
                                                        </th>
                                                        <th class="text-end">
                                                            Total Emission
                                                        </th>
                                                        <th class="text-end">Unit</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($emissionTableData21 as $row)
                                                    <tr @class(['table-active fw-bold'=> !empty($row['is_total'])])>


                                                        {{-- Emission Factor column --}}
                                                        <td class="text-end">
                                                            {{ number_format($row['emission_factor'], 4) }}
                                                            @if(!empty($row['ef_tooltip']))
                                                            <span class="badge badge-sm bg-light text-primary ms-1"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                                                title="{{ $row['ef_tooltip'] }}">i</span>
                                                            @endif
                                                        </td>

                                                        {{-- Total Emission column --}}
                                                        <td class="text-end">
                                                            {{ number_format($row['total_emission']) }}
                                                            @if(!empty($row['em_tooltip']))
                                                            <span class="badge badge-sm bg-light text-primary ms-1"
                                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                                data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                                                title="{{ $row['em_tooltip'] }}">i</span>
                                                            @endif
                                                        </td>

                                                        <td class="text-end">{{ $row['unit'] }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            {{-- <small class="text-muted">
                                                EF[2] sourced from CHP Tab → Table 6 (Net Emission/Elec Output).
                                                EF[4] (Total row) = blended factor for total purchased electricity.
                                            </small> --}}
                                            @endif
                                        </div>{{-- /table-2.1 --}}

                                        {{-- Scope 1 & 2 Summary (existing) --}}
                                        <div class="table-responsive mb-4">
                                            <h6 class="mb-3 text-primary">Scope 1 &amp; 2 Summary</h6>
                                            @if($energyTableData->isEmpty())
                                            <div class="alert alert-info">
                                                <i class="ti ti-info-circle"></i> Select year &amp; period to see
                                                emission estimates.
                                            </div>
                                            @else
                                            @php
                                            $scope1 = $energyTableData->sum('power') * 0.0004;
                                            $scope2 = $energyTableData->sum('power') * 0.0001;
                                            $totalCo2 = $scope1 + $scope2;
                                            @endphp
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Scope</th>
                                                        <th>CO2e (tCO2e)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Scope 1</td>
                                                        <td class="text-end">{{ number_format($scope1, 2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Scope 2</td>
                                                        <td class="text-end">{{ number_format($scope2, 2) }}</td>
                                                    </tr>
                                                    <tr class="table-active fw-bold">
                                                        <td>Total</td>
                                                        <td class="text-end">{{ number_format($totalCo2, 2) }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <small class="text-muted">Demo factors. Update with actual EF from
                                                regulation.</small>
                                            @endif
                                        </div>{{-- /scope-summary --}}

                                    </div>{{-- /col data emission --}}

                                </div>{{-- /row --}}
                            </div>{{-- /tab-pane steel-slab --}}

                            {{-- ====================================================== --}}
                            {{-- TAB: STEEL PLATE --}}
                            {{-- ====================================================== --}}
                            <div class="tab-pane {{ $activeTab === 'steel-plate' ? 'active show' : '' }}"
                                id="tabs-steel-plate">
                                <div class="row g-3">
                                    <div class="col-12 mb-4 filter-section">
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                                                <path d="M2 4h12M4 8h8M6 12h4" stroke="#378ADD" stroke-width="1.5"
                                                    stroke-linecap="round" />
                                            </svg>
                                            <span class="filter-label">Filter</span>
                                        </div>
                                        @if($availableYears->isNotEmpty())
                                        <div class="row g-2">
                                            <div class="col-12 col-md-5">
                                                <label class="form-label">Year</label>
                                                <select wire:model.live="periodYear" class="form-select">
                                                    <option value="">Select Year</option>
                                                    @foreach($availableYears as $year)
                                                    <option value="{{ $year }}">{{ $year }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-12 col-md-5">
                                                <label class="form-label">Period</label>
                                                <select wire:model.live="period" class="form-select" {{
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
                                            <div class="col-12 col-md-2">
                                                <label class="form-label">&nbsp;</label>
                                                <div class="mt-1">
                                                    @if(empty($periodYear) || empty($period))
                                                    <small class="text-muted">Select year and period</small>
                                                    @else
                                                    <span class="badge bg-blue-lt">Data for {{ strtoupper($period) }} {{
                                                        $periodYear }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>{{-- /filter --}}
                                    <div class="col-12">
                                        <h5 class="mb-3">Steel Plate Config</h5>
                                        <div class="alert alert-info">
                                            Steel Plate tables coming soon.
                                        </div>
                                    </div>
                                </div>{{-- /row --}}
                            </div>{{-- /tab-pane steel-plate --}}

                            {{-- ====================================================== --}}
                            {{-- TAB: STEEL HRC --}}
                            {{-- ====================================================== --}}
                            <div class="tab-pane {{ $activeTab === 'steel-hrc' ? 'active show' : '' }}"
                                id="tabs-steel-hrc">
                                @livewire('configuration-data-hrc')
                            </div>{{-- /tab-pane steel-hrc --}}

                            {{-- ====================================================== --}}
                            {{-- TAB: CHP --}}
                            {{-- ====================================================== --}}
                            <div class="tab-pane {{ $activeTab === 'chp' ? 'active show' : '' }}" id="tabs-chp">
                                @livewire('configuration-data-chp')
                            </div>{{-- /tab-pane chp --}}

                        </div>{{-- /tab-content --}}
                    </div>{{-- /card-body --}}
                </div>{{-- /card --}}
            </div>{{-- /col-md-12 --}}
        </div>{{-- /row --}}
    </div>{{-- /container-xl --}}
</div>{{-- /page-body --}}

@push('scripts')
<script>
    document.addEventListener('livewire:navigated', () => initTooltips());
    document.addEventListener('livewire:updated',   () => initTooltips());

    function initTooltips() {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            bootstrap.Tooltip.getOrCreateInstance(el, {
                boundary: 'window',
                html: true,
            });
        });
    }

    initTooltips();
</script>
@endpush