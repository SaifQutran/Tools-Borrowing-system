<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة أداة</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('admin.nav')

    <div class="container" style="padding: 2rem 1rem; max-width: 800px;">
        <h1 class="mb-3">إضافة أداة جديدة</h1>

        <div class="card">
            <form method="POST" action="{{ route('admin.tools.store') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label">اسم الأداة</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    @error('name')<span style="color: var(--danger); font-size: 0.875rem;">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">نوع الأداة</label>
                    <select name="tool_type_id" class="form-control" required>
                        <option value="">اختر النوع</option>
                        @foreach($toolTypes as $type)
                            <option value="{{ $type->id }}" {{ old('tool_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                    @error('tool_type_id')<span style="color: var(--danger); font-size: 0.875rem;">{{ $message }}</span>@enderror
                </div>

                

                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary">حفظ</button>
                    <a href="{{ route('admin.tools.index') }}" class="btn btn-outline">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
