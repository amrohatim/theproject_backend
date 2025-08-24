<?php

namespace App\Http\Controllers\ProductsManager;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\Product;
use App\Models\Category;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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



        $categories = Category::all();
        $branches = Branch::where('company_id', $company->id)->get();

        return view('products-manager.deals.create', compact('products', 'categories', 'branches'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_arabic' => 'required|string|max:255',
            'description' => 'nullable|string',
            'description_arabic' => 'nullable|string',
            'promotional_message' => 'nullable|string|max:500',
            'promotional_message_arabic' => 'nullable|string|max:500',
            'discount_percentage' => 'required|numeric|min:1|max:100',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
            'applies_to' => 'required|in:all,products',
            'product_ids' => 'required_if:applies_to,products|nullable|array',
            'product_ids.*' => 'exists:products,id',

        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('deals', 'public');
            $data['image'] = $imagePath;
        }

        // Handle product IDs based on applies_to value
        if ($data['applies_to'] === 'products') {
            // Keep product_ids as is - they will be stored as JSON
            if (isset($data['product_ids']) && is_array($data['product_ids'])) {
                // Ensure product_ids is properly formatted for JSON storage
                $data['product_ids'] = array_values($data['product_ids']);
            } else {
                $data['product_ids'] = null;
            }
        } else {
            // For 'all', clear product_ids
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



        $categories = Category::all();
        $branches = Branch::where('company_id', $company->id)->get();

        return view('products-manager.deals.edit', compact('deal', 'products', 'categories', 'branches'));
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
            'promotional_message' => 'nullable|string|max:500',
            'promotional_message_arabic' => 'nullable|string|max:500',
            'discount_percentage' => 'required|numeric|min:1|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
            'applies_to' => 'required|in:all,products',
            'product_ids' => 'required_if:applies_to,products|nullable|array',
            'product_ids.*' => 'exists:products,id',

        ]);

        $data = $request->all();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($deal->getRawOriginalImage()) {
                Storage::disk('public')->delete($deal->getRawOriginalImage());
            }
            
            $imagePath = $request->file('image')->store('deals', 'public');
            $data['image'] = $imagePath;
        }

        // Handle product IDs based on applies_to value
        if ($data['applies_to'] === 'products') {
            // Keep product_ids as is - they will be stored as JSON
            if (isset($data['product_ids']) && is_array($data['product_ids'])) {
                // Ensure product_ids is properly formatted for JSON storage
                $data['product_ids'] = array_values($data['product_ids']);
            } else {
                $data['product_ids'] = null;
            }
        } else {
            // For 'all', clear product_ids
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
        if ($deal->getRawOriginalImage()) {
            Storage::disk('public')->delete($deal->getRawOriginalImage());
        }

        $deal->delete();

        return redirect()->route('products-manager.deals.index')
            ->with('success', __('products_manager.deal_deleted_successfully'));
    }
}
