<div>

    {{-- Filter row — identical structure to other tabs --}}
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

    {{-- ======================================================== --}}
    {{-- SECTION (a) — Total Production Levels --}}
    {{-- ======================================================== --}}

    {{-- Action bar --}}
    <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom bg-light">
        <span class="text-muted small">
            <i class="ti ti-table me-1"></i>
            (a) Total Production Levels
        </span>
    </div>

    <div class="table-responsive ps-3">
        <table class="table table-bordered table-hover align-middle mb-0 small">
            <thead>
                <tr class="bg-primary text-white">
                    <th style="min-width:200px;">Description</th>
                    <th style="min-width:180px;">Production route</th>
                    <th class="text-center" style="width:90px;">Unit</th>
                    <th class="text-end" style="min-width:160px;">Amounts</th>
                </tr>
            </thead>
            <tbody class="bg-white">
                <tr>
                    <td class="fw-medium">Bubble approach</td>
                    <td>Iron or steel products — All production routes</td>
                    <td class="text-center">
                        <span class="badge bg-secondary-lt text-secondary">t</span>
                    </td>
                    <td class="text-end font-monospace">
                        @if($totalProduction !== null)
                        <div class="d-flex align-items-center justify-content-end gap-1 flex-nowrap">
                            <span class="fw-semibold text-success text-nowrap">{{ number_format($totalProduction, 3)
                                }}</span>
                            <span class="badge badge-sm bg-light text-primary flex-shrink-0" data-bs-toggle="tooltip"
                                data-bs-placement="left" data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                title="{{ $tooltipTotalProduction }}">i</span>
                        </div>
                        @else
                        <span class="text-muted opacity-50">—</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- ======================================================== --}}
    {{-- SECTION (g) — Calculation of Attributed Emissions --}}
    {{-- ======================================================== --}}

    {{-- Action bar --}}
    <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom border-top bg-light mt-3">
        <span class="text-muted small">
            <i class="ti ti-table me-1"></i>
            (g) Calculation of the Attributed Emissions
        </span>
    </div>

    <div class="table-responsive ps-3">
        <table class="table table-bordered table-hover align-middle mb-0 small">
            <thead>
                <tr class="bg-primary text-white">
                    <th style="min-width:300px;">Description</th>
                    <th class="text-center" style="width:120px;">Unit</th>
                    <th class="text-end" style="min-width:160px;">Value</th>
                </tr>
            </thead>
            <tbody class="bg-white">
                <tr>
                    <td class="fw-medium">
                        Directly attributable emissions (DirEm*)
                    </td>
                    <td class="text-center">
                        <span class="badge bg-secondary-lt text-secondary">tCO2e</span>
                    </td>
                    <td class="text-end font-monospace">
                        @if($dirEm !== null)
                        <div class="d-flex align-items-center justify-content-end gap-1 flex-nowrap">
                            <span class="fw-semibold text-success text-nowrap">{{ number_format($dirEm, 3) }}</span>
                            <span class="badge badge-sm bg-light text-primary flex-shrink-0" data-bs-toggle="tooltip"
                                data-bs-placement="left" data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                title="{{ $tooltipDirEm }}">i</span>
                        </div>
                        @else
                        <span class="text-muted opacity-50">—</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- ======================================================== --}}
    {{-- SECTION (h) — Import and Export of Measurable Heat --}}
    {{-- ======================================================== --}}

    {{-- Action bar --}}
    <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom border-top bg-light mt-3">
        <span class="text-muted small">
            <i class="ti ti-table me-1"></i>
            (h) Import and Export of Measurable Heat
        </span>
    </div>

    <div class="table-responsive ps-3">
        <table class="table table-bordered table-hover align-middle mb-0 small">
            <thead>
                <tr class="bg-primary text-white">
                    <th style="min-width:260px;">Import and export of measurable heat</th>
                    <th class="text-center" style="width:100px;">Unit</th>
                    <th class="text-end" style="min-width:160px;">Imported</th>
                    <th class="text-end" style="min-width:160px;">Exported</th>
                </tr>
            </thead>
            <tbody class="bg-white">

                {{-- Row 1: Amount of net measurable heat --}}
                <tr>
                    <td class="fw-medium">Amount of net measurable heat</td>
                    <td class="text-center">
                        <span class="badge bg-secondary-lt text-secondary">TJ</span>
                    </td>

                    {{-- Imported --}}
                    <td class="text-end font-monospace">
                        @if($hImportedHeat !== null)
                        <div class="d-flex align-items-center justify-content-end gap-1 flex-nowrap">
                            <span class="fw-semibold text-success text-nowrap">{{ number_format($hImportedHeat, 4)
                                }}</span>
                            <span class="badge badge-sm bg-light text-primary flex-shrink-0" data-bs-toggle="tooltip"
                                data-bs-placement="left" data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                title="{{ $tooltipHImportedHeat }}">i</span>
                        </div>
                        @else
                        <span class="text-muted opacity-50">—</span>
                        @endif
                    </td>

                    {{-- Exported --}}
                    <td class="text-end font-monospace">
                        @if($hExportedHeat !== null)
                        <div class="d-flex align-items-center justify-content-end gap-1 flex-nowrap">
                            <span class="fw-semibold text-nowrap">{{ number_format($hExportedHeat, 4) }}</span>
                            <span class="badge badge-sm bg-light text-primary flex-shrink-0" data-bs-toggle="tooltip"
                                data-bs-placement="left" data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                title="{{ $tooltipHExportedHeat }}">i</span>
                        </div>
                        @else
                        <span class="text-muted opacity-50">—</span>
                        @endif
                    </td>
                </tr>

                {{-- Row 2: Emissions factor --}}
                <tr class="bg-light">
                    <td class="fw-medium">Emissions factor</td>
                    <td class="text-center">
                        <span class="badge bg-secondary-lt text-secondary">tCO2/TJ</span>
                    </td>

                    {{-- Imported EF --}}
                    <td class="text-end font-monospace">
                        @if($hImportedEF !== null)
                        <div class="d-flex align-items-center justify-content-end gap-1 flex-nowrap">
                            <span class="fw-semibold text-success text-nowrap">{{ number_format($hImportedEF, 4)
                                }}</span>
                            <span class="badge badge-sm bg-light text-primary flex-shrink-0" data-bs-toggle="tooltip"
                                data-bs-placement="left" data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                title="{{ $tooltipHImportedEF }}">i</span>
                        </div>
                        @else
                        <span class="text-muted opacity-50">—</span>
                        @endif
                    </td>

                    {{-- Exported EF --}}
                    <td class="text-end font-monospace">
                        @if($hExportedEF !== null)
                        <div class="d-flex align-items-center justify-content-end gap-1 flex-nowrap">
                            <span class="fw-semibold text-nowrap">{{ number_format($hExportedEF, 4) }}</span>
                            <span class="badge badge-sm bg-light text-primary flex-shrink-0" data-bs-toggle="tooltip"
                                data-bs-placement="left" data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                title="{{ $tooltipHExportedEF }}">i</span>
                        </div>
                        @else
                        <span class="text-muted opacity-50">—</span>
                        @endif
                    </td>
                </tr>

            </tbody>
        </table>
    </div>

    {{-- Legend --}}
    <div class="d-flex align-items-center gap-3 px-3 py-2 border-top bg-light mt-0">
        <span class="text-muted small fw-semibold">Notes:</span>
        <span class="text-muted small">
            (a) Production = Coil Product + Plate Product.
            (g) DirEm* = Total Direct Emissions from C_Emissions sheet.
            (h) Steam conversion = 3.18/1000 TJ/Ton. Import EF uses steam EF 0.195 tCO2/Ton. Export EF uses Table 4.1
            Conv[3].
        </span>
    </div>

</div>