@php
    $details = json_decode($loan->admin_notes ?? '', true);
@endphp

<div style="max-height: 5.5rem; min-width: 180px; max-width: 260px; overflow-y: auto; padding: 0.25rem; border: 1px solid var(--gray-200); border-radius: 6px; background: var(--gray-50);">
    @if(is_array($details) && count($details) > 0)
        @foreach($details as $detail)
            <div style="padding: 0.25rem 0; border-bottom: 1px solid var(--gray-200);">
                <strong>{{ $detail['key'] ?? '-' }}:</strong>
                <span>{{ $detail['value'] ?? '-' }}</span>
            </div>
        @endforeach
    @elseif($loan->admin_notes)
        {{ $loan->admin_notes }}
    @else
        -
    @endif
</div>
