<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAppliedJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'job_id',
        'user_cv',
        'user_address',
        'user_phone',
        'user_email',
        'user_name',
    ];
}
