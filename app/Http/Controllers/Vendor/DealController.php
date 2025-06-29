<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\Product;
use App\Models\Service;
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

        // Get services for this vendor
        $services = Service::join('branches', 'services.branch_id', '=', 'branches.id')
            ->join('companies', 'branches.company_id', '=', 'companies.id')
            ->where('companies.user_id', Auth::id())
            ->select('services.*')
            ->get();

        return view('vendor.deals.create', compact('products', 'services'));
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
            'applies_to' => 'required|in:products,services',
            'product_ids' => 'required_if:applies_to,products|array',
            'service_ids' => 'required_if:applies_to,services|array',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('deals', 'public');
            $data['image'] = $imagePath;
        }

        // Ensure only the relevant IDs are set based on applies_to value
        if ($data['applies_to'] === 'products') {
            // Clear service_ids for product deals
            $data['service_ids'] = null;
        } elseif ($data['applies_to'] === 'services') {
            // Clear product_ids for service deals
            $data['product_ids'] = null;
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

        // Get services for this vendor
        $services = Service::join('branches', 'services.branch_id', '=', 'branches.id')
            ->join('companies', 'branches.company_id', '=', 'companies.id')
            ->where('companies.user_id', Auth::id())
            ->select('services.*')
            ->get();

        return view('vendor.deals.edit', compact('deal', 'products', 'services'));
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
            'applies_to' => 'required|in:products,services',
            'product_ids' => 'required_if:applies_to,products|array',
            'service_ids' => 'required_if:applies_to,services|array',
        ]);

        $data = $request->all();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            $oldImagePath = $deal->getRawOriginalImage();
            if ($oldImagePath && !empty(trim($oldImagePath))) {
                try {
                    // Check if file exists before attempting to delete
                    if (Storage::disk('public')->exists($oldImagePath)) {
                        Storage::disk('public')->delete($oldImagePath);
                    }
                } catch (\Exception $e) {
                    // Log the error but don't stop the update process
                    \Log::warning('Failed to delete old deal image: ' . $e->getMessage(), [
                        'deal_id' => $deal->id,
                        'image_path' => $oldImagePath
                    ]);
                }
            }

            $imagePath = $request->file('image')->store('deals', 'public');
            $data['image'] = $imagePath;
        }

        // Ensure only the relevant IDs are set based on applies_to value
        if ($data['applies_to'] === 'products') {
            // Clear service_ids for product deals
            $data['service_ids'] = null;
        } elseif ($data['applies_to'] === 'services') {
            // Clear product_ids for service deals
            $data['product_ids'] = null;
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
        $imagePath = $deal->getRawOriginalImage();
        if ($imagePath && !empty(trim($imagePath))) {
            try {
                // Check if file exists before attempting to delete
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
            } catch (\Exception $e) {
                // Log the error but don't stop the deletion process
                \Log::warning('Failed to delete deal image: ' . $e->getMessage(), [
                    'deal_id' => $deal->id,
                    'image_path' => $imagePath
                ]);
            }
        }

        $deal->delete();

        return redirect()->route('vendor.deals.index')
            ->with('success', 'Deal deleted successfully.');
    }
}
