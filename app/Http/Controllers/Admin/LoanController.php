<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoanRequest;
use App\Models\Tool;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $query = LoanRequest::with(['user', 'tool'])->latest();

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        if ($request->has('borrowed') && $request->borrowed != '') {
            $query->where('status', 'approved');
        }
        if ($request->has('pending') && $request->pending != '') {
            $query->where('status', 'pending');
        }
        
        $loans = $query->paginate(20);
        
        return view('admin.loans.index', compact('loans'));
    }

    public function getData(Request $request)
    {
        $query = LoanRequest::with(['user', 'tool'])->latest();

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $loans = $query->paginate(20);
        
        return response()->json([
            'html' => view('admin.loans.table-rows', compact('loans'))->render(),
            'count' => $loans->total()
        ]);
    }

    public function approve(Request $request, LoanRequest $loan)
    {
        $loan->update([
            'status' => 'approved',
            'approved_date' => now(),
        ]);

        $loan->tool->update(['status' => 'borrowed']);

        return back()->with('success', 'تم الموافقة على الطلب بنجاح');
    }

    public function reject(Request $request, LoanRequest $loan)
    {
        $loan->update([
            'status' => 'rejected',
            'admin_notes' => $request->notes,
        ]);

        return back()->with('success', 'تم رفض الطلب');
    }

    public function return(LoanRequest $loan)
    {
        $loan->update([
            'status' => 'returned',
            'return_date' => now(),
        ]);

        $loan->tool->update(['status' => 'available']);

        return back()->with('success', 'تم تسجيل إرجاع الأداة بنجاح');
    }
}
