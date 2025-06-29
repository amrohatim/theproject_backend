<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Vendor\DashboardController as VendorDashboardController;
use App\Http\Controllers\Vendor\SettingsController as VendorSettingsController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Web\RegistrationController;
use App\Models\Category;

// Include payment routes
require __DIR__.'/payment.php';

Route::get('/', [LandingController::class, 'index']);



// Color data inspection route
Route::get('/colors-data', function () {
    try {
        $colors = \App\Models\ProductColor::select('id', 'name', 'color_code', 'product_id')
            ->orderBy('id')
            ->get();

        $totalCount = $colors->count();

        // Group by color name
        $colorCounts = $colors->groupBy('name')->map(function ($group) {
            return $group->count();
        })->sortDesc();

        // Group by hex code
        $hexCounts = $colors->groupBy('color_code')->map(function ($group) {
            return $group->count();
        })->sortDesc();

        // Get non-black colors
        $nonBlackColors = $colors->filter(function ($color) {
            return $color->color_code !== '#000000' && $color->color_code !== '#000';
        });

        return response()->json([
            'total_colors' => $totalCount,
            'color_distribution' => $colorCounts,
            'hex_distribution' => $hexCounts,
            'non_black_colors_count' => $nonBlackColors->count(),
            'sample_colors' => $colors->take(50)->values(),
            'non_black_samples' => $nonBlackColors->take(20)->values()
        ], 200, [], JSON_PRETTY_PRINT);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});













// Route to fetch and display all service categories
Route::get('/fetch-service-categories', function () {
    try {
        // Fetch all service categories with their children and service counts
        $serviceCategories = Category::where('type', 'service')
            ->with(['children' => function($query) {
                $query->orderBy('name');
            }])
            ->withCount('services')
            ->orderBy('name')
            ->get();

        // Calculate total service counts including subcategories
        foreach ($serviceCategories as $category) {
            $totalServiceCount = $category->services()->count();

            // Add services from children if it's a parent category
            if ($category->children->isNotEmpty()) {
                foreach ($category->children as $child) {
                    $totalServiceCount += $child->services()->count();
                }
            }

            $category->total_service_count = $totalServiceCount;
        }

        return response()->json([
            'success' => true,
            'message' => 'Service categories fetched successfully',
            'total_categories' => $serviceCategories->count(),
            'categories' => $serviceCategories->map(function($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'image' => $category->image,
                    'parent_id' => $category->parent_id,
                    'is_active' => $category->is_active,
                    'type' => $category->type,
                    'icon' => $category->icon,
                    'view_count' => $category->view_count,
                    'purchase_count' => $category->purchase_count,
                    'trending_score' => $category->trending_score,
                    'services_count' => $category->services_count,
                    'total_service_count' => $category->total_service_count,
                    'children' => $category->children->map(function($child) {
                        return [
                            'id' => $child->id,
                            'name' => $child->name,
                            'description' => $child->description,
                            'image' => $child->image,
                            'parent_id' => $child->parent_id,
                            'is_active' => $child->is_active,
                            'type' => $child->type,
                            'icon' => $child->icon,
                            'view_count' => $child->view_count,
                            'purchase_count' => $child->purchase_count,
                            'trending_score' => $child->trending_score,
                            'services_count' => $child->services()->count(),
                        ];
                    })
                ];
            })
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error fetching service categories',
            'error' => $e->getMessage()
        ], 500);
    }
});

// Authentication routes (these would normally be handled by Laravel Fortify or Laravel Breeze)
Route::get('/login', function () {
    return view('auth.modern-login');
})->name('login')->middleware('guest');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    // Check if user exists
    $user = \App\Models\User::where('email', $credentials['email'])->first();

    if (!$user) {
        return back()->withErrors([
            'email' => 'No user found with this email address.',
        ]);
    }

    // Debug information
    \Illuminate\Support\Facades\Log::info('Login attempt', [
        'email' => $credentials['email'],
        'user_exists' => $user ? 'Yes' : 'No',
        'password_provided' => $credentials['password'] ? 'Yes' : 'No',
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();

        $user = Auth::user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'vendor') {
            return redirect()->route('vendor.dashboard');
        } elseif ($user->role === 'provider') {
            return redirect()->route('provider.dashboard');
        } else {
            return redirect('/');
        }
    }

    // If we get here, the password was incorrect
    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
})->name('login.attempt');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Registration routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegistrationController::class, 'showRegistrationChoice'])->name('register');

    // Vendor Registration - Vue.js Version
    Route::get('/register/vendor', function () {
        return view('vendor-registration-vue');
    })->name('register.vendor');

    // Legacy Vendor Registration (keep for fallback)
    Route::get('/register/vendor/legacy', [RegistrationController::class, 'showVendorRegistration'])->name('register.vendor.legacy');
    Route::post('/register/vendor', [RegistrationController::class, 'registerVendor'])->name('register.vendor.submit');
    Route::get('/register/vendor/company', [RegistrationController::class, 'showVendorCompanyForm'])->name('vendor.registration.company');
    Route::post('/register/vendor/company', [RegistrationController::class, 'registerVendorCompany'])->name('vendor.registration.company.submit');
    Route::get('/register/vendor/license', [RegistrationController::class, 'showVendorLicenseForm'])->name('vendor.registration.license');
    Route::post('/register/vendor/license', [RegistrationController::class, 'uploadVendorLicense'])->name('vendor.registration.license.submit');

    // Provider Registration
    Route::get('/register/provider', [RegistrationController::class, 'showProviderRegistration'])->name('register.provider');
    Route::post('/register/provider', [RegistrationController::class, 'registerProvider'])->name('register.provider.submit');
    Route::get('/register/provider/license', [RegistrationController::class, 'showProviderLicenseForm'])->name('provider.registration.license');
    Route::post('/register/provider/license', [RegistrationController::class, 'uploadProviderLicense'])->name('provider.registration.license.submit');

    // Merchant Registration (Vue.js)
    Route::get('/register/merchant', [RegistrationController::class, 'showMerchantRegistration'])->name('register.merchant');
    Route::get('/register/merchant/legacy', [RegistrationController::class, 'showMerchantRegistration'])->name('register.merchant.legacy');
    Route::post('/register/merchant', [RegistrationController::class, 'registerMerchant'])->name('register.merchant.submit');
    Route::get('/register/merchant/license', [RegistrationController::class, 'showMerchantLicenseForm'])->name('merchant.registration.license');
    Route::post('/register/merchant/license', [RegistrationController::class, 'uploadMerchantLicense'])->name('merchant.registration.license.submit');

    // Temporary registration email verification routes (public)
    Route::get('/vendor/email/verify/temp/{token}', [RegistrationController::class, 'showTempVendorEmailVerification'])->name('vendor.email.verify.temp');
    Route::post('/vendor/email/verify/temp', [RegistrationController::class, 'verifyTempVendorEmail'])->name('vendor.email.verify.temp.submit');
    Route::post('/vendor/email/resend/temp', [RegistrationController::class, 'resendTempVendorEmailVerification'])->name('vendor.email.resend.temp');

    // Temporary registration phone verification routes (public)
    Route::get('/vendor/otp/verify/temp/{token}', [RegistrationController::class, 'showTempVendorOtpVerification'])->name('vendor.otp.verify.temp');
    Route::post('/vendor/otp/verify/temp', [RegistrationController::class, 'verifyTempVendorOtp'])->name('vendor.otp.verify.temp.submit');
    Route::post('/vendor/otp/resend/temp', [RegistrationController::class, 'resendTempVendorOtp'])->name('vendor.otp.resend.temp');

    // OTP routes for registration
    Route::post('/register/send-otp', [RegistrationController::class, 'sendOtp'])->name('register.send-otp');
    Route::post('/register/verify-otp', [RegistrationController::class, 'verifyOtp'])->name('register.verify-otp');
});

// Email and OTP verification routes (accessible to authenticated users)
Route::middleware('auth')->group(function () {
    // Vendor verification routes
    Route::get('/vendor/email/verify/{user_id}', [RegistrationController::class, 'showVendorEmailVerification'])->name('vendor.email.verify');
    Route::post('/vendor/email/verify', [RegistrationController::class, 'verifyVendorEmail'])->name('vendor.email.verify.submit');
    Route::post('/vendor/email/resend', [RegistrationController::class, 'resendVendorEmailVerification'])->name('vendor.email.resend');

    Route::get('/vendor/otp/verify/{user_id}', [RegistrationController::class, 'showVendorOtpVerification'])->name('vendor.otp.verify');
    Route::post('/vendor/otp/verify', [RegistrationController::class, 'verifyVendorOtp'])->name('vendor.otp.verify.submit');
    Route::post('/vendor/otp/resend', [RegistrationController::class, 'resendVendorOtp'])->name('vendor.otp.resend');

    Route::get('/vendor/registration/status', [RegistrationController::class, 'showVendorRegistrationStatus'])->name('vendor.registration.status');

    // Provider verification routes
    Route::get('/provider/email/verify/{user_id}', [RegistrationController::class, 'showProviderEmailVerification'])->name('provider.email.verify');
    Route::post('/provider/email/verify', [RegistrationController::class, 'verifyProviderEmail'])->name('provider.email.verify.submit');
    Route::post('/provider/email/resend', [RegistrationController::class, 'resendProviderEmailVerification'])->name('provider.email.resend');

    Route::get('/provider/otp/verify/{user_id}', [RegistrationController::class, 'showProviderOtpVerification'])->name('provider.otp.verify');
    Route::post('/provider/otp/verify', [RegistrationController::class, 'verifyProviderOtp'])->name('provider.otp.verify.submit');
    Route::post('/provider/otp/resend', [RegistrationController::class, 'resendProviderOtp'])->name('provider.otp.resend');

    Route::get('/provider/registration/status', [RegistrationController::class, 'showProviderRegistrationStatus'])->name('provider.registration.status');

    // Merchant verification routes
    Route::get('/merchant/email/verify/{user_id}', [RegistrationController::class, 'showMerchantEmailVerification'])->name('merchant.email.verify');
    Route::post('/merchant/email/verify', [RegistrationController::class, 'verifyMerchantEmail'])->name('merchant.email.verify.submit');
    Route::post('/merchant/email/resend', [RegistrationController::class, 'resendMerchantEmailVerification'])->name('merchant.email.resend');

    Route::get('/merchant/otp/verify/{user_id}', [RegistrationController::class, 'showMerchantOtpVerification'])->name('merchant.otp.verify');
    Route::post('/merchant/otp/verify', [RegistrationController::class, 'verifyMerchantOtp'])->name('merchant.otp.verify.submit');
    Route::post('/merchant/otp/resend', [RegistrationController::class, 'resendMerchantOtp'])->name('merchant.otp.resend');

    Route::get('/merchant/registration/status', [RegistrationController::class, 'showMerchantRegistrationStatus'])->name('merchant.registration.status');
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::get('/users', function () {
        $users = \App\Models\User::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.users.index', compact('users'));
    })->name('users.index');
    Route::get('/users/create', function () {
        return view('admin.users.create');
    })->name('users.create');
    Route::post('/users', function (Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:admin,vendor,customer,provider,merchant',
            'status' => 'required|string|in:active,inactive',
        ]);

        $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        \App\Models\User::create($validated);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
    })->name('users.store');
    Route::get('/users/{id}/edit', function ($id) {
        $user = \App\Models\User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    })->name('users.edit');
    Route::put('/users/{id}', function (Illuminate\Http\Request $request, $id) {
        $user = \App\Models\User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|in:admin,vendor,customer,provider',
            'status' => 'required|string|in:active,inactive',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
    })->name('users.update');
    Route::delete('/users/{id}', function ($id) {
        $user = \App\Models\User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    })->name('users.destroy');

    // Categories
    Route::get('/categories', function (\Illuminate\Http\Request $request) {
        // If this is an AJAX request for parent categories only
        if ($request->has('parents_only') && $request->ajax()) {
            $parentCategories = \App\Models\Category::whereNull('parent_id')->orderBy('name')->get();
            return response()->json($parentCategories);
        }

        $categories = \App\Models\Category::with('parent')->orderBy('name')->paginate(10);
        return view('admin.categories.index', compact('categories'));
    })->name('categories.index');
    Route::post('/categories', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:product,service',
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // If parent_id is empty string, set it to null
        if ($validated['parent_id'] === '') {
            $validated['parent_id'] = null;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            try {
                // Store the image using Laravel's storage system for consistency
                $imagePath = $request->file('image')->store('categories', 'public');

                // Log the stored image path
                \Illuminate\Support\Facades\Log::info("Category image stored at: {$imagePath}");

                // Create the storage directory if it doesn't exist
                $storageDir = public_path('storage/categories');
                if (!file_exists($storageDir)) {
                    mkdir($storageDir, 0755, true);
                    \Illuminate\Support\Facades\Log::info("Created directory: {$storageDir}");
                }

                // Create symbolic link if it doesn't exist
                $linkPath = public_path('storage');
                $targetPath = storage_path('app/public');
                if (!file_exists($linkPath)) {
                    symlink($targetPath, $linkPath);
                    \Illuminate\Support\Facades\Log::info("Created storage symlink");
                }

                $validated['image'] = $imagePath;
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Error uploading category image: " . $e->getMessage());
                return redirect()->route('admin.categories.index')->with('error', 'Error uploading image: ' . $e->getMessage());
            }
        }

        \App\Models\Category::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully');
    })->name('categories.store');
    Route::get('/categories/{id}/edit', function ($id) {
        $category = \App\Models\Category::findOrFail($id);
        $parentCategories = \App\Models\Category::whereNull('parent_id')->where('id', '!=', $id)->orderBy('name')->get();
        return response()->json([
            'success' => true,
            'category' => $category,
            'parentCategories' => $parentCategories
        ]);
    })->name('categories.edit');
    Route::put('/categories/{id}', function (\Illuminate\Http\Request $request, $id) {
        $category = \App\Models\Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:product,service',
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // If parent_id is empty string, set it to null
        if ($validated['parent_id'] === '') {
            $validated['parent_id'] = null;
        }

        // Prevent category from being its own parent
        if ($validated['parent_id'] == $id) {
            return response()->json([
                'success' => false,
                'message' => 'A category cannot be its own parent'
            ], 400);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            try {
                // Delete old image if it exists
                if ($category->image) {
                    // Handle both old path format and new storage path format
                    $oldImagePath = $category->image;

                    // Check if it's a storage path (starts with 'categories/')
                    if (strpos($oldImagePath, 'categories/') === 0) {
                        $fullPath = public_path('storage/' . $oldImagePath);
                    } elseif (strpos($oldImagePath, '/storage/') === 0) {
                        // Already has /storage/ prefix
                        $fullPath = public_path($oldImagePath);
                    } else {
                        // Old format - direct path
                        $fullPath = public_path($oldImagePath);
                    }

                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                        \Illuminate\Support\Facades\Log::info("Deleted old category image: {$fullPath}");
                    }
                }

                // Store the image using Laravel's storage system for consistency
                $imagePath = $request->file('image')->store('categories', 'public');

                // Log the stored image path
                \Illuminate\Support\Facades\Log::info("Category image stored at: {$imagePath}");

                // Create the storage directory if it doesn't exist
                $storageDir = public_path('storage/categories');
                if (!file_exists($storageDir)) {
                    mkdir($storageDir, 0755, true);
                    \Illuminate\Support\Facades\Log::info("Created directory: {$storageDir}");
                }

                // Create symbolic link if it doesn't exist
                $linkPath = public_path('storage');
                $targetPath = storage_path('app/public');
                if (!file_exists($linkPath)) {
                    symlink($targetPath, $linkPath);
                    \Illuminate\Support\Facades\Log::info("Created storage symlink");
                }

                $validated['image'] = $imagePath;
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Error uploading category image: " . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Error uploading image: ' . $e->getMessage(),
                ], 500);
            }
        }

        $category->update($validated);

        // Refresh the category to get the updated data
        $category->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'category' => $category
        ]);
    })->name('categories.update');
    Route::delete('/categories/{id}', function ($id) {
        try {
            // Find the category
            $category = \App\Models\Category::findOrFail($id);

            // Check if this category is used by any products or services
            $hasProducts = $category->products()->count() > 0;
            $hasServices = $category->services()->count() > 0;

            if ($hasProducts || $hasServices) {
                return redirect()->route('admin.categories.index')->with('error', 'Cannot delete a category that is being used by products or services. Please reassign those items to another category first.');
            }

            // If this is a parent category, delete all its children first
            if ($category->parent_id === null) {
                // Get all child categories
                $childCategories = \App\Models\Category::where('parent_id', $category->id)->get();

                // Check if any child category is used by products or services
                foreach ($childCategories as $childCategory) {
                    $childHasProducts = $childCategory->products()->count() > 0;
                    $childHasServices = $childCategory->services()->count() > 0;

                    if ($childHasProducts || $childHasServices) {
                        return redirect()->route('admin.categories.index')->with('error', 'Cannot delete this parent category because one of its subcategories is being used by products or services.');
                    }
                }

                // Delete all child categories
                \App\Models\Category::where('parent_id', $category->id)->delete();
            }

            // Now delete the category itself
            $category->delete();

            return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('admin.categories.index')->with('error', 'An error occurred while deleting the category: ' . $e->getMessage());
        }
    })->name('categories.destroy');

    // Companies
    Route::get('/companies', function () {
        $companies = \App\Models\Company::with('user')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.companies.index', compact('companies'));
    })->name('companies.index');
    Route::get('/companies/create', function () {
        $users = \App\Models\User::where('role', 'vendor')->orderBy('name')->get();
        return view('admin.companies.create', compact('users'));
    })->name('companies.create');
    Route::post('/companies', function (Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'business_type' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:active,inactive,pending',
            'can_deliver' => 'nullable|boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('companies', 'public');
            $validated['logo'] = \Illuminate\Support\Facades\Storage::url($logoPath);
        }

        \App\Models\Company::create($validated);

        return redirect()->route('admin.companies.index')->with('success', 'Company created successfully');
    })->name('companies.store');
    Route::get('/companies/{id}', function ($id) {
        $company = \App\Models\Company::with(['user', 'branches'])->findOrFail($id);
        return view('admin.companies.show', compact('company'));
    })->name('companies.show');
    Route::get('/companies/{id}/edit', function ($id) {
        $company = \App\Models\Company::findOrFail($id);
        $users = \App\Models\User::where('role', 'vendor')->orderBy('name')->get();
        return view('admin.companies.edit', compact('company', 'users'));
    })->name('companies.edit');
    Route::put('/companies/{id}', function (Illuminate\Http\Request $request, $id) {
        $company = \App\Models\Company::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'business_type' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:active,inactive,pending',
            'can_deliver' => 'nullable|boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($company->logo && \Illuminate\Support\Facades\Storage::exists('public/' . str_replace('/storage/', '', $company->logo))) {
                \Illuminate\Support\Facades\Storage::delete('public/' . str_replace('/storage/', '', $company->logo));
            }

            $logoPath = $request->file('logo')->store('companies', 'public');
            $validated['logo'] = \Illuminate\Support\Facades\Storage::url($logoPath);
        }

        $company->update($validated);

        return redirect()->route('admin.companies.index')->with('success', 'Company updated successfully');
    })->name('companies.update');
    Route::delete('/companies/{id}', function ($id) {
        $company = \App\Models\Company::findOrFail($id);
        $company->delete();
        return redirect()->route('admin.companies.index')->with('success', 'Company deleted successfully');
    })->name('companies.destroy');

    // Branches
    Route::get('/branches', function () {
        $branches = \App\Models\Branch::with(['company'])->orderBy('created_at', 'desc')->paginate(10);
        $companies = \App\Models\Company::orderBy('name')->get();
        return view('admin.branches.index', compact('branches', 'companies'));
    })->name('branches.index');
    Route::get('/branches/create', function () {
        $companies = \App\Models\Company::orderBy('name')->get();
        return view('admin.branches.create', compact('companies'));
    })->name('branches.create');
    Route::post('/branches', function (Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'branch_code' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'status' => 'required|string|in:active,inactive',
        ]);

        \App\Models\Branch::create($validated);

        return redirect()->route('admin.branches.index')->with('success', 'Branch created successfully');
    })->name('branches.store');
    Route::get('/branches/{id}', function ($id) {
        $branch = \App\Models\Branch::with(['company', 'products', 'services'])->findOrFail($id);
        return view('admin.branches.show', compact('branch'));
    })->name('branches.show');
    Route::get('/branches/{id}/edit', function ($id) {
        $branch = \App\Models\Branch::findOrFail($id);
        $companies = \App\Models\Company::orderBy('name')->get();
        return view('admin.branches.edit', compact('branch', 'companies'));
    })->name('branches.edit');
    Route::put('/branches/{id}', function (Illuminate\Http\Request $request, $id) {
        $branch = \App\Models\Branch::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'branch_code' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'status' => 'required|string|in:active,inactive',
            'featured' => 'sometimes|boolean',
        ]);

        $branch->update($validated);

        return redirect()->route('admin.branches.index')->with('success', 'Branch updated successfully');
    })->name('branches.update');
    Route::delete('/branches/{id}', function ($id) {
        $branch = \App\Models\Branch::findOrFail($id);
        $branch->delete();
        return redirect()->route('admin.branches.index')->with('success', 'Branch deleted successfully');
    })->name('branches.destroy');

    // Products
    Route::get('/products', function () {
        $products = \App\Models\Product::with(['branch.company', 'category'])->orderBy('created_at', 'desc')->paginate(10);
        $categories = \App\Models\Category::orderBy('name')->get();
        $companies = \App\Models\Company::orderBy('name')->get();
        return view('admin.products.index', compact('products', 'categories', 'companies'));
    })->name('products.index');
    Route::get('/products/create', function () {
        // Get product categories with their children - force a fresh query to get the latest data
        $parentCategories = \App\Models\Category::where('type', 'product')
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get();
        $branches = \App\Models\Branch::with('company')->orderBy('name')->get();
        return view('admin.products.create', compact('parentCategories', 'branches'));
    })->name('products.create');
    Route::post('/products', function (Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'branch_id' => 'required|exists:branches,id',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'sometimes|boolean',
        ]);

        $validated['is_available'] = $request->has('is_available');

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/products'), $imageName);
            $validated['image'] = '/images/products/' . $imageName;
        }

        \App\Models\Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully');
    })->name('products.store');
    Route::get('/products/{id}', function ($id) {
        $product = \App\Models\Product::with(['branch.company', 'category'])->findOrFail($id);
        return view('admin.products.show', compact('product'));
    })->name('products.show');
    Route::get('/products/{id}/edit', function ($id) {
        $product = \App\Models\Product::findOrFail($id);
        // Get product categories with their children
        $parentCategories = \App\Models\Category::where('type', 'product')
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get();
        $branches = \App\Models\Branch::with('company')->orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'parentCategories', 'branches'));
    })->name('products.edit');
    Route::put('/products/{id}', function (Illuminate\Http\Request $request, $id) {
        $product = \App\Models\Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'branch_id' => 'required|exists:branches,id',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'sometimes|boolean',
            'featured' => 'sometimes|boolean',
        ]);

        $validated['is_available'] = $request->has('is_available');
        $validated['featured'] = $request->has('featured');

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && file_exists(public_path($product->image))) {
                unlink(public_path($product->image));
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/products'), $imageName);
            $validated['image'] = '/images/products/' . $imageName;
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully');
    })->name('products.update');
    Route::delete('/products/{id}', function ($id) {
        try {
            $product = \App\Models\Product::findOrFail($id);

            // Delete legacy image if exists (old format)
            if ($product->image && file_exists(public_path($product->image))) {
                unlink(public_path($product->image));
            }

            // The Product model's deleting event will handle cascading deletion
            // of colors, sizes, color-size combinations, specifications, and images
            $product->delete();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product and all related data deleted successfully');

        } catch (\Exception $e) {
            \Log::error('Error deleting product via admin route: ' . $e->getMessage(), [
                'product_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.products.index')
                ->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    })->name('products.destroy');

    // Providers
    Route::get('/providers', [\App\Http\Controllers\Admin\ProviderController::class, 'index'])->name('providers.index');
    Route::get('/providers/create', [\App\Http\Controllers\Admin\ProviderController::class, 'create'])->name('providers.create');
    Route::post('/providers', [\App\Http\Controllers\Admin\ProviderController::class, 'store'])->name('providers.store');
    Route::get('/providers/{id}', [\App\Http\Controllers\Admin\ProviderController::class, 'show'])->name('providers.show');
    Route::get('/providers/{id}/edit', [\App\Http\Controllers\Admin\ProviderController::class, 'edit'])->name('providers.edit');
    Route::put('/providers/{id}', [\App\Http\Controllers\Admin\ProviderController::class, 'update'])->name('providers.update');
    Route::delete('/providers/{id}', [\App\Http\Controllers\Admin\ProviderController::class, 'destroy'])->name('providers.destroy');

    // Services
    Route::get('/services', function () {
        $services = \App\Models\Service::with(['branch.company', 'category'])->orderBy('created_at', 'desc')->paginate(10);
        $categories = \App\Models\Category::orderBy('name')->get();
        $companies = \App\Models\Company::orderBy('name')->get();
        return view('admin.services.index', compact('services', 'categories', 'companies'));
    })->name('services.index');
    Route::get('/services/create', function () {
        // Get service categories with their children - force a fresh query to get the latest data
        $parentCategories = \App\Models\Category::where('type', 'service')
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get();
        $branches = \App\Models\Branch::with('company')->orderBy('name')->get();
        return view('admin.services.create', compact('parentCategories', 'branches'));
    })->name('services.create');
    Route::post('/services', function (Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'branch_id' => 'required|exists:branches,id',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'sometimes|boolean',
        ]);

        $validated['is_available'] = $request->has('is_available');

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/services'), $imageName);
            $validated['image'] = '/images/services/' . $imageName;
        }

        \App\Models\Service::create($validated);

        return redirect()->route('admin.services.index')->with('success', 'Service created successfully');
    })->name('services.store');
    Route::get('/services/{id}', function ($id) {
        $service = \App\Models\Service::with(['branch.company', 'category'])->findOrFail($id);
        return view('admin.services.show', compact('service'));
    })->name('services.show');
    Route::get('/services/{id}/edit', function ($id) {
        $service = \App\Models\Service::findOrFail($id);
        // Get service categories with their children
        $parentCategories = \App\Models\Category::where('type', 'service')
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get();
        $branches = \App\Models\Branch::with('company')->orderBy('name')->get();
        return view('admin.services.edit', compact('service', 'parentCategories', 'branches'));
    })->name('services.edit');
    Route::put('/services/{id}', function (Illuminate\Http\Request $request, $id) {
        $service = \App\Models\Service::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'branch_id' => 'required|exists:branches,id',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'sometimes|boolean',
            'featured' => 'sometimes|boolean',
        ]);

        $validated['is_available'] = $request->has('is_available');
        $validated['featured'] = $request->has('featured');

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($service->image && file_exists(public_path($service->image))) {
                unlink(public_path($service->image));
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/services'), $imageName);
            $validated['image'] = '/images/services/' . $imageName;
        }

        $service->update($validated);

        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully');
    })->name('services.update');
    Route::delete('/services/{id}', function ($id) {
        $service = \App\Models\Service::findOrFail($id);

        // Delete image if exists
        if ($service->image && file_exists(public_path($service->image))) {
            unlink(public_path($service->image));
        }

        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Service deleted successfully');
    })->name('services.destroy');

    // Shipping
    Route::get('/shipping/settings', [\App\Http\Controllers\Admin\ShippingController::class, 'settings'])->name('shipping.settings');
    Route::post('/shipping/settings', [\App\Http\Controllers\Admin\ShippingController::class, 'updateSettings'])->name('shipping.update-settings');
    Route::get('/shipping/orders', [\App\Http\Controllers\Admin\ShippingController::class, 'orders'])->name('shipping.orders');
    Route::get('/shipping/orders/{id}', [\App\Http\Controllers\Admin\ShippingController::class, 'orderDetails'])->name('shipping.order-details');
    Route::put('/shipping/orders/{id}/method', [\App\Http\Controllers\Admin\ShippingController::class, 'updateShippingMethod'])->name('shipping.update-method');
    Route::put('/shipping/orders/{id}/status', [\App\Http\Controllers\Admin\ShippingController::class, 'updateShippingStatus'])->name('shipping.update-status');
    Route::get('/shipping/shipments', [\App\Http\Controllers\Admin\ShippingController::class, 'shipments'])->name('shipping.shipments');
    Route::get('/shipping/shipments/{id}', [\App\Http\Controllers\Admin\ShippingController::class, 'shipmentDetails'])->name('shipping.shipment-details');
    Route::post('/shipping/track', [\App\Http\Controllers\Admin\ShippingController::class, 'trackShipment'])->name('shipping.track');
    Route::get('/shipping/vendors', [\App\Http\Controllers\Admin\ShippingController::class, 'vendors'])->name('shipping.vendors');
    Route::put('/shipping/vendors/{id}', [\App\Http\Controllers\Admin\ShippingController::class, 'updateVendorShipping'])->name('shipping.update-vendor');

    // Settings
    Route::get('/settings', function () {
        return view('admin.settings');
    })->name('settings');
    Route::get('/settings/general', function () {
        return view('admin.settings.general');
    })->name('settings.general');
    Route::get('/settings/payment', function () {
        return view('admin.settings.payment');
    })->name('settings.payment');
    Route::get('/settings/email', function () {
        return view('admin.settings.email');
    })->name('settings.email');
    Route::get('/settings/roles', function () {
        return view('admin.settings.roles');
    })->name('settings.roles');
    Route::get('/settings/commission', function () {
        return view('admin.settings.commission');
    })->name('settings.commission');
    Route::get('/settings/maintenance', function () {
        return view('admin.settings.maintenance');
    })->name('settings.maintenance');

    // Image testing route
    Route::get('/image/test', [\App\Http\Controllers\ImageTestController::class, 'index'])->name('image.test');

    // Image fix route
    Route::get('/fix/images', [\App\Http\Controllers\Admin\ImageFixController::class, 'fixImages'])->name('fix.images');

    // Registration management routes
    Route::get('/registrations', [\App\Http\Controllers\Admin\RegistrationController::class, 'index'])->name('registrations.index');
    Route::get('/registrations/{id}', [\App\Http\Controllers\Admin\RegistrationController::class, 'show'])->name('registrations.show');
    Route::patch('/registrations/{id}/approve', [\App\Http\Controllers\Admin\RegistrationController::class, 'approve'])->name('registrations.approve');
    Route::patch('/registrations/{id}/reject', [\App\Http\Controllers\Admin\RegistrationController::class, 'reject'])->name('registrations.reject');
    Route::get('/registrations/{id}/download-license', [\App\Http\Controllers\Admin\RegistrationController::class, 'downloadLicense'])->name('registrations.download-license');
});

// Vendor routes
Route::prefix('vendor')->name('vendor.')->middleware(['auth', \App\Http\Middleware\VendorMiddleware::class])->group(function () {
    Route::get('/dashboard', [VendorDashboardController::class, 'index'])->name('dashboard');

    // Deals
    Route::get('/deals', [\App\Http\Controllers\Vendor\DealController::class, 'index'])->name('deals.index');
    Route::get('/deals/create', [\App\Http\Controllers\Vendor\DealController::class, 'create'])->name('deals.create');
    Route::post('/deals', [\App\Http\Controllers\Vendor\DealController::class, 'store'])->name('deals.store');
    Route::get('/deals/{id}', [\App\Http\Controllers\Vendor\DealController::class, 'show'])->name('deals.show');
    Route::get('/deals/{id}/edit', [\App\Http\Controllers\Vendor\DealController::class, 'edit'])->name('deals.edit');
    Route::put('/deals/{id}', [\App\Http\Controllers\Vendor\DealController::class, 'update'])->name('deals.update');
    Route::delete('/deals/{id}', [\App\Http\Controllers\Vendor\DealController::class, 'destroy'])->name('deals.destroy');

    // Company
    Route::get('/company', function () {
        $company = \App\Models\Company::where('user_id', \Illuminate\Support\Facades\Auth::id())->first();
        if (!$company) {
            return redirect()->route('vendor.company.create');
        }
        return view('vendor.company.index', compact('company'));
    })->name('company.index');

    Route::get('/company/create', function () {
        // Check if the vendor already has a company
        $company = \App\Models\Company::where('user_id', \Illuminate\Support\Facades\Auth::id())->first();
        if ($company) {
            return redirect()->route('vendor.company.index')->with('info', 'You already have a company registered.');
        }
        return view('vendor.company.create');
    })->name('company.create');

    Route::post('/company', function (\Illuminate\Http\Request $request) {
        // Check if the vendor already has a company
        $existingCompany = \App\Models\Company::where('user_id', \Illuminate\Support\Facades\Auth::id())->first();
        if ($existingCompany) {
            return redirect()->route('vendor.company.index')->with('info', 'You already have a company registered.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'business_type' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'can_deliver' => 'nullable|boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['user_id'] = \Illuminate\Support\Facades\Auth::id();
        $validated['status'] = 'active';

        if ($request->hasFile('logo')) {
            // Add debug logging
            \Illuminate\Support\Facades\Log::info('Company logo upload started (create)', [
                'original_filename' => $request->file('logo')->getClientOriginalName(),
                'file_size' => $request->file('logo')->getSize(),
                'mime_type' => $request->file('logo')->getMimeType()
            ]);

            try {
                // Store the file in the storage directory
                $logoPath = $request->file('logo')->store('companies', 'public');
                $validated['logo'] = \Illuminate\Support\Facades\Storage::url($logoPath);

                \Illuminate\Support\Facades\Log::info('Company logo uploaded successfully (create)', [
                    'storage_path' => $logoPath,
                    'url_path' => $validated['logo']
                ]);

                // Create the public directory if it doesn't exist
                $publicDir = public_path('images/companies');
                if (!file_exists($publicDir)) {
                    mkdir($publicDir, 0755, true);
                    \Illuminate\Support\Facades\Log::info('Created public directory', [
                        'directory' => $publicDir
                    ]);
                }

                // Copy the file to the public directory for direct access
                $sourceFile = storage_path('app/public/' . $logoPath);
                $filename = basename($logoPath);
                $destinationFile = public_path('images/companies/' . $filename);

                if (file_exists($sourceFile)) {
                    if (copy($sourceFile, $destinationFile)) {
                        \Illuminate\Support\Facades\Log::info('Copied logo to public directory (create)', [
                            'from' => $sourceFile,
                            'to' => $destinationFile
                        ]);
                    } else {
                        \Illuminate\Support\Facades\Log::error('Failed to copy logo to public directory (create)', [
                            'from' => $sourceFile,
                            'to' => $destinationFile,
                            'error' => error_get_last()
                        ]);
                    }
                } else {
                    \Illuminate\Support\Facades\Log::error('Source file does not exist (create)', [
                        'path' => $sourceFile
                    ]);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error uploading company logo (create)', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $company = \App\Models\Company::create($validated);

        \Illuminate\Support\Facades\Log::info('Company created', [
            'company_id' => $company->id,
            'logo_path' => $company->logo
        ]);

        return redirect()->route('vendor.company.index')->with('success', 'Company created successfully');
    })->name('company.store');

    Route::put('/company', function (\Illuminate\Http\Request $request) {
        $company = \App\Models\Company::where('user_id', \Illuminate\Support\Facades\Auth::id())->first();

        if (!$company) {
            return redirect()->route('vendor.company.create');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'business_type' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'can_deliver' => 'nullable|boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            // Add debug logging
            \Illuminate\Support\Facades\Log::info('Company logo upload started', [
                'company_id' => $company->id,
                'original_filename' => $request->file('logo')->getClientOriginalName(),
                'file_size' => $request->file('logo')->getSize(),
                'mime_type' => $request->file('logo')->getMimeType()
            ]);

            // Delete old logo if exists
            if ($company->logo) {
                // Extract the filename from the URL
                $oldLogoPath = str_replace('/storage/', '', $company->logo);

                // Delete from storage
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($oldLogoPath)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldLogoPath);
                    \Illuminate\Support\Facades\Log::info('Deleted old logo from storage', [
                        'path' => $oldLogoPath
                    ]);
                }

                // Delete from public directory if exists
                $oldFilename = basename($oldLogoPath);
                $oldPublicPath = public_path('images/companies/' . $oldFilename);
                if (file_exists($oldPublicPath)) {
                    unlink($oldPublicPath);
                    \Illuminate\Support\Facades\Log::info('Deleted old logo from public directory', [
                        'path' => $oldPublicPath
                    ]);
                }
            }

            try {
                // Store the file in the storage directory
                $logoPath = $request->file('logo')->store('companies', 'public');
                $validated['logo'] = \Illuminate\Support\Facades\Storage::url($logoPath);

                \Illuminate\Support\Facades\Log::info('Company logo uploaded successfully', [
                    'storage_path' => $logoPath,
                    'url_path' => $validated['logo']
                ]);

                // Create the public directory if it doesn't exist
                $publicDir = public_path('images/companies');
                if (!file_exists($publicDir)) {
                    mkdir($publicDir, 0755, true);
                    \Illuminate\Support\Facades\Log::info('Created public directory', [
                        'directory' => $publicDir
                    ]);
                }

                // Copy the file to the public directory for direct access
                $sourceFile = storage_path('app/public/' . $logoPath);
                $filename = basename($logoPath);
                $destinationFile = public_path('images/companies/' . $filename);

                if (file_exists($sourceFile)) {
                    if (copy($sourceFile, $destinationFile)) {
                        \Illuminate\Support\Facades\Log::info('Copied logo to public directory', [
                            'from' => $sourceFile,
                            'to' => $destinationFile
                        ]);
                    } else {
                        \Illuminate\Support\Facades\Log::error('Failed to copy logo to public directory', [
                            'from' => $sourceFile,
                            'to' => $destinationFile,
                            'error' => error_get_last()
                        ]);
                    }
                } else {
                    \Illuminate\Support\Facades\Log::error('Source file does not exist', [
                        'path' => $sourceFile
                    ]);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error uploading company logo', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $company->update($validated);

        \Illuminate\Support\Facades\Log::info('Company updated', [
            'company_id' => $company->id,
            'logo_path' => $company->logo
        ]);

        return redirect()->route('vendor.company.index')->with('success', 'Company information updated successfully');
    })->name('company.update');

    Route::put('/company/address', function (\Illuminate\Http\Request $request) {
        $company = \App\Models\Company::where('user_id', \Illuminate\Support\Facades\Auth::id())->first();

        $validated = $request->validate([
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
        ]);

        // Map postal_code to zip_code
        if (isset($validated['postal_code'])) {
            $validated['zip_code'] = $validated['postal_code'];
            unset($validated['postal_code']);
        }

        if ($company) {
            $company->update($validated);
        }

        return redirect()->route('vendor.company.index')->with('success', 'Company address updated successfully');
    })->name('company.address.update');

    // Search suggestion routes for vendor dashboard (must be before resource routes)
    Route::get('/branches/search-suggestions', [\App\Http\Controllers\Vendor\BranchController::class, 'searchSuggestions'])->name('branches.search-suggestions');
    Route::get('/products/search-suggestions', [\App\Http\Controllers\Vendor\ProductController::class, 'searchSuggestions'])->name('products.search-suggestions');
    Route::get('/services/search-suggestions', [\App\Http\Controllers\Vendor\ServiceController::class, 'searchSuggestions'])->name('services.search-suggestions');
    Route::get('/orders/search-suggestions', [\App\Http\Controllers\Vendor\OrderController::class, 'searchSuggestions'])->name('orders.search-suggestions');
    Route::get('/bookings/search-suggestions', [\App\Http\Controllers\Vendor\BookingController::class, 'searchSuggestions'])->name('bookings.search-suggestions');

    // Branches
    Route::get('/branches', function () {
        $branches = \App\Models\Branch::with('company')
            ->join('companies', 'branches.company_id', '=', 'companies.id')
            ->where('companies.user_id', \Illuminate\Support\Facades\Auth::id())
            ->select('branches.*')
            ->orderBy('branches.created_at', 'desc')
            ->paginate(10);
        return view('vendor.branches.index', compact('branches'));
    })->name('branches.index');

    Route::get('/branches/create', function () {
        $companies = \App\Models\Company::where('user_id', \Illuminate\Support\Facades\Auth::id())->orderBy('name')->get();
        return view('vendor.branches.create', compact('companies'));
    })->name('branches.create');

    Route::post('/branches', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string|max:255',
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'emirate' => 'required|string|in:Dubai,Abu Dhabi,Sharjah,Ajman,Umm Al Quwain,Ras Al Khaimah,Fujairah',
            'description' => 'nullable|string',
            'status' => 'required|string|in:active,inactive',
            'days_open' => 'nullable|array',
            'opening_hours' => 'nullable|array',
            'branch_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'use_company_image' => 'sometimes|boolean',
        ]);

        // Verify that the company belongs to the authenticated user
        $company = \App\Models\Company::where('id', $validated['company_id'])
            ->where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->firstOrFail();

        // Add the user_id to the validated data
        $validated['user_id'] = \Illuminate\Support\Facades\Auth::id();

        // Add default values for required fields
        $validated['lat'] = $validated['lat'] ?? 0;
        $validated['lng'] = $validated['lng'] ?? 0;

        // Set use_company_image to true if not provided
        $validated['use_company_image'] = $request->has('use_company_image');

        // Handle branch image upload
        if ($request->hasFile('branch_image') && !$validated['use_company_image']) {
            try {
                // Store the image in the public disk
                $imagePath = $request->file('branch_image')->store('branches', 'public');
                $validated['branch_image'] = $imagePath;

                \Illuminate\Support\Facades\Log::info('Branch image uploaded successfully', [
                    'branch_name' => $validated['name'],
                    'image_path' => $imagePath
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error uploading branch image', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        // Process business hours
        $businessHours = [];
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($days as $day) {
            // Check if the day is marked as open
            if (isset($request->days_open[$day]) && $request->days_open[$day] == 1) {
                // Get opening and closing hours for the day
                $openTime = $request->opening_hours[$day]['open'] ?? '09:00';
                $closeTime = $request->opening_hours[$day]['close'] ?? '17:00';

                $businessHours[$day] = [
                    'is_open' => true,
                    'open' => $openTime,
                    'close' => $closeTime
                ];
            } else {
                $businessHours[$day] = [
                    'is_open' => false,
                    'open' => null,
                    'close' => null
                ];
            }
        }

        // Add business hours to validated data
        $validated['opening_hours'] = $businessHours;

        // Remove the days_open field as it's not in the model
        unset($validated['days_open']);

        $branch = \App\Models\Branch::create($validated);

        return redirect()->route('vendor.branches.index')->with('success', 'Branch created successfully');
    })->name('branches.store');

    Route::get('/branches/{id}', function ($id) {
        $branch = \App\Models\Branch::with(['company', 'products', 'services'])
            ->join('companies', 'branches.company_id', '=', 'companies.id')
            ->where('companies.user_id', \Illuminate\Support\Facades\Auth::id())
            ->where('branches.id', $id)
            ->select('branches.*')
            ->firstOrFail();
        return view('vendor.branches.show', compact('branch'));
    })->name('branches.show');

    Route::get('/branches/{id}/edit', function ($id) {
        $branch = \App\Models\Branch::join('companies', 'branches.company_id', '=', 'companies.id')
            ->where('companies.user_id', \Illuminate\Support\Facades\Auth::id())
            ->where('branches.id', $id)
            ->select('branches.*')
            ->firstOrFail();
        $companies = \App\Models\Company::where('user_id', \Illuminate\Support\Facades\Auth::id())->orderBy('name')->get();
        return view('vendor.branches.edit', compact('branch', 'companies'));
    })->name('branches.edit');

    Route::get('/branches/{id}/image', function ($id) {
        $branch = \App\Models\Branch::join('companies', 'branches.company_id', '=', 'companies.id')
            ->where('companies.user_id', \Illuminate\Support\Facades\Auth::id())
            ->where('branches.id', $id)
            ->select('branches.*')
            ->firstOrFail();
        $company = \App\Models\Company::where('id', $branch->company_id)->first();
        return view('vendor.branches.image', compact('branch', 'company'));
    })->name('branches.image');

    Route::put('/branches/{id}', function (\Illuminate\Http\Request $request, $id) {
        $branch = \App\Models\Branch::join('companies', 'branches.company_id', '=', 'companies.id')
            ->where('companies.user_id', \Illuminate\Support\Facades\Auth::id())
            ->where('branches.id', $id)
            ->select('branches.*')
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string|max:255',
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'emirate' => 'nullable|string|in:Dubai,Abu Dhabi,Sharjah,Ajman,Umm Al Quwain,Ras Al Khaimah,Fujairah',
            'description' => 'nullable|string',
            'status' => 'required|string|in:active,inactive',
            'days_open' => 'nullable|array',
            'opening_hours' => 'nullable|array',
            'branch_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'use_company_image' => 'sometimes|boolean',
        ]);

        // Verify that the company belongs to the authenticated user
        $company = \App\Models\Company::where('id', $validated['company_id'])
            ->where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->firstOrFail();

        // Keep the existing lat/lng values if not provided
        $validated['lat'] = $validated['lat'] ?? $branch->lat;
        $validated['lng'] = $validated['lng'] ?? $branch->lng;

        // Set use_company_image to true if not provided
        $validated['use_company_image'] = $request->has('use_company_image');

        // Handle branch image upload
        if ($request->hasFile('branch_image') && !$validated['use_company_image']) {
            try {
                // Delete old image if exists
                if ($branch->branch_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($branch->branch_image)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($branch->branch_image);
                    \Illuminate\Support\Facades\Log::info('Deleted old branch image', [
                        'branch_id' => $branch->id,
                        'image_path' => $branch->branch_image
                    ]);
                }

                // Store the image in the public disk
                $imagePath = $request->file('branch_image')->store('branches', 'public');
                $validated['branch_image'] = $imagePath;

                \Illuminate\Support\Facades\Log::info('Branch image uploaded successfully', [
                    'branch_id' => $branch->id,
                    'image_path' => $imagePath
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error uploading branch image', [
                    'branch_id' => $branch->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        } elseif ($validated['use_company_image'] && $branch->branch_image) {
            // If switching to use company image, remove the branch image
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($branch->branch_image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($branch->branch_image);
                \Illuminate\Support\Facades\Log::info('Deleted branch image when switching to company image', [
                    'branch_id' => $branch->id,
                    'image_path' => $branch->branch_image
                ]);
            }
            $validated['branch_image'] = null;
        }

        // Process business hours
        $businessHours = [];
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($days as $day) {
            // Check if the day is marked as open
            if (isset($request->days_open[$day]) && $request->days_open[$day] == 1) {
                // Get opening and closing hours for the day
                $openTime = $request->opening_hours[$day]['open'] ?? '09:00';
                $closeTime = $request->opening_hours[$day]['close'] ?? '17:00';

                $businessHours[$day] = [
                    'is_open' => true,
                    'open' => $openTime,
                    'close' => $closeTime
                ];
            } else {
                $businessHours[$day] = [
                    'is_open' => false,
                    'open' => null,
                    'close' => null
                ];
            }
        }

        // Add business hours to validated data
        $validated['opening_hours'] = $businessHours;

        // Remove the days_open field as it's not in the model
        unset($validated['days_open']);

        $branch->update($validated);

        return redirect()->route('vendor.branches.show', $branch->id)->with('success', 'Branch updated successfully');
    })->name('branches.update');

    Route::delete('/branches/{id}', function ($id) {
        $branch = \App\Models\Branch::join('companies', 'branches.company_id', '=', 'companies.id')
            ->where('companies.user_id', \Illuminate\Support\Facades\Auth::id())
            ->where('branches.id', $id)
            ->select('branches.*')
            ->firstOrFail();
        $branch->delete();
        return redirect()->route('vendor.branches.index')->with('success', 'Branch deleted successfully');
    })->name('branches.destroy');

    // Products
    Route::resource('products', \App\Http\Controllers\Vendor\ProductController::class);

    // Product Specifications
    Route::get('/products/{id}/specifications', [\App\Http\Controllers\Vendor\ProductSpecificationController::class, 'edit'])->name('products.specifications.edit');
    Route::post('/products/{id}/specifications', [\App\Http\Controllers\Vendor\ProductSpecificationController::class, 'updateSpecifications'])->name('products.specifications.update');
    Route::post('/products/{id}/colors', [\App\Http\Controllers\Vendor\ProductSpecificationController::class, 'updateColors'])->name('products.colors.update');
    Route::post('/products/{id}/sizes', [\App\Http\Controllers\Vendor\ProductSpecificationController::class, 'updateSizes'])->name('products.sizes.update');
    Route::post('/products/{id}/branches', [\App\Http\Controllers\Vendor\ProductSpecificationController::class, 'updateBranches'])->name('products.branches.update');

    // Color-Size API routes
    Route::post('/api/color-sizes/get-sizes-for-color', [\App\Http\Controllers\Vendor\ProductColorSizeController::class, 'getSizesForColor'])->name('api.color-sizes.get-sizes-for-color');
    Route::post('/api/color-sizes/validate-stock-allocation', [\App\Http\Controllers\Vendor\ProductColorSizeController::class, 'validateStockAllocation'])->name('api.color-sizes.validate-stock-allocation');
    Route::post('/api/color-sizes/get-color-stock-info', [\App\Http\Controllers\Vendor\ProductColorSizeController::class, 'getColorStockInfo'])->name('api.color-sizes.get-color-stock-info');
    Route::post('/api/color-sizes/save-combinations', [\App\Http\Controllers\Vendor\ProductColorSizeController::class, 'saveColorSizeCombinations'])->name('api.color-sizes.save-combinations');

    // Services
    Route::resource('services', \App\Http\Controllers\Vendor\ServiceController::class);

    // Orders
    Route::get('/orders', [\App\Http\Controllers\Vendor\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/pending', [\App\Http\Controllers\Vendor\OrderController::class, 'pendingOrders'])->name('orders.pending');
    Route::post('/orders/update-status', [\App\Http\Controllers\Vendor\OrderController::class, 'updateMultipleStatus'])->name('orders.update-multiple-status');
    Route::get('/orders/export', [\App\Http\Controllers\Vendor\OrderController::class, 'export'])->name('orders.export');
    Route::get('/orders/{order}', [\App\Http\Controllers\Vendor\OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/edit', [\App\Http\Controllers\Vendor\OrderController::class, 'edit'])->name('orders.edit');

    // Image testing and fix routes for vendors
    Route::get('/image/test', [\App\Http\Controllers\ImageTestController::class, 'index'])->name('image.test');
    Route::get('/fix/images', [\App\Http\Controllers\Admin\ImageFixController::class, 'fixImages'])->name('fix.images');
    Route::put('/orders/{order}', [\App\Http\Controllers\Vendor\OrderController::class, 'update'])->name('orders.update');
    Route::get('/orders/{order}/invoice', [\App\Http\Controllers\Vendor\OrderController::class, 'invoice'])->name('orders.invoice');

    // Order Items
    Route::get('/order-items/{id}/edit', [\App\Http\Controllers\Vendor\OrderItemController::class, 'edit'])->name('order-items.edit');
    Route::put('/order-items/{id}/update-status', [\App\Http\Controllers\Vendor\OrderItemController::class, 'updateStatus'])->name('order-items.update-status');
    Route::put('/orders/{orderId}/update-vendor-items-status', [\App\Http\Controllers\Vendor\OrderItemController::class, 'updateVendorItemsStatus'])->name('order-items.update-vendor-items-status');

    // Bookings
    Route::get('/bookings', [\App\Http\Controllers\Vendor\BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/calendar', [\App\Http\Controllers\Vendor\BookingController::class, 'calendar'])->name('bookings.calendar');
    Route::get('/bookings/export', [\App\Http\Controllers\Vendor\BookingController::class, 'export'])->name('bookings.export');
    Route::get('/bookings/{booking}', [\App\Http\Controllers\Vendor\BookingController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{booking}/edit', [\App\Http\Controllers\Vendor\BookingController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{booking}', [\App\Http\Controllers\Vendor\BookingController::class, 'update'])->name('bookings.update');
    Route::get('/bookings/{booking}/invoice', [\App\Http\Controllers\Vendor\BookingController::class, 'invoice'])->name('bookings.invoice');

    // Shipping
    Route::get('/shipping/settings', [\App\Http\Controllers\Vendor\ShippingController::class, 'settings'])->name('shipping.settings');
    Route::put('/shipping/settings', [\App\Http\Controllers\Vendor\ShippingController::class, 'updateSettings'])->name('shipping.update-settings');
    Route::get('/shipping/orders', [\App\Http\Controllers\Vendor\ShippingController::class, 'orders'])->name('shipping.orders');
    Route::get('/shipping/orders/{id}', [\App\Http\Controllers\Vendor\ShippingController::class, 'orderDetails'])->name('shipping.order-details');
    Route::put('/shipping/orders/{id}/status', [\App\Http\Controllers\Vendor\ShippingController::class, 'updateShippingStatus'])->name('shipping.update-status');
    Route::get('/shipping/shipments', [\App\Http\Controllers\Vendor\ShippingController::class, 'shipments'])->name('shipping.shipments');

    // Settings
    Route::get('/settings', function () {
        return view('vendor.settings');
    })->name('settings');
    Route::get('/settings/profile', function () {
        return view('vendor.settings.profile');
    })->name('settings.profile');
    Route::put('/settings/profile', [VendorSettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::get('/settings/security', function () {
        return view('vendor.settings.security');
    })->name('settings.security');
    Route::put('/settings/security', [VendorSettingsController::class, 'updatePassword'])->name('settings.security.update');
    Route::get('/settings/notifications', function () {
        return view('vendor.settings.notifications');
    })->name('settings.notifications');
    // Define settings.payment route
    Route::get('/settings/payment', 'App\Http\Controllers\Vendor\PaymentSettingsController@index')->name('settings.payment');

    // Alias for backward compatibility
    Route::get('/settings/payment-redirect', function() {
        return redirect()->route('vendor.settings.payment');
    })->name('settings.payment.redirect');
    Route::put('/settings/payment/preferences', 'App\Http\Controllers\Vendor\PaymentSettingsController@updatePayoutPreferences')->name('settings.payment.preferences.update');
    Route::post('/settings/payment/methods', 'App\Http\Controllers\Vendor\PaymentSettingsController@addPaymentMethod')->name('settings.payment.methods.add');
    Route::put('/settings/payment/methods/{id}', 'App\Http\Controllers\Vendor\PaymentSettingsController@updatePaymentMethod')->name('settings.payment.methods.update');
    Route::delete('/settings/payment/methods/{id}', 'App\Http\Controllers\Vendor\PaymentSettingsController@removePaymentMethod')->name('settings.payment.methods.remove');
    Route::post('/settings/payment/payout-methods', 'App\Http\Controllers\Vendor\PaymentSettingsController@addPayoutMethod')->name('settings.payment.payout-methods.add');
    Route::put('/settings/payment/payout-methods/{id}', 'App\Http\Controllers\Vendor\PaymentSettingsController@updatePayoutMethod')->name('settings.payment.payout-methods.update');
    Route::delete('/settings/payment/payout-methods/{id}', 'App\Http\Controllers\Vendor\PaymentSettingsController@removePayoutMethod')->name('settings.payment.payout-methods.remove');
    Route::get('/settings/hours', function () {
        return view('vendor.settings.hours');
    })->name('settings.hours');
    Route::get('/settings/tax', function () {
        return view('vendor.settings.tax');
    })->name('settings.tax');
    Route::get('/settings/deactivate', function () {
        return redirect()->route('vendor.settings')->with('success', 'Account deactivated successfully');
    })->name('settings.deactivate');
    Route::get('/settings/delete', function () {
        return redirect()->route('login')->with('success', 'Account deleted successfully');
    })->name('settings.delete');
});

// Provider routes
Route::prefix('provider')->name('provider.')->middleware(['auth', \App\Http\Middleware\ProviderMiddleware::class])->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Provider\DashboardController::class, 'index'])->name('dashboard');

    // Locations
    Route::get('/locations', [App\Http\Controllers\Provider\LocationController::class, 'index'])->name('locations.index');
    Route::post('/locations', [App\Http\Controllers\Provider\LocationController::class, 'store'])->name('locations.store');
    Route::put('/locations/{id}', [App\Http\Controllers\Provider\LocationController::class, 'update'])->name('locations.update');
    Route::delete('/locations/{id}', [App\Http\Controllers\Provider\LocationController::class, 'destroy'])->name('locations.destroy');

    // Products
    Route::get('/products', [App\Http\Controllers\Provider\ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [App\Http\Controllers\Provider\ProviderProductController::class, 'create'])->name('products.create');
    Route::post('/products', [App\Http\Controllers\Provider\ProviderProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}', [App\Http\Controllers\Provider\ProductController::class, 'show'])->name('products.show');
    Route::get('/products/{id}/edit', [App\Http\Controllers\Provider\ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [App\Http\Controllers\Provider\ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [App\Http\Controllers\Provider\ProductController::class, 'destroy'])->name('products.destroy');

    // Orders
    Route::get('/orders', [App\Http\Controllers\Provider\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [App\Http\Controllers\Provider\OrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{id}/status', [App\Http\Controllers\Provider\OrderController::class, 'updateStatus'])->name('orders.update-status');

    // Profile
    Route::get('/profile', [App\Http\Controllers\Provider\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [App\Http\Controllers\Provider\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [App\Http\Controllers\Provider\ProfileController::class, 'showChangePasswordForm'])->name('profile.change-password');
    Route::put('/profile/change-password', [App\Http\Controllers\Provider\ProfileController::class, 'changePassword'])->name('profile.update-password');

    // Provider Products
    Route::get('/provider-products', [App\Http\Controllers\Provider\ProviderProductController::class, 'index'])->name('provider-products.index');
    Route::get('/provider-products/create', [App\Http\Controllers\Provider\ProviderProductController::class, 'create'])->name('provider-products.create');
    Route::post('/provider-products', [App\Http\Controllers\Provider\ProviderProductController::class, 'store'])->name('provider-products.store');
    Route::get('/provider-products/{id}', [App\Http\Controllers\Provider\ProviderProductController::class, 'show'])->name('provider-products.show');
    Route::get('/provider-products/{id}/edit', [App\Http\Controllers\Provider\ProviderProductController::class, 'edit'])->name('provider-products.edit');
    Route::put('/provider-products/{id}', [App\Http\Controllers\Provider\ProviderProductController::class, 'update'])->name('provider-products.update');
    Route::delete('/provider-products/{id}', [App\Http\Controllers\Provider\ProviderProductController::class, 'destroy'])->name('provider-products.destroy');
});


require __DIR__.'/test.php';
