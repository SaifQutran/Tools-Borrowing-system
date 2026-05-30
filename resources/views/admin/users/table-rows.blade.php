@forelse($users as $user)
    <tr>
<td>
            {{ $user->name }} - <small style="color: var(--gray-600);">{{ $user->phone }}</small><br>
            
            <small style="color: var(--gray-600);">{{ $user->role == 'student' ? $user->academic_number : $user->employee_number }}</small>
        </td>
        <td>
            @if($user->role == 'student')
                <span class="badge badge-primary">طالب</span>
            @else
                <span class="badge badge-secondary">موظف</span>
            @endif
        </td>
        <td>{{ $user->academic_number ?? $user->employee_number }}</td>
        <td>
            @if($user->role == 'student')
                {{ $user->major?->name }}<br>
                <small style="color: var(--gray-600);">{{ $user->level?->name }}</small>
            @else
                {{ $user->department?->name }}
            @endif
        </td>
        <td>
            <livewire:increase-decrease-allowed-tools :user="$user" wire:key="allowed-tools-{{ $user->id }}" />
        </td>
        <td>
            @if($user->is_approved)
                <span class="badge badge-success">موافق</span>
            @else
                <span class="badge badge-warning">في الانتظار</span>
            @endif
        </td>
        <td>
            @if(!$user->is_approved)
                <form method="POST" action="{{ route('admin.users.approve', $user) }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success" style="padding: 0.5rem 1rem; font-size: 0.875rem;">موافقة</button>
                </form>
                <form method="POST" action="{{ route('admin.users.reject', $user) }}" style="display: inline; margin-right: 0.5rem;">
                    @csrf
                    <button type="submit" class="btn btn-danger" style="padding: 0.5rem 1rem; font-size: 0.875rem;" onclick="return confirm('سيتم حذف المستخدم نهائياً')">رفض</button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="padding: 0.5rem 1rem; font-size: 0.875rem;" onclick="return confirm('هل أنت متأكد؟')">حذف</button>
                </form>
            @endif
        </td>
    </tr>
@empty
    <tr><td colspan="6" class="text-center">لا توجد مستخدمين</td></tr>
@endforelse
