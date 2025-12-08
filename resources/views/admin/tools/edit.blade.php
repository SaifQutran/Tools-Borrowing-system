<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل الأداة</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('admin.nav')

    <div class="container" style="padding: 2rem 1rem; max-width: 800px;">
        <h1 class="mb-3">تعديل الأداة</h1>

        <div class="card">
            <form method="POST" action="{{ route('admin.tools.update', $tool) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">اسم الأداة</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $tool->name) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">نوع الأداة</label>
                    <select name="tool_type_id" class="form-control" required>
                        @foreach($toolTypes as $type)
                            <option value="{{ $type->id }}" {{ $tool->tool_type_id == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">كود الأداة</label>
                    <input type="text" name="code" class="form-control" value="{{ old('code', $tool->code) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">الحالة</label>
                    <select name="status" class="form-control" required>
                        <option value="available" {{ $tool->status == 'available' ? 'selected' : '' }}>متاح</option>
                        <option value="borrowed" {{ $tool->status == 'borrowed' ? 'selected' : '' }}>مستعار</option>
                    </select>
                </div>

                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary">تحديث</button>
                    <a href="{{ route('admin.tools.index') }}" class="btn btn-outline">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
