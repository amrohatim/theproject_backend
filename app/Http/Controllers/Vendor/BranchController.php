<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BusinessType;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
        $businessTypes = BusinessType::orderBy('business_name')->get();
        return view('vendor.branches.create', compact('companies', 'businessTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Debug: Log all incoming request data
        Log::info('=== BRANCH STORE DEBUG ===');
        Log::info('Request method: ' . $request->method());
        Log::info('Request URL: ' . $request->url());
        Log::info('All request data: ', $request->all());
        Log::info('Business type specifically: ' . ($request->input('business_type') ?? 'NULL'));
        Log::info('Business type backup: ' . ($request->input('business_type_backup') ?? 'NULL'));
        Log::info('Request has business_type: ' . ($request->has('business_type') ? 'YES' : 'NO'));

        // Fix: If business_type is missing but backup exists, use the backup
        if (!$request->has('business_type') || empty($request->input('business_type'))) {
            if ($request->has('business_type_backup') && !empty($request->input('business_type_backup'))) {
                $request->merge(['business_type' => $request->input('business_type_backup')]);
                Log::info('Used business_type_backup: ' . $request->input('business_type'));
            }
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'address' => 'required|string|max:255',
            'emirate' => 'required|string|max:255',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string',
            'business_type' => 'required|string|exists:business_types,business_name',
            'branch_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'use_company_image' => 'nullable|boolean',
            // License validation
            'license_file' => 'required|file|mimes:pdf|max:10240', // 10MB max, PDF only
            'license_start_date' => 'required|date',
            'license_end_date' => 'required|date|after:license_start_date',
        ], [
            'business_type.required' => 'Please select a business type.',
            'business_type.exists' => 'The selected business type is invalid.',
            'license_file.required' => 'Please upload a branch license document.',
            'license_file.file' => 'The license document must be a valid file.',
            'license_file.mimes' => 'The license document must be a PDF file only.',
            'license_file.max' => 'The license document must not exceed 10MB in size.',
            'license_start_date.required' => 'Please provide the license start date.',
            'license_start_date.date' => 'Please provide a valid license start date.',
            'license_end_date.required' => 'Please provide the license end date.',
            'license_end_date.date' => 'Please provide a valid license end date.',
            'license_end_date.after' => 'The license end date must be after the start date.',
        ]);

        Log::info('Validation passed. Validated data: ', $validated);

        // Verify the company belongs to the authenticated user
        $company = Company::where('id', $validated['company_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        try {
            DB::beginTransaction();

            // Prepare branch data (exclude license fields)
            $branchData = [
                'name' => $validated['name'],
                'company_id' => $validated['company_id'],
                'address' => $validated['address'],
                'emirate' => $validated['emirate'] ?? null,
                'lat' => $validated['lat'],
                'lng' => $validated['lng'],
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'description' => $validated['description'] ?? null,
                'business_type' => $validated['business_type'],
                'status' => 'active',
                'user_id' => Auth::id(),
            ];

            // Handle branch image upload
            if ($request->hasFile('branch_image')) {
                $imagePath = $request->file('branch_image')->store('branches', 'public');
                $branchData['branch_image'] = $imagePath;
                $branchData['use_company_image'] = false;
            }

            // Set use_company_image flag
            if ($request->has('use_company_image')) {
                $branchData['use_company_image'] = $request->boolean('use_company_image');
                // If using company image, clear branch image
                if ($branchData['use_company_image']) {
                    $branchData['branch_image'] = null;
                }
            }

            // Create the branch
            $branch = Branch::create($branchData);

            // Handle license file upload and create license record
            $licenseFilePath = $request->file('license_file')->store('branch_licenses', 'public');

            // Create the branch license record with pending status
            $branch->licenses()->create([
                'license_file_path' => $licenseFilePath,
                'start_date' => $validated['license_start_date'],
                'end_date' => $validated['license_end_date'],
                'status' => 'pending', // Set to pending for vendor-created licenses
                'uploaded_at' => now(),
                'verified_at' => null, // Will be set when admin approves
            ]);

            DB::commit();

            return redirect()->route('vendor.branches.index')
                ->with('success', 'Branch created successfully. License is pending approval.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create branch: ' . $e->getMessage());
        }
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
        $businessTypes = BusinessType::orderBy('business_name')->get();

        return view('vendor.branches.edit', compact('branch', 'companies', 'businessTypes'));
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
            'business_type' => 'required|string|exists:business_types,business_name',
            'branch_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'use_company_image' => 'nullable|boolean',
        ], [
            'business_type.required' => 'Please select a business type.',
            'business_type.exists' => 'The selected business type is invalid.',
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
     * Update branch information only (excluding license fields).
     */
    public function updateBranchInfo(Request $request, $id)
    {
        $branch = Branch::join('companies', 'branches.company_id', '=', 'companies.id')
            ->where('companies.user_id', Auth::id())
            ->where('branches.id', $id)
            ->select('branches.*')
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'business_type' => 'required|string|exists:business_types,business_name',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string|max:255',
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'emirate' => 'nullable|string|in:Dubai,Abu Dhabi,Sharjah,Ajman,Umm Al Quwain,Ras Al Khaimah,Fujairah',
            'description' => 'nullable|string',
            'status' => 'required|string|in:active,inactive',
            'days_open' => 'nullable|array',
            'opening_hours' => 'nullable|array',
            'branch_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'use_company_image' => 'sometimes|boolean',
        ], [
            'business_type.required' => 'Please select a business type.',
            'business_type.exists' => 'The selected business type is invalid.',
        ]);

        // Verify that the company belongs to the authenticated user
        $company = Company::where('id', $validated['company_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Keep the existing lat/lng values if not provided
        $validated['lat'] = $validated['lat'] ?? $branch->lat;
        $validated['lng'] = $validated['lng'] ?? $branch->lng;

        // Set use_company_image to true if not provided
        $validated['use_company_image'] = $request->has('use_company_image');

        // Handle branch image upload
        if ($request->hasFile('branch_image') && !$validated['use_company_image']) {
            try {
                // Delete old image if exists
                if ($branch->branch_image && Storage::disk('public')->exists($branch->branch_image)) {
                    Storage::disk('public')->delete($branch->branch_image);
                    Log::info('Deleted old branch image', [
                        'branch_id' => $branch->id,
                        'image_path' => $branch->branch_image
                    ]);
                }

                // Store the image in the public disk
                $imagePath = $request->file('branch_image')->store('branches', 'public');
                $validated['branch_image'] = $imagePath;

                Log::info('Branch image uploaded successfully', [
                    'branch_id' => $branch->id,
                    'image_path' => $imagePath
                ]);
            } catch (\Exception $e) {
                Log::error('Error uploading branch image', [
                    'branch_id' => $branch->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        } elseif ($validated['use_company_image'] && $branch->branch_image) {
            // If switching to use company image, remove the branch image
            if (Storage::disk('public')->exists($branch->branch_image)) {
                Storage::disk('public')->delete($branch->branch_image);
                Log::info('Deleted branch image when switching to company image', [
                    'branch_id' => $branch->id,
                    'image_path' => $branch->branch_image
                ]);
            }
            $validated['branch_image'] = null;
        }

        // Process business hours
        $businessHours = [];
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($days as $day) {
            // Check if the day is marked as open
            if (isset($request->days_open[$day]) && $request->days_open[$day] == 1) {
                // Get opening and closing hours for the day
                $openTime = $request->opening_hours[$day]['open'] ?? '09:00';
                $closeTime = $request->opening_hours[$day]['close'] ?? '17:00';

                $businessHours[$day] = [
                    'is_open' => true,
                    'open' => $openTime,
                    'close' => $closeTime
                ];
            } else {
                $businessHours[$day] = [
                    'is_open' => false,
                    'open' => null,
                    'close' => null
                ];
            }
        }

        // Add business hours to validated data
        $validated['opening_hours'] = $businessHours;

        // Remove the days_open field as it's not in the model
        unset($validated['days_open']);

        $branch->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Branch information updated successfully.'
        ]);
    }

    /**
     * Update branch license information with conditional status logic.
     */
    public function updateLicense(Request $request, $id)
    {
        $branch = Branch::join('companies', 'branches.company_id', '=', 'companies.id')
            ->where('companies.user_id', Auth::id())
            ->where('branches.id', $id)
            ->select('branches.*')
            ->firstOrFail();

        $validated = $request->validate([
            'license_file' => 'nullable|file|mimes:pdf|max:10240',
            'license_start_date' => 'required|date',
            'license_end_date' => 'required|date|after:license_start_date',
        ], [
            'license_file.file' => 'The license document must be a valid file.',
            'license_file.mimes' => 'The license document must be a PDF file only.',
            'license_file.max' => 'The license document must not exceed 10MB in size.',
            'license_start_date.required' => 'Please provide the license start date.',
            'license_start_date.date' => 'Please provide a valid license start date.',
            'license_end_date.required' => 'Please provide the license end date.',
            'license_end_date.date' => 'Please provide a valid license end date.',
            'license_end_date.after' => 'The license end date must be after the start date.',
        ]);

        // Get current license
        $currentLicense = $branch->licenses()->latest()->first();

        if (!$currentLicense) {
            return response()->json([
                'success' => false,
                'message' => 'No license found for this branch.'
            ], 404);
        }

        // Determine if license status should change to pending
        $shouldChangeToPending = false;

        // Check if license file is being uploaded
        if ($request->hasFile('license_file')) {
            $shouldChangeToPending = true;
        }

        // Check if license dates are being changed
        if ($currentLicense->start_date->format('Y-m-d') !== $validated['license_start_date'] ||
            $currentLicense->end_date->format('Y-m-d') !== $validated['license_end_date']) {
            $shouldChangeToPending = true;
        }

        // Prepare license update data
        $licenseUpdateData = [
            'start_date' => $validated['license_start_date'],
            'end_date' => $validated['license_end_date'],
        ];

        // Only change status to pending if license documents or dates are modified
        if ($shouldChangeToPending) {
            $licenseUpdateData['status'] = 'pending';
            $licenseUpdateData['verified_at'] = null;
        }

        // Handle license file upload if provided
        if ($request->hasFile('license_file')) {
            // Delete old license file if exists
            if ($currentLicense->license_file_path && Storage::disk('public')->exists($currentLicense->license_file_path)) {
                Storage::disk('public')->delete($currentLicense->license_file_path);
            }

            // Upload new license file
            $licenseFilePath = $request->file('license_file')->store('branch_licenses', 'public');
            $licenseUpdateData['license_file_path'] = $licenseFilePath;
            $licenseUpdateData['uploaded_at'] = now();
        }

        $currentLicense->update($licenseUpdateData);

        $message = $shouldChangeToPending
            ? 'License updated successfully. License is pending approval.'
            : 'License updated successfully.';

        return response()->json([
            'success' => true,
            'message' => $message,
            'requires_approval' => $shouldChangeToPending
        ]);
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

     public function branchimageByid($id)
    {
        $branch = Branch::find($id);
        if (!$branch) {
            return response()->json([
                'success' => false,
                'message' => 'Branch not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'branch_image' => $branch->branch_image,
        ]);
    }

}
