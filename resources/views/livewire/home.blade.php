<div class="p-4">
    <div class="flex items-start gap-6">
        <flux:calendar
            wire:model.live="date"
            size="sm"
            min="2025-02-01"
            max="today"
            with-today
        />

        <flux:card class="aspect-[2/1] w-full">
            <flux:chart :value="$this->data" wire:key="{{ $date }}" class="w-full h-full">
                <flux:chart.svg>
                    <flux:chart.line field="count" class="text-accent" />
                    <flux:chart.axis axis="x" field="label" tick-count="10">
                        <flux:chart.axis.tick />
                        <flux:chart.axis.line />
                    </flux:chart.axis>
                    <flux:chart.axis axis="y" field="count" :tick-values="[0, 1, 2, 3, 4, 5, 6, 7, 8]">
                        <flux:chart.axis.tick />
                        <flux:chart.axis.line />
                    </flux:chart.axis>
                    <flux:chart.cursor />
                </flux:chart.svg>

                <flux:chart.tooltip>
                    <flux:chart.tooltip.value field="label" />
                </flux:chart.tooltip>
            </flux:chart>
        </flux:card>
    </div>
</div>
