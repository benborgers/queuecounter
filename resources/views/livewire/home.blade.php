<div class="p-4 pt-5">
    <div class="flex items-end justify-between">
        <h1 class="ml-0.5 text-xl font-bold tracking-tight text-zinc-950 dark:text-white">
            How busy is the
            <a href="https://www.cs.tufts.edu/cs/40" target="_blank" class="underline decoration-zinc-300">
                CS 40</a>
            office hours queue?
        </h1>

        <flux:radio.group wire:model.live="mode" variant="segmented" size="sm">
            <flux:radio value="entry" label="Total Queue Joins" />
            <flux:radio value="snapshot" label="Max Queue Length" />
        </flux:radio.group>
    </div>

    <div class="mt-4 flex items-start gap-4">
        <div>
            <flux:card class="!p-2 !pb-4">
                <flux:calendar
                    wire:model.live="date"
                    size="sm"
                    min="{{ $this->calendarMinDate->format('Y-m-d') }}"
                    max="today"
                    with-today
                    wire:key="{{ $mode }}"
                />
                <p class="ml-4 mt-1 text-xs font-medium text-zinc-800 italic *:px-2 *:py-0.5 *:rounded-full">
                    <span class="bg-sky-100 dark:bg-sky-700 dark:text-white">= Design Due</span>
                    <span class="ml-1 bg-orange-100 dark:bg-orange-700 dark:text-white">= Homework Due</span>
                </p>
            </flux:card>

            <div class="mt-6">
                <flux:field variant="inline">
                    <flux:switch wire:model.live="hasComparison"/>
                    <flux:label>Compare to another date</flux:label>
                </flux:field>
            </div>

            @if ($this->hasComparison)
                <flux:card class="mt-2 !p-2">
                    <flux:calendar
                        wire:model.live="comparisonDate"
                        size="sm"
                        min="{{ $this->calendarMinDate->format('Y-m-d') }}"
                        max="today"
                        with-today
                        wire:key="{{ $mode }}"
                    />
                </flux:card>
            @endif
        </div>

        <flux:card class="aspect-[2/1] w-full transition-opacity duration-200" wire:loading.class="opacity-50">
            <flux:chart
                :value="$this->data"
                wire:key="{{ $mode }}{{ $date }} {{ $comparisonDate }}"
                class="w-full h-full"
            >
                <flux:chart.svg>
                    @if ($hasComparison)
                        <flux:chart.line field="comparisonCount" class="text-zinc-300 dark:text-zinc-600" stroke-width="2" stroke-dasharray="4 4" curve="none" />
                    @endif

                    <flux:chart.line field="count" class="text-accent" stroke-width="3" curve="none" />

                    <flux:chart.axis axis="x" field="label" tick-count="10">
                        <flux:chart.axis.tick />
                        <flux:chart.axis.line />
                    </flux:chart.axis>

                    <flux:chart.axis axis="y" field="count" :tick-values="range(0, $this->maxCount)">
                        <flux:chart.axis.tick />
                        <flux:chart.axis.grid />
                    </flux:chart.axis>
                    <flux:chart.cursor />
                </flux:chart.svg>

                <flux:chart.tooltip>
                    <flux:chart.tooltip.heading field="label" />
                    <flux:chart.tooltip.value
                        field="count"
                        label="{{ $mode === 'entry' ? 'Times people joined queue' : 'Queue length' }}"
                    />
                    @if ($hasComparison)
                        <flux:chart.tooltip.value
                            field="comparisonCount"
                            label="{{ $mode === 'entry' ? 'Times people joined queue' : 'Queue length' }} (comparison)"
                        />
                    @endif
                </flux:chart.tooltip>
            </flux:chart>
        </flux:card>
    </div>

    <style>
        {!! $this->designDeadlines->map(fn ($ymd) => "
        [data-date=\"{$ymd}\"]:not([disabled]) {
            background-color: var(--color-sky-100);
            border-radius: 100%;
        }

        .dark [data-date=\"{$ymd}\"]:not([disabled]) {
            background-color: var(--color-sky-700);
        }
        ")->join("\n") !!}

        {!! $this->homeworkDeadlines->map(fn ($ymd) => "
        [data-date=\"{$ymd}\"]:not([disabled]) {
            background-color: var(--color-orange-100);
            border-radius: 100%;
        }

        .dark [data-date=\"{$ymd}\"]:not([disabled]) {
            background-color: var(--color-orange-700);
        }
        ")->join("\n") !!}
    </style>
</div>
