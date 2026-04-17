<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-md-12">

                {{-- FILTER --}}
                <div class="mb-4 filter-section">
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
                            <select wire:model.live="period" class="form-select" {{ empty($periodYear) ? 'disabled' : ''
                                }}>
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
                                <span class="badge bg-blue-lt">Data for {{ strtoupper($period) }} {{ $periodYear
                                    }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="row g-4">

                    {{-- LEFT SIDE --}}
                    <div class="col-lg-6">

                        {{-- Table 1 --}}
                        <div class="table-responsive mb-4">
                            <h5 class="mb-3">Table 1 Power Consumption (MWh)</h5>
                            @if($hrcTable1Data->isEmpty())
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
                                    @foreach($hrcTable1Data as $row)
                                    <tr>
                                        <td>
                                            {{ $row['description'] }}
                                            @if(!empty($row['tooltip']))
                                            <span class="badge badge-sm bg-light text-primary ms-1"
                                                data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true"
                                                data-bs-custom-class="tooltip-wide"
                                                title="{{ $row['tooltip'] }}">i</span>
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format($row['power'], 2) }}</td>
                                        <td class="text-end">MWh</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif
                        </div>

                        {{-- Table 2 --}}
                        <div class="table-responsive mb-4">
                            <h5 class="mb-3">Table 2</h5>
                            @if($hrcTable2Data->isEmpty())
                            <div class="alert alert-info">
                                <i class="ti ti-info-circle"></i> Select year & period to see Table 2 data.
                            </div>
                            @else
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Description</th>
                                        <th>COG</th>
                                        <th>Unit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($hrcTable2Data as $row)
                                    <tr>
                                        <td>
                                            {{ $row['description'] }}
                                            @if(!empty($row['tooltip']))
                                            <span class="badge badge-sm bg-light text-primary ms-1"
                                                data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true"
                                                data-bs-custom-class="tooltip-wide"
                                                title="{{ $row['tooltip'] }}">i</span>
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format($row['cog'], 2) }}</td>
                                        <td class="text-end">Nm3</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif
                        </div>

                        {{-- Table 3 Natural Gas --}}
                        <div class="table-responsive mb-4">
                            <h5 class="mb-3">Table 3 Natural Gas</h5>
                            @if($hrcTable3Data->isEmpty())
                            <div class="alert alert-info">
                                <i class="ti ti-info-circle"></i> Select year & period to see Table 3 data.
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
                                    @foreach($hrcTable3Data as $row)
                                    <tr>
                                        <td>
                                            {{ $row['description'] }}
                                            @if(!empty($row['tooltip']))
                                            <span class="badge badge-sm bg-light text-primary ms-1"
                                                data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true"
                                                data-bs-custom-class="tooltip-wide"
                                                title="{{ $row['tooltip'] }}">i</span>
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format($row['quantity'], 2) }}</td>
                                        <td class="text-end">Nm3</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif
                        </div>

                    </div>
                    {{-- END LEFT SIDE --}}

                    {{-- RIGHT SIDE --}}
                    <div class="col-lg-6">

                        {{-- Table 1.1 Emission --}}
                        <div class="table-responsive mb-4">
                            <h5 class="mb-3">Table 1.1 Emission</h5>
                            @if($hrcEmissionTableData->isEmpty())
                            <div class="alert alert-info">
                                <i class="ti ti-info-circle"></i> No data available.
                            </div>
                            @else
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Emission Factor (tCO2/Mwh)</th>
                                        <th>Total Emission</th>
                                        <th>Unit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($hrcEmissionTableData as $row)
                                    <tr>
                                        <td class="text-end">
                                            @if($row['tooltip'])
                                            <span class="badge badge-sm bg-light text-primary ms-1"
                                                data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true"
                                                data-bs-custom-class="tooltip-wide"
                                                title="{{ $row['tooltip'] }}">i</span>
                                            @endif
                                            {{ $row['ef_value'] }}
                                        </td>
                                        <td class="text-end">{{ $row['total_emission'] }}</td>
                                        <td class="text-end">{{ $row['unit'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif
                        </div>

                        {{-- Table 2.2 --}}
                        <table class="table table-sm table-bordered">
                            <h5 class="mb-3">Table 2.2</h5>
                            <thead class="table-light">
                                <tr>
                                    <th>Conversion (TJ/m3)</th>
                                    <th>By Product Gas</th>
                                    <th>Unit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($hrcTable22Data as $row)
                                <tr>
                                    <td class="text-end">
                                        @if(!empty($row['tooltip']))
                                        <span class="badge badge-sm bg-light text-primary ms-1" data-bs-toggle="tooltip"
                                            data-bs-placement="top" data-bs-html="true"
                                            data-bs-custom-class="tooltip-wide" title="{{ $row['tooltip'] }}">i</span>
                                        @endif
                                        {{ $row['conversion_factor'] }}
                                    </td>
                                    <td class="text-end">{{ $row['total_tj'] }}</td> {{-- nilai hasil perhitungan --}}
                                    <td class="text-end">{{ $row['unit'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- Table 3.3 By Product Gas (Natural Gas) --}}
                        <div class="table-responsive mb-4">
                            <h5 class="mb-3">Table 3.3 By Product Gas</h5>
                            @if($hrcTable33Data->isEmpty())
                            <div class="alert alert-info">
                                <i class="ti ti-info-circle"></i> No data available.
                            </div>
                            @else
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Conversion (TJ/m3)</th>
                                        <th>By Product Gas</th>
                                        <th>Unit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($hrcTable33Data as $row)
                                    <tr>
                                        <td class="text-end">
                                            @if(!empty($row['tooltip']))
                                            <span class="badge badge-sm bg-light text-primary ms-1"
                                                data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true"
                                                data-bs-custom-class="tooltip-wide"
                                                title="{{ $row['tooltip'] }}">i</span>
                                            @endif
                                            {{ $row['conversion_factor'] }}
                                        </td>
                                        <td class="text-end">{{ $row['total_tj'] }}</td>
                                        <td class="text-end">{{ $row['unit'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif
                        </div>

                    </div>
                    {{-- END RIGHT SIDE --}}

                </div>
                {{-- END row g-4 --}}

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function initTooltips() {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            const existing = bootstrap.Tooltip.getInstance(el);
            if (existing) existing.dispose();
            new bootstrap.Tooltip(el, { boundary: 'window', html: true });
        });
    }

    document.addEventListener('DOMContentLoaded', initTooltips);
    document.addEventListener('livewire:navigated', initTooltips);
    document.addEventListener('livewire:morph.updated', initTooltips);
    Livewire.hook('commit', ({ succeed }) => {
        succeed(() => requestAnimationFrame(initTooltips));
    });
</script>
@endpush