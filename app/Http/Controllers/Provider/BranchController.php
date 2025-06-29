<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BranchController extends Controller
{
    /**
     * Display a listing of the branches.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $branches = Auth::user()->branches;
        return view('provider.branches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new branch.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('provider.branches.create');
    }

    /**
     * Store a newly created branch in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string',
            'branch_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'use_company_image' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['status'] = 'active';

        // Handle branch image upload
        if ($request->hasFile('branch_image')) {
            $imagePath = $request->file('branch_image')->store('branches', 'public');
            $data['branch_image'] = $imagePath;
            $data['use_company_image'] = false;
        }

        // Set use_company_image flag
        if ($request->has('use_company_image')) {
            $data['use_company_image'] = $request->boolean('use_company_image');
            // If using company image, clear branch image
            if ($data['use_company_image']) {
                $data['branch_image'] = null;
            }
        }

        Branch::create($data);

        return redirect()->route('provider.branches.index')
            ->with('success', 'Branch created successfully');
    }

    /**
     * Display the specified branch.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $branch = Branch::where('user_id', Auth::id())->findOrFail($id);
        return view('provider.branches.show', compact('branch'));
    }

    /**
     * Show the form for editing the specified branch.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $branch = Branch::where('user_id', Auth::id())->findOrFail($id);
        return view('provider.branches.edit', compact('branch'));
    }

    /**
     * Update the specified branch in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $branch = Branch::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string',
            'branch_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'use_company_image' => 'nullable|boolean',
        ]);

        $data = $request->all();

        // Handle branch image upload
        if ($request->hasFile('branch_image')) {
            // Delete old image if exists
            if ($branch->branch_image) {
                Storage::disk('public')->delete($branch->branch_image);
            }

            $imagePath = $request->file('branch_image')->store('branches', 'public');
            $data['branch_image'] = $imagePath;
            $data['use_company_image'] = false;
        }

        // Set use_company_image flag
        if ($request->has('use_company_image')) {
            $data['use_company_image'] = $request->boolean('use_company_image');
            // If using company image, clear branch image
            if ($data['use_company_image'] && $branch->branch_image) {
                Storage::disk('public')->delete($branch->branch_image);
                $data['branch_image'] = null;
            }
        }

        $branch->update($data);

        return redirect()->route('provider.branches.index')
            ->with('success', 'Branch updated successfully');
    }

    /**
     * Remove the specified branch from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $branch = Branch::where('user_id', Auth::id())->findOrFail($id);
        $branch->delete();

        return redirect()->route('provider.branches.index')
            ->with('success', 'Branch deleted successfully');
    }

    /**
     * Create a default branch for the provider if they don't have one.
     *
     * @param  \App\Models\User  $user
     * @return \App\Models\Branch|null
     */
    public static function createDefaultBranch($user)
    {
        if ($user->branches()->count() === 0) {
            // Create a default branch for the provider
            $provider = $user->providerRecord;
            $businessName = $provider ? $provider->business_name : 'Default Branch';

            return Branch::create([
                'user_id' => $user->id,
                'name' => $businessName,
                'address' => 'Default Address',
                'lat' => 0,
                'lng' => 0,
                'status' => 'active',
                'description' => 'Default branch created automatically',
            ]);
        }

        return null;
    }
}
