<div class="p-4 pt-5">
    <div class="flex items-end justify-between">
        <h1 class="ml-0.5 text-xl font-bold tracking-tight text-zinc-950 dark:text-white">
            Queue Usage Heatmap
            <span class="text-sm font-normal italic ml-2 text-zinc-500">
                (aggregated across all days)
            </span>
        </h1>

        <div class="flex gap-4">
            <flux:radio.group wire:model.live="mode" variant="segmented" size="sm">
                <flux:radio value="entry" label="Queue Joins" />
                <flux:radio value="snapshot" label="Queue Length" />
            </flux:radio.group>

            <a href="/" class="inline-flex items-center gap-1 text-sm font-medium text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">
                <flux:icon.arrow-left class="h-4 w-4" />
                Back to Daily View
            </a>
        </div>
    </div>

    <div class="mt-6">
        <flux:card class="transition-opacity duration-200" wire:loading.class="opacity-50">
            <div class="pb-4">
                <h2 class="text-lg font-medium text-zinc-900 dark:text-white">
                    Hourly Distribution (10am - 11pm)
                </h2>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Shows when students join the queue most frequently throughout the day.
                </p>
            </div>

            <div class="heatmap-container">
                @php
                    $maxValue = $this->maxCount;
                    $intervalCount = $this->intervalCount;
                    // Calculate intensity percentages
                    $intervalDataWithIntensity = collect($this->fifteenMinuteData)->map(function($item) use ($maxValue) {
                        $item['intensity'] = $maxValue > 0 ? ($item['count'] / $maxValue) : 0;
                        return $item;
                    });
                @endphp
                
                <div class="pb-8">
                    <!-- Responsive heatmap grid -->
                    <div class="heatmap-grid grid gap-0.5" style="grid-template-columns: repeat({{ $intervalCount }}, minmax(0, 1fr));">
                        @foreach($intervalDataWithIntensity as $interval)
                            <div 
                                class="heatmap-cell relative group aspect-square"
                                data-hour="{{ $interval['hour'] }}"
                                data-quarter="{{ $interval['quarter'] }}"
                                title="{{ $interval['count'] }} {{ $this->mode === 'entry' ? 'joins' : 'max queue length' }} at {{ sprintf('%02d:%02d', $interval['hour'], $interval['quarter'] * 15) }}"
                            >
                                <div class="absolute inset-0 bg-accent opacity-0 group-hover:opacity-10 rounded"></div>
                                <div 
                                    class="rounded w-full h-full bg-accent" 
                                    style="opacity: {{ max(0.05, $interval['intensity']) }}"
                                ></div>
                                
                                <!-- Tooltip that appears on hover -->
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 hidden group-hover:block z-10">
                                    <div class="bg-zinc-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                                        {{ $interval['count'] }} {{ $this->mode === 'entry' ? 'joins' : 'max length' }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Hour labels below the grid -->
                    <div class="mt-1 grid" style="grid-template-columns: repeat({{ $intervalCount }}, minmax(0, 1fr));">
                        @foreach($intervalDataWithIntensity as $interval)
                            @if($interval['has_label'])
                                <div class="relative" style="grid-column: span 4;">
                                    <div class="absolute top-0 left-0 transform -rotate-45 origin-top-left text-[10px] text-zinc-500 whitespace-nowrap">
                                        {{ $interval['label'] }}
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </flux:card>
    </div>

    <style>
        .heatmap-grid {
            min-height: 50px;
        }
        .heatmap-cell {
            min-width: 10px; /* Minimum width for very small screens */
        }
    </style>
</div>