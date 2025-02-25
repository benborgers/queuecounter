<?php

namespace App\Livewire;

use App\Models\Snapshot;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

const MINUTES_PER_PERIOD = 15;

class Home extends Component
{
    public Carbon $date;

    public function mount()
    {
        $this->date = Carbon::now();
    }

    #[Computed]
    public function data()
    {
        if (! isset($this->date)) {
            return [];
        }

        $start = $this->date->copy()->timezone('America/New_York')->startOfDay()->setHour(10);
        $end = $this->date->copy()->timezone('America/New_York')->endOfDay();

        $periods = [];

        $cursor = $start->copy();

        while ($cursor->lessThanOrEqualTo($end)) {
            $periods[] = [
                'period_start' => $cursor->copy(),
                'period_end' => $cursor->copy()->addMinutes(MINUTES_PER_PERIOD),
                'label' => $cursor->format('g:i a'),
            ];
            $cursor->addMinutes(MINUTES_PER_PERIOD);
        }

        // Get max counts for each time period directly from the database
        $snapshots = Snapshot::select([
                DB::raw('FLOOR(EXTRACT(EPOCH FROM timestamp) / ' . (MINUTES_PER_PERIOD * 60) . ') AS period_key'),
                DB::raw('MAX(count) AS max_count')
            ])
            ->whereBetween('timestamp', [$start->timezone('UTC'), $end->timezone('UTC')])
            ->groupBy('period_key')
            ->get()
            ->keyBy('period_key');

        // Map results to our periods
        $points = [];
        foreach ($periods as $period) {
            $periodKey = floor($period['period_start']->timestamp / (MINUTES_PER_PERIOD * 60));
            $points[] = [
                'label' => $period['label'],
                'count' => isset($snapshots[$periodKey]) ? $snapshots[$periodKey]->max_count : 0,
            ];
        }

        return $points;
    }
}
