<div class="p-4">
    <div class="flex items-start gap-6">
        <div>
            <flux:calendar
                wire:model.live="date"
                size="sm"
                min="2025-02-01"
                max="today"
                with-today
            />

            <p class="ml-4 mt-1 text-xs font-medium text-zinc-800 italic *:px-2 *:py-0.5 *:rounded-full">
                <span class="bg-sky-100">= Design Due</span>
                <span class="ml-1 bg-orange-100">= Homework Due</span>
            </p>
        </div>

        <flux:card class="aspect-[2/1] w-full transition-opacity duration-100" wire:loading.class="opacity-50">
            <flux:chart :value="$this->data" wire:key="{{ $date }}" class="w-full h-full">
                <flux:chart.svg>
                    <flux:chart.line field="count" class="text-accent" stroke-width="3" curve="none" />
                    <flux:chart.area field="count" class="text-accent/10" stroke-width="3" curve="none" />
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

    <style>
        {!! $this->designDeadlines->map(fn ($ymd) => "
        [data-date=\"{$ymd}\"]:not([disabled]) {
            background-color: var(--color-sky-100); /* text-sky-100 */
            border-radius: 100%;
        }
        ")->join("\n") !!}

        {!! $this->homeworkDeadlines->map(fn ($ymd) => "
        [data-date=\"{$ymd}\"]:not([disabled]) {
            background-color: var(--color-orange-100); /* text-orange-100 */
            border-radius: 100%;
        }
        ")->join("\n") !!}
    </style>
</div>
