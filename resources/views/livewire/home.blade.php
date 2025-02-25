<div class="p-4">
    <div class="flex items-start gap-6">
        <flux:calendar
            wire:model.live="date"
            size="sm"
            min="2025-01-01"
            max="today"
            with-today
        />

        <div>
            @json($this->data)
        </div>
    </div>
</div>
