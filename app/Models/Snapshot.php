<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Snapshot extends Model
{
    public $casts = [
        'timestamp' => 'datetime',
    ];
}
