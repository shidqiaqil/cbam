<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">CHP - Power Plant</h3>
                    </div>
                    <div class="card-body">
                        {{-- FILTER --}}
                        <div class="mb-4 filter-section">
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
                                    <select wire:model.live="period" class="form-select" {{ empty($periodYear)
                                        ? 'disabled' : '' }}>
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
                            {{-- Left: Fuel Input Quantity --}}
                            <div class="col-lg-6">
                                <div class="table-responsive mb-4">
                                    <h5 class="mb-3">Table 1 Fuel Input (Quantity)</h5>
                                    @if($chpTableData->isEmpty())
                                    <div class="alert alert-info">
                                        <i class="ti ti-info-circle"></i> Select year & period or <a
                                            href="/uploadfile">Upload Energy Data</a>.
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
                                            @foreach($chpTableData as $row)
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
                                                <td class="text-end">{{ number_format($row['quantity'], 2) }}</td>
                                                <td class="text-end">Nm³ or ton</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @endif
                                </div>

                                {{-- Table 2 Steam Output --}}
                                <div class="table-responsive mb-4">
                                    <h5 class="mb-3">Table 2 Steam Output (Quantity)</h5>
                                    @if($steamTableData->isEmpty())
                                    <div class="alert alert-info">
                                        <i class="ti ti-info-circle"></i> Select year & period to see Steam Output data.
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
                                            @foreach($steamTableData as $row)
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
                                                <td class="text-end">{{ number_format($row['quantity'], 2) }}</td>
                                                <td class="text-end">ton</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @endif
                                </div>

                                {{-- Table 3 Electricity Output --}}
                                <div class="table-responsive">
                                    <h5 class="mb-3">Table 3 Electricity Output</h5>
                                    @if($electricityTableData->isEmpty() || $electricityTableData[0]['quantity'] == 0)
                                    <div class="alert alert-info">
                                        <i class="ti ti-info-circle"></i> Select year & period to see Electricity Output
                                        data.
                                    </div>
                                    @else
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Description</th>
                                                <th>Source</th>
                                                <th>Unit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($electricityTableData as $row)
                                            <tr>
                                                <td>{{ $row['description'] }}</td>
                                                <td>
                                                    {{ number_format($row['quantity'], 0) }}
                                                    @if(!empty($row['tooltip']))
                                                    <span class="badge badge-sm bg-light text-primary ms-1"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                                        title="{{ $row['tooltip'] }}">i</span>
                                                    @endif
                                                </td>
                                                <td>kWh</td>
                                            </tr>
                                            <tr>
                                                <td>{{ $row['description'] }} / 1000</td>
                                                <td class="text-end">{{ number_format($row['quantity_mwh'], 2) }}
                                                    @if(isset($row['tooltip_mwh']))
                                                    <span class="badge badge-sm bg-light text-primary ms-1"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                                        title="{{ $row['tooltip_mwh'] }}">i</span>
                                                    @endif
                                                </td>
                                                <td>MWh</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @endif
                                </div>
                            </div>

                            {{-- Right column start --}}
                            <div class="col-lg-6">
                                {{-- Right: Fuel Input Emission --}}
                                <div class="table-responsive mb-4">
                                    <h5 class="mb-3">Table 1 Fuel Input Emission</h5>
                                    @if($emissionTableData->isEmpty())
                                    <div class="alert alert-info">
                                        <i class="ti ti-info-circle"></i> No data available.
                                    </div>
                                    @else
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Conversion</th>
                                                <th>By Product Gas (Tj)</th>
                                                <th>Unit (Tj)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($emissionTableData as $index => $row)
                                            <tr>
                                                <td>{{ $row['conversion'] === 'Total' ? 'Total' :
                                                    number_format($row['conversion'], 10) }}</td>
                                                <td class="text-end">{{ number_format($row['tj'], 2) }}</td>
                                                <td>Tj</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @endif
                                </div>

                                {{-- Table 2.2 Steam Output Conversion --}}
                                <div class="table-responsive mb-4">
                                    <h5 class="mb-3">Table 2.2 Steam Output Conversion</h5>
                                    @if($steamConversionTableData->isEmpty() || empty($steamTableData))
                                    <div class="alert alert-info">
                                        <i class="ti ti-info-circle"></i> Select year & period to see Steam Conversion
                                        data.
                                    </div>
                                    @else
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Conversion</th>
                                                <th>Steam</th>
                                                <th>Unit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($steamConversionTableData as $row)
                                            <tr>
                                                <td>{{ $row['conversion'] }}</td>
                                                <td class="text-end">
                                                    {{ is_numeric($row['steam']) ? number_format($row['steam'],
                                                    $row['unit'] === 'tCO2/Tj' ? 4 :
                                                    ($row['unit'] === 'tCo2/ton' ? 3 :
                                                    ($row['unit'] === 'Tj' ? 2 : 2))) : $row['steam'] }}
                                                    @if(!empty($row['tooltip']))
                                                    <span class="badge badge-sm bg-light text-primary ms-1"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                                        title="{{ $row['tooltip'] }}">i</span>
                                                    @endif
                                                </td>
                                                <td>{{ $row['unit'] }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @endif
                                </div>

                                {{-- Table 3.3 Electricity Output Conversion --}}
                                <div class="table-responsive mb-4">
                                    <h5 class="mb-3">Table 3.3 Steam Output Conversion</h5>
                                    @if($electricityConversionTableData->isEmpty() || empty($electricityTableData))
                                    <div class="alert alert-info">
                                        <i class="ti ti-info-circle"></i> Select year & period to see Electricity
                                        Conversion data.
                                    </div>
                                    @else
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Conversion</th>
                                                <th>Electricity</th>
                                                <th>Unit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($electricityConversionTableData as $row)
                                            <tr>
                                                <td>{{ $row['conversion'] }}</td>
                                                <td class="text-end">{{ number_format($row['electricity'], 1) }}
                                                    @if(!empty($row['tooltip']))
                                                    <span class="badge badge-sm bg-light text-primary ms-1"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                                        title="{{ $row['tooltip'] }}">i</span>
                                                    @endif
                                                </td>
                                                <td>{{ $row['unit'] }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:navigated', () => initTooltips());
    document.addEventListener('livewire:updated', () => initTooltips());

    function initTooltips() {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            bootstrap.Tooltip.getOrCreateInstance(el, { boundary: 'window', html: true });
        });
    }
    initTooltips();
    </script>
    @endpush