<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $majors = \App\Models\Major::all();
        $levels = \App\Models\Level::all();
        $departments = \App\Models\Department::all();
        
        return view('auth.register', compact('majors', 'levels', 'departments'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'in:student,staff'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        // Add role-specific validation
        if ($request->role === 'student') {
            $rules['academic_number'] = ['required', 'string', 'unique:users,academic_number'];
            $rules['major_id'] = ['required', 'exists:majors,id'];
            $rules['level_id'] = ['required', 'exists:levels,id'];
        } elseif ($request->role === 'staff') {
            $rules['employee_number'] = ['required', 'string', 'unique:users,employee_number'];
            $rules['department_id'] = ['required', 'exists:departments,id'];
        }

        $validated = $request->validate($rules);

        $user = User::create([
            'name' => $request->name,
            'role' => $request->role,
            'academic_number' => $request->academic_number,
            'employee_number' => $request->employee_number,
            'major_id' => $request->major_id,
            'level_id' => $request->level_id,
            'department_id' => $request->department_id,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_approved' => false, // Needs admin approval
        ]);

        event(new Registered($user));

        // Don't auto-login, redirect to pending approval message
        return redirect()->route('login')->with('status', 'تم إنشاء حسابك بنجاح. يرجى الانتظار حتى يتم الموافقة عليه من قبل الإدارة.');
    }
}
