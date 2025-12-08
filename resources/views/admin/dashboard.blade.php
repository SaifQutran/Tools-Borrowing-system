<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - الإدارة</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    <div class="navbar">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <h2 style="color: var(--white); margin: 0;">نظام استعارة الأدوات - لوحة الإدارة</h2>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <a href="{{ route('admin.dashboard') }}" class="nav-link">الرئيسية</a>
                <a href="{{ route('admin.tools.index') }}" class="nav-link">الأدوات</a>
                <a href="{{ route('admin.loans.index') }}" class="nav-link">الطلبات</a>
                <a href="{{ route('admin.users.index') }}" class="nav-link">المستخدمين</a>
                <a href="{{ route('admin.settings.index') }}" class="nav-link">الإعدادات</a>
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

        <div class="grid grid-4" style="margin-bottom: 2rem;">
            <div class="stat-card">
                <div class="stat-number">{{ $stats['total_tools'] }}</div>
                <div class="stat-label">إجمالي الأدوات</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--secondary) 0%, var(--accent) 100%);">
                <div class="stat-number">{{ $stats['borrowed_tools'] }}</div>
                <div class="stat-label">أدوات مستعارة</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--warning) 0%, #f97316 100%);">
                <div class="stat-number">{{ $stats['pending_requests'] }}</div>
                <div class="stat-label">طلبات معلقة</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--success) 0%, #059669 100%);">
                <div class="stat-number">{{ $stats['total_users'] }}</div>
                <div class="stat-label">إجمالي المستخدمين</div>
            </div>
        </div>

        @if($stats['pending_users'] > 0)
            <div class="alert alert-info mb-3">
                يوجد <strong>{{ $stats['pending_users'] }}</strong> مستخدم في انتظار الموافقة
                <a href="{{ route('admin.users.index') }}?approved=0" style="color: var(--primary); text-decoration: underline; margin-right: 0.5rem;">عرض</a>
            </div>
        @endif

        <div class="card">
            <div class="card-header">الطلبات النشطة</div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>المستخدم</th>
                            <th>الأداة</th>
                            <th>تاريخ الموافقة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($active_loans as $loan)
                            <tr>
                                <td>{{ $loan->user->name }}</td>
                                <td>{{ $loan->tool->name }}</td>
                                <td>{{ $loan->approved_date?->format('Y-m-d H:i') }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.loans.return', $loan) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-success" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                                            تسجيل الإرجاع
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">لا توجد طلبات نشطة</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
