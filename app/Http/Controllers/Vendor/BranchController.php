<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Branch::query()
            ->with(['company'])
            ->join('companies', 'branches.company_id', '=', 'companies.id')
            ->where('companies.user_id', Auth::id())
            ->select('branches.*');

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('branches.name', 'like', "%{$search}%")
                  ->orWhere('branches.phone', 'like', "%{$search}%")
                  ->orWhere('branches.address', 'like', "%{$search}%")
                  ->orWhere('branches.email', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('branches.status', $request->status);
        }

        // Apply sorting
        $sortBy = $request->get('sort', 'newest');
        switch ($sortBy) {
            case 'oldest':
                $query->orderBy('branches.created_at', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('branches.name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('branches.name', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('branches.created_at', 'desc');
                break;
        }

        $branches = $query->paginate(10);
        
        // Preserve query parameters in pagination
        $branches->appends($request->query());

        return view('vendor.branches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::where('user_id', Auth::id())->orderBy('name')->get();
        return view('vendor.branches.create', compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'address' => 'required|string|max:255',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string',
            'branch_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'use_company_image' => 'nullable|boolean',
        ]);

        // Verify the company belongs to the authenticated user
        $company = Company::where('id', $validated['company_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $data = $validated;
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

        return redirect()->route('vendor.branches.index')->with('success', 'Branch created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $branch = Branch::with(['company', 'products', 'services'])
            ->join('companies', 'branches.company_id', '=', 'companies.id')
            ->where('companies.user_id', Auth::id())
            ->where('branches.id', $id)
            ->select('branches.*')
            ->firstOrFail();
        
        return view('vendor.branches.show', compact('branch'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $branch = Branch::join('companies', 'branches.company_id', '=', 'companies.id')
            ->where('companies.user_id', Auth::id())
            ->where('branches.id', $id)
            ->select('branches.*')
            ->firstOrFail();
        
        $companies = Company::where('user_id', Auth::id())->orderBy('name')->get();
        
        return view('vendor.branches.edit', compact('branch', 'companies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $branch = Branch::join('companies', 'branches.company_id', '=', 'companies.id')
            ->where('companies.user_id', Auth::id())
            ->where('branches.id', $id)
            ->select('branches.*')
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'address' => 'required|string|max:255',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string',
            'branch_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'use_company_image' => 'nullable|boolean',
        ]);

        // Verify the company belongs to the authenticated user
        $company = Company::where('id', $validated['company_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $data = $validated;

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

        $branch->update($data);

        return redirect()->route('vendor.branches.show', $branch->id)->with('success', 'Branch updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $branch = Branch::join('companies', 'branches.company_id', '=', 'companies.id')
            ->where('companies.user_id', Auth::id())
            ->where('branches.id', $id)
            ->select('branches.*')
            ->firstOrFail();
        
        $branch->delete();
        
        return redirect()->route('vendor.branches.index')->with('success', 'Branch deleted successfully');
    }

    /**
     * Show the branch image management page.
     */
    public function image($id)
    {
        $branch = Branch::join('companies', 'branches.company_id', '=', 'companies.id')
            ->where('companies.user_id', Auth::id())
            ->where('branches.id', $id)
            ->select('branches.*')
            ->firstOrFail();
        
        $company = Company::where('id', $branch->company_id)->first();
        
        return view('vendor.branches.image', compact('branch', 'company'));
    }

    /**
     * Get search suggestions for branches.
     */
    public function searchSuggestions(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = Branch::query()
            ->with(['company'])
            ->join('companies', 'branches.company_id', '=', 'companies.id')
            ->where('companies.user_id', Auth::id())
            ->select('branches.*')
            ->where(function ($q) use ($query) {
                $q->where('branches.name', 'like', "%{$query}%")
                  ->orWhere('branches.phone', 'like', "%{$query}%")
                  ->orWhere('branches.address', 'like', "%{$query}%")
                  ->orWhere('branches.email', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($branch) use ($query) {
                return [
                    'id' => $branch->id,
                    'text' => $branch->name,
                    'type' => 'branch',
                    'icon' => 'fas fa-map-marker-alt',
                    'subtitle' => $branch->address,
                    'highlight' => $this->highlightMatch($branch->name, $query),
                ];
            });

        return response()->json($suggestions);
    }

    /**
     * Highlight matching text in search results
     */
    private function highlightMatch($text, $query)
    {
        if (empty($query) || empty($text)) {
            return $text;
        }

        return preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark>$1</mark>', $text);
    }

}
