<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class SsoController extends Controller
{
    public function sso()
    {
        Auth::logout();
        $getId = explode('$', request('ses'));

        $checkAccessLogin = Http::get(config('api.employee.compact_hash') . $getId[1] . '')->object();

        $getUser = User::firstWhere('id_employee', $checkAccessLogin->id_emp);
        if ($getUser) {

            if (Auth::loginUsingId($getUser->id)) {

                $employee = Http::get(config('api.employee.compact_hash') . $getId[1] . '')->object();

                if (($getUser->id_org_unit != $employee->id_org_unit) or ($getUser->id_emp != $employee->id_emp)) {
                    $getUser->id_employee = $employee->id_emp;
                    $getUser->id_org_unit = $employee->id_org_unit;
                    $getUser->id_department = $employee->id_department;
                    $getUser->team = $employee->team;
                    $getUser->department = $employee->department;
                    $getUser->save();
                }

                return redirect()->intended('/');
            } else {
                return back()->with('message_error', 'SSO failed, user not found');
            }
        } else {

            $employee = Http::get(config('api.employee.compact_hash') . $getId[1] . '')->object();
            $role =  User::ROLE_USER;

            $user = User::create([
                'name' => $employee->name,
                'email' => $employee->email,
                'password' => Hash::make($employee->id_emp),
                'id_employee' => $employee->id_emp,
                'id_org_unit' => $employee->id_org_unit,
                'id_department' => $employee->id_department,
                'id_division' => $employee->id_division,
                'id_job_position' => $employee->id_job_position,
                'job_position' => $employee->jobposition,
                'team' => $employee->team,
                'department' => $employee->department,
                'division' => $employee->division,
                'role' => $role,
                'sso_hash' => $employee->ssohash,
            ]);

            if (Auth::loginUsingId($user->id)) {
                return redirect()->intended('/');
            } else {
                return back()->with('loginError', 'SSO failed, user not found');
            }
            return redirect()->route('login')->with('message_error', 'SSO failed, user not found, you dont have permission to access this application.');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('message_success', 'Successfully Logout');
    }
}
