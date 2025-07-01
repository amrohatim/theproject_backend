<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    /**
     * Display a listing of the merchant's services.
     */
    public function index()
    {
        $user = Auth::user();

        // Get services from user's branches
        $userBranches = \App\Models\Branch::where('user_id', $user->id)->pluck('id');
        $services = Service::whereIn('branch_id', $userBranches)
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('merchant.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('merchant.services.create', compact('categories'));
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'duration' => 'nullable|integer|min:1',
            'is_available' => 'boolean',
        ], [
            'image.image' => 'The uploaded file must be an image.',
            'image.mimes' => 'The image must be a file of type: JPEG, PNG, JPG, GIF.',
            'image.max' => 'The image size must not exceed 2MB.',
            'name.required' => 'Service name is required.',
            'description.required' => 'Service description is required.',
            'price.required' => 'Service price is required.',
            'price.numeric' => 'Service price must be a valid number.',
            'price.min' => 'Service price must be greater than or equal to 0.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'The selected category is invalid.',
            'duration.min' => 'Duration must be at least 1 minute.',
        ]);

        $user = Auth::user();

        // Get user's first branch (or create one if none exists)
        $branch = \App\Models\Branch::where('user_id', $user->id)->first();
        if (!$branch) {
            $branch = \App\Models\Branch::create([
                'user_id' => $user->id,
                'name' => 'Main Store',
                'address' => 'Main Location',
                'emirate' => 'Dubai',
                'status' => 'active',
            ]);
        }

        $data = $request->all();
        $data['branch_id'] = $branch->id;

        // Handle image upload with enhanced validation and error handling
        if ($request->hasFile('image')) {
            try {
                $image = $request->file('image');

                // Additional server-side validation
                if (!$image->isValid()) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'The uploaded image file is corrupted or invalid.']);
                }

                // Validate file size (additional check)
                if ($image->getSize() > 2048 * 1024) { // 2MB in bytes
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'The image size must not exceed 2MB.']);
                }

                // Validate MIME type (additional check)
                $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                if (!in_array($image->getMimeType(), $allowedMimes)) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'The image must be a file of type: JPEG, PNG, JPG, GIF.']);
                }

                // Generate unique filename
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();

                // Store the image
                $imagePath = $image->storeAs('services', $imageName, 'public');

                if (!$imagePath) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'Failed to upload image. Please try again.']);
                }

                $data['image'] = $imagePath;

            } catch (\Exception $e) {
                \Log::error('Service image upload failed: ' . $e->getMessage());
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['image' => 'Failed to upload image. Please try again.']);
            }
        }

        try {
            Service::create($data);
            return redirect()->route('merchant.services.index')
                ->with('success', 'Service created successfully.');
        } catch (\Exception $e) {
            \Log::error('Service creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['general' => 'Failed to create service. Please try again.']);
        }
    }

    /**
     * Display the specified service.
     */
    public function show($id)
    {
        $user = Auth::user();
        $userBranches = \App\Models\Branch::where('user_id', $user->id)->pluck('id');

        $service = Service::whereIn('branch_id', $userBranches)
            ->with('category')
            ->findOrFail($id);

        return view('merchant.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $userBranches = \App\Models\Branch::where('user_id', $user->id)->pluck('id');

        $service = Service::whereIn('branch_id', $userBranches)->findOrFail($id);
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('merchant.services.edit', compact('service', 'categories'));
    }

    /**
     * Update the specified service in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $userBranches = \App\Models\Branch::where('user_id', $user->id)->pluck('id');

        $service = Service::whereIn('branch_id', $userBranches)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'duration' => 'nullable|integer|min:1',
            'duration_unit' => 'nullable|in:minutes,hours,days',
            'status' => 'in:active,inactive',
        ], [
            'image.image' => 'The uploaded file must be an image.',
            'image.mimes' => 'The image must be a file of type: JPEG, PNG, JPG, GIF.',
            'image.max' => 'The image size must not exceed 2MB.',
            'name.required' => 'Service name is required.',
            'description.required' => 'Service description is required.',
            'price.required' => 'Service price is required.',
            'price.numeric' => 'Service price must be a valid number.',
            'price.min' => 'Service price must be greater than or equal to 0.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'The selected category is invalid.',
            'duration.min' => 'Duration must be at least 1 minute.',
        ]);

        $data = $request->all();

        // Handle checkbox fields properly - checkboxes don't send values when unchecked
        $data['is_available'] = $request->has('is_available') ? true : false;
        $data['home_service'] = $request->has('home_service') ? true : false;

        // Handle image upload with enhanced validation and error handling
        if ($request->hasFile('image')) {
            try {
                $image = $request->file('image');

                // Additional server-side validation
                if (!$image->isValid()) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'The uploaded image file is corrupted or invalid.']);
                }

                // Validate file size (additional check)
                if ($image->getSize() > 2048 * 1024) { // 2MB in bytes
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'The image size must not exceed 2MB.']);
                }

                // Validate MIME type (additional check)
                $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                if (!in_array($image->getMimeType(), $allowedMimes)) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'The image must be a file of type: JPEG, PNG, JPG, GIF.']);
                }

                // Store old image path for cleanup
                $oldImagePath = $service->getRawImagePath();

                // Generate unique filename
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();

                // Store the new image
                $imagePath = $image->storeAs('services', $imageName, 'public');

                if (!$imagePath) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'Failed to upload image. Please try again.']);
                }

                $data['image'] = $imagePath;

                // Delete old image only after successful upload
                if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }

            } catch (\Exception $e) {
                \Log::error('Service image upload failed: ' . $e->getMessage());
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['image' => 'Failed to upload image. Please try again.']);
            }
        }

        try {
            $service->update($data);
            return redirect()->route('merchant.services.index')
                ->with('success', 'Service updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Service update failed: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['general' => 'Failed to update service. Please try again.']);
        }
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $userBranches = \App\Models\Branch::where('user_id', $user->id)->pluck('id');

        $service = Service::whereIn('branch_id', $userBranches)->findOrFail($id);

        // Image cleanup is handled automatically by the Service model's booted method
        $service->delete();

        return redirect()->route('merchant.services.index')
            ->with('success', 'Service deleted successfully.');
    }
}
