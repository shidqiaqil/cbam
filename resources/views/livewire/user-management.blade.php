<div class="page-wrapper">
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        Management
                    </div>
                    <h2 class="page-title">
                        Users
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal"
                            data-bs-target="#add">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 5l0 14" />
                                <path d="M5 12l14 0" />
                            </svg>
                            Add User
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">

            @session('message_success')
            <x-alert-message level="success" message="{{ session('message_success') }}" />
            @endsession

            @session('message_error')
            <x-alert-message level="danger" message="{{ session('message_error') }}" />
            @endsession

            <div class="row row-deck row-cards">

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">List Energy Analysis</h3>
                        </div>
                        <div class="card-body border-bottom py-3">
                            <div class="d-flex">
                                <div class="text-secondary">
                                    Show
                                    <div class="mx-2 d-inline-block">
                                        <select wire:model.live='entries'
                                            class="text-center form-select form-select-sm">
                                            <option value="10">10</option>
                                            <option value="30">30</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                    </div>
                                    entries
                                </div>
                                <div class="ms-auto text-secondary">
                                    Search:
                                    <div class="ms-2 d-inline-block">
                                        <input type="text" wire:model.live='search' class="form-control form-control-sm"
                                            aria-label="Search...">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table card-table table-vcenter text-nowrap datatable">
                                <thead>
                                    <tr>
                                        <th class="w-1">No.</th>
                                        <th>ID Employee</th>
                                        <th>Name</th>
                                        <th>Team</th>
                                        <th>Department</th>
                                        <th>Role</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($getUsers as $item)
                                    <tr>
                                        <td>{{ $loop->index + $getUsers->firstItem() }}.</td>
                                        <td>{{ $item->id_employee }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->team }}</td>
                                        <td>{{ $item->department }}</td>
                                        <td>{{ $item->getRoleNameAttribute() }}</td>
                                        <td class="text-end">
                                            <button type="button" wire:click="setEditUser('{{ $item->id }}')"
                                                class="btn btn-sm btn-outline-primary" title="Edit role">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="18"
                                                    height="18" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path
                                                        d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                    <path
                                                        d="M20.4 6.6a2 2 0 0 0 -2.8 0l-8.6 8.6v3h3l8.6 -8.6a2 2 0 0 0 0 -2.8z" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Data not available</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer d-flex align-items-center">
                            {{ $getUsers->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal modal-blur fade" id="add" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form wire:submit.prevent='save'>
                    <div class="modal-header">
                        <h5 class="modal-title">Add User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if (session('message_success'))
                        <x-alert-message level="success" message="{{ session('message_success') }}" />
                        @endif

                        @if (session('message_error'))
                        <x-alert-message level="danger" message="{{ session('message_error') }}" />
                        @endif

                        @error('email')
                        <x-alert-message level="danger" message="{{ $message }}" />
                        @enderror

                        <div wire:loading wire:target="save" class="alert alert-primary" role="alert">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Loading... </span>
                            </div>
                            Please wait while the data process is ongoing
                        </div>

                        <div class="input-group mb-3">
                            <input type="text" wire:model.live='id_employee' class="form-control"
                                placeholder="Enter ID Employee" name="id_emp" id="id_emp" />

                            <button class="btn btn-success" wire:click='getUserByIdEmployee()'
                                wire:loading.attr='disabled' type="button" id="btn-api">
                                <div wire:loading wire:target='getUserByIdEmployee'>
                                    <span class="spinner-grow spinner-grow-sm" aria-hidden="true"></span>
                                    <span class="status text-white" role="status">Loading...</span>
                                </div>
                                <span wire:loading.remove wire:target='getUserByIdEmployee'>
                                    Get Data
                                </span>
                            </button>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" wire:model='name' class="form-control" placeholder="Enter Name"
                                name="name" id="name" autocomplete="off" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Team</label>
                            <input type="text" wire:model='team' class="form-control" placeholder="Enter team"
                                name="team" id="team" autocomplete="off" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Department</label>
                            <input type="text" wire:model='department' class="form-control"
                                placeholder="Enter department" name="department" id="department" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Division</label>
                            <input type="text" wire:model='division' class="form-control" placeholder="Enter division"
                                name="division" id="division" />
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn me-auto" data-bs-dismiss="modal">Close</button>
                        <button wire:loading.attr="disabled" type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- Edit Role Modal -->
    @if($showEditRoleModal)
    <div class="modal fade show d-block" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <form wire:submit.prevent="updateRole">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit User Role</h5>
                        <button type="button" class="btn-close" wire:click="closeEditRoleModal"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Info user yang diedit --}}
                        <div class="mb-3 p-3 bg-light rounded">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-muted" width="16" height="16"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <circle cx="12" cy="7" r="4" />
                                    <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                </svg>
                                <span class="fw-semibold small">{{ $editUserName }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-muted" width="16" height="16"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <rect x="3" y="7" width="18" height="13" rx="2" />
                                    <path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2" />
                                </svg>
                                <span class="text-muted small">{{ $editUserDepartment }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-muted" width="16" height="16"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <circle cx="12" cy="12" r="9" />
                                    <path d="M12 8v4l3 3" />
                                </svg>
                                <span class="text-muted small">Role saat ini: <span class="badge bg-blue-lt">{{
                                        $editUserCurrentRole }}</span></span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role Baru</label>
                            <select wire:model="selectedRole" class="form-select">
                                <option value="">-- Select Role --</option>
                                @foreach($roles as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                            @error('selectedRole') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn me-auto" wire:click="closeEditRoleModal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" wire:click="closeEditRoleModal"></div>
    @endif
    @include('layouts.partials.footer')
</div>

@push('style')
<link rel="stylesheet" href="{{ asset('dist/css/jquery-ui.css') }}">
<style>
    .ui-widget-content {
        z-index: 9999 !important;
    }
</style>
@endpush

@push('script')
<script src="{{ asset('dist/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('dist/js/jquery-ui.js') }}"></script>
<script type="text/javascript">
    var api = "{{ config('api.employee.search_name') }}";

        $("#name" ).autocomplete({
            source: function( request, response ) {
              $.ajax({
                url: api,
                type: 'GET',
                dataType: "json",
                data: {
                   search: request.term
                },
                success: function( data ) {
                   response( data );
                }
              });
            },
            select: function (event, ui) {
               $('#name').val(ui.item.label);
               $('#team').val(ui.item.orgunit.org_unit_name);
               $("#id_emp").val(ui.item.id_emp);
               $("#department").val(ui.item.department.department_name);
               $("#division").val(ui.item.division.division_name);

               @this.set('id_employee', ui.item.id_emp);
               @this.set('team', ui.item.orgunit.org_unit_name);
               @this.set('department', ui.item.department.department_name);
               @this.set('division', ui.item.division.division_name);

               return false;
            }
          });
    
</script>
@endpush