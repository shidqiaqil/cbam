<div class="page-body">
    <div class="container-xl">

        {{-- ================================================================ --}}
        {{-- FLASH MESSAGE --}}
        {{-- ================================================================ --}}
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
                <div class="card">

                    {{-- ============================================================ --}}
                    {{-- CARD HEADER: Tabs only --}}
                    {{-- ============================================================ --}}
                    <div class="card-header d-block pb-0">

                        {{-- Tab Navigation --}}
                        <ul class="nav nav-tabs card-header-tabs nav-fill" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="#tabs-tab-1" class="nav-link {{ $activeTab === 'tab-1' ? 'active' : '' }}"
                                    data-bs-toggle="tab" wire:click="$set('activeTab', 'tab-1')">B_EmInst</a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-tab-2" class="nav-link {{ $activeTab === 'tab-2' ? 'active' : '' }}"
                                    data-bs-toggle="tab" wire:click="$set('activeTab', 'tab-2')">Tab 2</a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-tab-3" class="nav-link {{ $activeTab === 'tab-3' ? 'active' : '' }}"
                                    data-bs-toggle="tab" wire:click="$set('activeTab', 'tab-3')">Tab 3</a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-tab-4" class="nav-link {{ $activeTab === 'tab-4' ? 'active' : '' }}"
                                    data-bs-toggle="tab" wire:click="$set('activeTab', 'tab-4')">Tab 4</a>
                            </li>
                        </ul>
                    </div>{{-- /card-header --}}

                    <div class="card-body p-0">
                        <div class="tab-content">

                            {{-- ============================================================ --}}
                            {{-- TAB 1: B_EmInst --}}
                            {{-- ============================================================ --}}
                            <div class="tab-pane {{ $activeTab === 'tab-1' ? 'active show' : '' }}" id="tabs-tab-1">
                                <br>
                                <br>

                                {{-- Filter row --}}
                                <div class="d-flex align-items-center gap-2 px-3 py-2 border-bottom flex-wrap bg-white">
                                    {{-- Period Type --}}
                                    <div class="d-flex align-items-center gap-1">
                                        <label
                                            class="form-label mb-0 text-muted small fw-semibold text-nowrap">Period:</label>
                                        <select wire:model.live="periodType" class="form-select form-select-sm"
                                            style="min-width:120px;">
                                            <option value="monthly">Monthly</option>
                                            <option value="quarterly">Quarterly</option>
                                            <option value="yearly">Yearly</option>
                                        </select>
                                    </div>

                                    {{-- Year --}}
                                    <div class="d-flex align-items-center gap-1">
                                        <label class="form-label mb-0 text-muted small fw-semibold">Year:</label>
                                        <select wire:model.live="periodYear" class="form-select form-select-sm"
                                            style="min-width:90px;">
                                            @foreach($yearOptions as $y)
                                            <option value="{{ $y }}">{{ $y }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Period value (month / quarter / hidden for yearly) --}}
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

                                <br>

                                {{-- Action bar above table --}}
                                <div
                                    class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom bg-light">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-muted small">
                                            <i class="ti ti-table me-1"></i>
                                            B_EmInst &mdash; Emission Installation Data
                                        </span>
                                        @if($isEditing)
                                        <span class="badge bg-warning-lt text-warning">
                                            <i class="ti ti-pencil me-1"></i>Editing
                                        </span>
                                        @endif
                                    </div>

                                    <div class="d-flex align-items-center gap-2">
                                        @if(!$isEditing)
                                        <button wire:click="enterEditMode"
                                            class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
                                            <i class="ti ti-pencil"></i>
                                            <span>Edit</span>
                                        </button>
                                        @else
                                        <button wire:click="cancelEdit"
                                            class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
                                            <i class="ti ti-x"></i>
                                            <span>Cancel</span>
                                        </button>
                                        <button wire:click="save"
                                            class="btn btn-sm btn-success d-flex align-items-center gap-1">
                                            <div wire:loading wire:target="save"
                                                class="spinner-border spinner-border-sm me-1"></div>
                                            <i wire:loading.remove wire:target="save" class="ti ti-device-floppy"></i>
                                            <span>Save</span>
                                        </button>
                                        @endif
                                    </div>
                                </div>

                                {{-- Table --}}
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle mb-0 small">
                                        <thead>
                                            <tr class="bg-primary text-white">
                                                <th class="text-center px-2" style="width:36px;">No</th>
                                                <th style="min-width:130px;">Method</th>
                                                <th style="min-width:150px;">Source Stream Name</th>
                                                <th class="text-end" style="min-width:110px;">
                                                    Activity Data (AD)
                                                    <i class="ti ti-calculator ms-1 opacity-75"
                                                        title="Computed from source"></i>
                                                </th>
                                                <th class="text-center" style="width:90px;">AD Unit</th>
                                                <th class="text-end" style="min-width:110px;">
                                                    Net Calorific Value (NCV)
                                                    @if($isEditing)<span class="badge bg-white text-primary ms-1"
                                                        style="font-size:9px;">input</span>@endif
                                                </th>
                                                <th class="text-center" style="width:120px;">NCV Unit</th>
                                                <th class="text-end" style="min-width:110px;">
                                                    Emission Factor (EF)
                                                    @if($isEditing)<span class="badge bg-white text-primary ms-1"
                                                        style="font-size:9px;">input</span>@endif
                                                </th>
                                                <th class="text-center" style="width:120px;">EF Unit</th>
                                                <th class="text-end" style="min-width:110px;">
                                                    Carbon Content
                                                    @if($isEditing)<span class="badge bg-white text-primary ms-1"
                                                        style="font-size:9px;">input</span>@endif
                                                </th>
                                                <th class="text-center" style="width:120px;">C-Content Unit</th>
                                                <th class="text-end" style="min-width:120px;">CO2e Fossil (t)</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white">
                                            @foreach($rows as $index => $row)
                                            @php
                                            $co2eData = $this->computeCo2e($row);
                                            $co2e = $co2eData['value'];
                                            $co2eTooltip = $co2eData['tooltip'];
                                            $methodColor = match(strtolower($row['method'])) {
                                            'combustion' => 'text-orange',
                                            'mass balance' => 'text-blue',
                                            'process emissions', 'process emission' => 'text-green',
                                            default => '',
                                            };
                                            $methodBadge = match(strtolower($row['method'])) {
                                            'combustion' => 'bg-orange-lt text-orange',
                                            'mass balance' => 'bg-blue-lt text-blue',
                                            'process emissions', 'process emission' => 'bg-green-lt text-teal',
                                            default => 'bg-secondary-lt text-secondary',
                                            };
                                            @endphp
                                            <tr class="{{ $index % 2 === 0 ? '' : 'bg-light' }}">
                                                {{-- No --}}
                                                <td class="text-center fw-semibold text-muted px-2">{{ $row['row_order']
                                                    }}</td>

                                                {{-- Method (static) --}}
                                                <td>
                                                    <span class="badge {{ $methodBadge }} fw-normal"
                                                        style="font-size:11px;">
                                                        {{ $row['method'] }}
                                                    </span>
                                                </td>

                                                {{-- Source stream name (static) --}}
                                                <td class="fw-medium">{{ $row['source_stream_name'] }}</td>

                                                {{-- AD Value (formula / computed — read-only) --}}
                                                <td class="text-end font-monospace text-muted">
                                                    @if($row['ad_value'] !== null)
                                                    {{ number_format($row['ad_value'], 2) }}
                                                    @if(!empty($row['ad_tooltip']))
                                                    <span class="badge badge-sm bg-light text-primary ms-1"
                                                        data-bs-toggle="tooltip" data-bs-placement="left"
                                                        data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                                        title="{{ $row['ad_tooltip'] }}">i</span>
                                                    @endif
                                                    @else
                                                    <span class="text-muted opacity-50">—</span>
                                                    @endif
                                                </td>

                                                {{-- AD Unit (static) --}}
                                                <td class="text-center">
                                                    <span class="badge bg-secondary-lt text-secondary">{{
                                                        $row['ad_unit'] }}</span>
                                                </td>

                                                {{-- NCV Value --}}
                                                <td class="text-end p-1">
                                                    @if($isEditing)
                                                    <input type="number" step="any"
                                                        wire:model="rows.{{ $index }}.ncv_value"
                                                        class="form-control form-control-sm text-end font-monospace"
                                                        placeholder="0.000" style="min-width:90px;">
                                                    @else
                                                    <span class="font-monospace">
                                                        {!! $row['ncv_value'] !== null ?
                                                        number_format((float)$row['ncv_value'], 2) : '<span
                                                            class="text-muted opacity-50">—</span>' !!}
                                                    </span>
                                                    @endif
                                                </td>

                                                {{-- NCV Unit --}}
                                                <td class="text-center p-1">
                                                    @if($isEditing)
                                                    <select wire:model="rows.{{ $index }}.ncv_unit"
                                                        class="form-select form-select-sm">
                                                        <option value="">— select —</option>
                                                        @foreach($ncvUnits as $unit)
                                                        <option value="{{ $unit }}">{{ $unit }}</option>
                                                        @endforeach
                                                    </select>
                                                    @else
                                                    @if($row['ncv_unit'])
                                                    <span class="badge bg-secondary-lt text-secondary">{{
                                                        $row['ncv_unit'] }}</span>
                                                    @else
                                                    <span class="text-muted opacity-50">—</span>
                                                    @endif
                                                    @endif
                                                </td>

                                                {{-- EF Value --}}
                                                <td class="text-end p-1">
                                                    @if($isEditing)
                                                    <input type="number" step="any"
                                                        wire:model="rows.{{ $index }}.ef_value"
                                                        class="form-control form-control-sm text-end font-monospace"
                                                        placeholder="0.000" style="min-width:90px;">
                                                    @else
                                                    <span class="font-monospace">
                                                        {!! $row['ef_value'] !== null ?
                                                        number_format((float)$row['ef_value'], 3) : '<span
                                                            class="text-muted opacity-50">—</span>' !!}
                                                    </span>
                                                    @endif
                                                </td>

                                                {{-- EF Unit --}}
                                                <td class="text-center p-1">
                                                    @if($isEditing)
                                                    <select wire:model="rows.{{ $index }}.ef_unit"
                                                        class="form-select form-select-sm">
                                                        <option value="">— select —</option>
                                                        @foreach($efUnits as $unit)
                                                        <option value="{{ $unit }}">{{ $unit }}</option>
                                                        @endforeach
                                                    </select>
                                                    @else
                                                    @if($row['ef_unit'])
                                                    <span class="badge bg-secondary-lt text-secondary">{{
                                                        $row['ef_unit'] }}</span>
                                                    @else
                                                    <span class="text-muted opacity-50">—</span>
                                                    @endif
                                                    @endif
                                                </td>

                                                {{-- Carbon Content --}}
                                                <td class="text-end p-1">
                                                    @if($isEditing)
                                                    <input type="number" step="any"
                                                        wire:model="rows.{{ $index }}.carbon_content"
                                                        class="form-control form-control-sm text-end font-monospace"
                                                        placeholder="0.000" style="min-width:90px;">
                                                    @else
                                                    <span class="font-monospace">
                                                        {!! $row['carbon_content'] !== null ?
                                                        number_format((float)$row['carbon_content'], 4) : '<span
                                                            class="text-muted opacity-50">—</span>' !!}
                                                    </span>
                                                    @endif
                                                </td>

                                                {{-- C-Content Unit --}}
                                                <td class="text-center p-1">
                                                    @if($isEditing)
                                                    <select wire:model="rows.{{ $index }}.c_content_unit"
                                                        class="form-select form-select-sm">
                                                        <option value="">— select —</option>
                                                        @foreach($cContentUnits as $unit)
                                                        <option value="{{ $unit }}">{{ $unit }}</option>
                                                        @endforeach
                                                    </select>
                                                    @else
                                                    @if($row['c_content_unit'])
                                                    <span class="badge bg-secondary-lt text-secondary">{{
                                                        $row['c_content_unit'] }}</span>
                                                    @else
                                                    <span class="text-muted opacity-50">—</span>
                                                    @endif
                                                    @endif
                                                </td>

                                                {{-- CO2e Fossil (computed) --}}
                                                <td class="font-monospace">
                                                    @if($co2e !== null)
                                                    <div
                                                        class="d-flex align-items-center justify-content-end gap-1 flex-nowrap">
                                                        <span class="fw-semibold text-success text-nowrap">{{
                                                            number_format($co2e, 3) }}</span>
                                                        <span class="badge badge-sm bg-light text-primary flex-shrink-0"
                                                            data-bs-toggle="tooltip" data-bs-placement="left"
                                                            data-bs-html="true" data-bs-custom-class="tooltip-wide"
                                                            title="{{ $co2eTooltip }}">i</span>
                                                    </div>
                                                    @else
                                                    <div class="text-end"><span class="text-muted opacity-50">—</span>
                                                    </div>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>

                                        {{-- Total row --}}
                                        <tfoot>
                                            @php
                                            $totalCo2e = collect($rows)->sum(fn($r) => $this->computeCo2e($r)['value']
                                            ?? 0);
                                            @endphp
                                            <tr class="table-active fw-bold">
                                                <td colspan="11" class="text-end pe-3">Total CO2e Fossil (t)</td>
                                                <td class="text-end font-monospace text-success">
                                                    {{ $totalCo2e > 0 ? number_format($totalCo2e, 3) : '—' }}
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                {{-- Legend --}}
                                <div class="d-flex align-items-center gap-3 px-3 py-2 border-top bg-light">
                                    <span class="text-muted small fw-semibold">Method:</span>
                                    <span class="badge bg-orange-lt text-orange fw-normal">Combustion</span>
                                    <span class="badge bg-blue-lt text-blue fw-normal">Mass Balance</span>
                                    <span class="badge bg-green-lt text-teal fw-normal">Process Emissions</span>
                                    {{-- <span class="ms-auto text-muted" style="font-size:11px;">
                                        <i class="ti ti-info-circle me-1"></i>
                                        AD is sourced from input data. CO2e = AD × NCV × EF (or AD × C-Content × 44/12)
                                    </span> --}}
                                </div>

                            </div>{{-- /tab-pane tab-1 --}}

                            {{-- ============================================================ --}}
                            {{-- TAB 2 --}}
                            {{-- ============================================================ --}}
                            <div class="tab-pane {{ $activeTab === 'tab-2' ? 'active show' : '' }}" id="tabs-tab-2">
                                <div class="p-4">
                                    <div class="alert alert-info">
                                        <i class="ti ti-info-circle"></i> Content for Tab 2 will be added here.
                                    </div>
                                </div>
                            </div>

                            {{-- ============================================================ --}}
                            {{-- TAB 3 --}}
                            {{-- ============================================================ --}}
                            <div class="tab-pane {{ $activeTab === 'tab-3' ? 'active show' : '' }}" id="tabs-tab-3">
                                <div class="p-4">
                                    <div class="alert alert-info">
                                        <i class="ti ti-info-circle"></i> Content for Tab 3 will be added here.
                                    </div>
                                </div>
                            </div>

                            {{-- ============================================================ --}}
                            {{-- TAB 4 --}}
                            {{-- ============================================================ --}}
                            <div class="tab-pane {{ $activeTab === 'tab-4' ? 'active show' : '' }}" id="tabs-tab-4">
                                <div class="p-4">
                                    <div class="alert alert-info">
                                        <i class="ti ti-info-circle"></i> Content for Tab 4 will be added here.
                                    </div>
                                </div>
                            </div>

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