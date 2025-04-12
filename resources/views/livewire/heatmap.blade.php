<div class="p-4 pt-5">
    <div class="flex items-end justify-between">
        <h1 class="ml-0.5 text-xl font-bold tracking-tight text-zinc-950 dark:text-white">
            Queue Heatmap
            <span class="text-sm font-normal italic ml-2 text-zinc-500">
                (across all semester)
            </span>
        </h1>

        <div class="flex gap-4">
            <a href="/" class="inline-flex items-center gap-1 text-sm font-medium text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">
                <flux:icon.arrow-left class="h-4 w-4" />
                Back to daily graphs
            </a>
        </div>
    </div>

    <div class="mt-6 space-y-4">
        <!-- Weekday Heatmap -->
        <flux:card class="transition-opacity duration-200 !p-4" wire:loading.class="opacity-50">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">Monday – Friday</h2>
            <div class="heatmap-container">
                @php
                    $maxValue = $this->maxCount;
                    $intervalCount = $this->intervalCount;
                    $intervalDataWithIntensity = collect($this->weekdayFifteenMinuteData)->map(function($item) use ($maxValue) {
                        $item['intensity'] = $maxValue > 0 ? ($item['count'] / $maxValue) : 0;
                        return $item;
                    });
                @endphp

                <div class="pb-6">
                    <div class="heatmap-grid grid gap-0.5" style="grid-template-columns: repeat({{ $intervalCount }}, minmax(0, 1fr));">
                        @foreach($intervalDataWithIntensity as $interval)
                            @php
                                $hour = $interval['hour'];
                                $minute = $interval['quarter'] * 15;
                                $period = $hour < 12 ? 'am' : 'pm';
                                $displayHour = $hour % 12 == 0 ? 12 : $hour % 12;
                                $timeString = sprintf('%d:%02d%s', $displayHour, $minute, $period);
                            @endphp
                            <div
                                class="heatmap-cell relative group aspect-square"
                                data-hour="{{ $interval['hour'] }}"
                                data-quarter="{{ $interval['quarter'] }}"
                            >
                                <div class="absolute inset-0 bg-accent opacity-0 group-hover:opacity-10 rounded"></div>
                                <div
                                    class="rounded w-full h-full bg-accent"
                                    style="opacity: {{ max(0.05, $interval['intensity']) }}"
                                ></div>

                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 hidden group-hover:block z-10">
                                    <div class="bg-zinc-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                                        {{ $interval['count'] }} {{ $interval['count'] == 1 ? 'join' : 'joins' }} at {{ $timeString }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

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

        <!-- Saturday Heatmap -->
        <flux:card class="transition-opacity duration-200" wire:loading.class="opacity-50">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">Saturday</h2>
            <div class="heatmap-container">
                @php
                    $intervalDataWithIntensity = collect($this->saturdayFifteenMinuteData)->map(function($item) use ($maxValue) {
                        $item['intensity'] = $maxValue > 0 ? ($item['count'] / $maxValue) : 0;
                        return $item;
                    });
                @endphp

                <div class="pb-6">
                    <div class="heatmap-grid grid gap-0.5" style="grid-template-columns: repeat({{ $intervalCount }}, minmax(0, 1fr));">
                        @foreach($intervalDataWithIntensity as $interval)
                            @php
                                $hour = $interval['hour'];
                                $minute = $interval['quarter'] * 15;
                                $period = $hour < 12 ? 'am' : 'pm';
                                $displayHour = $hour % 12 == 0 ? 12 : $hour % 12;
                                $timeString = sprintf('%d:%02d%s', $displayHour, $minute, $period);
                            @endphp
                            <div
                                class="heatmap-cell relative group aspect-square"
                                data-hour="{{ $interval['hour'] }}"
                                data-quarter="{{ $interval['quarter'] }}"
                            >
                                <div class="absolute inset-0 bg-accent opacity-0 group-hover:opacity-10 rounded"></div>
                                <div
                                    class="rounded w-full h-full bg-accent"
                                    style="opacity: {{ max(0.05, $interval['intensity']) }}"
                                ></div>

                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 hidden group-hover:block z-10">
                                    <div class="bg-zinc-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                                        {{ $interval['count'] }} {{ $interval['count'] == 1 ? 'join' : 'joins' }} at {{ $timeString }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

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

        <!-- Sunday Heatmap -->
        <flux:card class="transition-opacity duration-200" wire:loading.class="opacity-50">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">Sunday</h2>
            <div class="heatmap-container">
                @php
                    $intervalDataWithIntensity = collect($this->sundayFifteenMinuteData)->map(function($item) use ($maxValue) {
                        $item['intensity'] = $maxValue > 0 ? ($item['count'] / $maxValue) : 0;
                        return $item;
                    });
                @endphp

                <div class="pb-6">
                    <div class="heatmap-grid grid gap-0.5" style="grid-template-columns: repeat({{ $intervalCount }}, minmax(0, 1fr));">
                        @foreach($intervalDataWithIntensity as $interval)
                            @php
                                $hour = $interval['hour'];
                                $minute = $interval['quarter'] * 15;
                                $period = $hour < 12 ? 'am' : 'pm';
                                $displayHour = $hour % 12 == 0 ? 12 : $hour % 12;
                                $timeString = sprintf('%d:%02d%s', $displayHour, $minute, $period);
                            @endphp
                            <div
                                class="heatmap-cell relative group aspect-square"
                                data-hour="{{ $interval['hour'] }}"
                                data-quarter="{{ $interval['quarter'] }}"
                            >
                                <div class="absolute inset-0 bg-accent opacity-0 group-hover:opacity-10 rounded"></div>
                                <div
                                    class="rounded w-full h-full bg-accent"
                                    style="opacity: {{ max(0.05, $interval['intensity']) }}"
                                ></div>

                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 hidden group-hover:block z-10">
                                    <div class="bg-zinc-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                                        {{ $interval['count'] }} {{ $interval['count'] == 1 ? 'join' : 'joins' }} at {{ $timeString }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

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
