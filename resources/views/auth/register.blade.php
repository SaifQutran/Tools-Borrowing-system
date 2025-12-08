<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب - نظام استعارة الأدوات</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); padding: 2rem 0;">
        <div class="card" style="max-width: 600px; width: 100%; margin: 1rem;">
            <div class="card-header text-center" style="border: none;">
                <div style="display: inline-block; margin-bottom: 1rem;">
                    <img src="{{ asset('storage/logo.png') }}" alt="Logo" class="logo-bg-white" style="height: 80px; width: auto;">
                </div>
                <h2>إنشاء حساب جديد</h2>
            </div>

            <form method="POST" action="{{ route('register') }}" id="registerForm">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="role">نوع الحساب</label>
                    <select id="role" class="form-control" name="role" required onchange="toggleFields()">
                        <option value="">اختر نوع الحساب</option>
                        <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>طالب</option>
                        <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>موظف</option>
                    </select>
                    @error('role')
                        <span style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="name">الاسم الكامل</label>
                    <input id="name" class="form-control" type="text" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <span style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Student Fields -->
                <div id="studentFields" style="display: none;">
                    <div class="form-group">
                        <label class="form-label" for="academic_number">الرقم الأكاديمي</label>
                        <input id="academic_number" class="form-control" type="text" name="academic_number" value="{{ old('academic_number') }}">
                        @error('academic_number')
                            <span style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="major_id">التخصص</label>
                        <select id="major_id" class="form-control" name="major_id">
                            <option value="">اختر التخصص</option>
                            @foreach($majors as $major)
                                <option value="{{ $major->id }}" {{ old('major_id') == $major->id ? 'selected' : '' }}>{{ $major->name }}</option>
                            @endforeach
                        </select>
                        @error('major_id')
                            <span style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="level_id">المستوى الدراسي</label>
                        <select id="level_id" class="form-control" name="level_id">
                            <option value="">اختر المستوى</option>
                            @foreach($levels as $level)
                                <option value="{{ $level->id }}" {{ old('level_id') == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
                            @endforeach
                        </select>
                        @error('level_id')
                            <span style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Staff Fields -->
                <div id="staffFields" style="display: none;">
                    <div class="form-group">
                        <label class="form-label" for="employee_number">الرقم الوظيفي</label>
                        <input id="employee_number" class="form-control" type="text" name="employee_number" value="{{ old('employee_number') }}">
                        @error('employee_number')
                            <span style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="department_id">القسم</label>
                        <select id="department_id" class="form-control" name="department_id">
                            <option value="">اختر القسم</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <span style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">رقم الجوال</label>
                    <input id="phone" class="form-control" type="text" name="phone" value="{{ old('phone') }}" required>
                    @error('phone')
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

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">تأكيد كلمة المرور</label>
                    <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem;">
                    إنشاء الحساب
                </button>

                <div class="text-center">
                    <a href="{{ route('login') }}" style="color: var(--primary); text-decoration: none;">
                        لديك حساب؟ سجل الدخول
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleFields() {
            const role = document.getElementById('role').value;
            const studentFields = document.getElementById('studentFields');
            const staffFields = document.getElementById('staffFields');

            if (role === 'student') {
                studentFields.style.display = 'block';
                staffFields.style.display = 'none';
            } else if (role === 'staff') {
                studentFields.style.display = 'none';
                staffFields.style.display = 'block';
            } else {
                studentFields.style.display = 'none';
                staffFields.style.display = 'none';
            }
        }

        // Initialize on page load
        toggleFields();
    </script>
</body>
</html>
