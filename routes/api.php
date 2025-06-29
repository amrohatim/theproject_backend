<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\BranchController;
use App\Http\Controllers\API\ServiceController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\CheckoutController;
use App\Http\Controllers\API\CompanyController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\ProviderController;
use App\Http\Controllers\API\ProviderRatingController;
use App\Http\Controllers\API\DealController;
use App\Http\Controllers\API\FilterController;
use App\Http\Controllers\API\SearchController;
use App\Http\Controllers\API\UserLocationController;
use App\Http\Controllers\API\VendorRegistrationController;
use App\Http\Controllers\API\ProviderRegistrationController;
use App\Http\Controllers\API\MerchantRegistrationController;
use App\Http\Controllers\API\EmailVerificationController;

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

// Health check endpoint
Route::get('/health-check', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API is running',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
});

// Public routes (no authentication required)
Route::prefix('public')->group(function () {
    Route::get('/branches', [BranchController::class, 'index']);
    Route::get('/branches/{id}', [BranchController::class, 'show']);
    Route::get('/companies/{id}', [CompanyController::class, 'show']);
    Route::get('/companies/{id}/branches', [CompanyController::class, 'getBranches']);
    Route::post('/branches/{id}/track-view', [BranchController::class, 'trackView']);

    // Provider public routes
    Route::get('/providers/{id}/ratings', [ProviderRatingController::class, 'index']);
});

// Public deal routes (no authentication required)
Route::get('/active-deals', [DealController::class, 'getActiveDeals']);

// Public trending routes (no authentication required)
Route::get('/top-vendors', [CompanyController::class, 'topVendors']);
Route::get('/popular-branches', [BranchController::class, 'popular']);
Route::get('/trending-categories', [CategoryController::class, 'trending']);

// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Email verification routes for vendor/provider registration (using Laravel email system)
Route::prefix('vendor/register')->group(function () {
    Route::post('/send-firebase-email-verification', [EmailVerificationController::class, 'sendVendorEmailVerification']);
    Route::post('/check-firebase-email-verification', [EmailVerificationController::class, 'checkVendorEmailVerification']);
});

Route::prefix('provider/register')->group(function () {
    Route::post('/send-firebase-email-verification', [EmailVerificationController::class, 'sendProviderEmailVerification']);
    Route::post('/check-firebase-email-verification', [EmailVerificationController::class, 'checkProviderEmailVerification']);
});

Route::prefix('merchant/register')->group(function () {
    Route::post('/send-firebase-email-verification', [EmailVerificationController::class, 'sendMerchantEmailVerification']);
    Route::post('/check-firebase-email-verification', [EmailVerificationController::class, 'checkMerchantEmailVerification']);
});

// Vendor Registration routes (public)
Route::prefix('vendor-registration')->group(function () {
    Route::post('/info', [VendorRegistrationController::class, 'registerVendorInfo']);
    Route::post('/verify-email', [VendorRegistrationController::class, 'verifyEmail']);
    Route::post('/company', [VendorRegistrationController::class, 'registerCompanyInfo']);
    Route::post('/license', [VendorRegistrationController::class, 'uploadLicense']);
    Route::post('/send-otp', [VendorRegistrationController::class, 'sendOtp']);
    Route::post('/verify-otp', [VendorRegistrationController::class, 'verifyOtp']);
    Route::get('/status', [VendorRegistrationController::class, 'getRegistrationStatus']);
    Route::post('/resend-email-verification', [VendorRegistrationController::class, 'resendEmailVerification']);
});

// Provider Registration routes (public)
Route::prefix('provider-registration')->group(function () {
    Route::post('/info', [ProviderRegistrationController::class, 'registerProviderInfo']);
    Route::post('/verify-email', [ProviderRegistrationController::class, 'verifyEmail']);
    Route::post('/license', [ProviderRegistrationController::class, 'uploadLicense']);
    Route::post('/send-otp', [ProviderRegistrationController::class, 'sendOtp']);
    Route::post('/verify-otp', [ProviderRegistrationController::class, 'verifyOtp']);
    Route::get('/status', [ProviderRegistrationController::class, 'getRegistrationStatus']);
    Route::post('/resend-email-verification', [ProviderRegistrationController::class, 'resendEmailVerification']);
    Route::post('/add-location', [ProviderRegistrationController::class, 'addVendorLocation']);
});

// Merchant Registration routes (public)
Route::prefix('merchant-registration')->group(function () {
    Route::post('/info', [MerchantRegistrationController::class, 'registerMerchantInfo']);
    Route::post('/verify-email', [MerchantRegistrationController::class, 'verifyEmail']);
    Route::post('/license', [MerchantRegistrationController::class, 'uploadLicense']);
    Route::post('/send-otp', [MerchantRegistrationController::class, 'sendOtp']);
    Route::post('/verify-otp', [MerchantRegistrationController::class, 'verifyOtp']);
    Route::get('/status', [MerchantRegistrationController::class, 'getRegistrationStatus']);
    Route::post('/resend-email-verification', [MerchantRegistrationController::class, 'resendEmailVerification']);
});

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [UserController::class, 'profile']);
    Route::put('/profile', [UserController::class, 'updateProfile']);

    // User management (admin only)
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);

    // User locations
    Route::get('/user/locations', [UserController::class, 'getLocations']);
    Route::get('/user-locations', [UserLocationController::class, 'index']);
    Route::post('/user-locations', [UserLocationController::class, 'store']);
    Route::get('/user-locations/{id}', [UserLocationController::class, 'show']);
    Route::put('/user-locations/{id}', [UserLocationController::class, 'update']);
    Route::delete('/user-locations/{id}', [UserLocationController::class, 'destroy']);
    Route::put('/user-locations/{id}/set-default', [UserLocationController::class, 'setDefault']);

    // Branch routes
    Route::get('/branches', [BranchController::class, 'index']);
    Route::get('/branches/nearby', [BranchController::class, 'getNearbyBranches']); // Must be before {id} route
    Route::get('/branches/{id}', [BranchController::class, 'show']);
    Route::put('/branches/{id}', [BranchController::class, 'update']);
    Route::put('/branches/{id}/featured', [BranchController::class, 'updateFeatured']);
    Route::post('/branches/{id}/track-view', [BranchController::class, 'trackView']);
    Route::post('/branches/{id}/track-order', [BranchController::class, 'trackOrder']);

    Route::get('/all-branches', [BranchController::class, 'getAll']);

    // Service routes
    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/services/{id}', [ServiceController::class, 'show']);
    Route::put('/services/{id}/featured', [ServiceController::class, 'updateFeatured']);

    // Product routes
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::get('/products/{id}/options', [ProductController::class, 'getOptions']);
    Route::post('/products/{id}/validate-options', [ProductController::class, 'validateOptions']);
    Route::put('/products/{id}/featured', [ProductController::class, 'updateFeatured']);
    
    // Stock management routes
    Route::get('/products/{id}/stock-availability', [ProductController::class, 'checkStockAvailability']);
    Route::get('/products/{id}/available-stock', [ProductController::class, 'getAvailableStock']);
    Route::get('/products/{id}/stock-info', [ProductController::class, 'getStockInfo']);

    // Featured content routes
    Route::get('/featured/products', [ProductController::class, 'getFeatured']);
    Route::get('/featured/services-deals', [ServiceController::class, 'getServicesWithDeals']);

    // Provider routes - specific routes first to avoid conflicts
    Route::get('/providers/categories-with-products', [ProviderController::class, 'getCategoriesWithProducts']);
    Route::get('/providers/products/category/{categoryId}', [ProviderController::class, 'getProductsByCategory']);
    Route::get('/providers/products', [ProviderController::class, 'getAllProducts']);
    Route::get('/providers/analytics', [ProviderController::class, 'getAnalytics']);
    Route::get('/providers', [ProviderController::class, 'index']);
    Route::get('/providers/{id}', [ProviderController::class, 'show']);
    Route::get('/providers/{id}/locations', [ProviderController::class, 'getProviderLocations']);
    Route::get('/providers/{id}/products', [ProviderController::class, 'getProducts']);
    Route::post('/providers/{id}/track-view', [ProviderController::class, 'trackView']);
    Route::post('/providers/{id}/track-order', [ProviderController::class, 'trackOrder']);

    // Provider rating routes
    Route::get('/providers/{id}/ratings', [ProviderRatingController::class, 'index']);
    Route::post('/providers/{id}/ratings', [ProviderRatingController::class, 'store']);
    Route::get('/providers/{id}/my-rating', [ProviderRatingController::class, 'show']);
    Route::delete('/providers/{id}/ratings', [ProviderRatingController::class, 'destroy']);

    // Booking routes
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::get('/bookings/{id}', [BookingController::class, 'show']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::put('/bookings/{id}', [BookingController::class, 'update']);
    Route::put('/bookings/{id}/cancel', [BookingController::class, 'cancel']);
    Route::post('/bookings/check-availability', [BookingController::class, 'checkAvailability']);

    // Order routes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::put('/orders/{id}/cancel', [OrderController::class, 'cancel']);
    Route::get('/orders/{id}/shipment', [OrderController::class, 'getShipment']);
    Route::post('/track-shipment', [OrderController::class, 'trackShipment']);

    // Checkout routes
    Route::post('/checkout', [CheckoutController::class, 'placeOrder']);

    // Company routes
    Route::get('/companies/{id}', [CompanyController::class, 'show']);
    Route::get('/companies/{id}/branches', [CompanyController::class, 'getBranches']);
    Route::post('/companies', [CompanyController::class, 'store']);
    Route::put('/companies/{id}', [CompanyController::class, 'update']);
    Route::get('/my-company', [CompanyController::class, 'getMyCompany']);

    Route::post('/companies/{id}/track-view', [CompanyController::class, 'trackView']);
    Route::post('/companies/{id}/track-order', [CompanyController::class, 'trackOrder']);
    Route::post('/companies/{id}/add-rating', [CompanyController::class, 'addRating']);

    // Category routes
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);

    Route::get('/categories-with-deals', [CategoryController::class, 'getCategoriesWithDeals']);
    Route::post('/categories/{id}/track-view', [CategoryController::class, 'trackView']);
    Route::post('/categories/{id}/track-purchase', [CategoryController::class, 'trackPurchase']);

    // Dashboard routes
    Route::get('/dashboard/admin', [DashboardController::class, 'getAdminStats']);
    Route::get('/dashboard/vendor', [DashboardController::class, 'getVendorStats']);

    // Deal routes
    Route::get('/deals', [DealController::class, 'index']);
    Route::post('/deals', [DealController::class, 'store']);
    Route::put('/deals/{id}', [DealController::class, 'update']);
    Route::delete('/deals/{id}', [DealController::class, 'destroy']);
    Route::get('/deals/{id}/products', [DealController::class, 'getProducts']);
    Route::get('/deals/{id}/services', [DealController::class, 'getServices']);
    Route::get('/deals/{id}/analytics', [DealController::class, 'getAnalytics']);
    Route::get('/deals/analytics', [DealController::class, 'getAllAnalytics']);

    // Filter routes
    // Route::get('/product-colors', [FilterController::class, 'getColors']);
    // Route::get('/standardized-sizes', [FilterController::class, 'getSizes']);
    Route::post('/search/filter', [\App\Http\Controllers\API\SearchController::class, 'filter']);

    // Image serving routes
    Route::get('/product-image/{filename}', [ProductController::class, 'getImage']);
    Route::get('/provider-product-image/{filename}', [ProductController::class, 'getProviderProductImage']);
});
