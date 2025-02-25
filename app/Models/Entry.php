<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    public $casts = [
        'timestamp' => 'datetime',
    ];
}
