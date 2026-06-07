<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Hall;
use App\Models\LoanDetailKey;
use App\Models\LoanRequest;
use App\Models\Tool;
use Illuminate\Http\Request;

class LoanRequestController extends Controller
{
    public function index()
    {
        $loans = LoanRequest::with('tool')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(20);

        return view('user.loans.index', compact('loans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tool_id' => 'required|exists:tools,id',
            'details' => 'nullable|array',
            'details.*.key_id' => 'required_with:details|exists:loan_detail_keys,id',
            'details.*.value' => 'nullable|string|max:1000',
        ]);

        $tool = Tool::findOrFail($request->tool_id);

        if ($tool->status !== 'available') {
            return back()->with('error', 'هذه الأداة غير متاحة حالياً');
        }
        if(auth()->user()->allowed_tools <= auth()->user()->loanRequests()->whereIn('status', ['pending', 'approved'])->count()) {
            return back()->with('error', 'لقد وصلت إلى الحد الأقصى للأدوات المسموح بها. يرجى إعادة الأدوات الحالية قبل طلب أدوات جديدة.');
        }

        // Check if user already has pending or active loan for this tool
        $existingLoan = LoanRequest::where('user_id', auth()->id())
            ->where('tool_id', $tool->id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($existingLoan) {
            return back()->with('error', 'لديك طلب قائم لهذه الأداة بالفعل');
        }

        LoanRequest::create([
            'user_id' => auth()->id(),
            'tool_id' => $tool->id,
            'status' => 'pending',
            'request_date' => now(),
            'admin_notes' => json_encode($this->formatLoanDetails($request->input('details', [])), JSON_UNESCAPED_UNICODE),
        ]);
        $tool->update(['status' => 'requested']);

        return back()->with('success', 'تم إرسال طلب الاستعارة بنجاح');
    }
    private function formatLoanDetails(array $details): array
    {
        $keys = LoanDetailKey::all()->keyBy('id');
        $halls = Hall::all()->keyBy('id');
        $formatted = [];

        foreach ($details as $detail) {
            $key = $keys->get((int) ($detail['key_id'] ?? 0));
            $value = trim((string) ($detail['value'] ?? ''));

            if (! $key || $value === '') {
                continue;
            }

            if ($key->value_type === 'hall') {
                $value = $halls->get((int) $value)?->name ?? $value;
            }

            $formatted[] = [
                'key' => $key->name,
                'value' => $value,
            ];
        }

        return $formatted;
    }
}
