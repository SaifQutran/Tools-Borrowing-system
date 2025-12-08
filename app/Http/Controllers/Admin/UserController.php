<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', '!=', 'admin')->with(['major', 'level', 'department']);

        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        if ($request->has('approved') && $request->approved != '') {
            $query->where('is_approved', $request->approved == '1');
        }

        $users = $query->latest()->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function getData(Request $request)
    {
        $query = User::where('role', '!=', 'admin')->with(['major', 'level', 'department']);

        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        if ($request->has('approved') && $request->approved != '') {
            $query->where('is_approved', $request->approved == '1');
        }

        $users = $query->latest()->paginate(20);

        return response()->json([
            'html' => view('admin.users.table-rows', compact('users'))->render(),
            'count' => $users->total()
        ]);
    }

    public function approve(User $user)
    {
        $user->update(['is_approved' => true]);
        return back()->with('success', 'تم الموافقة على المستخدم بنجاح');
    }

    public function reject(User $user)
    {
        $user->delete();
        return back()->with('success', 'تم رفض المستخدم وحذف حسابه');
    }

    public function destroy(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'لا يمكن حذف حساب المسؤول');
        }

        $user->delete();
        return back()->with('success', 'تم حذف المستخدم بنجاح');
    }
}
