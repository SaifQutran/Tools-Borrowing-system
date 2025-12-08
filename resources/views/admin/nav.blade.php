<div class="navbar">
    <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <img  src="{{ asset('storage/logo.png') }}" alt="Logo" class="logo-bg-white" style="height: 50px; width: auto;">
            <h2 style="color: var(--white); margin: 0;">نظام استعارة الأدوات - لوحة الإدارة</h2>
        </div>
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
