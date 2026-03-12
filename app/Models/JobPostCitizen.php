<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPostCitizen extends Model
{
    use HasFactory;

    protected $table = 'job_post_citizens';

    protected $fillable = [
        'owner_id',
        'title',
        'description',
        'salary',
        'nice_to_have',
        'phone_number',
        'email',
        'deadline',
        'type',
        'number_of_applications',
        'onsite',
        'location',
    ];

    protected $casts = [
        'salary' => 'integer',
        'deadline' => 'datetime',
        'number_of_applications' => 'integer',
        'onsite' => 'boolean',
    ];
}
