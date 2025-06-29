<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProviderController extends Controller
{
    /**
     * Display a listing of providers with filtering.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Provider::with('user');

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                  ->orWhere('business_type', 'like', "%{$search}%")
                  ->orWhere('registration_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply verified filter
        if ($request->filled('verified')) {
            if ($request->verified === '1') {
                $query->where('is_verified', true);
            } elseif ($request->verified === '0') {
                $query->where('is_verified', false);
            }
        }

        $providers = $query->orderBy('created_at', 'desc')->paginate(10);

        // Preserve query parameters in pagination
        $providers->appends($request->query());

        return view('admin.providers.index', compact('providers'));
    }

    /**
     * Show the form for creating a new provider.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.providers.create');
    }

    /**
     * Store a newly created provider in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the user data
        $userValidated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);

        // Validate the provider data
        $providerValidated = $request->validate([
            'business_name' => 'required|string|max:255',
            'business_type' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'status' => 'required|string|in:active,inactive,pending',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle the provider logo upload
        if ($request->hasFile('logo')) {
            // Get the file and optimize it
            $logo = $request->file('logo');
            
            // Create a filename with timestamp to avoid duplicates
            $filename = time() . '_' . $logo->getClientOriginalName();
            
            // Store with a specific path and filename to better control file storage
            $logoPath = $logo->storeAs('providers', $filename, 'public');
            $providerValidated['logo'] = Storage::url($logoPath);
        }

        // Begin transaction
        DB::beginTransaction();
        
        try {
            // Create the user with provider role
            $userValidated['password'] = Hash::make($userValidated['password']);
            $userValidated['role'] = 'provider';
            $userValidated['status'] = 'active';
            $user = User::create($userValidated);

            // Create the provider profile
            $providerValidated['user_id'] = $user->id;
            Provider::create($providerValidated);
            
            DB::commit();
            
            return redirect()->route('admin.providers.index')
                ->with('success', 'Provider created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            
            // If there was a logo upload, clean it up
            if (isset($logoPath)) {
                Storage::delete('public/' . str_replace('/storage/', '', $logoPath));
            }
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create provider: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified provider.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $provider = Provider::with('user', 'products')->findOrFail($id);

        // Check if provider has an associated user for display purposes
        if (!$provider->user) {
            return redirect()->route('admin.providers.index')
                ->withErrors(['error' => 'Provider does not have an associated user account.']);
        }

        return view('admin.providers.show', compact('provider'));
    }

    /**
     * Show the form for editing the specified provider.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $provider = Provider::with('user')->findOrFail($id);

        // Check if provider has an associated user
        if (!$provider->user) {
            return redirect()->route('admin.providers.index')
                ->withErrors(['error' => 'Provider does not have an associated user account and cannot be edited.']);
        }

        return view('admin.providers.edit', compact('provider'));
    }

    /**
     * Update the specified provider in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $provider = Provider::findOrFail($id);
        $user = $provider->user;

        // Check if provider has an associated user
        if (!$user) {
            return redirect()->route('admin.providers.index')
                ->withErrors(['error' => 'Provider does not have an associated user account.']);
        }

        // Validate the user data
        $userValidated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);

        // Validate the provider data
        $providerValidated = $request->validate([
            'business_name' => 'required|string|max:255',
            'business_type' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'status' => 'required|string|in:active,inactive,pending',
            'is_verified' => 'sometimes|boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle the provider logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($provider->logo && Storage::exists('public/' . str_replace('/storage/', '', $provider->logo))) {
                Storage::delete('public/' . str_replace('/storage/', '', $provider->logo));
            }

            $logoPath = $request->file('logo')->store('providers', 'public');
            $providerValidated['logo'] = Storage::url($logoPath);
        }

        // Handle password update
        if (!empty($userValidated['password'])) {
            $userValidated['password'] = Hash::make($userValidated['password']);
        } else {
            unset($userValidated['password']);
        }

        // Begin transaction
        DB::beginTransaction();
        
        try {
            // Update user
            $user->update($userValidated);
            
            // Update provider
            $provider->update($providerValidated);
            
            DB::commit();
            
            return redirect()->route('admin.providers.index')
                ->with('success', 'Provider updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            
            // If there was a logo upload, clean it up
            if (isset($logoPath)) {
                Storage::delete('public/' . str_replace('/storage/', '', $logoPath));
            }
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update provider: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified provider from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $provider = Provider::findOrFail($id);
        $user = $provider->user;

        // Begin transaction
        DB::beginTransaction();

        try {
            // Delete provider (which will cascade to products)
            $provider->delete();

            // Delete the user if it exists
            if ($user) {
                $user->delete();
            }

            DB::commit();

            return redirect()->route('admin.providers.index')
                ->with('success', 'Provider deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('admin.providers.index')
                ->withErrors(['error' => 'Failed to delete provider: ' . $e->getMessage()]);
        }
    }
}
