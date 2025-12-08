<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tool->name }}</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    <div class="navbar">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <h2 style="color: var(--white); margin: 0;">نظام استعارة الأدوات</h2>
            @auth
                <div style="display: flex; gap: 1rem;">
                    <a href="{{ route('dashboard') }}" class="nav-link">العودة للوحة التحكم</a>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="nav-link" style="background: none; border: none; cursor: pointer;">تسجيل الخروج</button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="nav-link">تسجيل الدخول</a>
            @endauth
        </div>
    </div>

    <div class="container" style="padding: 2rem 1rem; max-width: 800px;">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: var(--white); padding: 2rem; border-radius: 12px 12px 0 0; margin: -1.5rem -1.5rem 2rem -1.5rem; text-align: center;">
                <h1 style="margin: 0; margin-bottom: 0.5rem;">{{ $tool->name }}</h1>
                <span class="badge badge-secondary" style="background: rgba(255,255,255,0.2); font-size: 1rem;">{{ $tool->toolType->name }}</span>
            </div>

            <div class="form-group">
                <strong>كود الأداة:</strong> {{ $tool->code }}
            </div>

            <div class="form-group">
                <strong>الحالة:</strong>
                @if($tool->status == 'available')
                    <span class="badge badge-success">متاح</span>
                @else
                    <span class="badge badge-danger">مستعار</span>
                @endif
            </div>

            @if($tool->attributes)
                <div class="form-group">
                    <strong>تفاصيل إضافية:</strong>
                    <ul style="margin-top: 0.5rem; padding-right: 1.5rem;">
                        @foreach($tool->attributes as $key => $value)
                            <li>{{ $key }}: {{ $value }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @auth
                @if($tool->status == 'available')
                    <form method="POST" action="{{ route('loan.request') }}" class="mt-3">
                        @csrf
                        <input type="hidden" name="tool_id" value="{{ $tool->id }}">
                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem;">
                            طلب استعارة هذه الأداة
                        </button>
                    </form>
                @else
                    <div class="alert alert-error mt-3">
                        هذه الأداة غير متاحة حالياً (مستعارة)
                    </div>
                @endif
            @else
                <div class="alert alert-info mt-3">
                    يجب <a href="{{ route('login') }}" style="color: var(--primary); text-decoration: underline;">تسجيل الدخول</a> لطلب استعارة هذه الأداة
                </div>
            @endauth
        </div>
    </div>
</body>
</html>
