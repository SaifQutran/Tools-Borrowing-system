<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المستخدمين</title>
    @vite(['resources/css/app.css'])
    @livewireStyles
</head>
<body>
    @include('admin.nav')

    <div class="container" style="padding: 2rem 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap;">
            <h1 class="mb-3">إدارة المستخدمين</h1>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <div id="liveIndicator" style="width: 10px; height: 10px; border-radius: 50%; background: var(--success); box-shadow: 0 0 8px var(--success);"></div>
                    <span style="font-size: 0.875rem; color: var(--gray-600);">مباشر</span>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card mb-3" style="background: var(--gray-50);">
            <form method="GET" style="display: flex; gap: 1rem; align-items: end; flex-wrap: wrap;">
                <div class="form-group" style="margin: 0; flex: 1; min-width: 150px;">
                    <label class="form-label">نوع المستخدم</label>
                    <select name="role" class="form-control" id="roleFilter">
                        <option value="">الكل</option>
                        <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>طالب</option>
                        <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>موظف</option>
                    </select>
                </div>
                <div class="form-group" style="margin: 0; flex: 1; min-width: 150px;">
                    <label class="form-label">حالة الموافقة</label>
                    <select name="approved" class="form-control" id="approvedFilter">
                        <option value="">الكل</option>
                        <option value="1" {{ request('approved') == '1' ? 'selected' : '' }}>موافق عليه</option>
                        <option value="0" {{ request('approved') == '0' ? 'selected' : '' }}>في الانتظار</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">تصفية</button>
            </form>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>الاسم</th>
                            <th>النوع</th>
                            <th>الرقم</th>
                            <th>التفاصيل</th>
                            <th>الأدوات المسموح بها</th>
                            <th>الحالة</th>

                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody">
                        @include('admin.users.table-rows')
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">{{ $users->links() }}</div>
    </div>

    <script>
        let lastFetchTime = Date.now();
        
        // Fetch new data every 5 seconds
        setInterval(fetchUsers, 5000);

        function fetchUsers() {
            const roleFilter = document.getElementById('roleFilter').value;
            const approvedFilter = document.getElementById('approvedFilter').value;
            
            let url = '{{ route("admin.users.data") }}';
            const params = new URLSearchParams();
            if (roleFilter) params.append('role', roleFilter);
            if (approvedFilter) params.append('approved', approvedFilter);
            if (params.toString()) url += '?' + params.toString();
            
            // Pulse the indicator
            const indicator = document.getElementById('liveIndicator');
            indicator.style.opacity = '0.3';
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    // Update table body
                    const tableBody = document.getElementById('usersTableBody');
                    tableBody.innerHTML = data.html;

                    if (window.Livewire) {
                        if (typeof window.Livewire.rescan === 'function') {
                            window.Livewire.rescan(tableBody);
                        } else if (typeof window.Livewire.initTree === 'function') {
                            window.Livewire.initTree(tableBody);
                        }
                    }
                    
                    // Reset indicator
                    indicator.style.opacity = '1';
                    lastFetchTime = Date.now();
                })
                .catch(error => {
                    console.error('Error fetching users:', error);
                    indicator.style.background = 'var(--danger)';
                    indicator.style.boxShadow = '0 0 8px var(--danger)';
                });
        }
    </script>
    @livewireScripts
</body>
</html>
