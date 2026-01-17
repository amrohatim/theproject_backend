<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\GeneralWishlistBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GeneralWishlistBranchController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $branchIds = GeneralWishlistBranch::where('user_id', $userId)
            ->pluck('branch_id')
            ->toArray();

        if (empty($branchIds)) {
            return response()->json([
                'success' => true,
                'branches' => [],
            ]);
        }

        $branches = Branch::whereIn('id', $branchIds)->get();

        return response()->json([
            'success' => true,
            'branches' => $branches,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
        ]);

        $userId = Auth::id();
        $branchId = $validated['branch_id'];

        GeneralWishlistBranch::firstOrCreate([
            'user_id' => $userId,
            'branch_id' => $branchId,
        ]);

        $branch = Branch::find($branchId);

        if (!$branch) {
            return response()->json([
                'success' => false,
                'message' => 'Branch not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'branch' => $branch,
        ]);
    }

    public function destroy($branchId)
    {
        $userId = Auth::id();

        GeneralWishlistBranch::where('user_id', $userId)
            ->where('branch_id', $branchId)
            ->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
