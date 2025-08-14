<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\ServiceProvider;
use App\Models\User;
use App\Models\Branch;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ServiceProviderController extends Controller
{
    /**
     * Display a listing of service providers for the current vendor.
     */
    public function index()
    {
        $user = Auth::user();
        $company = $user->company;

        if (!$company) {
            return redirect()->route('vendor.settings')
                ->with('error', 'Company profile not found. Please complete your company setup first.');
        }

        $serviceProviders = ServiceProvider::with(['user', 'company'])
            ->where('company_id', $company->id)
            ->paginate(10);

        // Get branches for the company (for the create form)
        $branches = Branch::where('company_id', $company->id)->get();
        
        // Get services for the company (for the create form)
        $services = Service::whereHas('branch', function ($query) use ($company) {
            $query->where('company_id', $company->id);
        })->get();

        return view('vendor.settings.service-providers.index', compact('serviceProviders', 'branches', 'services'));
    }

    /**
     * Show the form for creating a new service provider.
     */
    public function create()
    {
        $user = Auth::user();
        $company = $user->company;

        if (!$company) {
            return redirect()->route('vendor.settings')
                ->with('error', 'Company profile not found. Please complete your company setup first.');
        }

        $branches = Branch::where('company_id', $company->id)->get();
        $services = Service::whereHas('branch', function ($query) use ($company) {
            $query->where('company_id', $company->id);
        })->get();

        return view('vendor.settings.service-providers.create', compact('branches', 'services'));
    }

    /**
     * Store a newly created service provider in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        if (!$company) {
            return redirect()->route('vendor.settings')
                ->with('error', 'Company profile not found. Please complete your company setup first.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
            'service_ids' => 'nullable|array',
            'service_ids.*' => 'exists:services,id',
        ]);

        // Validate that selected branches belong to the vendor's company
        if ($request->branch_ids) {
            $validBranches = Branch::where('company_id', $company->id)
                ->whereIn('id', $request->branch_ids)
                ->count();
            
            if ($validBranches !== count($request->branch_ids)) {
                return redirect()->back()
                    ->withErrors(['branch_ids' => 'Some selected branches do not belong to your company.'])
                    ->withInput();
            }
        }

        // Validate that selected services belong to the vendor's company branches
        if ($request->service_ids) {
            $validServices = Service::whereHas('branch', function ($query) use ($company) {
                $query->where('company_id', $company->id);
            })->whereIn('id', $request->service_ids)->count();
            
            if ($validServices !== count($request->service_ids)) {
                return redirect()->back()
                    ->withErrors(['service_ids' => 'Some selected services do not belong to your company.'])
                    ->withInput();
            }
        }

        DB::beginTransaction();
        try {
            // Create user account
            $serviceProviderUser = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => 'service_provider',
                'status' => 'active',
                'email_verified_at' => now(), // Auto-verify since created by vendor
            ]);

            // Create service provider profile
            $serviceProvider = ServiceProvider::create([
                'user_id' => $serviceProviderUser->id,
                'company_id' => $company->id,
                'branch_ids' => $request->branch_ids ?? [],
                'service_ids' => $request->service_ids ?? [],
                'number_of_services' => count($request->service_ids ?? []),
            ]);

            DB::commit();

            return redirect()->route('vendor.settings.service-providers.index')
                ->with('success', 'Service Provider created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to create Service Provider. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified service provider.
     */
    public function show(ServiceProvider $serviceProvider)
    {
        $user = Auth::user();
        $company = $user->company;

        // Ensure the service provider belongs to the current vendor's company
        if ($serviceProvider->company_id !== $company->id) {
            abort(403, 'Unauthorized access to this service provider.');
        }

        $serviceProvider->load(['user', 'company']);
        
        // Get the actual branches and services
        $branches = $serviceProvider->branches();
        $services = $serviceProvider->services();

        return view('vendor.settings.service-providers.show', compact('serviceProvider', 'branches', 'services'));
    }

    /**
     * Show the form for editing the specified service provider.
     */
    public function edit(ServiceProvider $serviceProvider)
    {
        $user = Auth::user();
        $company = $user->company;

        // Ensure the service provider belongs to the current vendor's company
        if ($serviceProvider->company_id !== $company->id) {
            abort(403, 'Unauthorized access to this service provider.');
        }

        $serviceProvider->load('user');
        
        $branches = Branch::where('company_id', $company->id)->get();
        $services = Service::whereHas('branch', function ($query) use ($company) {
            $query->where('company_id', $company->id);
        })->get();

        return view('vendor.settings.service-providers.edit', compact('serviceProvider', 'branches', 'services'));
    }

    /**
     * Update the specified service provider in storage.
     */
    public function update(Request $request, ServiceProvider $serviceProvider)
    {
        $user = Auth::user();
        $company = $user->company;

        // Ensure the service provider belongs to the current vendor's company
        if ($serviceProvider->company_id !== $company->id) {
            abort(403, 'Unauthorized access to this service provider.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($serviceProvider->user_id)],
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
            'service_ids' => 'nullable|array',
            'service_ids.*' => 'exists:services,id',
            'status' => 'required|in:active,inactive',
        ]);

        // Validate that selected branches belong to the vendor's company
        if ($request->branch_ids) {
            $validBranches = Branch::where('company_id', $company->id)
                ->whereIn('id', $request->branch_ids)
                ->count();
            
            if ($validBranches !== count($request->branch_ids)) {
                return redirect()->back()
                    ->withErrors(['branch_ids' => 'Some selected branches do not belong to your company.'])
                    ->withInput();
            }
        }

        // Validate that selected services belong to the vendor's company branches
        if ($request->service_ids) {
            $validServices = Service::whereHas('branch', function ($query) use ($company) {
                $query->where('company_id', $company->id);
            })->whereIn('id', $request->service_ids)->count();
            
            if ($validServices !== count($request->service_ids)) {
                return redirect()->back()
                    ->withErrors(['service_ids' => 'Some selected services do not belong to your company.'])
                    ->withInput();
            }
        }

        DB::beginTransaction();
        try {
            // Update user account
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => $request->status,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $serviceProvider->user->update($userData);

            // Update service provider profile
            $serviceProvider->update([
                'branch_ids' => $request->branch_ids ?? [],
                'service_ids' => $request->service_ids ?? [],
                'number_of_services' => count($request->service_ids ?? []),
            ]);

            DB::commit();

            return redirect()->route('vendor.settings.service-providers.index')
                ->with('success', 'Service Provider updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to update Service Provider. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified service provider from storage.
     */
    public function destroy(ServiceProvider $serviceProvider)
    {
        $user = Auth::user();
        $company = $user->company;

        // Ensure the service provider belongs to the current vendor's company
        if ($serviceProvider->company_id !== $company->id) {
            abort(403, 'Unauthorized access to this service provider.');
        }

        DB::beginTransaction();
        try {
            $serviceProviderName = $serviceProvider->user->name;
            
            // Delete the user account (this will cascade delete the service provider due to foreign key constraints)
            $serviceProvider->user->delete();

            DB::commit();

            return redirect()->route('vendor.settings.service-providers.index')
                ->with('success', "Service Provider '{$serviceProviderName}' has been deleted successfully.");

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to delete Service Provider. Please try again.');
        }
    }

    /**
     * Toggle the status of the specified service provider.
     */
    public function toggleStatus(ServiceProvider $serviceProvider)
    {
        $user = Auth::user();
        $company = $user->company;

        // Ensure the service provider belongs to the current vendor's company
        if ($serviceProvider->company_id !== $company->id) {
            abort(403, 'Unauthorized access to this service provider.');
        }

        $newStatus = $serviceProvider->user->status === 'active' ? 'inactive' : 'active';
        $serviceProvider->user->update(['status' => $newStatus]);

        $statusText = $newStatus === 'active' ? 'activated' : 'suspended';

        return redirect()->back()
            ->with('success', "Service Provider '{$serviceProvider->user->name}' has been {$statusText} successfully.");
    }
}
