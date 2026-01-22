<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\Product;
use App\Models\Service;
use App\Models\Category;
use App\Models\Branch;
use App\Models\BusinessType;
use App\Services\WebPImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
        $branches = Branch::join('companies', 'branches.company_id', '=', 'companies.id')
            ->where('companies.user_id', Auth::id())
            ->select('branches.*')
            ->orderBy('branches.name')
            ->get();

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

        // Get product and service IDs that already have active deals
        $productsWithActiveDeals = Deal::getProductIdsWithActiveDeals(null, Auth::id());
        $servicesWithActiveDeals = Deal::getServiceIdsWithActiveDeals(null, Auth::id());

        return view('vendor.deals.create', compact('branches', 'products', 'services', 'productsWithActiveDeals', 'servicesWithActiveDeals'));
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
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data = $this->applyBranchSelection($data);

        // Handle image upload with WebP conversion
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

                    Log::warning('WebP conversion failed for vendor deal, using fallback method', [
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
                Log::error('Error processing deal image upload', [
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

        // Validate that selected products/services don't have active deals
        if (isset($data['product_ids']) && is_array($data['product_ids'])) {
            $conflictingProducts = Deal::getConflictingProductIds($data['product_ids'], null, Auth::id());
            if (!empty($conflictingProducts)) {
                $productNames = Product::whereIn('id', $conflictingProducts)->pluck('name')->toArray();
                return back()->withErrors([
                    'product_ids' => 'The following products already have active deals: ' . implode(', ', $productNames)
                ])->withInput();
            }
        }

        if (isset($data['service_ids']) && is_array($data['service_ids'])) {
            $conflictingServices = Deal::getConflictingServiceIds($data['service_ids'], null, Auth::id());
            if (!empty($conflictingServices)) {
                $serviceNames = Service::whereIn('id', $conflictingServices)->pluck('name')->toArray();
                return back()->withErrors([
                    'service_ids' => 'The following services already have active deals: ' . implode(', ', $serviceNames)
                ])->withInput();
            }
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

        $branches = Branch::join('companies', 'branches.company_id', '=', 'companies.id')
            ->where('companies.user_id', Auth::id())
            ->select('branches.*')
            ->orderBy('branches.name')
            ->get();

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

        // Get product and service IDs that already have active deals (excluding current deal)
        $productsWithActiveDeals = Deal::getProductIdsWithActiveDeals($deal->id, Auth::id());
        $servicesWithActiveDeals = Deal::getServiceIdsWithActiveDeals($deal->id, Auth::id());

        return view('vendor.deals.edit', compact('deal', 'branches', 'products', 'services', 'productsWithActiveDeals', 'servicesWithActiveDeals'));
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
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $data = $request->all();
        $data = $this->applyBranchSelection($data);

        // Validate that selected products/services don't have active deals (excluding current deal)
        if (isset($data['product_ids']) && is_array($data['product_ids'])) {
            $conflictingProducts = Deal::getConflictingProductIds($data['product_ids'], $deal->id, Auth::id());
            if (!empty($conflictingProducts)) {
                $productNames = Product::whereIn('id', $conflictingProducts)->pluck('name')->toArray();
                return back()->withErrors([
                    'product_ids' => 'The following products already have active deals: ' . implode(', ', $productNames)
                ])->withInput();
            }
        }

        if (isset($data['service_ids']) && is_array($data['service_ids'])) {
            $conflictingServices = Deal::getConflictingServiceIds($data['service_ids'], $deal->id, Auth::id());
            if (!empty($conflictingServices)) {
                $serviceNames = Service::whereIn('id', $conflictingServices)->pluck('name')->toArray();
                return back()->withErrors([
                    'service_ids' => 'The following services already have active deals: ' . implode(', ', $serviceNames)
                ])->withInput();
            }
        }

        // Handle image upload with WebP conversion
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
                        Log::warning('Failed to delete old deal image: ' . $e->getMessage(), [
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

                    Log::warning('WebP conversion failed for vendor deal update, using fallback method', [
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
                Log::warning('Failed to delete deal image: ' . $e->getMessage(), [
                    'deal_id' => $deal->id,
                    'image_path' => $imagePath
                ]);
            }
        }

        $deal->delete();

        return redirect()->route('vendor.deals.index')
            ->with('success', 'Deal deleted successfully.');
    }

    private function applyBranchSelection(array $data): array
    {
        if (empty($data['branch_id'])) {
            $data['branch_id'] = null;
            $data['business_type_id'] = null;
            return $data;
        }

        $branch = Branch::join('companies', 'branches.company_id', '=', 'companies.id')
            ->where('companies.user_id', Auth::id())
            ->where('branches.id', $data['branch_id'])
            ->select('branches.*')
            ->first();

        if (!$branch) {
            abort(403, 'Invalid branch selection.');
        }

        $data['branch_id'] = $branch->id;
        $data['business_type_id'] = BusinessType::where('business_name', $branch->business_type)->value('id');

        return $data;
    }
}
