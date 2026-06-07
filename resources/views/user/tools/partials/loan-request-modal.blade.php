<div id="loanRequestModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="loanRequestTitle">طلب استعارة</h3>
            <button type="button" class="modal-close" onclick="closeLoanRequestModal()">&times;</button>
        </div>

        <form method="POST" action="{{ route('loan.request') }}" id="loanRequestForm">
            @csrf
            <input type="hidden" name="tool_id" id="loanRequestToolId">

            <div class="modal-body">
                <div id="loanDetailsRows" style="display: flex; flex-direction: column; gap: 0.75rem;"></div>

                <button type="button" class="btn btn-secondary" onclick="addLoanDetailRow()" style="margin-top: 1rem;">
                    إضافة حقل آخر
                </button>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    إرسال الطلب
                </button>
            </div>
        </form>
    </div>
</div>

@php
    $loanDetailKeysPayload = $loanDetailKeys->map(function ($key) {
        return [
            'id' => $key->id,
            'name' => $key->name,
            'value_type' => $key->value_type,
        ];
    })->values();

    $loanHallsPayload = $halls->map(function ($hall) {
        return [
            'id' => $hall->id,
            'name' => $hall->name,
        ];
    })->values();
@endphp

<script>
    const loanDetailKeys = @json($loanDetailKeysPayload);
    const loanHalls = @json($loanHallsPayload);
    let loanDetailIndex = 0;

    function openLoanRequestModal(toolId, toolName) {
        document.getElementById('loanRequestToolId').value = toolId;
        document.getElementById('loanRequestTitle').textContent = 'طلب استعارة - ' + toolName;
        document.getElementById('loanDetailsRows').innerHTML = '';
        loanDetailIndex = 0;
        addLoanDetailRow();
        document.getElementById('loanRequestModal').classList.add('active');
    }

    function closeLoanRequestModal() {
        document.getElementById('loanRequestModal').classList.remove('active');
    }

    function addLoanDetailRow() {
        const rows = document.getElementById('loanDetailsRows');
        const rowIndex = loanDetailIndex++;
        const row = document.createElement('div');
        row.className = 'loan-detail-row';
        row.dataset.index = rowIndex;
        row.style.cssText = 'display: grid; grid-template-columns: minmax(140px, 1fr) minmax(160px, 2fr) auto; gap: 0.5rem; align-items: center;';

        row.innerHTML = `
            <select name="details[${rowIndex}][key_id]" class="form-control" onchange="renderLoanDetailValue(this)" required>
                ${loanDetailKeys.map((key) => `<option value="${key.id}" data-value-type="${key.value_type}">${escapeHtml(key.name)}</option>`).join('')}
            </select>
            <div class="loan-detail-value"></div>
            <button type="button" class="btn btn-danger" onclick="this.closest('.loan-detail-row').remove()" style="padding: 0.5rem 0.75rem;">حذف</button>
        `;

        rows.appendChild(row);
        renderLoanDetailValue(row.querySelector('select'));
    }

    function renderLoanDetailValue(select) {
        const row = select.closest('.loan-detail-row');
        const valueContainer = row.querySelector('.loan-detail-value');
        const selectedOption = select.options[select.selectedIndex];
        const valueType = selectedOption.dataset.valueType;
        const rowIndex = row.dataset.index;

        if (valueType === 'hall') {
            valueContainer.innerHTML = `
                <select name="details[${rowIndex}][value]" class="form-control" required>
                    <option value="">اختر القاعة</option>
                    ${loanHalls.map((hall) => `<option value="${hall.id}">${escapeHtml(hall.name)}</option>`).join('')}
                </select>
            `;
        }else if(valueType === 'number'){
            valueContainer.innerHTML = `<input type="number" name="details[${rowIndex}][value]" class="form-control" required>`;
        } 
        else {
            valueContainer.innerHTML = `<input type="text" name="details[${rowIndex}][value]" class="form-control" required>`;
        }
    }

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    window.addEventListener('click', function(event) {
        const modal = document.getElementById('loanRequestModal');
        if (event.target === modal) {
            closeLoanRequestModal();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeLoanRequestModal();
        }
    });
</script>
