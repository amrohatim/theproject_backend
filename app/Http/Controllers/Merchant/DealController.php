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
use App\Services\WebPImageService;

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

        // Get products and services that already have active deals
        $productsWithActiveDeals = Deal::getProductIdsWithActiveDeals(null, Auth::id());
        $servicesWithActiveDeals = Deal::getServiceIdsWithActiveDeals(null, Auth::id());

        return view('merchant.deals.create', compact('products', 'services', 'productsWithActiveDeals', 'servicesWithActiveDeals'));
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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:20480', // 20MB = 20480KB
            'status' => 'required|in:active,inactive',
            'applies_to' => 'required|in:products,services,products_and_services',
            'product_ids' => 'required_if:applies_to,products|required_if:applies_to,products_and_services|array',
            'service_ids' => 'required_if:applies_to,services|required_if:applies_to,products_and_services|array',
        ]);

        // Check for conflicts with existing active deals
        $conflictingProducts = [];
        $conflictingServices = [];

        if (in_array($request->applies_to, ['products', 'products_and_services']) && $request->product_ids) {
            $conflictingProducts = Deal::getConflictingProductIds($request->product_ids, null, Auth::id());
        }

        if (in_array($request->applies_to, ['services', 'products_and_services']) && $request->service_ids) {
            $conflictingServices = Deal::getConflictingServiceIds($request->service_ids, null, Auth::id());
        }

        // If there are conflicts, return with validation errors
        if (!empty($conflictingProducts) || !empty($conflictingServices)) {
            $errors = [];

            if (!empty($conflictingProducts)) {
                $productNames = Product::whereIn('id', $conflictingProducts)->pluck('name')->toArray();
                $errors['product_ids'] = __('messages.products_already_have_active_deals', ['products' => implode(', ', $productNames)]);
            }

            if (!empty($conflictingServices)) {
                $serviceNames = Service::whereIn('id', $conflictingServices)->pluck('name')->toArray();
                $errors['service_ids'] = __('messages.services_already_have_active_deals', ['services' => implode(', ', $serviceNames)]);
            }

            return redirect()->back()
                ->withInput()
                ->withErrors($errors);
        }

        $data = $request->all();
        $data['user_id'] = Auth::id();

        // Handle image upload (required)
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            try {
                // Convert to WebP and store
                $webpService = new WebPImageService();
                $imagePath = $webpService->convertAndStoreWithUrl($file, 'deals', 75); // 75% quality

                if (!$imagePath) {
                    // Fallback to original upload method if WebP conversion fails
                    $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $fallbackPath = $file->storeAs('deals', $imageName, 'public');
                    $imagePath = $fallbackPath ? '/storage/' . $fallbackPath : null;

                    Log::warning('WebP conversion failed for merchant deal, using fallback method', [
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
                Log::error('Error processing deal image', [
                    'error' => $e->getMessage(),
                    'file' => $file->getClientOriginalName()
                ]);

                return redirect()->back()
                    ->withInput()
                    ->withErrors(['image' => 'Failed to process image. Please try again with a different image.']);
            }
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
        Deal::create($data);

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

        // Get products and services that already have active deals (excluding current deal)
        $productsWithActiveDeals = Deal::getProductIdsWithActiveDeals($deal->id, Auth::id());
        $servicesWithActiveDeals = Deal::getServiceIdsWithActiveDeals($deal->id, Auth::id());

        return view('merchant.deals.edit', compact('deal', 'products', 'services', 'productsWithActiveDeals', 'servicesWithActiveDeals'));
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480', // 20MB = 20480KB
            'status' => 'required|in:active,inactive',
            'applies_to' => 'required|in:products,services,products_and_services',
            'product_ids' => 'required_if:applies_to,products|required_if:applies_to,products_and_services|array',
            'service_ids' => 'required_if:applies_to,services|required_if:applies_to,products_and_services|array',
        ]);

        // Check for conflicts with existing active deals (excluding current deal)
        $conflictingProducts = [];
        $conflictingServices = [];

        if (in_array($request->applies_to, ['products', 'products_and_services']) && $request->product_ids) {
            $conflictingProducts = Deal::getConflictingProductIds($request->product_ids, $deal->id, Auth::id());
        }

        if (in_array($request->applies_to, ['services', 'products_and_services']) && $request->service_ids) {
            $conflictingServices = Deal::getConflictingServiceIds($request->service_ids, $deal->id, Auth::id());
        }

        // If there are conflicts, return with validation errors
        if (!empty($conflictingProducts) || !empty($conflictingServices)) {
            $errors = [];

            if (!empty($conflictingProducts)) {
                $productNames = Product::whereIn('id', $conflictingProducts)->pluck('name')->toArray();
                $errors['product_ids'] = __('messages.products_already_have_active_deals', ['products' => implode(', ', $productNames)]);
            }

            if (!empty($conflictingServices)) {
                $serviceNames = Service::whereIn('id', $conflictingServices)->pluck('name')->toArray();
                $errors['service_ids'] = __('messages.services_already_have_active_deals', ['services' => implode(', ', $serviceNames)]);
            }

            return redirect()->back()
                ->withInput()
                ->withErrors($errors);
        }

        $data = $request->all();

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            try {
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

                // Convert to WebP and store
                $webpService = new WebPImageService();
                $imagePath = $webpService->convertAndStoreWithUrl($file, 'deals', 75); // 75% quality

                if (!$imagePath) {
                    // Fallback to original upload method if WebP conversion fails
                    $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $fallbackPath = $file->storeAs('deals', $imageName, 'public');
                    $imagePath = $fallbackPath ? '/storage/' . $fallbackPath : null;

                    Log::warning('WebP conversion failed for merchant deal update, using fallback method', [
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
                Log::error('Error processing deal image update', [
                    'error' => $e->getMessage(),
                    'file' => $file->getClientOriginalName(),
                    'deal_id' => $deal->id
                ]);

                return redirect()->back()
                    ->withInput()
                    ->withErrors(['image' => 'Failed to process image. Please try again with a different image.']);
            }
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