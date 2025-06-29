<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PayoutMethod;

class PayoutMethodController extends Controller
{
    /**
     * Get a payout method by ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $payoutMethod = PayoutMethod::findOrFail($id);

        return response()->json($payoutMethod);
    }
}
