<?php

use App\Console\Commands\CheckQueue;
use Illuminate\Support\Facades\Schedule;

Schedule::command(CheckQueue::class)
    ->everyTwoSeconds()
    ->timezone('America/New_York')
    ->between('9:00', '23:59')
    ->runInBackground();
