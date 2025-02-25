<?php

namespace App\Livewire;

use App\Models\Snapshot;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

const MINUTES_PER_PERIOD = 5;

class Home extends Component
{
    public Carbon $date;

    public bool $hasComparison = false;

    public ?Carbon $comparisonDate = null;

    public function mount()
    {
        $this->date = Carbon::now()->startOfDay();
    }

    #[Computed]
    public function designDeadlines()
    {
        return collect([
            '2025-01-23',
            '2025-02-03',
            '2025-02-17',
            '2025-02-26',
            '2025-04-02',
        ]);
    }

    #[Computed]
    public function homeworkDeadlines()
    {
        return collect([
            '2025-01-29',
            '2025-02-10',
            '2025-02-20',
            '2025-03-06',
            '2025-03-27',
            '2025-04-10',
            '2025-04-21',
            '2025-04-28',
        ]);
    }

    #[Computed]
    public function dataForDate($date)
    {
        if (! isset($date)) {
            return [];
        }

        $start = $date->copy()->shiftTimezone('America/New_York')->startOfDay()->setHour(10);
        $end = $date->copy()->shiftTimezone('America/New_York')->endOfDay();

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
            DB::raw('FLOOR(EXTRACT(EPOCH FROM timestamp) / '.(MINUTES_PER_PERIOD * 60).') AS period_key'),
            DB::raw('MAX(count) AS max_count'),
        ])
            ->whereBetween('timestamp', [$start->timezone('UTC'), $end->timezone('UTC')])
            ->groupBy('period_key')
            ->get()
            ->keyBy('period_key');

        // Map results to our periods
        $points = [];
        foreach ($periods as $period) {
            $isFuture = $period['period_start']->isFuture();

            $periodKey = floor($period['period_start']->timestamp / (MINUTES_PER_PERIOD * 60));

            $point = [
                'label' => $period['label'],
                'count' => isset($snapshots[$periodKey]) ? $snapshots[$periodKey]->max_count : 0,
            ];

            if ($isFuture) {
                unset($point['count']);
            }

            $points[] = $point;
        }

        // This fixes the area chart - if the last part of the area chart is up high, it fills diagonally.
        // Instead, add an artificial 0 count.
        if (! isset(end($points)['count'])) {
            foreach ($points as $index => $point) {
                if (! isset($point['count'])) {
                    // Not sure why 2 extra points are needed, but it seems to work.
                    $points[$index]['count'] = 0;
                    $points[$index + 1]['count'] = 0;
                    break; // Only add count to the first missing period
                }
            }
        }

        return $points;
    }

    #[Computed]
    public function data()
    {
        $data1 = $this->dataForDate($this->date);
        $data2 = $this->dataForDate($this->comparisonDate);

        if (! $this->hasComparison) {
            return $data1;
        }

        $mergedData = [];
        foreach ($data1 as $index => $point) {
            $mergedPoint = $point;

            if (isset($data2[$index]) && isset($data2[$index]['count'])) {
                $mergedPoint['comparisonCount'] = $data2[$index]['count'];
            }

            $mergedData[] = $mergedPoint;
        }

        return $mergedData;
    }

    #[Computed]
    public function maxCount()
    {
        return Snapshot::max('count');
    }

    #[Computed]
    public function lastCheckedQueue()
    {
        return Cache::get('last-checked-queue');
    }

    public function updatedHasComparison()
    {
        if (! $this->hasComparison) {
            $this->comparisonDate = null;
        }
    }

    public function updatedComparisonDate()
    {
        if (! $this->comparisonDate) {
            $this->hasComparison = false;
        }
    }
}
