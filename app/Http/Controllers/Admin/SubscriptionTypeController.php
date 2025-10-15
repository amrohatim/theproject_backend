<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubscriptionTypeController extends Controller
{
    /**
     * Display a listing of subscription types.
     */
    public function index(Request $request)
    {
        $query = SubscriptionType::query();

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('period', 'like', "%{$search}%");
            });
        }

        // Apply type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Apply period filter
        if ($request->filled('period')) {
            $query->where('period', $request->period);
        }

        // Order by type and period
        $query->orderBy('type')->orderBy('period');

        $subscriptionTypes = $query->paginate(15);

        return view('admin.subscription-types.index', compact('subscriptionTypes'));
    }

    /**
     * Show the form for creating a new subscription type.
     */
    public function create()
    {
        return view('admin.subscription-types.create');
    }

    /**
     * Store a newly created subscription type in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:vendor,merchant,provider',
            'period' => 'required|string|in:monthly,yearly',
            'charge' => 'required|numeric|min:0|max:999999.99',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'alert_message' => 'nullable|string|max:255',
        ]);

        // Check if combination of type and period already exists
        $exists = SubscriptionType::where('type', $validated['type'])
            ->where('period', $validated['period'])
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'A subscription plan for this user type and period already exists. Please edit the existing plan instead.');
        }

        SubscriptionType::create($validated);

        return redirect()->route('admin.subscription-types.index')
            ->with('success', 'Subscription type created successfully.');
    }

    /**
     * Display the specified subscription type.
     */
    public function show(SubscriptionType $subscriptionType)
    {
        return view('admin.subscription-types.show', compact('subscriptionType'));
    }

    /**
     * Show the form for editing the specified subscription type.
     */
    public function edit(SubscriptionType $subscriptionType)
    {
        return view('admin.subscription-types.edit', compact('subscriptionType'));
    }

    /**
     * Update the specified subscription type in storage.
     */
    public function update(Request $request, SubscriptionType $subscriptionType)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:vendor,merchant,provider',
            'period' => 'required|string|in:monthly,yearly',
            'charge' => 'required|numeric|min:0|max:999999.99',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'alert_message' => 'nullable|string|max:255',
        ]);

        // Check if combination of type and period already exists (excluding current record)
        $exists = SubscriptionType::where('type', $validated['type'])
            ->where('period', $validated['period'])
            ->where('id', '!=', $subscriptionType->id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'A subscription plan for this user type and period already exists.');
        }

        $subscriptionType->update($validated);

        return redirect()->route('admin.subscription-types.index')
            ->with('success', 'Subscription type updated successfully.');
    }

    /**
     * Remove the specified subscription type from storage.
     */
    public function destroy(SubscriptionType $subscriptionType)
    {
        try {
            $subscriptionType->delete();
            return redirect()->route('admin.subscription-types.index')
                ->with('success', 'Subscription type deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.subscription-types.index')
                ->with('error', 'Error deleting subscription type. It may be in use.');
        }
    }
}

