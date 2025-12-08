<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Tool;
use App\Models\LoanRequest;

class DashboardController extends Controller
{
    public function index()
    {
        $available_tools = Tool::where('status', 'available')->count();
        
        $my_active_loans = LoanRequest::where('user_id', auth()->id())
            ->where('status', 'approved')
            ->count();

        $my_pending_requests = LoanRequest::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->count();

        $recent_loans = LoanRequest::with('tool')
            ->where('user_id', auth()->id())
            ->latest()
            ->take(5)
            ->get();

        return view('user.dashboard', compact('available_tools', 'my_active_loans', 'my_pending_requests', 'recent_loans'));
    }
}
