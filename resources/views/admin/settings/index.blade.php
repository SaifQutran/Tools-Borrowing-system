<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الإعدادات</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('admin.nav')

    <div class="container" style="padding: 2rem 1rem;">
        <h1 class="mb-3">إعدادات النظام</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="grid grid-2">
            <!-- Majors -->
            <div class="card">
                <div class="card-header">التخصصات</div>
                <form method="POST" action="{{ route('admin.settings.majors.store') }}" class="mb-3">
                    @csrf
                    <div style="display: flex; gap: 0.5rem;">
                        <input type="text" name="name" class="form-control" placeholder="إضافة تخصص جديد" required>
                        <button type="submit" class="btn btn-primary">إضافة</button>
                    </div>
                </form>
                <ul style="list-style: none; padding: 0;">
                    @foreach($majors as $major)
                        <li style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem; border-bottom: 1px solid var(--gray-200);">
                            <span>{{ $major->name }}</span>
                            <form method="POST" action="{{ route('admin.settings.majors.delete', $major) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">حذف</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Levels -->
            <div class="card">
                <div class="card-header">المستويات الدراسية</div>
                <form method="POST" action="{{ route('admin.settings.levels.store') }}" class="mb-3">
                    @csrf
                    <div style="display: flex; gap: 0.5rem;">
                        <input type="text" name="name" class="form-control" placeholder="إضافة مستوى جديد" required>
                        <button type="submit" class="btn btn-primary">إضافة</button>
                    </div>
                </form>
                <ul style="list-style: none; padding: 0;">
                    @foreach($levels as $level)
                        <li style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem; border-bottom: 1px solid var(--gray-200);">
                            <span>{{ $level->name }}</span>
                            <form method="POST" action="{{ route('admin.settings.levels.delete', $level) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">حذف</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Departments -->
            <div class="card">
                <div class="card-header">الأقسام</div>
                <form method="POST" action="{{ route('admin.settings.departments.store') }}" class="mb-3">
                    @csrf
                    <div style="display: flex; gap: 0.5rem;">
                        <input type="text" name="name" class="form-control" placeholder="إضافة قسم جديد" required>
                        <button type="submit" class="btn btn-primary">إضافة</button>
                    </div>
                </form>
                <ul style="list-style: none; padding: 0;">
                    @foreach($departments as $dept)
                        <li style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem; border-bottom: 1px solid var(--gray-200);">
                            <span>{{ $dept->name }}</span>
                            <form method="POST" action="{{ route('admin.settings.departments.delete', $dept) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">حذف</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Tool Types -->
            <div class="card">
                <div class="card-header">أنواع الأدوات</div>
                <form method="POST" action="{{ route('admin.settings.tool-types.store') }}" class="mb-3">
                    @csrf
                    <div style="display: flex; gap: 0.5rem;">
                        <input type="text" name="name" class="form-control" placeholder="إضافة نوع جديد" required>
                        <button type="submit" class="btn btn-primary">إضافة</button>
                    </div>
                </form>
                <ul style="list-style: none; padding: 0;">
                    @foreach($toolTypes as $type)
                        <li style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem; border-bottom: 1px solid var(--gray-200);">
                            <span>{{ $type->name }}</span>
                            <form method="POST" action="{{ route('admin.settings.tool-types.delete', $type) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">حذف</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Halls -->
            <div class="card">
                <div class="card-header">القاعات</div>
                <form method="POST" action="{{ route('admin.settings.halls.store') }}" class="mb-3">
                    @csrf
                    <div style="display: flex; gap: 0.5rem;">
                        <input type="text" name="name" class="form-control" placeholder="إضافة قاعة جديدة" required>
                        <button type="submit" class="btn btn-primary">إضافة</button>
                    </div>
                </form>
                <ul style="list-style: none; padding: 0;">
                    @foreach($halls as $hall)
                        <li style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem; border-bottom: 1px solid var(--gray-200);">
                            <span>{{ $hall->name }}</span>
                            <form method="POST" action="{{ route('admin.settings.halls.delete', $hall) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">حذف</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
