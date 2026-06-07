<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Hall;
use App\Models\LoanDetailKey;
use Illuminate\Support\Facades\Auth;
use App\Models\Tool;
use App\Models\ToolType;

class ToolController extends Controller
{
    public function index()
    {
        
        $whoWillSee = Auth::user()->role == 'student' ? "seen_by_std": "seen_by_emp";
        $query = Tool::with('toolType')
            ->where($whoWillSee,true)
            ->where('status', 'available')
            ->whereDoesntHave('loanRequests', function($q) {
                $q->where('status', 'pending');
            });

        // Add search functionality
        if (request()->has('search') && request('search') != '') {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%');
            });
        }

        $tools = $query->latest()->paginate(12);
        $toolTypes = ToolType::all();

        $loanDetailKeys = LoanDetailKey::all();
        $halls = Hall::all();

        return view('user.tools.index', compact('tools', 'toolTypes', 'loanDetailKeys', 'halls'));
    }

    public function show(Tool $tool)
    {
        $tool->load('toolType');
        $loanDetailKeys = LoanDetailKey::all();
        $halls = Hall::all();

        return view('user.tools.show', compact('tool', 'loanDetailKeys', 'halls'));
    }
}
