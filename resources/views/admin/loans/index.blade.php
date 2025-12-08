<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الطلبات</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('admin.nav')

    <div class="container" style="padding: 2rem 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap;">
            <h1 class="mb-3">طلبات الاستعارة</h1>
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
                <div class="form-group" style="margin: 0; flex: 1; min-width: 200px;">
                    <label class="form-label">تصفية حسب الحالة</label>
                    <select name="status" class="form-control" id="statusFilter">
                        <option value="">الكل</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>موافق عليه</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>تم الإرجاع</option>
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
                            <th>المستخدم</th>
                            <th>الأداة</th>
                            <th>تاريخ الطلب</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="loansTableBody">
                        @include('admin.loans.table-rows')
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">{{ $loans->links() }}</div>
    </div>

    <script>
        let lastFetchTime = Date.now();
        
        // Fetch new data every 5 seconds
        setInterval(fetchLoans, 5000);

        function fetchLoans() {
            const statusFilter = document.getElementById('statusFilter').value;
            const url = '{{ route("admin.loans.data") }}' + (statusFilter ? '?status=' + statusFilter : '');
            
            // Pulse the indicator
            const indicator = document.getElementById('liveIndicator');
            indicator.style.opacity = '0.3';
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    // Update table body
                    document.getElementById('loansTableBody').innerHTML = data.html;
                    
                    // Reset indicator
                    indicator.style.opacity = '1';
                    lastFetchTime = Date.now();
                })
                .catch(error => {
                    console.error('Error fetching loans:', error);
                    indicator.style.background = 'var(--danger)';
                    indicator.style.boxShadow = '0 0 8px var(--danger)';
                });
        }
    </script>
</body>
</html>
