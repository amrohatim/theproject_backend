<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentMethodController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService = null)
    {
        $this->paymentService = $paymentService ?? new PaymentService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // Check if user is authenticated
            if (!Auth::check()) {
                Log::error('User not authenticated when trying to access payment methods');
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $paymentMethods = Auth::user()->paymentMethods()
                ->orderBy('is_default', 'desc')
                ->get();

            return response()->json($paymentMethods);
        } catch (\Exception $e) {
            Log::error('Error retrieving payment methods: ' . $e->getMessage(), [
                'user_id' => Auth::id() ?? 'not authenticated',
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to retrieve payment methods'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            Log::error('User not authenticated when trying to create payment method');
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'provider_type' => 'required|string|in:stripe,paypal',
            'payment_type' => 'required|string|in:credit_card,paypal',
            'name' => 'required|string|max:255',
            'card_brand' => 'required_if:payment_type,credit_card|nullable|string|in:visa,mastercard',
            'last_four' => 'required_if:payment_type,credit_card|nullable|string|size:4',
            'expiry_month' => 'required_if:payment_type,credit_card|nullable|string|max:2',
            'expiry_year' => 'required_if:payment_type,credit_card|nullable|string|max:4',
            'billing_email' => 'required_if:payment_type,paypal|nullable|email|max:255',
            'is_default' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $user = Auth::user();
            $data = $request->all();
            $data['is_default'] = $request->has('is_default') ? (bool)$request->is_default : false;

            $paymentMethod = $this->paymentService->createPaymentMethod($user, $data);

            return response()->json($paymentMethod, 201);
        } catch (\Exception $e) {
            Log::error('Error creating payment method: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'data' => $request->all(),
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to create payment method'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            // Check if user is authenticated
            if (!Auth::check()) {
                Log::error('User not authenticated when trying to access payment method', [
                    'id' => $id
                ]);
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Find the payment method and ensure it belongs to the authenticated user
            $paymentMethod = PaymentMethod::where('id', $id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$paymentMethod) {
                return response()->json(['error' => 'Payment method not found'], 404);
            }

            // Log successful retrieval
            Log::info('Payment method retrieved successfully', [
                'id' => $id,
                'user_id' => Auth::id(),
                'payment_type' => $paymentMethod->payment_type
            ]);

            // Make sure boolean values are properly cast
            $paymentMethod->is_default = (bool)$paymentMethod->is_default;
            $paymentMethod->is_verified = (bool)$paymentMethod->is_verified;

            return response()->json($paymentMethod);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error retrieving payment method', [
                'id' => $id,
                'user_id' => Auth::id() ?? 'not authenticated',
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Payment method not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            Log::error('User not authenticated when trying to update payment method', [
                'id' => $id
            ]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $paymentMethod = PaymentMethod::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$paymentMethod) {
            return response()->json(['error' => 'Payment method not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'card_brand' => 'sometimes|string|in:visa,mastercard',
            'expiry_month' => 'sometimes|string|max:2',
            'expiry_year' => 'sometimes|string|max:4',
            'billing_email' => 'sometimes|email|max:255',
            'is_default' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $data = $request->all();
            if ($request->has('is_default')) {
                $data['is_default'] = (bool) $request->is_default;
            }

            $updatedPaymentMethod = $this->paymentService->updatePaymentMethod($paymentMethod, $data);

            return response()->json($updatedPaymentMethod);
        } catch (\Exception $e) {
            Log::error('Error updating payment method: ' . $e->getMessage(), [
                'id' => $id,
                'user_id' => Auth::id(),
                'data' => $request->all(),
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to update payment method'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            Log::error('User not authenticated when trying to delete payment method', [
                'id' => $id
            ]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $paymentMethod = PaymentMethod::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$paymentMethod) {
            return response()->json(['error' => 'Payment method not found'], 404);
        }

        try {
            $this->paymentService->deletePaymentMethod($paymentMethod);

            return response()->json(['message' => 'Payment method deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting payment method: ' . $e->getMessage(), [
                'id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to delete payment method'], 500);
        }
    }
}
