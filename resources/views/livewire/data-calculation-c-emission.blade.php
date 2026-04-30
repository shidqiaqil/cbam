<div class="page-body">
    <div class="container-xl">

        {{-- FLASH MESSAGE --}}
        @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible mb-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="ti ti-circle-check me-2 fs-4"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row row-cards">
            <div class="col-md-12">

                {{-- CARD (FLAT STYLE) --}}
                <div class="card border-0 shadow-none">

                    {{-- FILTER BAR --}}
                    <div class="d-flex align-items-center gap-2 px-0 py-2 border-bottom flex-wrap bg-white">

                        <div class="d-flex align-items-center gap-0">
                            <label class="form-label mb-0 text-muted small fw-semibold">Period:</label>
                            <select wire:model.live="periodType" class="form-select form-select-sm"
                                style="min-width:120px;">
                                <option value="monthly">Monthly</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                        </div>

                        <div class="d-flex align-items-center gap-1">
                            <label class="form-label mb-0 text-muted small fw-semibold">Year:</label>
                            <select wire:model.live="periodYear" class="form-select form-select-sm"
                                style="min-width:90px;">
                                @foreach($yearOptions as $y)
                                <option value="{{ $y }}">{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if($periodType === 'monthly')
                        <div class="d-flex align-items-center gap-1">
                            <label class="form-label mb-0 text-muted small fw-semibold">Month:</label>
                            <select wire:model.live="period" class="form-select form-select-sm"
                                style="min-width:130px;">
                                @foreach($monthOptions as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        @elseif($periodType === 'quarterly')
                        <div class="d-flex align-items-center gap-1">
                            <label class="form-label mb-0 text-muted small fw-semibold">Quarter:</label>
                            <select wire:model.live="period" class="form-select form-select-sm"
                                style="min-width:130px;">
                                @foreach($quarterOptions as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="ms-auto">
                            <span class="badge bg-blue-lt text-blue fw-semibold px-3 py-2">
                                <i class="ti ti-calendar me-1"></i>
                                @if($periodType === 'monthly')
                                {{ $monthOptions[$period] ?? $period }} {{ $periodYear }}
                                @elseif($periodType === 'quarterly')
                                {{ $quarterOptions[$period] ?? $period }} {{ $periodYear }}
                                @else
                                Annual {{ $periodYear }}
                                @endif
                            </span>
                        </div>
                    </div>

                    {{-- ACTION BAR --}}
                    <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom bg-light">
                        <span class="text-muted small">
                            <i class="ti ti-table me-1"></i>
                            C_Emissions Summary
                        </span>
                    </div>

                    {{-- TABLE --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0 small">

                            <thead>
                                <tr class="bg-primary text-white">
                                    <th style="min-width:250px;">Installation level data</th>
                                    <th class="text-center" style="width:120px;">Unit</th>
                                    <th class="text-end" style="min-width:160px;">Total direct emissions</th>
                                    <th class="text-end" style="min-width:160px;">Total indirect emissions</th>
                                    <th class="text-end" style="min-width:160px;">Total emissions</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white">

                                {{-- DIRECT --}}
                                <tr>
                                    <td class="fw-medium">
                                        from sheet "B_EmInst"
                                        <span class="badge badge-sm bg-light text-primary ms-1" data-bs-toggle="tooltip"
                                            data-bs-html="true"
                                            title='<strong>Source:</strong><br>Sum of CO2e Fossil (t) from Tab 1'>
                                            i
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary-lt text-secondary">tCO2e</span>
                                    </td>
                                    <td class="text-end fw-semibold text-success font-monospace">
                                        {{ $directEmissions ? number_format($directEmissions, 3) : '—' }}
                                    </td>
                                    <td class="text-end text-muted">—</td>
                                    <td class="text-end text-muted">—</td>
                                </tr>

                                {{-- INDIRECT --}}
                                <tr class="bg-light">
                                    <td class="fw-medium">
                                        manual entries
                                        <span class="badge badge-sm bg-light text-primary ms-1" data-bs-toggle="tooltip"
                                            data-bs-html="true"
                                            title='<strong>Source:</strong><br>ConfigurationData → Table 8'>
                                            i
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary-lt text-secondary">tCO2e</span>
                                    </td>
                                    <td class="text-end text-muted">—</td>
                                    <td class="text-end fw-semibold text-success font-monospace">
                                        {{ $indirectEmissions ? number_format($indirectEmissions, 3) : '—' }}
                                    </td>
                                    <td class="text-end text-muted">—</td>
                                </tr>

                            </tbody>

                            {{-- TOTAL --}}
                            <tfoot>
                                <tr class="table-active fw-bold">
                                    <td colspan="2" class="text-end pe-3">Total Emissions (tCO2e)</td>
                                    <td class="text-end font-monospace text-success">
                                        {{ $directEmissions ? number_format($directEmissions, 3) : '—' }}
                                    </td>
                                    <td class="text-end font-monospace text-success">
                                        {{ $indirectEmissions ? number_format($indirectEmissions, 3) : '—' }}
                                    </td>
                                    <td class="text-end font-monospace text-success">
                                        {{ $totalEmissions ? number_format($totalEmissions, 3) : '—' }}
                                    </td>
                                </tr>
                            </tfoot>

                        </table>
                    </div>

                    {{-- LEGEND --}}
                    <div class="d-flex align-items-center gap-3 px-3 py-2 border-top bg-light">
                        <span class="text-muted small fw-semibold">Notes:</span>
                        <span class="text-muted small">
                            Direct = B_EmInst total. Indirect = ConfigurationData Table 8.
                        </span>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

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