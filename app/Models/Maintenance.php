<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    protected $table = 'maintenance';

    protected $fillable = [
        'maintenance',
        'message',
        'start_at',
        'end_at',
        'platform',
    ];

    protected $casts = [
        'maintenance' => 'boolean',
        'start_at' => 'date',
        'end_at' => 'date',
    ];
}
