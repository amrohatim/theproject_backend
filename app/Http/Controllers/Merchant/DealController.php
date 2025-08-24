<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DealController extends Controller
{
    /**
     * Display a listing of the merchant's deals.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $deals = Deal::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('merchant.deals.index', compact('deals'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get products for this merchant
        $products = Product::where('user_id', Auth::id())->get();

        // Get services for this merchant (direct ownership)
        $services = Service::where('merchant_id', Auth::id())->get();

        return view('merchant.deals.create', compact('products', 'services'));
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
            'description_arabic' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    $description = $request->input('description');
                    $descriptionArabic = $value;

                    // If one is filled, both must be filled
                    if ((!empty($description) && empty($descriptionArabic)) ||
                        (empty($description) && !empty($descriptionArabic))) {
                        $fail(__('messages.description_both_or_none'));
                    }
                },
            ],
            'promotional_message' => 'nullable|string|max:50',
            'promotional_message_arabic' => 'nullable|string|max:50',
            'discount_percentage' => 'required|numeric|min:1|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
            'applies_to' => 'required|in:products,services,products_and_services',
            'product_ids' => 'required_if:applies_to,products|required_if:applies_to,products_and_services|array',
            'service_ids' => 'required_if:applies_to,services|required_if:applies_to,products_and_services|array',
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
            // Clear service_ids for product-only deals
            $data['service_ids'] = null;
        } elseif ($data['applies_to'] === 'services') {
            // Clear product_ids for service-only deals
            $data['product_ids'] = null;
        }
        // For 'products_and_services', keep both arrays as they are

        // Create the deal
        $deal = Deal::create($data);

        return redirect()->route('merchant.deals.index')
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

        return view('merchant.deals.show', compact('deal'));
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

        // Get products for this merchant
        $products = Product::where('user_id', Auth::id())->get();

        // Get services for this merchant (direct ownership)
        $services = Service::where('merchant_id', Auth::id())->get();

        return view('merchant.deals.edit', compact('deal', 'products', 'services'));
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
            'title_arabic' => 'required|string|max:255',
            'description' => 'nullable|string',
            'description_arabic' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    $description = $request->input('description');
                    $descriptionArabic = $value;

                    // If one is filled, both must be filled
                    if ((!empty($description) && empty($descriptionArabic)) ||
                        (empty($description) && !empty($descriptionArabic))) {
                        $fail(__('messages.description_both_or_none'));
                    }
                },
            ],
            'promotional_message' => 'nullable|string|max:50',
            'promotional_message_arabic' => 'nullable|string|max:50',
            'discount_percentage' => 'required|numeric|min:1|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
            'applies_to' => 'required|in:products,services,products_and_services',
            'product_ids' => 'required_if:applies_to,products|required_if:applies_to,products_and_services|array',
            'service_ids' => 'required_if:applies_to,services|required_if:applies_to,products_and_services|array',
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
                    Log::warning('Failed to delete old deal image: ' . $e->getMessage(), [
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
            // Clear service_ids for product-only deals
            $data['service_ids'] = null;
        } elseif ($data['applies_to'] === 'services') {
            // Clear product_ids for service-only deals
            $data['product_ids'] = null;
        }
        // For 'products_and_services', keep both arrays as they are

        // Update the deal
        $deal->update($data);

        return redirect()->route('merchant.deals.index')
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
                Log::warning('Failed to delete deal image: ' . $e->getMessage(), [
                    'deal_id' => $deal->id,
                    'image_path' => $imagePath
                ]);
            }
        }

        $deal->delete();

        return redirect()->route('merchant.deals.index')
            ->with('success', 'Deal deleted successfully.');
    }
}