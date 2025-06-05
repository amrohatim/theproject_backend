<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ServiceController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CompanyController;
use App\Http\Controllers\API\BranchController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PaymentMethodController;
use App\Http\Controllers\API\PayoutMethodController;
use App\Http\Controllers\API\ImageTestController;
use App\Http\Controllers\API\CheckoutController;
use App\Http\Controllers\API\ShipmentController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\DealController;
use App\Http\Controllers\API\ProductSpecificationController;
use App\Http\Controllers\API\ServiceSpecificationController;
use App\Http\Controllers\API\ProductBranchController;
use App\Http\Controllers\API\ImageUploadController;
use App\Http\Controllers\API\ProviderController;
use App\Http\Controllers\API\VendorRatingController;
use App\Http\Controllers\API\BranchRatingController;
use App\Http\Controllers\API\ProviderRatingController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\SizeCategoryController;
use App\Http\Controllers\API\SearchController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Health check endpoint for testing API connectivity
Route::get('/health-check', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Laravel API is running',
        'timestamp' => now()->toDateTimeString(),
    ]);
});

// Database seeding endpoint (admin only)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/seed-database', function (Request $request) {
        // Check if user is admin
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only administrators can seed the database.',
            ], 403);
        }

        try {
            // Run the database seeders
            Artisan::call('db:seed');

            return response()->json([
                'success' => true,
                'message' => 'Database seeded successfully',
                'output' => Artisan::output(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to seed database: ' . $e->getMessage(),
            ], 500);
        }
    });
});

// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
});

// Public product routes (no auth required)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// Public service routes (no auth required)
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{id}', [ServiceController::class, 'show']);

// Protected product routes (auth required)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    Route::put('/products/{id}/featured', [ProductController::class, 'updateFeatured']);
});

// Featured routes (no auth required for public display)
Route::get('/featured/products', [ProductController::class, 'featured']);
Route::get('/featured/services', [ServiceController::class, 'featured']);
Route::get('/featured/branches', [BranchController::class, 'featured']);
Route::get('/featured/all', [DashboardController::class, 'featuredItems']);
Route::get('/featured/deals', [ProductController::class, 'deals']);

// Image test routes (no auth required)
Route::get('/test/images', [ImageTestController::class, 'testImages']);
Route::get('/test/image-urls', function() {
    return response()->json([
        'success' => true,
        'app_url' => config('app.url'),
        'storage_link_exists' => file_exists(public_path('storage')),
        'test_urls' => [
            'direct_image_url' => url('/images/products/smartphone-x.jpg'),
            'storage_image_url' => url('/storage/products/pisHTvjmajAKcCn0DW4k8GCWUfVgEzrHdB7JkKKr.png'),
        ]
    ]);
});

// Image upload route (auth required)
Route::middleware('auth:sanctum')->post('/upload-image', [ImageUploadController::class, 'upload']);

// Direct image access route (no auth required)
Route::get('/direct-image/{folder}/{filename}', function ($folder, $filename) {
    $path = "app/public/{$folder}/{$filename}";
    $fullPath = storage_path($path);

    if (!file_exists($fullPath)) {
        return response()->json(['error' => 'Image not found'], 404);
    }

    $file = file_get_contents($fullPath);
    $type = mime_content_type($fullPath);

    return response($file, 200)
        ->header('Content-Type', $type)
        ->header('Content-Disposition', 'inline; filename="' . $filename . '"')
        ->header('Cache-Control', 'public, max-age=86400')
        ->header('Access-Control-Allow-Origin', '*');
});

// Special route for product color images (no auth required)
Route::get('/product-color-image/{filename}', function ($filename) {
    // Try multiple possible locations
    $possiblePaths = [
        storage_path("app/public/product-colors/{$filename}"),
        public_path("storage/product-colors/{$filename}"),
        public_path("images/product-colors/{$filename}")
    ];

    $fullPath = null;
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            $fullPath = $path;
            break;
        }
    }

    if (!$fullPath) {
        return response()->json(['error' => 'Image not found'], 404);
    }

    $file = file_get_contents($fullPath);
    $type = mime_content_type($fullPath);

    return response($file, 200)
        ->header('Content-Type', $type)
        ->header('Content-Disposition', 'inline; filename="' . $filename . '"')
        ->header('Cache-Control', 'public, max-age=86400')
        ->header('Access-Control-Allow-Origin', '*');
});

// Special route for provider product images (no auth required)
Route::get('/provider-product-image/{filename}', function ($filename) {
    // Try multiple possible locations
    $possiblePaths = [
        public_path("images/provider_products/{$filename}"),
        storage_path("app/public/provider_products/{$filename}"),
        public_path("storage/provider_products/{$filename}")
    ];

    $fullPath = null;
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            $fullPath = $path;
            break;
        }
    }

    if (!$fullPath) {
        \Illuminate\Support\Facades\Log::warning("Provider product image not found: {$filename}");
        return response()->json(['error' => 'Provider product image not found'], 404);
    }

    $file = file_get_contents($fullPath);
    $type = mime_content_type($fullPath);

    return response($file, 200)
        ->header('Content-Type', $type)
        ->header('Content-Disposition', 'inline; filename="' . $filename . '"')
        ->header('Cache-Control', 'public, max-age=86400')
        ->header('Access-Control-Allow-Origin', '*');
});

// Special route for regular product images (no auth required)
Route::get('/product-image/{filename}', function ($filename) {
    // Try multiple possible locations
    $possiblePaths = [
        public_path("images/products/{$filename}"),
        storage_path("app/public/products/{$filename}"),
        public_path("storage/products/{$filename}")
    ];

    $fullPath = null;
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            $fullPath = $path;
            break;
        }
    }

    if (!$fullPath) {
        \Illuminate\Support\Facades\Log::warning("Product image not found: {$filename}");
        return response()->json(['error' => 'Product image not found'], 404);
    }

    $file = file_get_contents($fullPath);
    $type = mime_content_type($fullPath);

    return response($file, 200)
        ->header('Content-Type', $type)
        ->header('Content-Disposition', 'inline; filename="' . $filename . '"')
        ->header('Cache-Control', 'public, max-age=86400')
        ->header('Access-Control-Allow-Origin', '*');
});

// Special route for service images (no auth required)
Route::get('/service-image/{filename}', function ($filename) {
    // Try multiple possible locations
    $possiblePaths = [
        storage_path("app/public/services/{$filename}"),
        public_path("storage/services/{$filename}"),
        public_path("images/services/{$filename}")
    ];

    $fullPath = null;
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            $fullPath = $path;
            break;
        }
    }

    if (!$fullPath) {
        \Illuminate\Support\Facades\Log::warning("Service image not found: {$filename}");
        return response()->json(['error' => 'Service image not found'], 404);
    }

    $file = file_get_contents($fullPath);
    $type = mime_content_type($fullPath);

    return response($file, 200)
        ->header('Content-Type', $type)
        ->header('Content-Disposition', 'inline; filename="' . $filename . '"')
        ->header('Cache-Control', 'public, max-age=86400')
        ->header('Access-Control-Allow-Origin', '*');
});

// Special route for category images (no auth required)
Route::get('/category-image/{filename}', function ($filename) {
    // Try multiple possible locations
    $possiblePaths = [
        storage_path("app/public/categories/{$filename}"),
        public_path("storage/categories/{$filename}"),
        public_path("images/categories/{$filename}")
    ];

    $fullPath = null;
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            $fullPath = $path;
            break;
        }
    }

    if (!$fullPath) {
        return response()->json(['error' => 'Category image not found'], 404);
    }

    $file = file_get_contents($fullPath);
    $type = mime_content_type($fullPath);

    return response($file, 200)
        ->header('Content-Type', $type)
        ->header('Content-Disposition', 'inline; filename="' . $filename . '"')
        ->header('Cache-Control', 'public, max-age=86400')
        ->header('Access-Control-Allow-Origin', '*');
});

// Checkout, Orders, Bookings, and Shipment routes
Route::middleware('auth:sanctum')->group(function () {
    // Checkout
    Route::post('/checkout', [CheckoutController::class, 'placeOrder']);

    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::put('/orders/{id}/cancel', [OrderController::class, 'cancel']);

    // Bookings
    Route::get('/bookings', [App\Http\Controllers\API\BookingController::class, 'index']);
    Route::post('/bookings', [App\Http\Controllers\API\BookingController::class, 'store']);
    Route::get('/bookings/{id}', [App\Http\Controllers\API\BookingController::class, 'show']);
    Route::put('/bookings/{id}', [App\Http\Controllers\API\BookingController::class, 'update']);
    Route::put('/bookings/{id}/cancel', [App\Http\Controllers\API\BookingController::class, 'cancel']);
    Route::post('/bookings/check-availability', [App\Http\Controllers\API\BookingController::class, 'checkAvailability']);

    // Shipment tracking
    Route::get('/orders/{orderId}/shipment', [ShipmentController::class, 'getShipmentDetails']);
});

// Public shipment tracking (no auth required)
Route::post('/track-shipment', [ShipmentController::class, 'trackShipment']);

// Test image URL for a specific category
Route::get('/test/category-image/{id}', function ($id) {
    $category = \App\Models\Category::find($id);
    if (!$category) {
        return response()->json(['error' => 'Category not found'], 404);
    }

    $imagePath = $category->image;
    $relativePath = str_replace(config('app.url'), '', $imagePath);
    $publicPath = public_path(ltrim($relativePath, '/'));

    return response()->json([
        'success' => true,
        'category' => $category->toArray(),
        'image_url' => $imagePath,
        'app_url' => config('app.url'),
        'storage_link_exists' => file_exists(public_path('storage')),
        'image_exists' => $imagePath ? file_exists($publicPath) : false,
        'relative_path' => $relativePath,
        'public_path' => $publicPath,
        'alternative_urls' => [
            'storage_url' => url('/storage/categories/' . basename($imagePath)),
            'direct_url' => url('/images/categories/' . basename($imagePath)),
        ]
    ]);
});

// Protected service routes (auth required)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/services', [ServiceController::class, 'store']);
    Route::put('/services/{id}', [ServiceController::class, 'update']);
    Route::delete('/services/{id}', [ServiceController::class, 'destroy']);
    Route::put('/services/{id}/featured', [ServiceController::class, 'updateFeatured']);
});

// Public category routes (no auth required)
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::get('/trending-categories', [CategoryController::class, 'trending']);
Route::get('/categories-with-deals', [CategoryController::class, 'categoriesWithDeals']);

// Protected category routes (auth required)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    Route::post('/categories/{id}/track-view', [CategoryController::class, 'trackView']);
    Route::post('/categories/{id}/track-purchase', [CategoryController::class, 'trackPurchase']);
});

// User routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile', [UserController::class, 'updateProfile']);

    // User locations
    Route::get('/user/locations', [App\Http\Controllers\API\UserLocationController::class, 'index']);
    Route::post('/user/locations', [App\Http\Controllers\API\UserLocationController::class, 'store']);
    Route::get('/user/locations/{id}', [App\Http\Controllers\API\UserLocationController::class, 'show']);
    Route::put('/user/locations/{id}', [App\Http\Controllers\API\UserLocationController::class, 'update']);
    Route::delete('/user/locations/{id}', [App\Http\Controllers\API\UserLocationController::class, 'destroy']);
    Route::post('/user/locations/{id}/default', [App\Http\Controllers\API\UserLocationController::class, 'setDefault']);
});

// Company routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/companies', [CompanyController::class, 'index']);
    Route::post('/companies', [CompanyController::class, 'store']);
    Route::get('/companies/{id}', [CompanyController::class, 'show']);
    Route::put('/companies/{id}', [CompanyController::class, 'update']);
    Route::delete('/companies/{id}', [CompanyController::class, 'destroy']);
    Route::get('/my-company', [CompanyController::class, 'myCompany']);
    Route::post('/companies/{id}/track-view', [CompanyController::class, 'trackView']);
    Route::post('/companies/{id}/track-order', [CompanyController::class, 'trackOrder']);
    Route::post('/companies/{id}/add-rating', [CompanyController::class, 'addRating']);
});

// Public company routes (no auth required)
Route::get('/top-vendors', [CompanyController::class, 'topVendors']);
Route::get('/public/companies/{id}', [CompanyController::class, 'publicShow']);
Route::get('/public/companies/{id}/branches', [CompanyController::class, 'publicCompanyBranches']);

// Public branch routes (no auth required)
Route::get('/public/branches', [BranchController::class, 'publicIndex']);
Route::get('/public/branches/{id}', [BranchController::class, 'show']);
Route::get('/popular-branches', [BranchController::class, 'popular']);
Route::get('/all-branches', [BranchController::class, 'allBranches']);

// Protected branch routes (auth required)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/branches', [BranchController::class, 'index']);
    Route::get('/branches/{id}', [BranchController::class, 'show']);
    Route::post('/branches', [BranchController::class, 'store']);
    Route::put('/branches/{id}', [BranchController::class, 'update']);
    Route::delete('/branches/{id}', [BranchController::class, 'destroy']);
    Route::get('/my-branches', [BranchController::class, 'myBranches']);
    Route::put('/branches/{id}/featured', [BranchController::class, 'updateFeatured']);
    Route::post('/branches/{id}/track-view', [BranchController::class, 'trackView']);
    Route::post('/branches/{id}/track-order', [BranchController::class, 'trackOrder']);
});

// Dashboard routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard/admin', [DashboardController::class, 'adminStats']);
    Route::get('/dashboard/vendor', [DashboardController::class, 'vendorStats']);
});

// Deal routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/deals', [DealController::class, 'index']);
    Route::get('/deals/{id}', [DealController::class, 'show']);
    Route::post('/deals', [DealController::class, 'store']);
    Route::put('/deals/{id}', [DealController::class, 'update']);
    Route::delete('/deals/{id}', [DealController::class, 'destroy']);
});

// Public deal routes (no auth required)
Route::get('/active-deals', function (Request $request) {
    // Create a new request with status=active parameter
    $request->merge(['status' => 'active']);
    return app()->make(App\Http\Controllers\API\DealController::class)->index($request);
});

// Deal products route (no auth required)
Route::get('/deals/{id}/products', [DealController::class, 'getProducts']);

// Public Product Specifications routes (no auth required)
Route::get('/products/{productId}/specifications', [ProductSpecificationController::class, 'getSpecifications']);
Route::get('/products/{productId}/colors', [ProductSpecificationController::class, 'getColors']);
Route::get('/products/{productId}/sizes', [ProductSpecificationController::class, 'getSizes']);

// Filter-specific routes (no auth required)
Route::get('/product-colors', [ProductSpecificationController::class, 'getAllProductColors']);
Route::get('/standardized-colors', [ProductSpecificationController::class, 'getStandardizedColors']);
Route::get('/standardized-sizes', [ProductSpecificationController::class, 'getStandardizedSizes']);

// Protected Product Specifications routes (auth required)
Route::middleware('auth:sanctum')->group(function () {
    // Product specifications
    Route::post('/products/{productId}/specifications', [ProductSpecificationController::class, 'updateSpecifications']);

    // Product colors
    Route::post('/products/{productId}/colors', [ProductSpecificationController::class, 'updateColors']);

    // Product sizes
    Route::post('/products/{productId}/sizes', [ProductSpecificationController::class, 'updateSizes']);
});

// Size Category routes (public and protected)
// Public size category routes (no auth required)
Route::get('/size-categories', [SizeCategoryController::class, 'index']);
Route::get('/size-categories/{categoryName}/sizes', [SizeCategoryController::class, 'getSizes']);
Route::get('/categories/{categoryId}/default-sizes', [SizeCategoryController::class, 'getCategoryDefaultSizes']);
Route::get('/size-data', [SizeCategoryController::class, 'getSizeData']);

// Protected size category routes (auth required)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/size-categories/validate', [SizeCategoryController::class, 'validateSize']);
});

// Service Specifications routes
Route::middleware('auth:sanctum')->group(function () {
    // Service specifications
    Route::get('/services/{serviceId}/specifications', [ServiceSpecificationController::class, 'getSpecifications']);
    Route::post('/services/{serviceId}/specifications', [ServiceSpecificationController::class, 'updateSpecifications']);

    // Service specification templates
    Route::get('/categories/{categoryId}/service-templates', [ServiceSpecificationController::class, 'getTemplates']);
    Route::post('/categories/{categoryId}/service-templates', [ServiceSpecificationController::class, 'updateTemplates']);
    Route::post('/services/{serviceId}/apply-templates', [ServiceSpecificationController::class, 'applyTemplates']);
});

// Multi-Branch Product routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/products/{productId}/branches', [ProductBranchController::class, 'getBranches']);
    Route::post('/products/{productId}/branches', [ProductBranchController::class, 'updateBranches']);
    Route::post('/products/add-to-branches', [ProductBranchController::class, 'addProductToBranches']);
});

// Provider routes (both public and protected)
// Public provider routes (no auth required)
Route::get('/providers', [ProviderController::class, 'index']);
Route::get('/providers/products', [ProviderController::class, 'getAllProducts']);
Route::get('/providers/products/category/{categoryId}', [ProviderController::class, 'getProductsByCategory']);
Route::get('/providers/analytics', [ProviderController::class, 'getAnalytics']);
Route::get('/providers/{id}', [ProviderController::class, 'show']);
Route::get('/providers/{id}/products', [ProviderController::class, 'getProviderProducts']);
Route::get('/providers/{id}/locations', [ProviderController::class, 'getProviderLocations']);

// Provider tracking routes (authenticated)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/providers/{id}/track-view', [ProviderController::class, 'trackView']);
    Route::post('/providers/{id}/track-order', [ProviderController::class, 'trackOrder']);
});

// Payment and Payout Method routes
Route::middleware(['auth'])->group(function () {
    Route::get('/payment-methods/{id}', [PaymentMethodController::class, 'show']);
    Route::get('/payout-methods/{id}', [PayoutMethodController::class, 'show']);
});

// Rating routes
Route::middleware('auth:sanctum')->group(function () {
    // Vendor ratings (customer → vendor)
    Route::get('/vendors/{vendorId}/ratings', [VendorRatingController::class, 'index']);
    Route::post('/vendors/{vendorId}/ratings', [VendorRatingController::class, 'store']);
    Route::get('/vendors/{vendorId}/my-rating', [VendorRatingController::class, 'show']);
    Route::delete('/vendors/{vendorId}/ratings', [VendorRatingController::class, 'destroy']);

    // Branch ratings (customer → branch)
    Route::get('/branches/{branchId}/ratings', [BranchRatingController::class, 'index']);
    Route::post('/branches/{branchId}/ratings', [BranchRatingController::class, 'store']);
    Route::get('/branches/{branchId}/my-rating', [BranchRatingController::class, 'show']);
    Route::delete('/branches/{branchId}/ratings', [BranchRatingController::class, 'destroy']);

    // Provider ratings (vendor → provider)
    Route::get('/providers/{providerId}/ratings', [ProviderRatingController::class, 'index']);
    Route::post('/providers/{providerId}/ratings', [ProviderRatingController::class, 'store']);
    Route::get('/providers/{providerId}/my-rating', [ProviderRatingController::class, 'show']);
    Route::delete('/providers/{providerId}/ratings', [ProviderRatingController::class, 'destroy']);
});

// Public rating routes (no auth required for viewing)
Route::get('/public/vendors/{vendorId}/ratings', [VendorRatingController::class, 'index']);
Route::get('/public/branches/{branchId}/ratings', [BranchRatingController::class, 'index']);
Route::get('/public/providers/{providerId}/ratings', [ProviderRatingController::class, 'index']);

// Public review routes (no auth required for viewing)
Route::get('/public/{type}/{id}/reviews', [ReviewController::class, 'index']);

// Search and filter routes (no auth required)
Route::post('/search/filter', [SearchController::class, 'filter']);

// Protected review routes (auth required)
Route::middleware('auth:sanctum')->group(function () {
    // Review management
    Route::post('/{type}/{id}/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{reviewId}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{reviewId}', [ReviewController::class, 'destroy']);
    Route::get('/{type}/{id}/my-review', [ReviewController::class, 'getUserReview']);
    Route::post('/reviews/{reviewId}/like', [ReviewController::class, 'toggleLike']);
});
