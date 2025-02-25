<?php

use App\Console\Commands\CheckQueue;
use Illuminate\Support\Facades\Schedule;

Schedule::command(CheckQueue::class)
    ->everyTwoSeconds()
    ->runInBackground();
