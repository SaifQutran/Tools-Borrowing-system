<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الأدوات</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    @include('admin.nav')

    <div class="container" style="padding: 2rem 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <h1 style="margin: 0;">إدارة الأدوات</h1>
            <a href="{{ route('admin.tools.create') }}" class="btn btn-primary">إضافة أداة جديدة</a>
        </div>



        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert" style="background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;">{{ $errors->first() }}</div>
        @endif

        @if(session('import_errors'))
            <div class="alert" style="background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;">
                <strong>Rows skipped:</strong>
                <ul style="margin: 0.5rem 0 0;">
                    @foreach(session('import_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>الاسم</th>
                            <th>النوع</th>
                            <th>الكود</th>
                            <th>ظاهرة لـ</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tools as $tool)
                            <tr>
                                <td>{{ $tool->name }}</td>
                                <td>{{ $tool->toolType->name }}</td>
                                <td><code>{{ $tool->code }}</code></td>
                                <td>

                                    <livewire:tool-visibility :tool="$tool" wire:key="tool-visibility-{{ $tool->id }}" />
                        </td>
                                <td>
                                    @if($tool->status == 'available')
                                        <span class="badge badge-success">متاح</span>
                                    @elseif($tool->status == 'borrowed')
                                        <span class="badge badge-danger">مستعار</span>
                                    @else
                                        <span class="badge badge-warning">تم طلبها</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.tools.edit', $tool) }}" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.875rem; margin-left: 0.5rem;">تعديل</a>
                                    <button onclick='showQR({{ $tool->id }}, @json($tool->name), @json($tool->code))' class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem; margin-left: 0.5rem;">QR</button>
                                    <form method="POST" action="{{ route('admin.tools.destroy', $tool) }}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" style="padding: 0.5rem 1rem; font-size: 0.875rem;" onclick="return confirm('هل أنت متأكد؟')">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center">لا توجد أدوات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">{{ $tools->links() }}</div>
        <form method="POST" action="{{ route('admin.tools.import') }}" enctype="multipart/form-data" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap;">
            @csrf
            <input type="file" name="tools_file" accept=".xlsx,.csv" required class="form-control" style="max-width: 260px;">
            <button type="submit" class="btn btn-secondary">استيراد Excel</button>
            <a href="{{ route('admin.tools.import.template') }}" class="btn btn-primary">قالب excel</a>
            <a href="{{ route('admin.tools.download-all-qrs') }}"  style="margin-right: 20px" class="btn btn-primary">تحميل جميع رموز QR</a>
        </form>
    </div>
    <!-- QR Code Modal -->
    <div id="qrModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalToolName">رمز QR</h3>
                <button class="modal-close" onclick="closeQRModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="qrLoading" style="text-align: center; padding: 2rem;">
                    <div class="spinner" style="margin: 0 auto;"></div>
                    <p style="margin-top: 1rem; color: var(--gray-600);">جاري إنشاء رمز QR...</p>
                </div>
                <img id="qrImage" src="" alt="QR Code" style="display: none; max-width: 100%; border: 2px solid var(--gray-300); padding: 1rem; background: white;">
                <div id="qrToolDetails" style="display: none; margin-top: 1rem; text-align: center;">
                    <p id="qrToolNameField" style="margin: 0; font-weight: 600;">اسم الأداة: <span id="qrToolNameText"></span></p>
                    <p id="qrToolCodeField" style="margin: 0.25rem 0 0; color: #4b5563;">كود الأداة: <span id="qrToolCodeText"></span></p>
                </div>
            </div>
            <div class="modal-footer" id="qrFooter" style="display: none;">
                <a id="qrDownload" href="" download class="btn btn-primary" style="width: 100%;">
                    تحميل رمز QR
                </a>
            </div>
        </div>
    </div>

    <script>
        function showQR(toolId, toolName, toolCode) {
            document.getElementById('modalToolName').textContent = 'رمز QR - ' + toolName;
            document.getElementById('qrModal').classList.add('active');
            
            // Show loading
            document.getElementById('qrLoading').style.display = 'block';
            document.getElementById('qrImage').style.display = 'none';
            document.getElementById('qrFooter').style.display = 'none';
            document.getElementById('qrToolDetails').style.display = 'none';
            
            // Generate and load QR code
            const qrUrl = '/admin/tools/' + toolId + '/qr-show';
            const downloadUrl = '/admin/tools/' + toolId + '/qr';
            
            // Load the QR code image
            const img = document.getElementById('qrImage');
            img.onload = function() {
                document.getElementById('qrLoading').style.display = 'none';
                document.getElementById('qrImage').style.display = 'block';
                document.getElementById('qrFooter').style.display = 'block';
                document.getElementById('qrToolDetails').style.display = 'block';
            };
            img.src = qrUrl + '?t=' + new Date().getTime(); // Add timestamp to prevent cache
            
            document.getElementById('qrToolNameText').textContent = toolName;
            document.getElementById('qrToolCodeText').textContent = toolCode;
            document.getElementById('qrDownload').href = downloadUrl;
            document.getElementById('qrDownload').download = 'QR_' + toolName + '.png';
        }

        function closeQRModal() {
            document.getElementById('qrModal').classList.remove('active');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('qrModal');
            if (event.target === modal) {
                closeQRModal();
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeQRModal();
            }
        });
    </script>
</body>
</html>
