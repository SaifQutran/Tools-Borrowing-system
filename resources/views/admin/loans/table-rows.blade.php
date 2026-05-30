@forelse($loans as $loan)
    <tr>
        <td>
            {{ $loan->user->name }} - <small style="color: var(--gray-600);">{{ $loan->user->phone }}</small><br>
            
            <small style="color: var(--gray-600);">{{ $loan->user->role == 'student' ? $loan->user->academic_number : $loan->user->employee_number }}</small>
        </td>
        <td>{{ $loan->tool->name }}</td>
        <td>{{ $loan->request_date->format('Y-m-d H:i') }}</td>
        <td>
            @if($loan->status == 'pending')
                <span class="badge badge-warning">قيد الانتظار</span>
            @elseif($loan->status == 'approved')
                <span class="badge badge-success">موافق عليه</span>
            @elseif($loan->status == 'rejected')
                <span class="badge badge-danger">مرفوض</span>
            @else
                <span class="badge badge-secondary">تم الإرجاع</span>
            @endif
        </td>
        <td>
            @if($loan->status == 'pending')
                <form method="POST" action="{{ route('admin.loans.approve', $loan) }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success" style="padding: 0.5rem 1rem; font-size: 0.875rem;">موافقة</button>
                </form>
                <form method="POST" action="{{ route('admin.loans.reject', $loan) }}" style="display: inline; margin-right: 0.5rem;">
                    @csrf
                    <button type="submit" class="btn btn-danger" style="padding: 0.5rem 1rem; font-size: 0.875rem;">رفض</button>
                </form>
            @elseif($loan->status == 'approved')
                <form method="POST" action="{{ route('admin.loans.return', $loan) }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">تسجيل الإرجاع</button>
                </form>
            @else
                -
            @endif
        </td>
    </tr>
@empty
    <tr><td colspan="5" class="text-center">لا توجد طلبات</td></tr>
@endforelse
