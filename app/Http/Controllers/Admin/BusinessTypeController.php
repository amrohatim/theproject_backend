<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BusinessTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BusinessType::query();

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('business_name', 'like', "%{$search}%");
        }

        // Order by name
        $query->orderBy('business_name');

        $businessTypes = $query->paginate(15);

        return view('admin.business-types.index', compact('businessTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.business-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255|unique:business_types,business_name',
        ]);

        BusinessType::create($validated);

        return redirect()->route('admin.business-types.index')
            ->with('success', 'Business type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BusinessType $businessType)
    {
        return view('admin.business-types.show', compact('businessType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BusinessType $businessType)
    {
        return view('admin.business-types.edit', compact('businessType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BusinessType $businessType)
    {
        $validated = $request->validate([
            'business_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('business_types', 'business_name')->ignore($businessType->id),
            ],
        ]);

        $businessType->update($validated);

        return redirect()->route('admin.business-types.index')
            ->with('success', 'Business type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BusinessType $businessType)
    {
        try {
            $businessType->delete();
            return redirect()->route('admin.business-types.index')
                ->with('success', 'Business type deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.business-types.index')
                ->with('error', 'Error deleting business type. It may be in use.');
        }
    }
}
