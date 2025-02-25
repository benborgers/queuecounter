<?php

namespace App\Livewire;

use App\Models\Snapshot;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;

const MINUTES_PER_PERIOD = 5;

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
        $start = $this->date->copy()->timezone('America/New_York')->startOfDay();
        $end = $this->date->copy()->timezone('America/New_York')->endOfDay();

        $snapshots = Snapshot::whereBetween('timestamp', [$start, $end])->get();

        $points = [];

        $cursor = $start->copy();

        while ($cursor->lessThanOrEqualTo($end)) {
            $relevantSnapshots = $snapshots
                ->where('timestamp', '>=', $cursor)
                ->where('timestamp', '<=', $cursor->copy()->addMinutes(MINUTES_PER_PERIOD));

            $points[] = [
                'label' => $cursor->format('g:i a'),
                'count' => $relevantSnapshots->max('count') ?? 0,
            ];

            $cursor->addMinutes(MINUTES_PER_PERIOD);
        }

        return $points;
    }
}
