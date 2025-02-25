<?php

use App\Models\Snapshot;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $latestSnapshot = Snapshot::latest()->first();
    return "Latest snapshot was “{$latestSnapshot->count}” — {$latestSnapshot->created_at->diffForHumans()}";
});
