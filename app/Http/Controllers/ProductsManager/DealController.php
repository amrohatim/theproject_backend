<?php

namespace App\Http\Controllers\ProductsManager;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\Product;
use App\Models\Category;
use App\Models\Branch;
use App\Models\BusinessType;
use App\Services\WebPImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DealController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $productsManager = $user->productsManager;

        if (!$productsManager) {
            return redirect('/')->with('error', 'Products manager profile not found.');
        }

        $company = $productsManager->company;

        // Get all users in the same company (vendors, service providers, products managers, etc.)
        $companyUserIds = collect();

        // Add vendor users (users who own the company)
        $vendorUsers = \App\Models\User::whereHas('company', function($query) use ($company) {
            $query->where('id', $company->id);
        })->pluck('id');
        $companyUserIds = $companyUserIds->merge($vendorUsers);

        // Add products manager users in the same company
        $productsManagerUsers = \App\Models\User::whereHas('productsManager', function($query) use ($company) {
            $query->where('company_id', $company->id);
        })->pluck('id');
        $companyUserIds = $companyUserIds->merge($productsManagerUsers);

        // Add service provider users in the same company (if they exist)
        $serviceProviderUsers = \App\Models\User::whereHas('serviceProvider', function($query) use ($company) {
            $query->where('company_id', $company->id);
        })->pluck('id');
        $companyUserIds = $companyUserIds->merge($serviceProviderUsers);

        // Get deals created by any user in the company
        $deals = Deal::whereIn('user_id', $companyUserIds->unique()->toArray())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('products-manager.deals.index', compact('deals'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        $productsManager = $user->productsManager;

        if (!$productsManager) {
            return redirect('/')->with('error', 'Products manager profile not found.');
        }

        $company = $productsManager->company;

        // Get all products for the company (through branches)
        $products = Product::join('branches', 'products.branch_id', '=', 'branches.id')
            ->where('branches.company_id', $company->id)
            ->select('products.*')
            ->get();

        // Get product IDs that already have active deals
        $productsWithActiveDeals = Deal::getProductIdsWithActiveDeals(null, Auth::id());

        $categories = Category::all();
        $branches = Branch::where('company_id', $company->id)->get();

        return view('products-manager.deals.create', compact('products', 'categories', 'branches', 'productsWithActiveDeals'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $productsManager = $user->productsManager;

        if (!$productsManager) {
            return redirect('/')->with('error', 'Products manager profile not found.');
        }

        $company = $productsManager->company;

        $request->validate([
            'title' => 'required|string|max:255',
            'title_arabic' => 'required|string|max:255',
            'description' => 'nullable|string',
            'description_arabic' => 'nullable|string',
            'promotional_message' => 'nullable|string|max:50',
            'promotional_message_arabic' => 'nullable|string|max:50',
            'discount_percentage' => 'required|numeric|min:1|max:100',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:20480', // 20MB = 20480KB
            'status' => 'required|in:active,inactive',
            'applies_to' => 'required|in:products',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'branch_id' => [
                'required',
                Rule::exists('branches', 'id')->where('company_id', $company->id),
            ],

        ]);

        // Validate bilingual promotional message (if one is provided, both must be provided)
        $promotionalMessage = $request->input('promotional_message');
        $promotionalMessageArabic = $request->input('promotional_message_arabic');

        if ((!empty($promotionalMessage) && empty($promotionalMessageArabic)) ||
            (empty($promotionalMessage) && !empty($promotionalMessageArabic))) {
            return back()->withErrors([
                'promotional_message' => __('messages.promotional_message_both_or_none')
            ])->withInput();
        }

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data = $this->applyBranchSelection($data, $company->id);

        // Validate that selected products don't have active deals
        if (isset($data['product_ids']) && is_array($data['product_ids'])) {
            $conflictingProducts = Deal::getConflictingProductIds($data['product_ids'], null, Auth::id());
            if (!empty($conflictingProducts)) {
                $productNames = Product::whereIn('id', $conflictingProducts)->pluck('name')->toArray();
                return back()->withErrors([
                    'product_ids' => 'The following products already have active deals: ' . implode(', ', $productNames)
                ])->withInput();
            }
        }

        // Handle image upload with WebP compression
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            try {
                // Convert to WebP and store
                $webpService = new WebPImageService();
                $imagePath = $webpService->convertAndStoreWithUrl($file, 'deals');

                if (!$imagePath) {
                    // Fallback to original upload method if WebP conversion fails
                    $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $fallbackPath = $file->storeAs('deals', $imageName, 'public');
                    $imagePath = $fallbackPath ? '/storage/' . $fallbackPath : null;

                    Log::warning('WebP conversion failed for products manager deal, using fallback method', [
                        'original_name' => $file->getClientOriginalName(),
                        'fallback_path' => $imagePath
                    ]);
                }

                if (!$imagePath) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'Failed to upload image. Please try again.']);
                }

                $data['image'] = $imagePath;

            } catch (\Exception $e) {
                Log::error('Error processing products manager deal image upload', [
                    'error' => $e->getMessage(),
                    'file' => $file->getClientOriginalName()
                ]);

                return redirect()->back()
                    ->withInput()
                    ->withErrors(['image' => 'Failed to process image. Please try again with a different image.']);
            }
        }

        // Handle product IDs - since we only support 'products' now
        if (isset($data['product_ids']) && is_array($data['product_ids'])) {
            // Ensure product_ids is properly formatted for JSON storage
            $data['product_ids'] = array_values($data['product_ids']);
        } else {
            $data['product_ids'] = null;
        }

        // Remove any fields that shouldn't be mass assigned
        $dealData = collect($data)->except(['_token'])->toArray();

        // Add the user_id to the deal data
        $dealData['user_id'] = Auth::id();

        // Create the deal
        $deal = Deal::create($dealData);

        return redirect()->route('products-manager.deals.index')
            ->with('success', __('products_manager.deal_created_successfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Deal  $deal
     * @return \Illuminate\Http\Response
     */
    public function edit(Deal $deal)
    {
        $user = Auth::user();
        $productsManager = $user->productsManager;

        if (!$productsManager) {
            return redirect('/')->with('error', 'Products manager profile not found.');
        }

        $company = $productsManager->company;

        // Verify the deal belongs to someone in the company
        $dealUser = $deal->user;
        $dealUserCompanyId = null;

        // Check if user has direct company relationship (vendor)
        if ($dealUser->company) {
            $dealUserCompanyId = $dealUser->company->id;
        }
        // Check if user has company through productsManager relationship
        elseif ($dealUser->productsManager) {
            $dealUserCompanyId = $dealUser->productsManager->company_id;
        }
        // Check if user has company through serviceProvider relationship
        elseif ($dealUser->serviceProvider) {
            $dealUserCompanyId = $dealUser->serviceProvider->company_id;
        }

        if (!$dealUserCompanyId || $dealUserCompanyId !== $company->id) {
            return redirect()->route('products-manager.deals.index')
                ->with('error', 'Deal not found.');
        }

        // Get all products for the company (through branches)
        $products = Product::join('branches', 'products.branch_id', '=', 'branches.id')
            ->where('branches.company_id', $company->id)
            ->select('products.*')
            ->get();

        // Get product IDs that already have active deals (excluding current deal)
        $productsWithActiveDeals = Deal::getProductIdsWithActiveDeals($deal->id, Auth::id());

        $categories = Category::all();
        $branches = Branch::where('company_id', $company->id)->get();

        return view('products-manager.deals.edit', compact('deal', 'products', 'categories', 'branches', 'productsWithActiveDeals'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Deal  $deal
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Deal $deal)
    {
        $user = Auth::user();
        $productsManager = $user->productsManager;

        if (!$productsManager) {
            return redirect('/')->with('error', 'Products manager profile not found.');
        }

        $company = $productsManager->company;

        // Verify the deal belongs to someone in the company
        $dealUser = $deal->user;
        $dealUserCompanyId = null;

        // Check if user has direct company relationship (vendor)
        if ($dealUser->company) {
            $dealUserCompanyId = $dealUser->company->id;
        }
        // Check if user has company through productsManager relationship
        elseif ($dealUser->productsManager) {
            $dealUserCompanyId = $dealUser->productsManager->company_id;
        }
        // Check if user has company through serviceProvider relationship
        elseif ($dealUser->serviceProvider) {
            $dealUserCompanyId = $dealUser->serviceProvider->company_id;
        }

        if (!$dealUserCompanyId || $dealUserCompanyId !== $company->id) {
            return redirect()->route('products-manager.deals.index')
                ->with('error', 'Deal not found.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'title_arabic' => 'required|string|max:255',
            'description' => 'nullable|string',
            'description_arabic' => 'nullable|string',
            'promotional_message' => 'nullable|string|max:50',
            'promotional_message_arabic' => 'nullable|string|max:50',
            'discount_percentage' => 'required|numeric|min:1|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480', // 20MB = 20480KB
            'status' => 'required|in:active,inactive',
            'applies_to' => 'required|in:products',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'branch_id' => [
                'required',
                Rule::exists('branches', 'id')->where('company_id', $company->id),
            ],

        ]);

        // Validate bilingual promotional message (if one is provided, both must be provided)
        $promotionalMessage = $request->input('promotional_message');
        $promotionalMessageArabic = $request->input('promotional_message_arabic');

        if ((!empty($promotionalMessage) && empty($promotionalMessageArabic)) ||
            (empty($promotionalMessage) && !empty($promotionalMessageArabic))) {
            return back()->withErrors([
                'promotional_message' => __('messages.promotional_message_both_or_none')
            ])->withInput();
        }

        $data = $request->all();
        $data = $this->applyBranchSelection($data, $company->id);

        // Validate that selected products don't have active deals (excluding current deal)
        if (isset($data['product_ids']) && is_array($data['product_ids'])) {
            $conflictingProducts = Deal::getConflictingProductIds($data['product_ids'], $deal->id, Auth::id());
            if (!empty($conflictingProducts)) {
                $productNames = Product::whereIn('id', $conflictingProducts)->pluck('name')->toArray();
                return back()->withErrors([
                    'product_ids' => 'The following products already have active deals: ' . implode(', ', $productNames)
                ])->withInput();
            }
        }

        // Handle image upload with WebP compression
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            try {
                // Delete old image if exists
                $oldImagePath = $deal->getRawOriginalImage();
                if ($oldImagePath && !empty(trim($oldImagePath))) {
                    try {
                        $webpService = new WebPImageService();
                        $webpService->deleteImage($oldImagePath);
                    } catch (\Exception $e) {
                        // Log the error but don't stop the update process
                        Log::warning('Failed to delete old products manager deal image: ' . $e->getMessage(), [
                            'deal_id' => $deal->id,
                            'image_path' => $oldImagePath
                        ]);
                    }
                }

                // Convert to WebP and store
                $webpService = new WebPImageService();
                $imagePath = $webpService->convertAndStoreWithUrl($file, 'deals');

                if (!$imagePath) {
                    // Fallback to original upload method if WebP conversion fails
                    $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $fallbackPath = $file->storeAs('deals', $imageName, 'public');
                    $imagePath = $fallbackPath ? '/storage/' . $fallbackPath : null;

                    Log::warning('WebP conversion failed for products manager deal update, using fallback method', [
                        'original_name' => $file->getClientOriginalName(),
                        'fallback_path' => $imagePath
                    ]);
                }

                if (!$imagePath) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'Failed to upload image. Please try again.']);
                }

                $data['image'] = $imagePath;

            } catch (\Exception $e) {
                Log::error('Error processing products manager deal image update', [
                    'error' => $e->getMessage(),
                    'file' => $file->getClientOriginalName(),
                    'deal_id' => $deal->id
                ]);

                return redirect()->back()
                    ->withInput()
                    ->withErrors(['image' => 'Failed to process image. Please try again with a different image.']);
            }
        }

        // Handle product IDs - since we only support 'products' now
        if (isset($data['product_ids']) && is_array($data['product_ids'])) {
            // Ensure product_ids is properly formatted for JSON storage
            $data['product_ids'] = array_values($data['product_ids']);
        } else {
            $data['product_ids'] = null;
        }

        // Remove any fields that shouldn't be mass assigned
        $dealData = collect($data)->except(['_token'])->toArray();

        // Update the deal
        $deal->update($dealData);

        return redirect()->route('products-manager.deals.index')
            ->with('success', __('products_manager.deal_updated_successfully'));
    }

    /**
     * Apply branch selection from products manager action.
     */
    private function applyBranchSelection(array $data, int $companyId): array
    {
        if (empty($data['branch_id'])) {
            $data['branch_id'] = null;
            $data['business_type_id'] = null;
            return $data;
        }

        $branch = Branch::where('company_id', $companyId)
            ->where('id', $data['branch_id'])
            ->first();

        if (!$branch) {
            abort(403, 'Invalid branch selection.');
        }

        $data['branch_id'] = $branch->id;
        $data['business_type_id'] = BusinessType::where('business_name', $branch->business_type)->value('id');

        return $data;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Deal  $deal
     * @return \Illuminate\Http\Response
     */
    public function destroy(Deal $deal)
    {
        $user = Auth::user();
        $productsManager = $user->productsManager;

        if (!$productsManager) {
            return redirect('/')->with('error', 'Products manager profile not found.');
        }

        $company = $productsManager->company;

        // Verify the deal belongs to someone in the company
        $dealUser = $deal->user;
        $dealUserCompanyId = null;

        // Check if user has direct company relationship (vendor)
        if ($dealUser->company) {
            $dealUserCompanyId = $dealUser->company->id;
        }
        // Check if user has company through productsManager relationship
        elseif ($dealUser->productsManager) {
            $dealUserCompanyId = $dealUser->productsManager->company_id;
        }
        // Check if user has company through serviceProvider relationship
        elseif ($dealUser->serviceProvider) {
            $dealUserCompanyId = $dealUser->serviceProvider->company_id;
        }

        if (!$dealUserCompanyId || $dealUserCompanyId !== $company->id) {
            return redirect()->route('products-manager.deals.index')
                ->with('error', 'Deal not found.');
        }

        // Delete image if exists
        $imagePath = $deal->getRawOriginalImage();
        if ($imagePath && !empty(trim($imagePath))) {
            try {
                $webpService = new WebPImageService();
                $webpService->deleteImage($imagePath);
            } catch (\Exception $e) {
                // Log the error but don't stop the deletion process
                Log::warning('Failed to delete products manager deal image: ' . $e->getMessage(), [
                    'deal_id' => $deal->id,
                    'image_path' => $imagePath
                ]);
            }
        }

        $deal->delete();

        return redirect()->route('products-manager.deals.index')
            ->with('success', __('products_manager.deal_deleted_successfully'));
    }
}
