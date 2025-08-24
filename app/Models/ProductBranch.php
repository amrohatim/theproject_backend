<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBranch extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'branch_id',
        'stock',
        'is_available',
        'price',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'stock' => 'integer',
        'is_available' => 'boolean',
        'price' => 'float',
    ];

    /**
     * Get the product that owns the branch association.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the branch that is associated with the product.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
