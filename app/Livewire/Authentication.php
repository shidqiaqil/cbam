<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Authentication extends Component
{
    public $id_employee = '';
    public $password = '';

    #[Layout('layouts.app-auth')]
    #[Title('Sign In')]
    public function render()
    {
        return view('livewire.authentication');
    }

    public function signIn()
    {
        $credentials = $this->validate([
            'id_employee' => 'required',
            'password' => 'required',
        ]);

        try {
            if (Auth::attempt($credentials)) {

                $getApiEmployee = Http::get(config('api.employee.employees') . $this->id_employee . '')->object();

                $getUserDetail = User::where('id_employee', $this->id_employee)->first();

                if (($getUserDetail->id_org_unit != $getApiEmployee->id_ou) or ($getUserDetail->id_employee != $getApiEmployee->id_emp)) {
                    $getUserDetail->id_employee = $getApiEmployee->id_emp;
                    $getUserDetail->id_org_unit = $getApiEmployee->id_ou;
                    $getUserDetail->id_department = $getApiEmployee->id_department;
                    $getUserDetail->team = $getApiEmployee->team;
                    $getUserDetail->department = $getApiEmployee->department;
                    $getUserDetail->sso_hash = $getApiEmployee->ssohash;
                    $getUserDetail->save();
                }

                session()->regenerate();

                return redirect()->intended('/');
            } else {
                session()->flash('message_error', 'Login Failed. User not found.');
            }
        } catch (\Exception $e) {
            session()->flash('message_error', 'An error ocurred ' . $e->getMessage());
        }
    }
}
