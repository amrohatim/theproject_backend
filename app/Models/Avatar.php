<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Avatar extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description', 
        'avatar_image',
        'original_filename',
        'mime_type',
        'file_size',
        'width',
        'height'
    ];
    
    protected $casts = [
        'file_size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    // Accessor for full image URL
    public function getImageUrlAttribute()
    {
        return Storage::url($this->avatar_image);
    }
    
    // Accessor for formatted file size
    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
