<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Tool;
use App\Models\ToolType;

class ToolController extends Controller
{
    public function index()
    {
        $query = Tool::with('toolType')
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

        return view('user.tools.index', compact('tools', 'toolTypes'));
    }

    public function show(Tool $tool)
    {
        $tool->load('toolType');
        return view('user.tools.show', compact('tool'));
    }
}
