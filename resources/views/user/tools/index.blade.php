<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الأدوات المتاحة</title>
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
        <h1 class="mb-3">الأدوات المتاحة</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <!-- Search Bar -->
        <div class="card" style="background: var(--gray-50); margin-bottom: 2rem;">
            <form method="GET" action="{{ route('tools.index') }}">
                <div style="display: flex; gap: 1rem; align-items: end; flex-wrap: wrap;">
                    <div class="form-group" style="margin: 0; flex: 1; min-width: 200px;">
                        <label class="form-label">بحث عن أداة</label>
                        <input type="text" name="search" class="form-control" placeholder="ابحث بالاسم أو الكود..." value="{{ request('search') }}">
                    </div>
                    <button type="submit" class="btn btn-primary">بحث</button>
                    @if(request('search'))
                        <a href="{{ route('tools.index') }}" class="btn btn-outline">إلغاء البحث</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="grid grid-3">
            @forelse($tools as $tool)
                <div class="card">
                    <div style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: var(--white); padding: 1rem; border-radius: 8px 8px 0 0; margin: -1.5rem -1.5rem 1rem -1.5rem;">
                        <h3 style="margin: 0; font-size: 1.25rem;">{{ $tool->name }}</h3>
                        <span class="badge badge-secondary" style="margin-top: 0.5rem; background: rgba(255,255,255,0.2);">{{ $tool->toolType->name }}</span>
                    </div>
                    <p style="color: var(--gray-600); margin-bottom: 1rem;">
                        <strong>الكود:</strong> {{ $tool->code }}
                    </p>
                    @auth
                        
                 
                    <button type="button" onclick='openLoanRequestModal({{ $tool->id }}, @json($tool->name))' class="btn btn-primary" style="width: 100%;">
                        طلب استعارة
                    </button>
                    @else
                        <a href="{{ route('login') }}" class="nav-link">تسجيل الدخول</a>
                    @endauth
                    <a href="{{ route('tool.show', $tool) }}" style="display: block; text-align: center; margin-top: 0.5rem; color: var(--primary); text-decoration: none;">
                        عرض التفاصيل
                    </a>
                </div>
            @empty
                <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
                    @if(request('search'))
                        <p style="font-size: 1.25rem; color: var(--gray-600);">لم يتم العثور على أدوات مطابقة لبحثك</p>
                        <a href="{{ route('tools.index') }}" class="btn btn-primary" style="margin-top: 1rem;">عرض جميع الأدوات</a>
                    @else
                        <p style="font-size: 1.25rem; color: var(--gray-600);">لا توجد أدوات متاحة حالياً</p>
                    @endif
                </div>
            @endforelse
        </div>

        <div class="mt-3">
            {{ $tools->appends(['search' => request('search')])->links() }}
        </div>
    </div>

    @include('user.tools.partials.loan-request-modal')
</body>
</html>
