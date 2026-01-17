<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralWishlistMerchant extends Model
{
    use HasFactory;

    protected $table = 'general_wishlist_merchants';

    protected $fillable = [
        'user_id',
        'merchant_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}
