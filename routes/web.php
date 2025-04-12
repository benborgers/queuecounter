<?php

use App\Livewire;
use Illuminate\Support\Facades\Route;

Route::get('/', Livewire\Home::class);
Route::get('/heatmap', Livewire\Heatmap::class);
