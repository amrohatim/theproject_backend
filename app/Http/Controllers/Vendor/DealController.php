<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\Product;
use App\Models\Category;
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
        $deals = Deal::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('vendor.deals.index', compact('deals'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get products for this vendor
        $products = Product::join('branches', 'products.branch_id', '=', 'branches.id')
            ->join('companies', 'branches.company_id', '=', 'companies.id')
            ->where('companies.user_id', Auth::id())
            ->select('products.*')
            ->get();

        // Get all categories
        $categories = Category::all();

        return view('vendor.deals.create', compact('products', 'categories'));
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
            'description' => 'nullable|string',
            'promotional_message' => 'nullable|string|max:50',
            'discount_percentage' => 'required|numeric|min:1|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
            'applies_to' => 'required|in:all,products,categories',
            'product_ids' => 'required_if:applies_to,products|array',
            'category_ids' => 'required_if:applies_to,categories|array',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('deals', 'public');
            $data['image'] = $imagePath;
        }

        // Handle product_ids based on applies_to value
        if ($data['applies_to'] === 'all') {
            // Get all product IDs for this vendor
            $productIds = Product::join('branches', 'products.branch_id', '=', 'branches.id')
                ->join('companies', 'branches.company_id', '=', 'companies.id')
                ->where('companies.user_id', Auth::id())
                ->pluck('products.id')
                ->toArray();

            $data['product_ids'] = $productIds;
        } elseif ($data['applies_to'] === 'categories' && isset($data['category_ids'])) {
            // Get all product IDs that belong to the selected categories and are owned by this vendor
            $productIds = Product::join('branches', 'products.branch_id', '=', 'branches.id')
                ->join('companies', 'branches.company_id', '=', 'companies.id')
                ->where('companies.user_id', Auth::id())
                ->whereIn('products.category_id', $data['category_ids'])
                ->pluck('products.id')
                ->toArray();

            $data['product_ids'] = $productIds;
        }

        // Create the deal
        $deal = Deal::create($data);

        return redirect()->route('vendor.deals.index')
            ->with('success', 'Deal created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $deal = Deal::where('user_id', Auth::id())
            ->findOrFail($id);

        return view('vendor.deals.show', compact('deal'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $deal = Deal::where('user_id', Auth::id())
            ->findOrFail($id);

        // Get products for this vendor
        $products = Product::join('branches', 'products.branch_id', '=', 'branches.id')
            ->join('companies', 'branches.company_id', '=', 'companies.id')
            ->where('companies.user_id', Auth::id())
            ->select('products.*')
            ->get();

        // Get all categories
        $categories = Category::all();

        return view('vendor.deals.edit', compact('deal', 'products', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $deal = Deal::where('user_id', Auth::id())
            ->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'promotional_message' => 'nullable|string|max:50',
            'discount_percentage' => 'required|numeric|min:1|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
            'applies_to' => 'required|in:all,products,categories',
            'product_ids' => 'required_if:applies_to,products|array',
            'category_ids' => 'required_if:applies_to,categories|array',
        ]);

        $data = $request->all();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($deal->image) {
                Storage::disk('public')->delete($deal->getRawOriginalImage());
            }

            $imagePath = $request->file('image')->store('deals', 'public');
            $data['image'] = $imagePath;
        }

        // Handle product_ids based on applies_to value
        if ($data['applies_to'] === 'all') {
            // Get all product IDs for this vendor
            $productIds = Product::join('branches', 'products.branch_id', '=', 'branches.id')
                ->join('companies', 'branches.company_id', '=', 'companies.id')
                ->where('companies.user_id', Auth::id())
                ->pluck('products.id')
                ->toArray();

            $data['product_ids'] = $productIds;
        } elseif ($data['applies_to'] === 'categories' && isset($data['category_ids'])) {
            // Get all product IDs that belong to the selected categories and are owned by this vendor
            $productIds = Product::join('branches', 'products.branch_id', '=', 'branches.id')
                ->join('companies', 'branches.company_id', '=', 'companies.id')
                ->where('companies.user_id', Auth::id())
                ->whereIn('products.category_id', $data['category_ids'])
                ->pluck('products.id')
                ->toArray();

            $data['product_ids'] = $productIds;
        }

        // Update the deal
        $deal->update($data);

        return redirect()->route('vendor.deals.index')
            ->with('success', 'Deal updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deal = Deal::where('user_id', Auth::id())
            ->findOrFail($id);

        // Delete image if exists
        if ($deal->image) {
            Storage::disk('public')->delete($deal->getRawOriginalImage());
        }

        $deal->delete();

        return redirect()->route('vendor.deals.index')
            ->with('success', 'Deal deleted successfully.');
    }
}
