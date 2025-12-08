<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلباتي</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    <div class="navbar">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <img src="{{ asset('storage/logo.png') }}" alt="Logo" class="logo-bg-white" style="height: 50px; width: auto;">
                <h2 style="color: var(--white); margin: 0;">نظام استعارة الأدوات</h2>
            </div>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <a href="{{ route('dashboard') }}" class="nav-link">الرئيسية</a>
                <a href="{{ route('tools.index') }}" class="nav-link">الأدوات المتاحة</a>
                <a href="{{ route('loans.my') }}" class="nav-link">طلباتي</a>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="nav-link" style="background: none; border: none; cursor: pointer;">تسجيل الخروج</button>
                </form>
            </div>
        </div>
    </div>

    <div class="container" style="padding: 2rem 1rem;">
        <h1 class="mb-3">طلبات الاستعارة الخاصة بي</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="table-responsive">
                <table class="table">
                <thead>
                    <tr>
                        <th>الأداة</th>
                        <th>تاريخ الطلب</th>
                        <th>الحالة</th>
                        <th>تاريخ الموافقة</th>
                        <th>تاريخ الإرجاع</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $loan)
                        <tr>
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
                            <td>{{ $loan->approved_date?->format('Y-m-d H:i') ?? '-' }}</td>
                            <td>{{ $loan->return_date?->format('Y-m-d H:i') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">ليس لديك أي طلبات</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>

        <div class="mt-3">{{ $loans->links() }}</div>
    </div>
</body>
</html>
