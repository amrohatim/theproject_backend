<?php

namespace App\Http\Controllers\ServiceProvider;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    /**
     * Display a listing of services that the service provider can manage.
     */
    public function index()
    {
        $user = Auth::user();
        $serviceProvider = $user->serviceProvider;

        if (!$serviceProvider) {
            return redirect('/')->with('error', 'Service provider profile not found.');
        }

        // Get services that this service provider can manage
        $services = Service::whereIn('id', $serviceProvider->service_ids ?? [])
            ->with(['branch'])
            ->paginate(10);

        // Get branches this service provider can access
        $branches = Branch::whereIn('id', $serviceProvider->branch_ids ?? [])->get();

        return view('service-provider.services.index', compact('services', 'branches', 'serviceProvider'));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create()
    {
        $user = Auth::user();
        $serviceProvider = $user->serviceProvider;

        if (!$serviceProvider) {
            return redirect('/')->with('error', 'Service provider profile not found.');
        }

        // Get branches this service provider can access
        $branches = Branch::whereIn('id', $serviceProvider->branch_ids ?? [])->get();

        return view('service-provider.services.create', compact('branches', 'serviceProvider'));
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $serviceProvider = $user->serviceProvider;

        if (!$serviceProvider) {
            return redirect('/')->with('error', 'Service provider profile not found.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'branch_id' => 'required|exists:branches,id',
            'category' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Verify the branch belongs to the service provider's accessible branches
        if (!in_array($request->branch_id, $serviceProvider->branch_ids ?? [])) {
            return redirect()->back()
                ->withErrors(['branch_id' => 'You do not have access to this branch.'])
                ->withInput();
        }

        $serviceData = $request->except('image');
        $serviceData['status'] = 'active';

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/services'), $imageName);
            $serviceData['image'] = 'images/services/' . $imageName;
        }

        $service = Service::create($serviceData);

        // Add this service to the service provider's service list
        $serviceIds = $serviceProvider->service_ids ?? [];
        if (!in_array($service->id, $serviceIds)) {
            $serviceIds[] = $service->id;
            $serviceProvider->update([
                'service_ids' => $serviceIds,
                'number_of_services' => count($serviceIds),
            ]);
        }

        return redirect()->route('service-provider.services.index')
            ->with('success', 'Service created successfully.');
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service)
    {
        $user = Auth::user();
        $serviceProvider = $user->serviceProvider;

        if (!$serviceProvider) {
            return redirect('/')->with('error', 'Service provider profile not found.');
        }

        // Check if the service provider can access this service
        if (!in_array($service->id, $serviceProvider->service_ids ?? [])) {
            abort(403, 'You do not have access to this service.');
        }

        $service->load(['branch', 'bookings' => function ($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }]);

        return view('service-provider.services.show', compact('service', 'serviceProvider'));
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(Service $service)
    {
        $user = Auth::user();
        $serviceProvider = $user->serviceProvider;

        if (!$serviceProvider) {
            return redirect('/')->with('error', 'Service provider profile not found.');
        }

        // Check if the service provider can access this service
        if (!in_array($service->id, $serviceProvider->service_ids ?? [])) {
            abort(403, 'You do not have access to this service.');
        }

        // Get branches this service provider can access
        $branches = Branch::whereIn('id', $serviceProvider->branch_ids ?? [])->get();

        return view('service-provider.services.edit', compact('service', 'branches', 'serviceProvider'));
    }

    /**
     * Update the specified service in storage.
     */
    public function update(Request $request, Service $service)
    {
        $user = Auth::user();
        $serviceProvider = $user->serviceProvider;

        if (!$serviceProvider) {
            return redirect('/')->with('error', 'Service provider profile not found.');
        }

        // Check if the service provider can access this service
        if (!in_array($service->id, $serviceProvider->service_ids ?? [])) {
            abort(403, 'You do not have access to this service.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'branch_id' => 'required|exists:branches,id',
            'category' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Verify the branch belongs to the service provider's accessible branches
        if (!in_array($request->branch_id, $serviceProvider->branch_ids ?? [])) {
            return redirect()->back()
                ->withErrors(['branch_id' => 'You do not have access to this branch.'])
                ->withInput();
        }

        $serviceData = $request->except('image');

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($service->image && file_exists(public_path($service->image))) {
                unlink(public_path($service->image));
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/services'), $imageName);
            $serviceData['image'] = 'images/services/' . $imageName;
        }

        $service->update($serviceData);

        return redirect()->route('service-provider.services.index')
            ->with('success', 'Service updated successfully.');
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy(Service $service)
    {
        $user = Auth::user();
        $serviceProvider = $user->serviceProvider;

        if (!$serviceProvider) {
            return redirect('/')->with('error', 'Service provider profile not found.');
        }

        // Check if the service provider can access this service
        if (!in_array($service->id, $serviceProvider->service_ids ?? [])) {
            abort(403, 'You do not have access to this service.');
        }

        // Remove image if exists
        if ($service->image && file_exists(public_path($service->image))) {
            unlink(public_path($service->image));
        }

        // Remove service from service provider's list
        $serviceIds = $serviceProvider->service_ids ?? [];
        $serviceIds = array_filter($serviceIds, function ($id) use ($service) {
            return $id != $service->id;
        });

        $serviceProvider->update([
            'service_ids' => array_values($serviceIds),
            'number_of_services' => count($serviceIds),
        ]);

        $service->delete();

        return redirect()->route('service-provider.services.index')
            ->with('success', 'Service deleted successfully.');
    }
}
