<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'branch_id',
        'category_id',
        'user_id',
        'name',
        'price',
        'original_price',
        'stock',
        'sku',
        'featured',
        'description',
        'image',
        'rating',
        'is_available',
        'is_multi_branch',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'float',
        'original_price' => 'float',
        'stock' => 'integer',
        'featured' => 'boolean',
        'rating' => 'float',
        'is_available' => 'boolean',
        'is_multi_branch' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Handle cascading deletion when a product is being deleted
        static::deleting(function ($product) {
            try {
                DB::beginTransaction();

                Log::info("Starting cascading deletion for product ID: {$product->id}");

                // Delete all color-size combinations first (most specific)
                $colorSizesCount = $product->colorSizes()->count();
                if ($colorSizesCount > 0) {
                    $product->colorSizes()->delete();
                    Log::info("Deleted {$colorSizesCount} color-size combinations for product {$product->id}");
                }

                // Delete all color images before deleting colors
                $colors = $product->colors()->get();
                foreach ($colors as $color) {
                    if ($color->image) {
                        $rawImagePath = $color->getRawImagePath();
                        if ($rawImagePath && Storage::disk('public')->exists($rawImagePath)) {
                            Storage::disk('public')->delete($rawImagePath);
                            Log::info("Deleted color image: {$rawImagePath}");
                        }
                    }
                }

                // Delete all colors
                $colorsCount = $product->colors()->count();
                if ($colorsCount > 0) {
                    $product->colors()->delete();
                    Log::info("Deleted {$colorsCount} colors for product {$product->id}");
                }

                // Delete all sizes
                $sizesCount = $product->sizes()->count();
                if ($sizesCount > 0) {
                    $product->sizes()->delete();
                    Log::info("Deleted {$sizesCount} sizes for product {$product->id}");
                }

                // Delete all specifications
                $specificationsCount = $product->specifications()->count();
                if ($specificationsCount > 0) {
                    $product->specifications()->delete();
                    Log::info("Deleted {$specificationsCount} specifications for product {$product->id}");
                }

                // Delete main product image
                if ($product->image) {
                    $rawImagePath = $product->getRawImagePath();
                    if ($rawImagePath && Storage::disk('public')->exists($rawImagePath)) {
                        Storage::disk('public')->delete($rawImagePath);
                        Log::info("Deleted main product image: {$rawImagePath}");
                    }
                }

                DB::commit();
                Log::info("Successfully completed cascading deletion for product ID: {$product->id}");

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Error during cascading deletion for product ID: {$product->id}. Error: " . $e->getMessage());
                throw $e; // Re-throw to prevent deletion if cleanup fails
            }
        });
    }

    /**
     * Get the image attribute with relative path for use with asset() helper.
     * No longer falls back to default color image if main image is missing.
     *
     * @param  string|null  $value
     * @return string|null
     */
    public function getImageAttribute($value)
    {
        // Return the full image URL using Laravel routes to avoid 403 errors
        return \App\Helpers\ImageHelper::getFullImageUrl($value);
    }



    /**
     * Get the default color for the product.
     *
     * @return \App\Models\ProductColor|null
     */
    public function getDefaultColor()
    {
        // Try to get the default color
        $defaultColor = $this->colors()->where('is_default', true)->first();

        // If no default color is set, get the first color
        if (!$defaultColor) {
            $defaultColor = $this->colors()->first();
        }

        return $defaultColor;
    }

    /**
     * Get the default color image for the product.
     * This method exclusively returns the default color image without falling back to the main product image.
     *
     * @return string|null
     */
    public function getDefaultColorImage()
    {
        // Get the default color
        $defaultColor = $this->getDefaultColor();

        // If we have a default color with an image, return it
        if ($defaultColor && $defaultColor->image) {
            \Illuminate\Support\Facades\Log::debug("Product {$this->id} default color image from color {$defaultColor->id}: {$defaultColor->image}");
            return $defaultColor->image;
        }

        // If no default color image is available, return null (no fallback to main product image)
        \Illuminate\Support\Facades\Log::debug("Product {$this->id} has no default color image");
        return null;
    }

    /**
     * Get the raw image path without URL processing.
     *
     * @return string|null
     */
    public function getRawImagePath()
    {
        return parent::getRawOriginal('image');
    }

    /**
     * Update the product's main image from a color image.
     *
     * @param  string|null  $colorImagePath
     * @return bool
     */
    public function updateMainImageFromColorImage($colorImagePath)
    {
        if (!$colorImagePath) {
            return false;
        }

        // Log the image path we're using
        \Illuminate\Support\Facades\Log::debug("Updating product {$this->id} main image to: {$colorImagePath}");

        // Update the product's main image to match the color image
        $this->image = $colorImagePath;

        // Ensure the image is saved correctly
        $saved = $this->save();

        // Log the result
        if ($saved) {
            \Illuminate\Support\Facades\Log::debug("Successfully updated product image to: {$this->getRawImagePath()}");
        } else {
            \Illuminate\Support\Facades\Log::error("Failed to update product image");
        }

        return $saved;
    }

    /**
     * Get the branch that owns the product.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the user that owns the product.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the reviews for the product.
     */
    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }



    /**
     * Get the provider products pivot records.
     */
    public function providerProducts()
    {
        return $this->hasMany(ProviderProduct::class, 'product_id');
    }

    /**
     * Get the providers that have this product.
     */
    public function providers()
    {
        return $this->belongsToMany(Provider::class, 'provider_products', 'product_id', 'provider_id');
    }



    /**
     * Get the specifications for the product.
     */
    public function specifications()
    {
        return $this->hasMany(ProductSpecification::class)
            ->orderBy('display_order');
    }

    /**
     * Get the colors for the product.
     */
    public function colors()
    {
        return $this->hasMany(ProductColor::class)
            ->orderBy('display_order');
    }

    /**
     * Get the sizes for the product.
     */
    public function sizes()
    {
        return $this->hasMany(ProductSize::class)
            ->orderBy('display_order');
    }

    /**
     * Get the color-size combinations for the product.
     */
    public function colorSizes()
    {
        return $this->hasMany(ProductColorSize::class);
    }

    /**
     * Get available color-size combinations.
     */
    public function availableColorSizes()
    {
        return $this->hasMany(ProductColorSize::class)
            ->where('is_available', true)
            ->where('stock', '>', 0);
    }

    /**
     * Get the branches for the product (for multi-branch products).
     */
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'product_branches')
            ->withPivot('stock', 'is_available', 'price')
            ->withTimestamps();
    }

    /**
     * Get the product-branch pivot records.
     */
    public function productBranches()
    {
        return $this->hasMany(ProductBranch::class);
    }

    /**
     * Scope a query to filter products by price range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  float  $min
     * @param  float  $max
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByPrice($query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    /**
     * Scope a query to filter products by minimum rating.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  float  $rating
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByRating($query, $rating)
    {
        return $query->where('rating', '>=', $rating);
    }

    /**
     * Scope a query to filter products by availability.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  bool  $available
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByAvailability($query, $available = true)
    {
        return $query->where('is_available', $available);
    }

    /**
     * Scope a query to filter products by stock status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  bool  $inStock
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByStock($query, $inStock = true)
    {
        return $inStock ? $query->where('stock', '>', 0) : $query;
    }

    /**
     * Scope a query to filter products by discount status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  bool  $hasDiscount
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByDiscount($query, $hasDiscount = true)
    {
        return $hasDiscount
            ? $query->whereNotNull('original_price')->whereColumn('original_price', '>', 'price')
            : $query;
    }

    /**
     * Scope a query to filter products by category.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $categoryId
     * @param  bool  $includeSubcategories Whether to include products from subcategories
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByCategory($query, $categoryId, $includeSubcategories = false)
    {
        if (!$includeSubcategories) {
            return $query->where('category_id', $categoryId);
        }

        // Get the category and its subcategories
        $category = Category::find($categoryId);
        if (!$category) {
            return $query->where('category_id', $categoryId);
        }

        // Get all subcategory IDs
        $subcategoryIds = $category->children()->pluck('id')->toArray();

        // Include the parent category ID and all subcategory IDs
        $allCategoryIds = array_merge([$categoryId], $subcategoryIds);

        return $query->whereIn('category_id', $allCategoryIds);
    }

    /**
     * Scope a query to filter products by branch.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $branchId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope a query to filter products by emirate.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $emirate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByEmirate($query, $emirate)
    {
        // Map emirate codes to emirate names
        $emirateMapping = [
            'DXB' => 'Dubai',
            'AUH' => 'Abu Dhabi',
            'SHJ' => 'Sharjah',
            'AJM' => 'Ajman',
            'RAK' => 'Ras Al Khaimah',
            'FUJ' => 'Fujairah',
            'UAQ' => 'Umm Al Quwain',
        ];

        // Check if the provided emirate is a code or name
        $emirateName = $emirateMapping[$emirate] ?? $emirate;

        return $query->whereHas('branch', function ($branchQuery) use ($emirateName, $emirate) {
            // Filter by either the mapped name or the original value (in case it's already a name)
            $branchQuery->where(function ($q) use ($emirateName, $emirate) {
                $q->where('emirate', $emirateName)
                  ->orWhere('emirate', $emirate);
            });
        });
    }

    /**
     * Scope a query to search products by name or description.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Scope a query to filter featured products.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  bool  $featured
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByFeatured($query, $featured = true)
    {
        return $query->where('featured', $featured);
    }

    /**
     * Scope a query to filter products that have active deals.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByActiveDeals($query)
    {
        $today = now()->format('Y-m-d');

        return $query->whereRaw("EXISTS (
            SELECT 1 FROM deals
            WHERE deals.status = 'active'
            AND deals.start_date <= '$today'
            AND deals.end_date >= '$today'
            AND (
                (deals.applies_to = 'all' AND EXISTS (
                    SELECT 1 FROM branches
                    INNER JOIN companies ON branches.company_id = companies.id
                    WHERE branches.id = products.branch_id
                    AND companies.user_id = deals.user_id
                ))
                OR (deals.applies_to = 'categories' AND JSON_SEARCH(deals.category_ids, 'one', products.category_id) IS NOT NULL)
                OR (deals.applies_to = 'products' AND JSON_SEARCH(deals.product_ids, 'one', products.id) IS NOT NULL)
            )
        )");
    }

    /**
     * Scope a query to sort products.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $sortBy
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortBy($query, $sortBy)
    {
        switch ($sortBy) {
            case 'price_low':
                return $query->orderBy('price', 'asc');
            case 'price_high':
                return $query->orderBy('price', 'desc');
            case 'rating':
                return $query->orderBy('rating', 'desc');
            case 'newest':
                return $query->orderBy('created_at', 'desc');
            case 'popularity':
            default:
                return $query->orderBy('rating', 'desc');
        }
    }
}
