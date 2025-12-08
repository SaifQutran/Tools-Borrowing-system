<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم</title>
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
                <span style="color: var(--white);">مرحباً، {{ auth()->user()->name }}</span>
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
        <h1 class="mb-3">لوحة التحكم</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <div class="grid grid-3" style="margin-bottom: 2rem;">
            <div class="stat-card">
                <div class="stat-number">{{ $available_tools }}</div>
                <div class="stat-label">أدوات متاحة</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--secondary) 0%, var(--accent) 100%);">
                <div class="stat-number">{{ $my_active_loans }}</div>
                <div class="stat-label">استعاراتي النشطة</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--warning) 0%, #f97316 100%);">
                <div class="stat-number">{{ $my_pending_requests }}</div>
                <div class="stat-label">طلبات قيد الانتظار</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">آخر طلباتي</div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>الأداة</th>
                            <th>تاريخ الطلب</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_loans as $loan)
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">لا توجد طلبات</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="text-center mt-3">
            <a href="{{ route('tools.index') }}" class="btn btn-primary">تصفح الأدوات المتاحة</a>
        </div>
    </div>
</body>
</html>
