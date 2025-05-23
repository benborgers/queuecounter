<?php

namespace App\Livewire;

use App\Models\Entry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Heatmap extends Component
{
    // Start hour (10am) and end hour (23pm) to match Home component
    private $startHour = 10;
    private $endHour = 23;

    #[Computed]
    public function weekdayFifteenMinuteData()
    {
        return $this->getEntryFifteenMinuteData('weekday');
    }

    #[Computed]
    public function saturdayFifteenMinuteData()
    {
        return $this->getEntryFifteenMinuteData('saturday');
    }

    #[Computed]
    public function sundayFifteenMinuteData()
    {
        return $this->getEntryFifteenMinuteData('sunday');
    }

    private function getEntryFifteenMinuteData($dayType)
    {
        $query = Entry::select([
            DB::raw('EXTRACT(HOUR FROM timestamp AT TIME ZONE \'UTC\' AT TIME ZONE \'America/New_York\') AS hour'),
            DB::raw('FLOOR(EXTRACT(MINUTE FROM timestamp AT TIME ZONE \'UTC\' AT TIME ZONE \'America/New_York\') / 30) AS half_hour'),
            DB::raw('COUNT(*) as count')
        ])
            ->whereRaw('EXTRACT(HOUR FROM timestamp AT TIME ZONE \'UTC\' AT TIME ZONE \'America/New_York\') BETWEEN ? AND ?',
                [$this->startHour, $this->endHour]);

        // Add day type filter
        switch ($dayType) {
            case 'weekday':
                $query->whereRaw('EXTRACT(DOW FROM timestamp AT TIME ZONE \'UTC\' AT TIME ZONE \'America/New_York\') BETWEEN 1 AND 5');
                break;
            case 'saturday':
                $query->whereRaw('EXTRACT(DOW FROM timestamp AT TIME ZONE \'UTC\' AT TIME ZONE \'America/New_York\') = 6');
                break;
            case 'sunday':
                $query->whereRaw('EXTRACT(DOW FROM timestamp AT TIME ZONE \'UTC\' AT TIME ZONE \'America/New_York\') = 0');
                break;
        }

        $intervalData = $query->groupBy('hour', 'half_hour')
            ->orderBy('hour')
            ->orderBy('half_hour')
            ->get();

        Log::info("Interval data for {$dayType}: " . json_encode($intervalData, JSON_PRETTY_PRINT));

        return $this->formatFifteenMinuteData($intervalData);
    }

    private function formatFifteenMinuteData($intervalData)
    {
        // Create a map to easily look up data
        $dataMap = [];
        foreach ($intervalData as $interval) {
            $key = $interval->hour . '_' . $interval->half_hour;
            $dataMap[$key] = $interval->count;
        }

        // Create array with all 30-minute intervals for the filtered hours
        $data = [];
        for ($hour = $this->startHour; $hour <= $this->endHour; $hour++) {
            for ($half = 0; $half < 2; $half++) {
                $formattedHour = $hour % 12 == 0 ? 12 : $hour % 12;
                $amPm = $hour < 12 ? 'am' : 'pm';
                $key = $hour . '_' . $half;

                // Only add hour label for the first interval of each hour
                $label = $half == 0 ? $formattedHour . $amPm : '';

                $data[] = [
                    'hour' => $hour,
                    'half' => $half,
                    'interval_key' => ($hour - $this->startHour) * 2 + $half, // 0-based index for the day's slice
                    'label' => $label,
                    'count' => $dataMap[$key] ?? 0,
                    'has_label' => $half == 0,
                ];
            }
        }

        return $data;
    }

    #[Computed]
    public function maxCount()
    {
        return Entry::select(DB::raw('COUNT(*) as count'))
            ->whereRaw('EXTRACT(HOUR FROM timestamp AT TIME ZONE \'UTC\' AT TIME ZONE \'America/New_York\') BETWEEN ? AND ?',
                [$this->startHour, $this->endHour])
            ->groupBy([
                DB::raw('EXTRACT(HOUR FROM timestamp AT TIME ZONE \'UTC\' AT TIME ZONE \'America/New_York\')'),
                DB::raw('FLOOR(EXTRACT(MINUTE FROM timestamp AT TIME ZONE \'UTC\' AT TIME ZONE \'America/New_York\') / 30)')
            ])
            ->orderBy('count', 'desc')
            ->value('count') ?? 0;
    }

    #[Computed]
    public function intervalCount()
    {
        // Calculate total number of 30-minute intervals in our range
        return ($this->endHour - $this->startHour + 1) * 2;
    }

    public function render()
    {
        return view('livewire.heatmap');
    }
}
