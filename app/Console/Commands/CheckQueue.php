<?php

namespace App\Console\Commands;

use App\Models\Entry;
use App\Models\Snapshot;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckQueue extends Command
{
    protected $signature = 'app:check-queue';

    public function handle()
    {
        $json = Http::timeout(0.5)
            ->get(env('QUEUE_ENDPOINT'))
            ->json();

        $count = count($json);

        if ($count > 0) {
            Snapshot::create([
                'count' => $count,
                'timestamp' => now(),
            ]);
        }

        foreach ($json as $entry) {
            Entry::firstOrCreate([
                'hash' => md5("{$entry['cslogin']}-{$entry['meetingid']}-{$entry['time']}"),
                'timestamp' => Carbon::createFromTimestamp($entry['time']),
            ]);
        }
    }
}
