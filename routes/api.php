<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PasswordResetController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\BranchController;
use App\Http\Controllers\API\BranchRatingController;
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
use App\Http\Controllers\API\VendorRatingController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\DealController;
use App\Http\Controllers\API\FilterController;
use App\Http\Controllers\API\SearchController;
use App\Http\Controllers\API\ValidationController;
use App\Http\Controllers\API\UserLocationController;
use App\Http\Controllers\API\VendorRegistrationController;
use App\Http\Controllers\API\ProviderRegistrationController;
use App\Http\Controllers\API\MerchantRegistrationController;
use App\Http\Controllers\API\MerchantController;
use App\Http\Controllers\API\EmailVerificationController;
use App\Http\Controllers\API\ProductSpecificationController;
use App\Http\Controllers\API\BusinessTypeController;
use App\Http\Controllers\API\CustomerNotificationController;
use App\Http\Controllers\API\FcmTokenController;
use App\Http\Controllers\API\SizeCategoryController;
use App\Http\Controllers\API\ProviderWishlistController;
use App\Http\Controllers\API\GeneralWishlistController;
use App\Http\Controllers\API\GeneralWishlistServiceController;
use App\Http\Controllers\API\GeneralWishlistBranchController;
use App\Http\Controllers\API\GeneralWishlistMerchantController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\API\JobController as ApiJobController;
use App\Http\Controllers\API\JobApplicationController;

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

// Language routes (public)
Route::prefix('language')->group(function () {
    Route::get('/supported', [LanguageController::class, 'getSupportedLanguages']);
    Route::post('/switch', [LanguageController::class, 'switchLanguage']);
    Route::get('/current', [LanguageController::class, 'getCurrentLanguage']);
    Route::get('/rtl-info', [LanguageController::class, 'getRtlInfo']);
    Route::get('/switcher-data', [LanguageController::class, 'getLanguageSwitcherData']);
    Route::post('/format-number', [LanguageController::class, 'formatNumber']);
    Route::post('/convert-to-arabic-numbers', [LanguageController::class, 'convertToArabicNumbers']);
});

// Public routes (no authentication required)
Route::prefix('public')->group(function () {
    Route::get('/search/suggestions', [SearchController::class, 'suggestions']);
    Route::get('/branches', [BranchController::class, 'index']);
    Route::get('/branches/{id}', [BranchController::class, 'show']);
    Route::get('/companies', [CompanyController::class, 'publicIndex']);
    Route::get('/companies/{id}', [CompanyController::class, 'show']);
    Route::get('/companies/{id}/branches', [CompanyController::class, 'getBranches']);
    Route::post('/branches/{id}/track-view', [BranchController::class, 'trackView']);

    // Provider public routes
    Route::get('/providers/{id}/ratings', [ProviderRatingController::class, 'index']);

    // Vendor public routes
    Route::get('/vendors/{id}/ratings', [VendorRatingController::class, 'index']);
    Route::get('/companies/{id}/vendor-ratings', [VendorRatingController::class, 'getByCompanyId']);

    // Branch public routes
    Route::get('/branches/{id}/ratings', [BranchRatingController::class, 'index']);
    Route::get('/branches/{id}/products-by-category', [ProductController::class, 'branchProductsByCategory']);
    Route::get('/branches/{id}/services-by-category', [ServiceController::class, 'branchServicesByCategory']);

    // Review public routes
    Route::get('/{type}/{id}/reviews', [ReviewController::class, 'index']);

    // Image serving routes (public)
    Route::get('/company-logo/{filename}', [\App\Http\Controllers\ImageController::class, 'serveCompanyLogo']);
    Route::get('/direct-image/companies/{filename}', [\App\Http\Controllers\ImageController::class, 'serveCompanyLogo']);

    // Merchant public routes
    Route::get('/merchants', [MerchantController::class, 'index']);
    Route::get('/merchants/{id}', [MerchantController::class, 'show']);
    Route::get('/merchants/{id}/products', [MerchantController::class, 'getProducts']);
    Route::get('/merchants/{id}/services', [MerchantController::class, 'getServices']);
    Route::get('/merchants/{id}/deals', [MerchantController::class, 'getDeals']);
    Route::post('/merchants/{id}/track-view', [MerchantController::class, 'trackView']);
});

// Public deal routes (no authentication required)
Route::get('/active-deals', [DealController::class, 'getActiveDeals']);
Route::get('/deals/{id}/products', [DealController::class, 'getProducts']);
Route::get('/deals/{id}/services', [DealController::class, 'getServices']);

// Size categories (public)
Route::get('/size-categories', [SizeCategoryController::class, 'index']);

// Public trending routes (no authentication required)
Route::get('/top-vendors', [CompanyController::class, 'topVendors']);
Route::get('/popular-branches', [BranchController::class, 'popular']);
Route::get('/trending-categories', [CategoryController::class, 'trending']);
Route::get('/trending-products', [ProductController::class, 'trendingProducts']);
Route::get('/trending-services', [ServiceController::class, 'trendingServices']);

// Public business type routes (no authentication required)
Route::get('/business-types', [BusinessTypeController::class, 'index']);
Route::get('/business-types/from-branches', [BusinessTypeController::class, 'getFromBranches']);
Route::get('/business-types/suggestions', [BusinessTypeController::class, 'suggestions']);
Route::get('/business-types/branch-search', [BusinessTypeController::class, 'searchBranches']);
Route::get('/business-types/branches', [BusinessTypeController::class, 'getBranches']);
Route::get('/business-types/products', [BusinessTypeController::class, 'getProducts']);
Route::get('/business-types/services', [BusinessTypeController::class, 'getServices']);
Route::get('/business-types/categories', [BusinessTypeController::class, 'getCategories']);
Route::get('/emirates', [BusinessTypeController::class, 'getEmirates']);

// Debug route to test business types
Route::get('/debug/business-types', function() {
    $businessTypes = \App\Models\BusinessType::all();
    $branches = \App\Models\Branch::select('business_type')->whereNotNull('business_type')->groupBy('business_type')->get();

    return response()->json([
        'business_types_table' => $businessTypes,
        'unique_branch_types' => $branches,
        'storage_url' => url('storage/'),
        'app_url' => url('/'),
    ]);
});

// Public filter routes (no authentication required)
Route::get('/product-colors', [\App\Http\Controllers\API\ProductSpecificationController::class, 'getAllProductColors']);
Route::get('/standardized-sizes', [\App\Http\Controllers\API\ProductSpecificationController::class, 'getStandardizedSizes']);
Route::get('/available-sizes', [\App\Http\Controllers\API\ProductSpecificationController::class, 'getAvailableSizes']);

// Public content routes (no authentication required) - for guest browsing
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{id}', [ServiceController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::get('/featured/products', [ProductController::class, 'getFeatured']);
Route::get('/featured/services', [ServiceController::class, 'featured']);
Route::get('/jobs/featured', [ApiJobController::class, 'featured']);

// Public filter routes (no authentication required) - for guest filtering
Route::post('/search/filter', [\App\Http\Controllers\API\SearchController::class, 'filter']);

// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/login/social', [AuthController::class, 'socialLogin']);
Route::post('/password/forgot', [PasswordResetController::class, 'request']);
Route::post('/password/email', [PasswordResetController::class, 'request']);
Route::post('/password/verify-code', [PasswordResetController::class, 'verifyCode']);
Route::post('/password/reset', [PasswordResetController::class, 'reset']);
Route::post('/fcm/register', [FcmTokenController::class, 'register']);

// Customer Registration routes (public)
Route::prefix('customer-registration')->group(function () {
    Route::post('/send-email-verification', [App\Http\Controllers\API\CustomerRegistrationController::class, 'sendEmailVerification']);
    Route::post('/verify-email', [App\Http\Controllers\API\CustomerRegistrationController::class, 'verifyEmail']);
    Route::post('/send-phone-verification', [App\Http\Controllers\API\CustomerRegistrationController::class, 'sendPhoneVerification']);
    Route::post('/verify-phone', [App\Http\Controllers\API\CustomerRegistrationController::class, 'verifyPhone']);
    Route::post('/check-username', [App\Http\Controllers\API\CustomerRegistrationController::class, 'checkUsername']);
    Route::get('/avatars', [App\Http\Controllers\API\CustomerRegistrationController::class, 'getAvatars']);
    Route::post('/complete', [App\Http\Controllers\API\CustomerRegistrationController::class, 'completeRegistration']);
});

// Email verification routes for vendor/provider registration (using Laravel email system)
Route::prefix('vendor/register')->group(function () {
    Route::post('/validate-info', [VendorRegistrationController::class, 'registerVendorInfo']);
    Route::post('/send-firebase-email-verification', [EmailVerificationController::class, 'sendVendorEmailVerification']);
    Route::post('/check-firebase-email-verification', [EmailVerificationController::class, 'checkVendorEmailVerification']);
});

Route::prefix('provider/register')->group(function () {
    Route::post('/validate-info', [ProviderRegistrationController::class, 'registerProviderInfo']);
    Route::post('/send-firebase-email-verification', [EmailVerificationController::class, 'sendProviderEmailVerification']);
    Route::post('/check-firebase-email-verification', [EmailVerificationController::class, 'checkProviderEmailVerification']);
});

Route::prefix('merchant/register')->group(function () {
    Route::post('/send-firebase-email-verification', [EmailVerificationController::class, 'sendMerchantEmailVerification']);
    Route::post('/check-firebase-email-verification', [EmailVerificationController::class, 'checkMerchantEmailVerification']);
});

// Validation routes (public)
Route::prefix('validate')->group(function () {
    Route::post('/business-name', [ValidationController::class, 'validateBusinessName']);
    Route::post('/email-status', [ValidationController::class, 'validateEmailStatus']);
    Route::post('/phone-status', [ValidationController::class, 'validatePhoneStatus']);
});

// Vendor Registration routes (public) - with session middleware for session-based flow
Route::prefix('vendor-registration')->middleware(['web'])->group(function () {
    Route::post('/info', [VendorRegistrationController::class, 'registerVendorInfo']);
    Route::post('/verify-email', [VendorRegistrationController::class, 'verifyEmail']);
    Route::post('/company', [VendorRegistrationController::class, 'registerCompanyInfo']);
    Route::post('/send-otp', [VendorRegistrationController::class, 'sendOtp']);
    Route::post('/verify-otp', [VendorRegistrationController::class, 'verifyOtp']);
    Route::get('/status', [VendorRegistrationController::class, 'getRegistrationStatus']);
    Route::post('/resend-email-verification', [VendorRegistrationController::class, 'resendEmailVerification']);

    // Phone verification routes
    Route::post('/send-phone-otp', [VendorRegistrationController::class, 'sendPhoneVerificationOTP']);
    Route::post('/verify-phone-otp', [VendorRegistrationController::class, 'verifyPhoneOTPAndCreateUser']);
    Route::post('/resend-phone-otp', [VendorRegistrationController::class, 'resendPhoneVerificationOTP']);
});

// Provider Registration routes (public)
Route::prefix('provider-registration')->group(function () {
    Route::post('/info', [ProviderRegistrationController::class, 'registerProviderInfo']);
    Route::post('/verify-email', [ProviderRegistrationController::class, 'verifyEmail']);
    Route::post('/license', [ProviderRegistrationController::class, 'uploadLicense']);
    Route::post('/send-otp', [ProviderRegistrationController::class, 'sendOtp']);
    Route::post('/verify-otp', [ProviderRegistrationController::class, 'verifyOtp']);
    Route::post('/resend-otp', [ProviderRegistrationController::class, 'resendOtp']);
    Route::post('/send-phone-otp', [ProviderRegistrationController::class, 'sendPhoneOtp']);
    Route::post('/verify-phone-otp', [ProviderRegistrationController::class, 'verifyPhoneOtp']);
    Route::post('/resend-phone-otp', [ProviderRegistrationController::class, 'resendPhoneOtp']);
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
    Route::post('/resend-otp', [MerchantRegistrationController::class, 'resendOtp']);
    Route::post('/send-phone-otp', [MerchantRegistrationController::class, 'sendPhoneOtp']);
    Route::post('/verify-phone-otp', [MerchantRegistrationController::class, 'verifyPhoneOtp']);
    Route::post('/resend-phone-otp', [MerchantRegistrationController::class, 'resendPhoneOtp']);
    Route::get('/status', [MerchantRegistrationController::class, 'getRegistrationStatus']);
    Route::post('/resend-email-verification', [MerchantRegistrationController::class, 'resendEmailVerification']);
});

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [UserController::class, 'profile']);
    Route::put('/profile', [UserController::class, 'updateProfile']);

    Route::get('/provider-wishlist', [ProviderWishlistController::class, 'index']);
    Route::post('/provider-wishlist', [ProviderWishlistController::class, 'store']);
    Route::delete('/provider-wishlist/{productId}', [ProviderWishlistController::class, 'destroy']);

    Route::get('/general-wishlist', [GeneralWishlistController::class, 'index']);
    Route::post('/general-wishlist', [GeneralWishlistController::class, 'store']);
    Route::delete('/general-wishlist/{productId}', [GeneralWishlistController::class, 'destroy']);
    Route::delete('/general-wishlist', [GeneralWishlistController::class, 'clear']);

    Route::get('/general-wishlist/services', [GeneralWishlistServiceController::class, 'index']);
    Route::post('/general-wishlist/services', [GeneralWishlistServiceController::class, 'store']);
    Route::delete('/general-wishlist/services/{serviceId}', [GeneralWishlistServiceController::class, 'destroy']);

    Route::get('/general-wishlist/branches', [GeneralWishlistBranchController::class, 'index']);
    Route::post('/general-wishlist/branches', [GeneralWishlistBranchController::class, 'store']);
    Route::delete('/general-wishlist/branches/{branchId}', [GeneralWishlistBranchController::class, 'destroy']);

    Route::get('/general-wishlist/merchants', [GeneralWishlistMerchantController::class, 'index']);
    Route::post('/general-wishlist/merchants', [GeneralWishlistMerchantController::class, 'store']);
    Route::delete('/general-wishlist/merchants/{merchantId}', [GeneralWishlistMerchantController::class, 'destroy']);

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

    // Service routes (authenticated - for management)
    Route::put('/services/{id}/featured', [ServiceController::class, 'updateFeatured']);

    // Product routes (authenticated - for management and detailed operations)
    Route::get('/products/{id}/options', [ProductController::class, 'getOptions']);
    Route::post('/products/{id}/validate-options', [ProductController::class, 'validateOptions']);
    Route::put('/products/{id}/featured', [ProductController::class, 'updateFeatured']);

    // Stock management routes
    Route::get('/products/{id}/stock-availability', [ProductController::class, 'checkStockAvailability']);
    Route::get('/products/{id}/available-stock', [ProductController::class, 'getAvailableStock']);
    Route::get('/products/{id}/stock-info', [ProductController::class, 'getStockInfo']);

    // Featured content routes (authenticated - for management)
    Route::get('/featured/services-deals', [ServiceController::class, 'getServicesWithDeals']);

    // Job applications
    Route::get('/jobs/{job}/application-status', [JobApplicationController::class, 'status']);
    Route::post('/jobs/{job}/apply', [JobApplicationController::class, 'apply']);

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

    // Vendor rating routes
    Route::get('/vendors/{id}/ratings', [VendorRatingController::class, 'index']);
    Route::post('/vendors/{id}/ratings', [VendorRatingController::class, 'store']);
    Route::get('/vendors/{id}/my-rating', [VendorRatingController::class, 'show']);
    Route::delete('/vendors/{id}/ratings', [VendorRatingController::class, 'destroy']);

    // Company vendor rating routes (company-based vendor rating)
    Route::post('/companies/{id}/vendor-ratings', [VendorRatingController::class, 'storeByCompanyId']);
    Route::get('/companies/{id}/my-vendor-rating', [VendorRatingController::class, 'showByCompanyId']);

    // Branch rating routes
    Route::get('/branches/{id}/ratings', [BranchRatingController::class, 'index']);
    Route::post('/branches/{id}/ratings', [BranchRatingController::class, 'store']);
    Route::get('/branches/{id}/my-rating', [BranchRatingController::class, 'show']);
    Route::delete('/branches/{id}/ratings', [BranchRatingController::class, 'destroy']);

    // Customer notifications
    Route::apiResource('customer-notifications', CustomerNotificationController::class);

    // Review routes
    Route::get('/{type}/{id}/reviews', [ReviewController::class, 'index']);
    Route::post('/{type}/{id}/reviews', [ReviewController::class, 'store']);
    Route::get('/{type}/{id}/my-review', [ReviewController::class, 'getUserReview']);
    Route::put('/reviews/{reviewId}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{reviewId}', [ReviewController::class, 'destroy']);

    // Booking routes
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::get('/bookings/{id}', [BookingController::class, 'show']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::put('/bookings/{id}', [BookingController::class, 'update']);
    Route::put('/bookings/{id}/cancel', [BookingController::class, 'cancel']);
    Route::post('/bookings/check-availability', [BookingController::class, 'checkAvailability']);
    Route::get('/vendor/bookings/analytics', [BookingController::class, 'vendorAnalytics']);

    // Order routes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::put('/orders/{id}/cancel', [OrderController::class, 'cancel']);
    Route::get('/orders/{id}/shipment', [OrderController::class, 'getShipment']);
    Route::post('/track-shipment', [OrderController::class, 'trackShipment']);
    Route::get('/vendor/orders/analytics', [OrderController::class, 'vendorAnalytics']);

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

    // Category routes (authenticated - for management and tracking)
    Route::get('/categories-with-deals', [CategoryController::class, 'getCategoriesWithDeals']);
    Route::post('/categories/{id}/track-view', [CategoryController::class, 'trackView']);
    Route::post('/categories/{id}/track-purchase', [CategoryController::class, 'trackPurchase']);

    // Dashboard routes
    Route::get('/dashboard/admin', [DashboardController::class, 'getAdminStats']);
    Route::get('/dashboard/vendor', [DashboardController::class, 'getVendorStats']);

    // Deal routes (authenticated)
    Route::get('/deals', [DealController::class, 'index']);
    Route::post('/deals', [DealController::class, 'store']);
    Route::put('/deals/{id}', [DealController::class, 'update']);
    Route::delete('/deals/{id}', [DealController::class, 'destroy']);
    Route::get('/deals/{id}/analytics', [DealController::class, 'getAnalytics']);
    Route::get('/deals/analytics', [DealController::class, 'getAllAnalytics']);

    // Image serving routes
    Route::get('/product-image/{filename}', [ProductController::class, 'getImage']);
    Route::get('/provider-product-image/{filename}', [ProductController::class, 'getProviderProductImage']);
});
