<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SizeCategory;
use App\Models\StandardizedSize;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SizeCategoryController extends Controller
{
    /**
     * Display a listing of the size categories.
     */
    public function index(Request $request)
    {
        $query = SizeCategory::query()
            ->withCount(['standardizedSizes', 'categories']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('display_name', 'like', "%{$search}%")
                    ->orWhere('name_arabic', 'like', "%{$search}%")
                    ->orWhere('display_name_arabic', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $sizeCategories = $query
            ->orderBy('display_order')
            ->orderBy('name')
            ->paginate(10)
            ->appends($request->query());

        return view('admin.size-categories.index', compact('sizeCategories'));
    }

    /**
     * Show the form for creating a new size category.
     */
    public function create()
    {
        return view('admin.size-categories.create');
    }

    /**
     * Store a newly created size category in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:size_categories,name',
            'name_arabic' => 'nullable|string|max:255',
            'display_name' => 'required|string|max:255',
            'display_name_arabic' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'sizes' => 'array',
            'sizes.*.name' => 'nullable|string|max:255',
            'sizes.*.value' => 'nullable|string|max:255',
            'sizes.*.additional_info' => 'nullable|string|max:255',
            'sizes.*.display_order' => 'nullable|integer|min:0',
            'sizes.*.is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['display_order'] = $validated['display_order'] ?? 0;

        DB::transaction(function () use ($validated, $request) {
            $sizeCategory = SizeCategory::create($validated);

            $sizes = collect($request->input('sizes', []))
                ->filter(fn ($size) => !empty($size['name']))
                ->values();

            $this->assertUniqueNames($sizes);

            foreach ($sizes as $index => $sizeData) {
                $sizeCategory->standardizedSizes()->create([
                    'name' => $sizeData['name'],
                    'value' => $sizeData['value'] ?? null,
                    'additional_info' => $sizeData['additional_info'] ?? null,
                    'display_order' => $sizeData['display_order'] ?? $index,
                    'is_active' => isset($sizeData['is_active']) ? (bool) $sizeData['is_active'] : true,
                ]);
            }
        });

        return redirect()->route('admin.size-categories.index')->with('success', 'Size category created successfully');
    }

    /**
     * Show the form for editing the specified size category.
     */
    public function edit(SizeCategory $sizeCategory)
    {
        $sizeCategory->load('standardizedSizes');

        return view('admin.size-categories.edit', compact('sizeCategory'));
    }

    /**
     * Update the specified size category in storage.
     */
    public function update(Request $request, SizeCategory $sizeCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:size_categories,name,' . $sizeCategory->id,
            'name_arabic' => 'nullable|string|max:255',
            'display_name' => 'required|string|max:255',
            'display_name_arabic' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'sizes' => 'array',
            'sizes.*.id' => 'nullable|exists:standardized_sizes,id',
            'sizes.*.name' => 'nullable|string|max:255',
            'sizes.*.value' => 'nullable|string|max:255',
            'sizes.*.additional_info' => 'nullable|string|max:255',
            'sizes.*.display_order' => 'nullable|integer|min:0',
            'sizes.*.is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', false);
        $validated['display_order'] = $validated['display_order'] ?? 0;

        DB::transaction(function () use ($sizeCategory, $validated, $request) {
            $sizeCategory->update($validated);

            $sizes = collect($request->input('sizes', []))
                ->filter(fn ($size) => !empty($size['name']))
                ->values();

            $this->assertUniqueNames($sizes);

            $keepIds = [];

            foreach ($sizes as $index => $sizeData) {
                $payload = [
                    'name' => $sizeData['name'],
                    'value' => $sizeData['value'] ?? null,
                    'additional_info' => $sizeData['additional_info'] ?? null,
                    'display_order' => $sizeData['display_order'] ?? $index,
                    'is_active' => isset($sizeData['is_active']) ? (bool) $sizeData['is_active'] : true,
                ];

                if (!empty($sizeData['id'])) {
                    $standardizedSize = $sizeCategory->standardizedSizes()->where('id', $sizeData['id'])->firstOrFail();
                    $standardizedSize->update($payload);
                    $keepIds[] = $standardizedSize->id;
                } else {
                    $newSize = $sizeCategory->standardizedSizes()->create($payload);
                    $keepIds[] = $newSize->id;
                }
            }

            $sizeCategory->standardizedSizes()
                ->whereNotIn('id', $keepIds)
                ->delete();
        });

        return redirect()->route('admin.size-categories.index')->with('success', 'Size category updated successfully');
    }

    /**
     * Remove the specified size category from storage.
     */
    public function destroy(SizeCategory $sizeCategory)
    {
        // Prevent deletion if linked categories exist
        if ($sizeCategory->categories()->exists()) {
            return redirect()->route('admin.size-categories.index')
                ->with('error', 'Cannot delete a size category linked to existing categories. Reassign them first.');
        }

        $sizeCategory->delete();

        return redirect()->route('admin.size-categories.index')->with('success', 'Size category deleted successfully');
    }

    /**
     * Ensure size names are unique within the submitted set.
     */
    protected function assertUniqueNames($sizes): void
    {
        $names = $sizes->pluck('name')->filter();
        if ($names->count() !== $names->unique()->count()) {
            throw ValidationException::withMessages([
                'sizes' => 'Size names must be unique within a category.',
            ]);
        }
    }
}
