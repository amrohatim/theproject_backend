<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\PayoutPreference;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display the payment settings page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        // Get payment methods
        $paymentMethods = $user->paymentMethods()->orderBy('is_default', 'desc')->get();

        // Get payout methods
        $payoutMethods = $user->payoutMethods()->orderBy('is_default', 'desc')->get();

        // Get payout preferences
        $payoutPreference = $user->payoutPreference ?? new PayoutPreference([
            'payout_frequency' => 'weekly',
            'minimum_payout_amount' => 50.00,
            'currency' => 'USD'
        ]);

        // Get recent payment transactions
        $transactions = $user->paymentTransactions()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('vendor.payment.index', compact(
            'paymentMethods',
            'payoutMethods',
            'payoutPreference',
            'transactions'
        ));
    }

    /**
     * Show the form for creating a new payment method.
     *
     * @return \Illuminate\View\View
     */
    public function createPaymentMethod()
    {
        return view('vendor.payment.create-payment-method');
    }

    /**
     * Store a newly created payment method.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storePaymentMethod(Request $request)
    {
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
            'is_verified' => 'sometimes|boolean',
            'verified_at' => 'sometimes|nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->route('vendor.payment.create-payment-method')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $user = Auth::user();
            $data = $request->all();

            // Convert checkbox to boolean
            $data['is_default'] = $request->has('is_default') ? true : false;

            // Set default values for required fields if not provided
            if (!isset($data['is_verified'])) {
                $data['is_verified'] = true;
            }

            if (!isset($data['verified_at'])) {
                $data['verified_at'] = now();
            }

            // Debug log
            Log::info('Creating payment method with data:', [
                'user_id' => $user->id,
                'data' => $data,
                'request_all' => $request->all(),
                'payment_type' => $request->input('payment_type'),
                'provider_type' => $request->input('provider_type')
            ]);

            $this->paymentService->createPaymentMethod($user, $data);

            return redirect()->route('vendor.payment.index')
                ->with('success', 'Payment method added successfully.');
        } catch (\Exception $e) {
            Log::error('Error adding payment method: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);

            return redirect()->route('vendor.payment.create-payment-method')
                ->with('error', 'An error occurred while adding the payment method: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing a payment method.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function editPaymentMethod($id)
    {
        $paymentMethod = PaymentMethod::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('vendor.payment.edit-payment-method', compact('paymentMethod'));
    }

    /**
     * Update the specified payment method.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePaymentMethod(Request $request, $id)
    {
        $paymentMethod = PaymentMethod::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'card_brand' => 'required_if:payment_type,credit_card|nullable|string|in:visa,mastercard',
            'expiry_month' => 'required_if:payment_type,credit_card|nullable|string|max:2',
            'expiry_year' => 'required_if:payment_type,credit_card|nullable|string|max:4',
            'billing_email' => 'required_if:payment_type,paypal|nullable|email|max:255',
            'is_default' => 'sometimes|boolean',
            'is_verified' => 'sometimes|boolean',
            'verified_at' => 'sometimes|nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->route('vendor.payment.edit-payment-method', $id)
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $request->all();

            // Convert checkbox to boolean
            $data['is_default'] = $request->has('is_default') ? true : false;

            // Preserve existing values if not provided
            if (!isset($data['is_verified'])) {
                $data['is_verified'] = $paymentMethod->is_verified;
            }

            if (!isset($data['verified_at'])) {
                $data['verified_at'] = $paymentMethod->verified_at ?? now();
            }

            // Debug log
            Log::info('Updating payment method with data:', [
                'payment_method_id' => $paymentMethod->id,
                'data' => $data
            ]);

            $this->paymentService->updatePaymentMethod($paymentMethod, $data);

            return redirect()->route('vendor.payment.index')
                ->with('success', 'Payment method updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating payment method: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'payment_method_id' => $id,
                'data' => $request->all()
            ]);

            return redirect()->route('vendor.payment.edit-payment-method', $id)
                ->with('error', 'An error occurred while updating the payment method: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified payment method.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyPaymentMethod($id)
    {
        $paymentMethod = PaymentMethod::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        try {
            $this->paymentService->deletePaymentMethod($paymentMethod);

            return redirect()->route('vendor.payment.index')
                ->with('success', 'Payment method removed successfully.');
        } catch (\Exception $e) {
            Log::error('Error removing payment method: ' . $e->getMessage());

            return redirect()->route('vendor.payment.index')
                ->with('error', 'An error occurred while removing the payment method. Please try again.');
        }
    }

    /**
     * Show the payment history page.
     *
     * @return \Illuminate\View\View
     */
    public function history()
    {
        $user = Auth::user();
        $transactions = $user->paymentTransactions()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('vendor.payment.history', compact('transactions'));
    }
}
