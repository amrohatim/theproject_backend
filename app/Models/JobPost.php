<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'title',
        'description',
        'salary',
        'nice_to_have',
        'deadline',
        'type',
        'type_other',
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

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'type');
    }
}
