<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAppliedCitizensJob extends Model
{
    use HasFactory;

    protected $table = 'customer_applied_citizens_jobs';

    protected $fillable = [
        'owner_id',
        'user_id',
        'job_citizens_id',
        'user_cv',
        'user_address',
        'user_phone',
        'user_email',
        'user_name',
        'password_image',
    ];
}
