<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\ProductsManager;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductsManagerController extends Controller
{
    /**
     * Display a listing of products managers for the current vendor.
     */
    public function index()
    {
        $user = Auth::user();
        $company = $user->company;

        if (!$company) {
            return redirect()->route('vendor.settings')
                ->with('error', 'Company profile not found. Please complete your company setup first.');
        }

        $productsManagers = ProductsManager::with(['user', 'company'])
            ->where('company_id', $company->id)
            ->paginate(10);

        return view('vendor.settings.products-managers.index', compact('productsManagers'));
    }

    /**
     * Show the form for creating a new products manager.
     */
    public function create()
    {
        $user = Auth::user();
        $company = $user->company;

        if (!$company) {
            return redirect()->route('vendor.settings')
                ->with('error', 'Company profile not found. Please complete your company setup first.');
        }

        return view('vendor.settings.products-managers.create');
    }

    /**
     * Store a newly created products manager in storage.
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
        ]);

        DB::beginTransaction();
        try {
            // Create user account
            $productsManagerUser = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => 'products_manager',
                'status' => 'active',
                'email_verified_at' => now(), // Auto-verify since created by vendor
            ]);

            // Create products manager profile
            $productsManager = ProductsManager::create([
                'user_id' => $productsManagerUser->id,
                'company_id' => $company->id,
            ]);

            DB::commit();

            return redirect()->route('vendor.settings.products-managers.index')
                ->with('success', 'Products Manager created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to create Products Manager. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified products manager.
     */
    public function show(ProductsManager $productsManager)
    {
        $user = Auth::user();
        $company = $user->company;

        // Ensure the products manager belongs to the current vendor's company
        if ($productsManager->company_id !== $company->id) {
            abort(403, 'Unauthorized access to this products manager.');
        }

        $productsManager->load(['user', 'company']);
        
        // Get statistics for this products manager
        $statistics = $productsManager->getStatistics();

        return view('vendor.settings.products-managers.show', compact('productsManager', 'statistics'));
    }

    /**
     * Show the form for editing the specified products manager.
     */
    public function edit(ProductsManager $productsManager)
    {
        $user = Auth::user();
        $company = $user->company;

        // Ensure the products manager belongs to the current vendor's company
        if ($productsManager->company_id !== $company->id) {
            abort(403, 'Unauthorized access to this products manager.');
        }

        $productsManager->load('user');

        return view('vendor.settings.products-managers.edit', compact('productsManager'));
    }

    /**
     * Update the specified products manager in storage.
     */
    public function update(Request $request, ProductsManager $productsManager)
    {
        $user = Auth::user();
        $company = $user->company;

        // Ensure the products manager belongs to the current vendor's company
        if ($productsManager->company_id !== $company->id) {
            abort(403, 'Unauthorized access to this products manager.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($productsManager->user_id)],
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

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

            $productsManager->user->update($userData);

            DB::commit();

            return redirect()->route('vendor.settings.products-managers.index')
                ->with('success', 'Products Manager updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to update Products Manager. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified products manager from storage.
     */
    public function destroy(ProductsManager $productsManager)
    {
        $user = Auth::user();
        $company = $user->company;

        // Ensure the products manager belongs to the current vendor's company
        if ($productsManager->company_id !== $company->id) {
            abort(403, 'Unauthorized access to this products manager.');
        }

        DB::beginTransaction();
        try {
            $productsManagerName = $productsManager->user->name;
            
            // Delete the user account (this will cascade delete the products manager due to foreign key constraints)
            $productsManager->user->delete();

            DB::commit();

            return redirect()->route('vendor.settings.products-managers.index')
                ->with('success', "Products Manager '{$productsManagerName}' has been deleted successfully.");

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to delete Products Manager. Please try again.');
        }
    }

    /**
     * Toggle the status of the specified products manager.
     */
    public function toggleStatus(ProductsManager $productsManager)
    {
        $user = Auth::user();
        $company = $user->company;

        // Ensure the products manager belongs to the current vendor's company
        if ($productsManager->company_id !== $company->id) {
            abort(403, 'Unauthorized access to this products manager.');
        }

        $newStatus = $productsManager->user->status === 'active' ? 'inactive' : 'active';
        $productsManager->user->update(['status' => $newStatus]);

        $statusText = $newStatus === 'active' ? 'activated' : 'suspended';

        return redirect()->back()
            ->with('success', "Products Manager '{$productsManager->user->name}' has been {$statusText} successfully.");
    }

    /**
     * Get products manager statistics for AJAX requests.
     */
    public function getStatistics(ProductsManager $productsManager)
    {
        $user = Auth::user();
        $company = $user->company;

        // Ensure the products manager belongs to the current vendor's company
        if ($productsManager->company_id !== $company->id) {
            abort(403, 'Unauthorized access to this products manager.');
        }

        $statistics = $productsManager->getStatistics();

        return response()->json($statistics);
    }
}
