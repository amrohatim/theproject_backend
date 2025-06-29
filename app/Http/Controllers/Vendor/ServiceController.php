<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Service::query()
            ->with(['branch', 'category'])
            ->whereHas('branch', function ($query) {
                $query->whereHas('company', function ($query) {
                    $query->where('user_id', Auth::id());
                });
            });

        // Apply filters
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category')) {
            $query->filterByCategory($request->category);
        }

        if ($request->filled('branch')) {
            $query->filterByBranch($request->branch);
        }

        $services = $query->latest()->paginate(10);

        // Get service categories for the filter dropdown
        $categories = Category::where('type', 'service')
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        // Get branches that belong to the vendor's company for the filter dropdown
        $branches = Branch::whereHas('company', function ($query) {
            $query->where('user_id', Auth::id());
        })->orderBy('name')->get();

        return view('vendor.services.index', compact('services', 'categories', 'branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get service categories with their children - force a fresh query to get the latest data
        $parentCategories = Category::where('type', 'service')
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        // Get branches that belong to the vendor's company
        $branches = Branch::whereHas('company', function ($query) {
            $query->where('user_id', Auth::id());
        })->orderBy('name')->get();

        // Check if the vendor has any branches
        if ($branches->isEmpty()) {
            return redirect()->route('vendor.branches.create')
                ->with('warning', 'You need to create a branch before adding services. Please create a branch first.');
        }

        // Check if there are any categories
        if ($parentCategories->isEmpty()) {
            return redirect()->route('vendor.services.index')
                ->with('warning', 'No service categories found. Please contact the administrator.');
        }

        return view('vendor.services.create', compact('parentCategories', 'branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'branch_id' => 'required|exists:branches,id',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Verify that the branch belongs to the vendor's company
        $branch = Branch::findOrFail($request->branch_id);

        // Check if the branch's company belongs to the authenticated user
        $companyBelongsToUser = $branch->company && $branch->company->user_id === Auth::id();

        if (!$companyBelongsToUser) {
            return redirect()->back()->with('error', 'You do not have permission to add services to this branch.');
        }

        $data = $request->except('image');
        $data['is_available'] = $request->has('is_available') ? true : false;
        $data['home_service'] = $request->has('home_service') ? true : false;

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('services', 'public');
            $data['image'] = Storage::url($imagePath);
        }

        Service::create($data);

        return redirect()->route('vendor.services.index')
            ->with('success', 'Service created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        // Check if the service belongs to the vendor's company
        $userBranches = Branch::whereHas('company', function ($query) {
            $query->where('user_id', Auth::id());
        })->pluck('id')->toArray();

        if (!in_array($service->branch_id, $userBranches)) {
            return redirect()->route('vendor.services.index')
                ->with('error', 'You do not have permission to edit this service.');
        }

        // Get categories
        $categories = Category::orderBy('name')->get();

        // Get branches that belong to the vendor's company
        $branches = Branch::whereHas('company', function ($query) {
            $query->where('user_id', Auth::id());
        })->orderBy('name')->get();

        return view('vendor.services.edit', compact('service', 'categories', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        // Check if the service belongs to the vendor's company
        $userBranches = Branch::whereHas('company', function ($query) {
            $query->where('user_id', Auth::id());
        })->pluck('id')->toArray();

        if (!in_array($service->branch_id, $userBranches)) {
            return redirect()->route('vendor.services.index')
                ->with('error', 'You do not have permission to update this service.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'branch_id' => 'required|exists:branches,id',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Verify that the branch belongs to the vendor's company
        $branch = Branch::findOrFail($request->branch_id);

        // Check if the branch's company belongs to the authenticated user
        $companyBelongsToUser = $branch->company && $branch->company->user_id === Auth::id();

        if (!$companyBelongsToUser) {
            return redirect()->back()->with('error', 'You do not have permission to move services to this branch.');
        }

        $data = $request->except('image');
        $data['is_available'] = $request->has('is_available') ? true : false;
        $data['home_service'] = $request->has('home_service') ? true : false;

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($service->image && Storage::exists('public/' . str_replace('/storage/', '', $service->image))) {
                Storage::delete('public/' . str_replace('/storage/', '', $service->image));
            }

            $imagePath = $request->file('image')->store('services', 'public');
            $data['image'] = Storage::url($imagePath);
        }

        $service->update($data);

        return redirect()->route('vendor.services.index')
            ->with('success', 'Service updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        // Check if the service belongs to the vendor's company
        $userBranches = Branch::whereHas('company', function ($query) {
            $query->where('user_id', Auth::id());
        })->pluck('id')->toArray();

        if (!in_array($service->branch_id, $userBranches)) {
            return redirect()->route('vendor.services.index')
                ->with('error', 'You do not have permission to delete this service.');
        }

        // Delete image if exists
        if ($service->image && Storage::exists('public/' . str_replace('/storage/', '', $service->image))) {
            Storage::delete('public/' . str_replace('/storage/', '', $service->image));
        }

        $service->delete();

        return redirect()->route('vendor.services.index')
            ->with('success', 'Service deleted successfully.');
    }

    /**
     * Get search suggestions for services.
     */
    public function searchSuggestions(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = Service::query()
            ->with(['branch', 'category'])
            ->whereHas('branch', function ($q) {
                $q->whereHas('company', function ($q) {
                    $q->where('user_id', Auth::id());
                });
            })
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhereHas('category', function ($q) use ($query) {
                      $q->where('name', 'like', "%{$query}%");
                  });
            })
            ->limit(10)
            ->get()
            ->map(function ($service) use ($query) {
                return [
                    'id' => $service->id,
                    'text' => $service->name,
                    'type' => 'service',
                    'icon' => 'fas fa-concierge-bell',
                    'subtitle' => $service->category->name ?? 'No Category',
                    'highlight' => $this->highlightMatch($service->name, $query),
                ];
            });

        return response()->json($suggestions);
    }

    /**
     * Highlight matching text in search results.
     */
    private function highlightMatch($text, $query)
    {
        return preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark>$1</mark>', $text);
    }
}
