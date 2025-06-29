<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Branch;
use App\Models\ProductBranch;
use Illuminate\Support\Facades\Auth;

class ProductBranchController extends Controller
{
    /**
     * Get branches for a product.
     *
     * @param  int  $productId
     * @return \Illuminate\Http\Response
     */
    public function getBranches($productId)
    {
        $product = Product::with(['branches' => function ($query) {
            $query->orderBy('name');
        }])->findOrFail($productId);

        return response()->json([
            'success' => true,
            'is_multi_branch' => $product->is_multi_branch,
            'branches' => $product->branches,
        ]);
    }

    /**
     * Update branches for a product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $productId
     * @return \Illuminate\Http\Response
     */
    public function updateBranches(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        // Validate user has permission to update this product
        $this->authorizeProductAccess($product);

        $request->validate([
            'branches' => 'required|array',
            'branches.*.branch_id' => 'required|exists:branches,id',
            'branches.*.stock' => 'required|integer|min:0',
            'branches.*.is_available' => 'boolean',
            'branches.*.price' => 'nullable|numeric|min:0',
        ]);

        // Update product to be multi-branch
        $product->update(['is_multi_branch' => true]);

        // Delete existing branch associations
        $product->productBranches()->delete();

        // Add new branch associations
        foreach ($request->branches as $branchData) {
            // Validate user has access to this branch
            $branch = Branch::findOrFail($branchData['branch_id']);
            $this->authorizeBranchAccess($branch);

            $product->productBranches()->create([
                'branch_id' => $branchData['branch_id'],
                'stock' => $branchData['stock'],
                'is_available' => $branchData['is_available'] ?? true,
                'price' => $branchData['price'] ?? $product->price,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product branches updated successfully',
            'branches' => $product->branches()->orderBy('name')->get(),
        ]);
    }

    /**
     * Add a product to multiple branches at once.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addProductToBranches(Request $request)
    {
        $request->validate([
            'branch_ids' => 'required|array',
            'branch_ids.*' => 'required|exists:branches,id',
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'is_available' => 'boolean',
        ]);

        // Validate user has access to all branches
        foreach ($request->branch_ids as $branchId) {
            $branch = Branch::findOrFail($branchId);
            $this->authorizeBranchAccess($branch);
        }

        // Create the product with the first branch as the primary branch
        $primaryBranchId = $request->branch_ids[0];
        
        $productData = $request->except(['branch_ids']);
        $productData['branch_id'] = $primaryBranchId;
        $productData['is_multi_branch'] = count($request->branch_ids) > 1;
        
        $product = Product::create($productData);

        // If there are multiple branches, add the product to those branches
        if (count($request->branch_ids) > 1) {
            foreach ($request->branch_ids as $branchId) {
                // Skip the primary branch as it's already associated
                if ($branchId == $primaryBranchId) {
                    continue;
                }
                
                $product->productBranches()->create([
                    'branch_id' => $branchId,
                    'stock' => $request->stock,
                    'is_available' => $request->is_available ?? true,
                    'price' => $request->price,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added to multiple branches successfully',
            'product' => $product->load('branches'),
        ], 201);
    }

    /**
     * Authorize product access for the current user.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    private function authorizeProductAccess($product)
    {
        $user = Auth::user();

        // Admin can access any product
        if ($user->role === 'admin') {
            return;
        }

        // Check if the product belongs to the user's company
        $userBranches = $user->branches()->pluck('id')->toArray();
        
        if (!in_array($product->branch_id, $userBranches)) {
            abort(403, 'You do not have permission to update this product.');
        }
    }

    /**
     * Authorize branch access for the current user.
     *
     * @param  \App\Models\Branch  $branch
     * @return void
     */
    private function authorizeBranchAccess($branch)
    {
        $user = Auth::user();

        // Admin can access any branch
        if ($user->isAdmin()) {
            return;
        }

        // Check if the branch belongs to the user's company
        $userBranches = $user->branches()->pluck('id')->toArray();
        
        if (!in_array($branch->id, $userBranches)) {
            abort(403, 'You do not have permission to access this branch.');
        }
    }
}
