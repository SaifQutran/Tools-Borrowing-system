<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - نظام استعارة الأدوات</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);">
        <div class="card" style="max-width: 450px; width: 100%; margin: 1rem;">
            <div class="card-header text-center" style="border: none;">
                <div style="display: inline-block; margin-bottom: 1rem;">
                    <img src="{{ asset('storage/logo.png') }}" alt="Logo" class="logo-bg-white" style="height: 80px; width: auto;">
                </div>
                <h2>تسجيل الدخول</h2>
                <p style="color: var(--gray-600); font-size: 0.9rem;">نظام استعارة الأدوات والمعدات التعليمية</p>
            </div>

            @if (session('status'))
                <div class="alert alert-info">{{ session('status') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="login_id">الرقم الأكاديمي / الرقم الوظيفي</label>
                    <input id="login_id" class="form-control" type="text" name="login_id" value="{{ old('login_id') }}" required autofocus>
                    @error('login_id')
                        <span style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">كلمة المرور</label>
                    <input id="password" class="form-control" type="password" name="password" required>
                    @error('password')
                        <span style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group" style="display: flex; align-items: center;">
                    <input id="remember" type="checkbox" name="remember" style="margin-left: 0.5rem;">
                    <label for="remember" style="margin: 0; font-weight: normal;">تذكرني</label>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem;">
                    تسجيل الدخول
                </button>

                <div class="text-center">
                    <a href="{{ route('register') }}" style="color: var(--primary); text-decoration: none;">
                        ليس لديك حساب؟ أنشئ حساباً جديداً
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
