<div class="row g-3">

    {{-- FILTER --}}
    <div class="col-12 mb-4 filter-section">
        <div class="d-flex align-items-center gap-2 mb-3">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <path d="M2 4h12M4 8h8M6 12h4" stroke="#378ADD" stroke-width="1.5" stroke-linecap="round" />
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
                <select wire:model.live="period" class="form-select" {{ empty($periodYear) ? 'disabled' : '' }}>
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
                    <small class="text-muted">Select year and period to filter data below</small>
                    @else
                    <span class="badge bg-blue-lt">Data for {{ strtoupper($period) }} {{ $periodYear }}</span>
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
            @if($steelPlateTable1->isEmpty())
            <div class="alert alert-info">
                <i class="ti ti-info-circle"></i> Select year &amp; period or <a href="/uploadfile">Upload Energy
                    Data</a>.
            </div>
            @else
            <table class="table table-sm table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Description</th>
                        <th class="text-end">Power</th>
                        <th class="text-end">Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($steelPlateTable1 as $row)
                    <tr>
                        <td>
                            {{ $row['description'] }}
                            @if(!empty($row['tooltip']))
                            <span class="badge badge-sm bg-light text-primary ms-1" data-bs-toggle="tooltip"
                                data-bs-placement="top" data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                title="{{ $row['tooltip'] }}">i</span>
                            @endif
                        </td>
                        <td class="text-end">{{ number_format((float)$row['power'], 2) }}</td>
                        <td class="text-end">{{ $row['unit'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>{{-- /table-1 --}}

        {{-- Table 2 --}}
        <div class="table-responsive mb-4">
            <h6 class="mb-3 text-primary">Table 2</h6>
            @if($steelPlateTable2->isEmpty())
            <div class="alert alert-info">
                <i class="ti ti-info-circle"></i> Select year &amp; period to see Table 2 data.
            </div>
            @else
            <table class="table table-sm table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Description</th>
                        <th class="text-end">COG</th>
                        <th class="text-end">Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($steelPlateTable2 as $row)
                    <tr>
                        <td>
                            {{ $row['description'] }}
                            @if(!empty($row['tooltip']))
                            <span class="badge badge-sm bg-light text-primary ms-1" data-bs-toggle="tooltip"
                                data-bs-placement="top" data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                title="{{ $row['tooltip'] }}">i</span>
                            @endif
                        </td>
                        <td class="text-end">{{ number_format((float)$row['cog'], 2) }}</td>
                        <td class="text-end">{{ $row['unit'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>{{-- /table-2 --}}

    </div>{{-- /col data energy --}}

    {{-- RIGHT COLUMN: DATA EMISSION --}}
    <div class="col-12 col-lg-6">
        <h5 class="mb-3">Data Emission (CO2e)</h5>

        {{-- Table 1.1 --}}
        <div class="table-responsive mb-4">
            <h6 class="mb-3 text-primary">Table 1.1 – Electricity Emission</h6>
            @if($steelPlateTable11->isEmpty())
            <div class="alert alert-info">
                <i class="ti ti-info-circle"></i> Select year &amp; period to see Table 1.1 data.
            </div>
            @else
            <table class="table table-sm table-bordered">
                <thead class="table-light">
                    <tr>
                        <th class="text-end">
                            Emission Factor
                            <br><small class="text-muted">(tCO2/MWh)</small>
                        </th>
                        <th class="text-end">Total Emission</th>
                        <th class="text-end">Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($steelPlateTable11 as $row)
                    <tr>
                        <td class="text-end">
                            {{ number_format((float)$row['emission_factor'], 4) }}
                            @if(!empty($row['ef_tooltip']))
                            <span class="badge badge-sm bg-light text-primary ms-1" data-bs-toggle="tooltip"
                                data-bs-placement="top" data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                title="{{ $row['ef_tooltip'] }}">i</span>
                            @endif
                        </td>
                        <td class="text-end">
                            {{ number_format((float)$row['total_emission'], 2) }}
                            @if(!empty($row['em_tooltip']))
                            <span class="badge badge-sm bg-light text-primary ms-1" data-bs-toggle="tooltip"
                                data-bs-placement="top" data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                title="{{ $row['em_tooltip'] }}">i</span>
                            @endif
                        </td>
                        <td class="text-end">{{ $row['unit'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>{{-- /table-1.1 --}}

        {{-- Table 2.1 --}}
        <div class="table-responsive mb-4">
            <h6 class="mb-3 text-primary">Table 2.1 – By Product Gas</h6>
            @if($steelPlateTable21->isEmpty())
            <div class="alert alert-info">
                <i class="ti ti-info-circle"></i> Select year &amp; period to see Table 2.1 data.
            </div>
            @else
            <table class="table table-sm table-bordered">
                <thead class="table-light">
                    <tr>
                        <th class="text-end">
                            Conversion
                            <br><small class="text-muted">(TJ/m3)</small>
                        </th>
                        <th class="text-end">By Product Gas</th>
                        <th class="text-end">Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($steelPlateTable21 as $row)
                    <tr>
                        <td class="text-end">
                            {{ number_format((float)$row['conversion'], 9) }}
                            @if(!empty($row['conv_tooltip']))
                            <span class="badge badge-sm bg-light text-primary ms-1" data-bs-toggle="tooltip"
                                data-bs-placement="top" data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                title="{{ $row['conv_tooltip'] }}">i</span>
                            @endif
                        </td>
                        <td class="text-end">
                            {{ number_format((float)$row['byproduct'], 2) }}
                            @if(!empty($row['bp_tooltip']))
                            <span class="badge badge-sm bg-light text-primary ms-1" data-bs-toggle="tooltip"
                                data-bs-placement="top" data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                title="{{ $row['bp_tooltip'] }}">i</span>
                            @endif
                        </td>
                        <td class="text-end">{{ $row['unit'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>{{-- /table-2.1 --}}

    </div>{{-- /col data emission --}}

</div>{{-- /row --}}