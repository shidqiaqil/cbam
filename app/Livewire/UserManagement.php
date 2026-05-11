<?php

namespace App\Livewire;

use App\Models\User as ModelsUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    public $entries = '10';

    public $search = '';

    #[Validate('required|max:6')]
    public $id_employee = '';

    #[Validate('unique:users,email')]
    public $email = '';

    public $name = '';

    public $team = '';

    public $department = '';

    public $division = '';

    public $id_org_unit = '';

    public $id_department = '';

    public $id_division = '';

    public $id_job_position = '';

    public $job_position = '';

    public $role = '';

    public $password = '';

    public $sso_hash = '';

    // selected user for edit
    public $selectedUserId = null;
    public $selectedRole = null;
    public $showEditRoleModal = false;
    public $editUserName = '';
    public $editUserDepartment = '';
    public $editUserCurrentRole = '';

    #[Layout('layouts.app')]
    #[Title('Management Users')]
    public function render()
    {
        $getUsers = ModelsUser::when($this->search, function ($query) {
            $query->whereAny(['id_employee', 'name'], 'LIKE', '%' . $this->search . '%');
        })
            ->paginate($this->entries);

        $roles = ModelsUser::roles();

        return view('livewire.user-management', compact('getUsers', 'roles'));
    }

    private function resetInput()
    {
        $this->email = '';
        $this->name = '';
        $this->team = '';
        $this->department = '';
        $this->division = '';
        $this->id_org_unit = '';
        $this->id_department = '';
        $this->id_division = '';
        $this->id_job_position = '';
        $this->job_position = '';
        $this->role = '';
        $this->sso_hash = '';
    }

    public function getUserByIdEmployee()
    {
        $getApiEmployee = Http::get(config('api.employee.employees') . $this->id_employee)->object();

        $this->name = $getApiEmployee->name;
        $this->email = $getApiEmployee->email;
        $this->team = $getApiEmployee->team;
        $this->department = $getApiEmployee->department;
        $this->division = $getApiEmployee->division;
        $this->id_org_unit = $getApiEmployee->id_ou;
        $this->id_department = $getApiEmployee->id_department;
        $this->id_division = $getApiEmployee->id_division;
        $this->id_job_position = $getApiEmployee->id_job_position;
        $this->job_position = $getApiEmployee->jobposition;
        $this->sso_hash = $getApiEmployee->ssohash;
    }

    public function save()
    {
        $this->validate();

        DB::beginTransaction();

        try {

            $this->getUserByIdEmployee();

            ModelsUser::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->id_employee),
                'id_employee' => $this->id_employee,
                'id_org_unit' => $this->id_org_unit,
                'id_department' => $this->id_department,
                'id_division' => $this->id_division,
                'id_job_position' => $this->id_job_position,
                'job_position' => $this->job_position,
                'team' => $this->team,
                'department' => $this->department,
                'division' => $this->division,
                'role' => 1,
                'sso_hash' => $this->sso_hash,
            ]);

            session()->flash('message_success', 'New User Added Successfully.');
            $this->resetInput();

            DB::commit();
        } catch (\Exception $e) {
            session()->flash('message_error', $e->getMessage());

            DB::rollBack();
        }
    }

    // =====================
    // Role editing
    // =====================

    #[On('setEditUser')]
    public function setEditUser($userId)
    {
        $user = ModelsUser::find($userId);
        if (! $user) return;

        $this->selectedUserId = $user->id;
        $this->selectedRole = $user->role;
        $this->editUserName = $user->name;
        $this->editUserDepartment = $user->department;
        $this->editUserCurrentRole = $user->getRoleNameAttribute();
        $this->showEditRoleModal = true;
    }

    public function closeEditRoleModal()
    {
        $this->showEditRoleModal = false;
        $this->selectedUserId = null;
        $this->selectedRole = null;
        $this->editUserName = '';
        $this->editUserDepartment = '';
        $this->editUserCurrentRole = '';
    }

    #[On('selectedRole')]
    public function setSelectedRole($role)
    {
        $this->selectedRole = $role;
    }

    public function updateRole()
    {
        $userId = $this->selectedUserId;
        $role = $this->selectedRole;


        $validKeys = array_keys(ModelsUser::roles());
        if (! in_array($role, $validKeys)) {
            session()->flash('message_error', 'Invalid role selected');
            return;
        }

        $user = ModelsUser::find($userId);
        if (! $user) {
            session()->flash('message_error', 'User not found');
            return;
        }

        $user->role = $role;
        $user->save();

        session()->flash('message_success', 'User role updated successfully.');

        // reset & tutup modal
        $this->closeEditRoleModal();
    }
}
