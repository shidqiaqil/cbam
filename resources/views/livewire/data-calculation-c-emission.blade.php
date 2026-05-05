<div>


    {{-- Filter row — identical structure to tab-1 --}}
    <div class="d-flex align-items-center gap-2 px-3 py-2 border-bottom flex-wrap bg-white">
        <div class="d-flex align-items-center gap-1">
            <label class="form-label mb-0 text-muted small fw-semibold text-nowrap">Period:</label>
            <select wire:model.live="periodType" class="form-select form-select-sm" style="min-width:120px;">
                <option value="monthly">Monthly</option>
                <option value="quarterly">Quarterly</option>
                <option value="yearly">Yearly</option>
            </select>
        </div>
        <div class="d-flex align-items-center gap-1">
            <label class="form-label mb-0 text-muted small fw-semibold">Year:</label>
            <select wire:model.live="periodYear" class="form-select form-select-sm" style="min-width:90px;">
                @foreach($yearOptions as $y)
                <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
        </div>
        @if($periodType === 'monthly')
        <div class="d-flex align-items-center gap-1">
            <label class="form-label mb-0 text-muted small fw-semibold">Month:</label>
            <select wire:model.live="period" class="form-select form-select-sm" style="min-width:130px;">
                @foreach($monthOptions as $val => $label)
                <option value="{{ $val }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        @elseif($periodType === 'quarterly')
        <div class="d-flex align-items-center gap-1">
            <label class="form-label mb-0 text-muted small fw-semibold">Quarter:</label>
            <select wire:model.live="period" class="form-select form-select-sm" style="min-width:130px;">
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

    {{-- Action bar — identical structure to tab-1 --}}
    <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom bg-light">
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small">
                <i class="ti ti-table me-1"></i>
                C_Emissions &amp; Energy &mdash; Installation Level Data
            </span>
        </div>
    </div>

    {{-- Table — identical wrapper to tab-1 --}}
    <div class="table-responsive ps-3">
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

                {{-- Row 1: from sheet B_EmInst --}}
                <tr>
                    <td class="fw-medium">
                        from sheet &ldquo;B_EmInst&rdquo;
                        <span class="badge badge-sm bg-light text-primary ms-1" data-bs-toggle="tooltip"
                            data-bs-placement="right" data-bs-html="true" data-bs-custom-class="tooltip-wide"
                            title="{{ $tooltipRow1Label }}">i</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-secondary-lt text-secondary">tCO2e</span>
                    </td>
                    <td class="text-end fw-semibold text-success font-monospace">
                        {{ $directEmissions !== null ? number_format($directEmissions, 3) : '—' }}
                    </td>
                    <td class="text-end font-monospace">
                        <span class="text-muted opacity-50">—</span>
                    </td>
                    <td class="text-end font-monospace">
                        <span class="text-muted opacity-50">—</span>
                    </td>
                </tr>

                {{-- Row 2: manual entries --}}
                <tr class="bg-light">
                    <td class="fw-medium">
                        manual entries
                        <span class="badge badge-sm bg-light text-primary ms-1" data-bs-toggle="tooltip"
                            data-bs-placement="right" data-bs-html="true" data-bs-custom-class="tooltip-wide"
                            title="{{ $tooltipRow2Label }}">i</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-secondary-lt text-secondary">tCO2e</span>
                    </td>
                    <td class="text-end font-monospace">
                        <span class="text-muted opacity-50">—</span>
                    </td>
                    <td class="text-end fw-semibold font-monospace">
                        {{ $indirectEmissions !== null ? number_format($indirectEmissions, 3) : '—' }}
                    </td>
                    <td class="text-end font-monospace">
                        <span class="text-muted opacity-50">—</span>
                    </td>
                </tr>

            </tbody>

            {{-- Total row — identical tfoot to tab-1 --}}
            <tfoot>
                <tr class="table-active fw-bold">
                    <td colspan="2" class="text-end pe-3">Results:</td>

                    {{-- Direct --}}
                    <td class="text-end font-monospace">
                        <div class="d-flex align-items-center justify-content-end gap-1 flex-nowrap">
                            <span class="text-success text-nowrap">
                                {{ $directEmissions !== null ? number_format($directEmissions, 3) : '—' }}
                            </span>
                            @if($directEmissions !== null)
                            <span class="badge badge-sm bg-light text-primary flex-shrink-0" data-bs-toggle="tooltip"
                                data-bs-placement="left" data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                title="{{ $tooltipResultDirect }}">i</span>
                            @endif
                        </div>
                    </td>

                    {{-- Indirect --}}
                    <td class="text-end font-monospace">
                        <div class="d-flex align-items-center justify-content-end gap-1 flex-nowrap">
                            <span class="text-nowrap">
                                {{ $indirectEmissions !== null ? number_format($indirectEmissions, 3) : '—' }}
                            </span>
                            @if($indirectEmissions !== null)
                            <span class="badge badge-sm bg-light text-primary flex-shrink-0" data-bs-toggle="tooltip"
                                data-bs-placement="left" data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                title="{{ $tooltipResultIndirect }}">i</span>
                            @endif
                        </div>
                    </td>

                    {{-- Total --}}
                    <td class="text-end font-monospace">
                        <div class="d-flex align-items-center justify-content-end gap-1 flex-nowrap">
                            <span class="text-primary text-nowrap">
                                {{ $totalEmissions !== null ? number_format($totalEmissions, 3) : '—' }}
                            </span>
                            @if($totalEmissions !== null)
                            <span class="badge badge-sm bg-light text-primary flex-shrink-0" data-bs-toggle="tooltip"
                                data-bs-placement="left" data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                title="{{ $tooltipResultTotal }}">i</span>
                            @endif
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Legend — identical to tab-1 --}}
    <div class="d-flex align-items-center gap-3 px-3 py-2 border-top bg-light">
        <span class="text-muted small fw-semibold">Notes:</span>
        <span class="text-muted small">
            Direct = B_EmInst total CO2e fossil.
            Indirect = ConfigurationData Table 8.
            Total = Direct + Indirect.
        </span>
    </div>

</div>