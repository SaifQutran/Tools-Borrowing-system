<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tool;
use App\Models\LoanRequest;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_tools' => Tool::count(),
            'borrowed_tools' => Tool::where('status', 'borrowed')->count(),
            'pending_requests' => LoanRequest::where('status', 'pending')->count(),
            'total_users' => User::where('role', '!=', 'admin')->count(),
            'pending_users' => User::where('is_approved', false)->where('role', '!=', 'admin')->count(),
        ];

        $active_loans = LoanRequest::with(['user', 'tool'])
            ->where('status', 'approved')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'active_loans'));
    }
}
