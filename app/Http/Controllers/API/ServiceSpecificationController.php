<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceSpecification;
use App\Models\ServiceSpecificationTemplate;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class ServiceSpecificationController extends Controller
{
    /**
     * Get specifications for a service.
     *
     * @param  int  $serviceId
     * @return \Illuminate\Http\Response
     */
    public function getSpecifications($serviceId)
    {
        $service = Service::with(['specifications' => function ($query) {
            $query->orderBy('display_order');
        }])->findOrFail($serviceId);

        return response()->json([
            'success' => true,
            'specifications' => $service->specifications,
        ]);
    }

    /**
     * Add or update specifications for a service.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $serviceId
     * @return \Illuminate\Http\Response
     */
    public function updateSpecifications(Request $request, $serviceId)
    {
        $service = Service::findOrFail($serviceId);

        // Validate user has permission to update this service
        $this->authorizeServiceAccess($service);

        $request->validate([
            'specifications' => 'required|array',
            'specifications.*.key' => 'required|string|max:255',
            'specifications.*.value' => 'required|string',
            'specifications.*.display_order' => 'integer',
        ]);

        // Delete existing specifications
        $service->specifications()->delete();

        // Add new specifications
        foreach ($request->specifications as $index => $spec) {
            $service->specifications()->create([
                'key' => $spec['key'],
                'value' => $spec['value'],
                'display_order' => $spec['display_order'] ?? $index,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Service specifications updated successfully',
            'specifications' => $service->specifications()->orderBy('display_order')->get(),
        ]);
    }

    /**
     * Get specification templates for a category.
     *
     * @param  int  $categoryId
     * @return \Illuminate\Http\Response
     */
    public function getTemplates($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        
        $templates = ServiceSpecificationTemplate::where('category_id', $categoryId)
            ->orderBy('display_order')
            ->get();

        return response()->json([
            'success' => true,
            'category' => $category->name,
            'templates' => $templates,
        ]);
    }

    /**
     * Add or update specification templates for a category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $categoryId
     * @return \Illuminate\Http\Response
     */
    public function updateTemplates(Request $request, $categoryId)
    {
        $category = Category::findOrFail($categoryId);

        // Only admin can update templates
        if (!Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $request->validate([
            'templates' => 'required|array',
            'templates.*.key' => 'required|string|max:255',
            'templates.*.default_value' => 'nullable|string',
            'templates.*.description' => 'nullable|string',
            'templates.*.is_required' => 'boolean',
            'templates.*.display_order' => 'integer',
        ]);

        // Delete existing templates
        ServiceSpecificationTemplate::where('category_id', $categoryId)->delete();

        // Add new templates
        foreach ($request->templates as $index => $template) {
            ServiceSpecificationTemplate::create([
                'category_id' => $categoryId,
                'key' => $template['key'],
                'default_value' => $template['default_value'] ?? null,
                'description' => $template['description'] ?? null,
                'is_required' => $template['is_required'] ?? false,
                'display_order' => $template['display_order'] ?? $index,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Service specification templates updated successfully',
            'templates' => ServiceSpecificationTemplate::where('category_id', $categoryId)
                ->orderBy('display_order')
                ->get(),
        ]);
    }

    /**
     * Apply specification templates to a service.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $serviceId
     * @return \Illuminate\Http\Response
     */
    public function applyTemplates(Request $request, $serviceId)
    {
        $service = Service::findOrFail($serviceId);
        
        // Validate user has permission to update this service
        $this->authorizeServiceAccess($service);

        // Get templates for the service's category
        $templates = ServiceSpecificationTemplate::where('category_id', $service->category_id)
            ->orderBy('display_order')
            ->get();

        if ($templates->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No templates found for this service category',
            ], 404);
        }

        // Apply templates to service
        foreach ($templates as $index => $template) {
            // Check if specification already exists
            $existingSpec = $service->specifications()
                ->where('key', $template->key)
                ->first();

            if (!$existingSpec) {
                $service->specifications()->create([
                    'key' => $template->key,
                    'value' => $template->default_value ?? '',
                    'display_order' => $index,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Service specification templates applied successfully',
            'specifications' => $service->specifications()->orderBy('display_order')->get(),
        ]);
    }

    /**
     * Authorize service access for the current user.
     *
     * @param  \App\Models\Service  $service
     * @return void
     */
    private function authorizeServiceAccess($service)
    {
        $user = Auth::user();

        // Admin can access any service
        if ($user->isAdmin()) {
            return;
        }

        // Check if the service belongs to the user's company
        $userBranches = $user->branches()->pluck('id')->toArray();
        
        if (!in_array($service->branch_id, $userBranches)) {
            abort(403, 'You do not have permission to update this service.');
        }
    }
}
