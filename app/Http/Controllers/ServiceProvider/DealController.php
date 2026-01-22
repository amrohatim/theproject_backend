<?php

namespace App\Http\Controllers\ServiceProvider;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\Service;
use App\Models\Branch;
use App\Models\BusinessType;
use App\Services\WebPImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DealController extends Controller
{
    /**
     * Display a listing of deals for services this provider manages.
     */
    public function index()
    {
        $user = Auth::user();

        // For service providers, we don't need the serviceProvider relationship
        // They can create deals directly as users with service_provider role

        // Get deals created by this service provider user
        try {
            $activeDeals = Deal::where('user_id', $user->id)
                ->where('applies_to', 'services')
                ->where('status', 'active')
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            Log::info('Service Provider Deals Query Result', [
                'user_id' => $user->id,
                'deals_count' => $activeDeals->count(),
                'deals' => $activeDeals->pluck('title', 'id')->toArray()
            ]);
        } catch (\Exception $e) {
            // Fallback to empty collection if query fails
            $activeDeals = collect();
            Log::error('Error fetching service provider deals: ' . $e->getMessage());
        }

        return view('service-provider.deals.index', compact('activeDeals'));
    }

    /**
     * Show the form for creating a new deal.
     */
    public function create()
    {
        $user = Auth::user();

        // Get the service provider record
        $serviceProvider = $user->serviceProvider;

        if (!$serviceProvider) {
            return redirect()->route('service-provider.dashboard')
                ->with('error', 'Service provider profile not found.');
        }

        // Get services that this service provider can manage
        $serviceIds = $serviceProvider->service_ids ?? [];
        $services = Service::whereIn('id', $serviceIds)
            ->with(['branch'])
            ->get();

        // Get service IDs that already have active deals
        $servicesWithActiveDeals = Deal::getServiceIdsWithActiveDeals(null, $user->id);

        // Get branches this service provider has access to
        $branchIds = $serviceProvider->branch_ids ?? [];
        $branches = Branch::whereIn('id', $branchIds)->get();

        return view('service-provider.deals.create', compact('services', 'branches', 'servicesWithActiveDeals'));
    }

    /**
     * Store a newly created deal.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

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
            'promotional_message_arabic' => [
                'nullable',
                'string',
                'max:50',
                function ($attribute, $value, $fail) use ($request) {
                    $promotional = $request->input('promotional_message');
                    $promotionalArabic = $value;

                    // If one is filled, both must be filled
                    if ((!empty($promotional) && empty($promotionalArabic)) ||
                        (empty($promotional) && !empty($promotionalArabic))) {
                        $fail(__('messages.promotional_message_both_or_none'));
                    }
                },
            ],
            'discount_percentage' => 'required|numeric|min:1|max:100',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:20480', // 20MB = 20480KB
            'status' => 'required|in:active,inactive',
            'service_ids' => 'required|array|min:1',
            'service_ids.*' => 'exists:services,id',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        // Validate that all selected services belong to this service provider
        $selectedServices = $request->input('service_ids', []);
        $serviceProvider = $user->serviceProvider;
        $allowedServices = $serviceProvider->service_ids ?? [];
        $allowedBranchIds = $serviceProvider->branch_ids ?? [];

        foreach ($selectedServices as $serviceId) {
            if (!in_array($serviceId, $allowedServices)) {
                return back()->withErrors(['service_ids' => 'You can only create deals for services you manage.']);
            }
        }

        $selectedBranchId = $request->input('branch_id');
        if ($selectedBranchId && !in_array($selectedBranchId, $allowedBranchIds)) {
            return back()->withErrors(['branch_id' => 'You can only select branches you manage.'])->withInput();
        }

        // Validate that selected services don't have active deals
        if (!empty($selectedServices)) {
            $conflictingServices = Deal::getConflictingServiceIds($selectedServices, null, $user->id);
            if (!empty($conflictingServices)) {
                $serviceNames = Service::whereIn('id', $conflictingServices)->pluck('name')->toArray();
                return back()->withErrors([
                    'service_ids' => 'The following services already have active deals: ' . implode(', ', $serviceNames)
                ])->withInput();
            }
        }

        $dealData = [
            'user_id' => $user->id,
            'title' => $request->input('title'),
            'title_arabic' => $request->input('title_arabic'),
            'description' => $request->input('description'),
            'description_arabic' => $request->input('description_arabic'),
            'promotional_message' => $request->input('promotional_message'),
            'promotional_message_arabic' => $request->input('promotional_message_arabic'),
            'discount_percentage' => $request->input('discount_percentage'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'status' => $request->input('status'),
            'applies_to' => 'services',
            'service_ids' => $selectedServices,
            'branch_id' => $request->input('branch_id'),
        ];
        $dealData = $this->applyBranchSelection($dealData, $allowedBranchIds);

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

                    Log::warning('WebP conversion failed for service provider deal, using fallback method', [
                        'original_name' => $file->getClientOriginalName(),
                        'fallback_path' => $imagePath
                    ]);
                }

                if (!$imagePath) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'Failed to upload image. Please try again.']);
                }

                $dealData['image'] = $imagePath;

            } catch (\Exception $e) {
                Log::error('Error processing service provider deal image upload', [
                    'error' => $e->getMessage(),
                    'file' => $file->getClientOriginalName()
                ]);

                return redirect()->back()
                    ->withInput()
                    ->withErrors(['image' => 'Failed to process image. Please try again with a different image.']);
            }
        }

        try {
            Deal::create($dealData);
            return redirect()->route('service-provider.deals.index')
                ->with('success', 'Deal created successfully.');
        } catch (\Exception $e) {
            Log::error('Deal creation failed: ' . $e->getMessage());
            return back()->withErrors(['general' => 'Failed to create deal. Please try again.']);
        }
    }

    /**
     * Show the form for editing a deal.
     */
    public function edit(Deal $deal)
    {
        $user = Auth::user();
        $serviceProvider = $user->serviceProvider;

        if (!$serviceProvider) {
            return redirect('/')->with('error', 'Service provider profile not found.');
        }

        // Check if this deal belongs to the current user and applies to services
        if ($deal->user_id !== $user->id || $deal->applies_to !== 'services') {
            return redirect()->route('service-provider.deals.index')
                ->with('error', 'Deal not found or access denied.');
        }

        // Verify that the deal's services are within the service provider's scope
        $dealServiceIds = $deal->service_ids ?? [];
        $allowedServiceIds = $serviceProvider->service_ids ?? [];

        foreach ($dealServiceIds as $serviceId) {
            if (!in_array($serviceId, $allowedServiceIds)) {
                return redirect()->route('service-provider.deals.index')
                    ->with('error', 'You do not have permission to edit this deal.');
            }
        }

        // Get services that this service provider can manage
        $services = Service::whereIn('id', $allowedServiceIds)
            ->with(['branch'])
            ->get();

        // Get service IDs that already have active deals (excluding current deal)
        $servicesWithActiveDeals = Deal::getServiceIdsWithActiveDeals($deal->id, $user->id);

        // Get branches this service provider has access to
        $branchIds = $serviceProvider->branch_ids ?? [];
        $branches = Branch::whereIn('id', $branchIds)->get();

        return view('service-provider.deals.edit', compact('deal', 'services', 'branches', 'serviceProvider', 'servicesWithActiveDeals'));
    }

    /**
     * Update the specified deal.
     */
    public function update(Request $request, Deal $deal)
    {
        $user = Auth::user();
        $serviceProvider = $user->serviceProvider;

        if (!$serviceProvider) {
            return redirect('/')->with('error', 'Service provider profile not found.');
        }

        // Check if this deal belongs to the current user and applies to services
        if ($deal->user_id !== $user->id || $deal->applies_to !== 'services') {
            return redirect()->route('service-provider.deals.index')
                ->with('error', 'Deal not found or access denied.');
        }

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
            'promotional_message_arabic' => [
                'nullable',
                'string',
                'max:50',
                function ($attribute, $value, $fail) use ($request) {
                    $promotional = $request->input('promotional_message');
                    $promotionalArabic = $value;

                    // If one is filled, both must be filled
                    if ((!empty($promotional) && empty($promotionalArabic)) ||
                        (empty($promotional) && !empty($promotionalArabic))) {
                        $fail(__('messages.promotional_message_both_or_none'));
                    }
                },
            ],
            'discount_percentage' => 'required|numeric|min:1|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:20480', // 20MB = 20480KB
            'status' => 'required|in:active,inactive',
            'service_ids' => 'required|array|min:1',
            'service_ids.*' => 'exists:services,id',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        // Validate that all selected services belong to this service provider
        $selectedServices = $request->input('service_ids', []);
        $allowedServiceIds = $serviceProvider->service_ids ?? [];
        $allowedBranchIds = $serviceProvider->branch_ids ?? [];

        foreach ($selectedServices as $serviceId) {
            if (!in_array($serviceId, $allowedServiceIds)) {
                return back()->withErrors(['service_ids' => 'You can only create deals for services you manage.']);
            }
        }

        $selectedBranchId = $request->input('branch_id');
        if ($selectedBranchId && !in_array($selectedBranchId, $allowedBranchIds)) {
            return back()->withErrors(['branch_id' => 'You can only select branches you manage.'])->withInput();
        }

        // Validate that selected services don't have active deals (excluding current deal)
        if (!empty($selectedServices)) {
            $conflictingServices = Deal::getConflictingServiceIds($selectedServices, $deal->id, $user->id);
            if (!empty($conflictingServices)) {
                $serviceNames = Service::whereIn('id', $conflictingServices)->pluck('name')->toArray();
                return back()->withErrors([
                    'service_ids' => 'The following services already have active deals: ' . implode(', ', $serviceNames)
                ])->withInput();
            }
        }

        $dealData = [
            'title' => $request->input('title'),
            'title_arabic' => $request->input('title_arabic'),
            'description' => $request->input('description'),
            'description_arabic' => $request->input('description_arabic'),
            'promotional_message' => $request->input('promotional_message'),
            'promotional_message_arabic' => $request->input('promotional_message_arabic'),
            'discount_percentage' => $request->input('discount_percentage'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'status' => $request->input('status'),
            'service_ids' => $selectedServices,
            'branch_id' => $request->input('branch_id'),
        ];
        $dealData = $this->applyBranchSelection($dealData, $allowedBranchIds);

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
                        Log::warning('Failed to delete old service provider deal image: ' . $e->getMessage(), [
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

                    Log::warning('WebP conversion failed for service provider deal update, using fallback method', [
                        'original_name' => $file->getClientOriginalName(),
                        'fallback_path' => $imagePath
                    ]);
                }

                if (!$imagePath) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'Failed to upload image. Please try again.']);
                }

                $dealData['image'] = $imagePath;

            } catch (\Exception $e) {
                Log::error('Error processing service provider deal image update', [
                    'error' => $e->getMessage(),
                    'file' => $file->getClientOriginalName(),
                    'deal_id' => $deal->id
                ]);

                return redirect()->back()
                    ->withInput()
                    ->withErrors(['image' => 'Failed to process image. Please try again with a different image.']);
            }
        }

        try {
            $deal->update($dealData);
            return redirect()->route('service-provider.deals.index')
                ->with('success', 'Deal updated successfully.');
        } catch (\Exception $e) {
            Log::error('Deal update failed: ' . $e->getMessage());
            return back()->withErrors(['general' => 'Failed to update deal. Please try again.']);
        }
    }

    /**
     * Remove the specified deal.
     */
    public function destroy(Deal $deal)
    {
        $user = Auth::user();
        $serviceProvider = $user->serviceProvider;

        if (!$serviceProvider) {
            return redirect('/')->with('error', 'Service provider profile not found.');
        }

        // Check if this deal belongs to the current user and applies to services
        if ($deal->user_id !== $user->id || $deal->applies_to !== 'services') {
            return redirect()->route('service-provider.deals.index')
                ->with('error', 'Deal not found or access denied.');
        }

        try {
            // Delete image if exists
            $imagePath = $deal->getRawOriginalImage();
            if ($imagePath && !empty(trim($imagePath))) {
                try {
                    $webpService = new WebPImageService();
                    $webpService->deleteImage($imagePath);
                } catch (\Exception $e) {
                    // Log the error but don't stop the deletion process
                    Log::warning('Failed to delete service provider deal image: ' . $e->getMessage(), [
                        'deal_id' => $deal->id,
                        'image_path' => $imagePath
                    ]);
                }
            }

            $deal->delete();
            return redirect()->route('service-provider.deals.index')
                ->with('success', 'Deal deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Deal deletion failed: ' . $e->getMessage());
            return redirect()->route('service-provider.deals.index')
                ->with('error', 'Failed to delete deal. Please try again.');
        }
    }

    private function applyBranchSelection(array $dealData, array $allowedBranchIds): array
    {
        if (empty($dealData['branch_id'])) {
            $dealData['branch_id'] = null;
            $dealData['business_type_id'] = null;
            return $dealData;
        }

        if (!in_array($dealData['branch_id'], $allowedBranchIds)) {
            abort(403, 'Invalid branch selection.');
        }

        $branch = Branch::whereIn('id', $allowedBranchIds)
            ->where('id', $dealData['branch_id'])
            ->first();

        if (!$branch) {
            abort(403, 'Invalid branch selection.');
        }

        $dealData['branch_id'] = $branch->id;
        $dealData['business_type_id'] = BusinessType::where('business_name', $branch->business_type)->value('id');

        return $dealData;
    }
}
