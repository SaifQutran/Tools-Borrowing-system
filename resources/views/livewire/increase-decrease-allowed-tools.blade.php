<div style="display: inline-flex; align-items: center; gap: 0.5rem;">
    <button
        type="button"
        wire:click="decrease"
        wire:loading.attr="disabled"
        aria-label="Decrease allowed tools"
        class="btn btn-secondary"
        style="width: 2rem; height: 2rem; padding: 0; line-height: 1;"
        @disabled($allowedTools === 0)
    >
        -
    </button>

    <span style="display: inline-flex; min-width: 2rem; justify-content: center; font-weight: 600;">
        {{ $allowedTools }}
    </span>

    <button
        type="button"
        wire:click="increase"
        wire:loading.attr="disabled"
        aria-label="Increase allowed tools"
        class="btn btn-primary"
        style="width: 2rem; height: 2rem; padding: 0; line-height: 1;"
    >
        +
    </button>
</div>
