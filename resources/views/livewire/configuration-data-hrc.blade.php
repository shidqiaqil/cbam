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
                                        <th class="text-end">Power</th>
                                        <th class="text-end">Unit</th>
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
                                        <td class="text-end">{{ number_format((float)$row['power'], 2) }}</td>
                                        <td class="text-end">MWh</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif
                        </div>{{-- /table-1 --}}

                        {{-- Table 2 --}}
                        <div class="table-responsive mb-4">
                            <h5 class="mb-3">Table 2</h5>
                            @if($hrcTable2Data->isEmpty())
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
                                        <td class="text-end">{{ number_format((float)$row['cog'], 2) }}</td>
                                        <td class="text-end">Nm3</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif
                        </div>{{-- /table-2 --}}

                        {{-- Table 3 --}}
                        <div class="table-responsive mb-4">
                            <h5 class="mb-3">Table 3 Natural Gas</h5>
                            @if($hrcTable3Data->isEmpty())
                            <div class="alert alert-info">
                                <i class="ti ti-info-circle"></i> Select year &amp; period to see Table 3 data.
                            </div>
                            @else
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Description</th>
                                        <th class="text-end">Quantity</th>
                                        <th class="text-end">Unit</th>
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
                                        <td class="text-end">{{ number_format((float)$row['quantity'], 2) }}</td>
                                        <td class="text-end">Nm3</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif
                        </div>{{-- /table-3 --}}

                    </div>
                    {{-- END LEFT SIDE --}}

                    {{-- RIGHT SIDE --}}
                    <div class="col-lg-6">

                        {{-- Table 1.1 --}}
                        <div class="table-responsive mb-4">
                            <h5 class="mb-3">Table 1.1 Emission</h5>
                            @if($hrcTable11Data->isEmpty())
                            <div class="alert alert-info">
                                <i class="ti ti-info-circle"></i> No data available.
                            </div>
                            @else
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-end">Emission Factor (tCO2/MWh)</th>
                                        <th class="text-end">Total Emission</th>
                                        <th class="text-end">Unit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($hrcTable11Data as $row)
                                    <tr>
                                        <td class="text-end">
                                            {{ number_format((float)$row['emission_factor'], 4) }}
                                            @if(!empty($row['tooltip']))
                                            <span class="badge badge-sm bg-light text-primary ms-1"
                                                data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true"
                                                data-bs-custom-class="tooltip-wide"
                                                title="{{ $row['tooltip'] }}">i</span>
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format((float)$row['total_emission'], 2) }}</td>
                                        <td class="text-end">{{ $row['unit'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif
                        </div>{{-- /table-1.1 --}}

                        {{-- Table 2.1 --}}
                        <div class="table-responsive mb-4">
                            <h5 class="mb-3">Table 2.1</h5>
                            @if($hrcTable21Data->isEmpty())
                            <div class="alert alert-info">
                                <i class="ti ti-info-circle"></i> No data available.
                            </div>
                            @else
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-end">Conversion (TJ/m3)</th>
                                        <th class="text-end">By Product Gas</th>
                                        <th class="text-end">Unit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($hrcTable21Data as $row)
                                    <tr>
                                        <td class="text-end">
                                            {{ number_format((float)$row['conversion_factor'], 9) }}
                                            @if(!empty($row['tooltip']))
                                            <span class="badge badge-sm bg-light text-primary ms-1"
                                                data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true"
                                                data-bs-custom-class="tooltip-wide"
                                                title="{{ $row['tooltip'] }}">i</span>
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format((float)$row['by_product_gas'], 2) }}</td>
                                        <td class="text-end">{{ $row['unit'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif
                        </div>{{-- /table-2.1 --}}

                        {{-- Table 3.1 --}}
                        <div class="table-responsive mb-4">
                            <h5 class="mb-3">Table 3.1 By Product Gas</h5>
                            @if($hrcTable31Data->isEmpty())
                            <div class="alert alert-info">
                                <i class="ti ti-info-circle"></i> No data available.
                            </div>
                            @else
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-end">Conversion (TJ/m3)</th>
                                        <th class="text-end">By Product Gas</th>
                                        <th class="text-end">Unit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($hrcTable31Data as $row)
                                    <tr>
                                        <td class="text-end">
                                            {{ number_format((float)$row['conversion_factor'], 9) }}
                                            @if(!empty($row['tooltip']))
                                            <span class="badge badge-sm bg-light text-primary ms-1"
                                                data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true"
                                                data-bs-custom-class="tooltip-wide"
                                                title="{{ $row['tooltip'] }}">i</span>
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format((float)$row['by_product_gas'], 2) }}</td>
                                        <td class="text-end">{{ $row['unit'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif
                        </div>{{-- /table-3.1 --}}

                    </div>
                    {{-- END RIGHT SIDE --}}

                </div>{{-- /row g-4 --}}

                {{-- ================================================================ --}}
                {{-- TABLE 4, 5, 6 — full width --}}
                {{-- ================================================================ --}}
                <div class="col-12">

                    {{-- Table 4 --}}
                    <div class="table-responsive mb-4">
                        <h6 class="mb-3 text-primary">Table 4 – Natural Gas</h6>
                        @if($hrcTable4Data->isEmpty())
                        <div class="alert alert-info">
                            <i class="ti ti-info-circle"></i> Select year &amp; period to see Table 4 data.
                        </div>
                        @else
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Description</th>
                                    <th class="text-end">Natural Gas</th>
                                    <th class="text-end">Unit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($hrcTable4Data as $row)
                                <tr>
                                    <td>
                                        {{ $row['description'] }}
                                        @if(!empty($row['tooltip']))
                                        <span class="badge badge-sm bg-light text-primary ms-1" data-bs-toggle="tooltip"
                                            data-bs-placement="top" data-bs-html="true"
                                            data-bs-custom-class="tooltip-wide" title="{{ $row['tooltip'] }}">i</span>
                                        @endif
                                    </td>
                                    <td class="text-end">{{ number_format((float)$row['natural_gas'], 2) }}</td>
                                    <td class="text-end">{{ $row['unit'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                    </div>{{-- /table-4 --}}

                    {{-- Table 5 --}}
                    <div class="table-responsive mb-4">
                        <h6 class="mb-3 text-primary">Table 5 – Electricity Summary</h6>
                        @if($hrcTable5Data->isEmpty())
                        <div class="alert alert-info">
                            <i class="ti ti-info-circle"></i> Select year &amp; period to see Table 5 data.
                        </div>
                        @else
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Items</th>
                                    <th class="text-end">MWh</th>
                                    <th class="text-end">EF</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($hrcTable5Data as $row)
                                <tr @class(['table-active fw-bold'=> $row['items'] === 'Electricity Total'])>
                                    <td>
                                        {{ $row['items'] }}
                                        @if(!empty($row['tooltip']))
                                        <span class="badge badge-sm bg-light text-primary ms-1" data-bs-toggle="tooltip"
                                            data-bs-placement="top" data-bs-html="true"
                                            data-bs-custom-class="tooltip-wide" title="{{ $row['tooltip'] }}">i</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($row['mwh'] !== null)
                                        {{ number_format((float)$row['mwh'], 2) }}
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($row['ef'] !== null)
                                        {{ number_format((float)$row['ef'], 4) }}
                                        @if(!empty($row['ef_tooltip']))
                                        <span class="badge badge-sm bg-light text-primary ms-1" data-bs-toggle="tooltip"
                                            data-bs-placement="top" data-bs-html="true"
                                            data-bs-custom-class="tooltip-wide"
                                            title="{{ $row['ef_tooltip'] }}">i</span>
                                        @endif
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                    </div>{{-- /table-5 --}}

                    {{-- Table 6 --}}
                    <div class="table-responsive mb-4">
                        <h6 class="mb-3 text-primary">Table 6 – By Product Gas Emission</h6>
                        @if($hrcTable6Data->isEmpty())
                        <div class="alert alert-info">
                            <i class="ti ti-info-circle"></i> Select year &amp; period to see Table 6 data.
                        </div>
                        @else
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-end">Emission Factor (tCO2/TJ)</th>
                                    <th class="text-end">Total Emission</th>
                                    <th class="text-end">Unit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($hrcTable6Data as $row)
                                <tr>
                                    <td class="text-end">
                                        {{ number_format((float)$row['emission_factor'], 1) }}
                                        @if(!empty($row['ef_tooltip']))
                                        <span class="badge badge-sm bg-light text-primary ms-1" data-bs-toggle="tooltip"
                                            data-bs-placement="top" data-bs-html="true"
                                            data-bs-custom-class="tooltip-wide"
                                            title="{{ $row['ef_tooltip'] }}">i</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        {{ number_format((float)$row['total_emission'], 2) }}
                                        @if(!empty($row['em_tooltip']))
                                        <span class="badge badge-sm bg-light text-primary ms-1" data-bs-toggle="tooltip"
                                            data-bs-placement="top" data-bs-html="true"
                                            data-bs-custom-class="tooltip-wide"
                                            title="{{ $row['em_tooltip'] }}">i</span>
                                        @endif
                                    </td>
                                    <td class="text-end">{{ $row['unit'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                    </div>{{-- /table-6 --}}

                </div>{{-- /col full width --}}

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