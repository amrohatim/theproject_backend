<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;

use App\Models\PayoutMethod;
use App\Models\PayoutPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentSettingsController extends Controller
{
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

        return view('vendor.settings.payment', compact(
            'paymentMethods',
            'payoutMethods',
            'payoutPreference',
            'transactions'
        ));
    }

    /**
     * Add a new payment method.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addPaymentMethod(Request $request)
    {
        // Log the request data for debugging
        Log::info('Add Payment Method Request Data:', $request->all());

        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:credit_card,paypal',
            'name' => 'required|string|max:255',
            'card_type' => 'required_if:type,credit_card|nullable|string|in:visa,mastercard',
            'last_four' => 'required_if:type,credit_card|nullable|string|size:4',
            'expiry_month' => 'required_if:type,credit_card|nullable|string|max:2',
            'expiry_year' => 'required_if:type,credit_card|nullable|string|max:4',
            'email' => 'required_if:type,paypal|nullable|email|max:255',
            'is_default' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            Log::warning('Payment method validation failed', [
                'errors' => $validator->errors()->toArray(),
                'input' => $request->all()
            ]);

            return redirect()->route('vendor.settings.payment')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $isDefault = $request->has('is_default');

            // If this is the default payment method, unset any existing default
            if ($isDefault) {
                $user->paymentMethods()->update(['is_default' => false]);
            }

            // Create the new payment method
            $paymentMethod = new PaymentMethod();
            $paymentMethod->user_id = $user->id;
            $paymentMethod->payment_type = $request->type;
            $paymentMethod->name = $request->name;

            // Set credit card specific fields
            if ($request->type === 'credit_card') {
                $paymentMethod->card_brand = $request->card_type;
                $paymentMethod->last_four = $request->last_four;
                $paymentMethod->expiry_month = $request->expiry_month;
                $paymentMethod->expiry_year = $request->expiry_year;
            }
            // Set PayPal specific fields
            else if ($request->type === 'paypal') {
                $paymentMethod->billing_email = $request->email;
            }

            $paymentMethod->is_default = $isDefault;
            $paymentMethod->is_verified = true;
            $paymentMethod->verified_at = now();
            $paymentMethod->provider_type = 'stripe'; // Default provider
            $paymentMethod->save();

            DB::commit();

            Log::info('Payment method added successfully', [
                'id' => $paymentMethod->id,
                'user_id' => $user->id,
                'type' => $request->type
            ]);

            return redirect()->route('vendor.settings.payment')
                ->with('success', 'Payment method added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding payment method: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return redirect()->route('vendor.settings.payment')
                ->with('error', 'An error occurred while adding the payment method. Please try again.');
        }
    }

    /**
     * Update an existing payment method.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePaymentMethod(Request $request, $id)
    {
        // Log the request data for debugging
        Log::info('Update Payment Method Request Data:', $request->all());

        try {
            // Find the payment method and ensure it belongs to the authenticated user
            $paymentMethod = PaymentMethod::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $validator = Validator::make($request->all(), [
                'type' => 'required|string|in:credit_card,paypal',
                'name' => 'required|string|max:255',
                'card_type' => 'required_if:type,credit_card|string|in:visa,mastercard',
                'expiry_month' => 'required_if:type,credit_card|string|max:2',
                'expiry_year' => 'required_if:type,credit_card|string|max:4',
                'email' => 'required_if:type,paypal|email|max:255',
                'is_default' => 'sometimes|boolean',
            ]);

            if ($validator->fails()) {
                Log::warning('Payment method validation failed', [
                    'errors' => $validator->errors()->toArray(),
                    'input' => $request->all()
                ]);

                return redirect()->route('vendor.settings.payment')
                    ->withErrors($validator)
                    ->withInput();
            }

            DB::beginTransaction();

            $isDefault = $request->has('is_default');

            // If this is the default payment method, unset any existing default
            if ($isDefault && !$paymentMethod->is_default) {
                Auth::user()->paymentMethods()->where('id', '!=', $id)->update(['is_default' => false]);
            }

            // Update the payment method
            // Map form fields to database fields
            $paymentMethod->payment_type = $request->type;
            $paymentMethod->name = $request->name;

            // Set credit card specific fields
            if ($request->type === 'credit_card') {
                $paymentMethod->card_brand = $request->card_type;
                $paymentMethod->expiry_month = $request->expiry_month;
                $paymentMethod->expiry_year = $request->expiry_year;

                // Clear PayPal fields if switching from PayPal to credit card
                $paymentMethod->billing_email = null;
            }
            // Set PayPal specific fields
            else if ($request->type === 'paypal') {
                $paymentMethod->billing_email = $request->email;

                // Clear credit card fields if switching from credit card to PayPal
                $paymentMethod->card_brand = null;
                $paymentMethod->expiry_month = null;
                $paymentMethod->expiry_year = null;
            }

            $paymentMethod->is_default = $isDefault;
            $paymentMethod->save();

            DB::commit();

            Log::info('Payment method updated successfully', [
                'id' => $id,
                'user_id' => Auth::id(),
                'type' => $request->type
            ]);

            return redirect()->route('vendor.settings.payment')
                ->with('success', 'Payment method updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating payment method', [
                'id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('vendor.settings.payment')
                ->with('error', 'An error occurred while updating the payment method. Please try again.');
        }
    }

    /**
     * Remove a payment method.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removePaymentMethod($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);

        try {
            DB::beginTransaction();

            // If this is the default payment method, find another one to make default
            if ($paymentMethod->is_default) {
                $newDefault = Auth::user()->paymentMethods()->where('id', '!=', $id)->first();
                if ($newDefault) {
                    $newDefault->is_default = true;
                    $newDefault->save();
                }
            }

            // Delete the payment method
            $paymentMethod->delete();

            DB::commit();

            return redirect()->route('vendor.settings.payment')
                ->with('success', 'Payment method removed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error removing payment method: ' . $e->getMessage());

            return redirect()->route('vendor.settings.payment')
                ->with('error', 'An error occurred while removing the payment method. Please try again.');
        }
    }

    /**
     * Add a new payout method.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addPayoutMethod(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:bank_account,paypal',
            'name' => 'required|string|max:255',
            'bank_name' => 'required_if:type,bank_account|nullable|string|max:255',
            'account_number' => 'required_if:type,bank_account|nullable|string|max:255',
            'routing_number' => 'required_if:type,bank_account|nullable|string|max:255',
            'account_type' => 'required_if:type,bank_account|nullable|string|in:checking,savings',
            'email' => 'required_if:type,paypal|nullable|email|max:255',
            'is_default' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('vendor.settings.payment')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $isDefault = $request->has('is_default');

            // If this is the default payout method, unset any existing default
            if ($isDefault) {
                $user->payoutMethods()->update(['is_default' => false]);
            }

            // Create the new payout method
            $payoutMethod = new PayoutMethod($request->all());
            $payoutMethod->user_id = $user->id;
            $payoutMethod->is_default = $isDefault;

            // Set last_four for bank accounts
            if ($request->type === 'bank_account' && $request->account_number) {
                $payoutMethod->last_four = substr($request->account_number, -4);
            }

            $payoutMethod->save();

            // If this is the default payout method, update the payout preference
            if ($isDefault) {
                $payoutPreference = $user->payoutPreference;
                if ($payoutPreference) {
                    $payoutPreference->default_payout_method_id = $payoutMethod->id;
                    $payoutPreference->save();
                } else {
                    $payoutPreference = new PayoutPreference([
                        'user_id' => $user->id,
                        'default_payout_method_id' => $payoutMethod->id,
                    ]);
                    $payoutPreference->save();
                }
            }

            DB::commit();

            return redirect()->route('vendor.settings.payment')
                ->with('success', 'Payout method added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding payout method: ' . $e->getMessage());

            return redirect()->route('vendor.settings.payment')
                ->with('error', 'An error occurred while adding the payout method. Please try again.');
        }
    }

    /**
     * Update an existing payout method.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePayoutMethod(Request $request, $id)
    {
        $payoutMethod = PayoutMethod::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:bank_account,paypal',
            'name' => 'sometimes|string|max:255',
            'bank_name' => 'sometimes|string|max:255',
            'account_type' => 'sometimes|string|in:checking,savings',
            'email' => 'sometimes|email|max:255',
            'is_default' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('vendor.settings.payment')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $isDefault = $request->has('is_default');

            // If this is the default payout method, unset any existing default
            if ($isDefault && !$payoutMethod->is_default) {
                $user->payoutMethods()->where('id', '!=', $id)->update(['is_default' => false]);
            }

            // Update the payout method
            $payoutMethod->fill($request->only(['type', 'name', 'bank_name', 'account_type', 'email']));
            $payoutMethod->is_default = $isDefault;
            $payoutMethod->save();

            // If this is the default payout method, update the payout preference
            if ($isDefault) {
                $payoutPreference = $user->payoutPreference;
                if ($payoutPreference) {
                    $payoutPreference->default_payout_method_id = $payoutMethod->id;
                    $payoutPreference->save();
                } else {
                    $payoutPreference = new PayoutPreference([
                        'user_id' => $user->id,
                        'default_payout_method_id' => $payoutMethod->id,
                    ]);
                    $payoutPreference->save();
                }
            }

            DB::commit();

            return redirect()->route('vendor.settings.payment')
                ->with('success', 'Payout method updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating payout method: ' . $e->getMessage());

            return redirect()->route('vendor.settings.payment')
                ->with('error', 'An error occurred while updating the payout method. Please try again.');
        }
    }

    /**
     * Remove a payout method.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removePayoutMethod($id)
    {
        $payoutMethod = PayoutMethod::findOrFail($id);

        try {
            DB::beginTransaction();

            $user = Auth::user();

            // If this is the default payout method, find another one to make default
            if ($payoutMethod->is_default) {
                $newDefault = $user->payoutMethods()->where('id', '!=', $id)->first();
                if ($newDefault) {
                    $newDefault->is_default = true;
                    $newDefault->save();

                    // Update the payout preference
                    $payoutPreference = $user->payoutPreference;
                    if ($payoutPreference) {
                        $payoutPreference->default_payout_method_id = $newDefault->id;
                        $payoutPreference->save();
                    }
                } else {
                    // If there are no other payout methods, set the default_payout_method_id to null
                    $payoutPreference = $user->payoutPreference;
                    if ($payoutPreference) {
                        $payoutPreference->default_payout_method_id = null;
                        $payoutPreference->save();
                    }
                }
            }

            // Delete the payout method
            $payoutMethod->delete();

            DB::commit();

            return redirect()->route('vendor.settings.payment')
                ->with('success', 'Payout method removed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error removing payout method: ' . $e->getMessage());

            return redirect()->route('vendor.settings.payment')
                ->with('error', 'An error occurred while removing the payout method. Please try again.');
        }
    }

    /**
     * Update payout preferences.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePayoutPreferences(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payout_frequency' => 'required|string|in:daily,weekly,biweekly,monthly',
            'minimum_payout_amount' => 'required|numeric|min:0',
            'currency' => 'sometimes|string|size:3',
        ]);

        if ($validator->fails()) {
            return redirect()->route('vendor.settings.payment')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $payoutPreference = $user->payoutPreference;

            if ($payoutPreference) {
                // Update existing payout preference
                $payoutPreference->payout_frequency = $request->payout_frequency;
                $payoutPreference->minimum_payout_amount = $request->minimum_payout_amount;
                if ($request->has('currency')) {
                    $payoutPreference->currency = $request->currency;
                }
                $payoutPreference->save();
            } else {
                // Create new payout preference
                $payoutPreference = new PayoutPreference([
                    'user_id' => $user->id,
                    'payout_frequency' => $request->payout_frequency,
                    'minimum_payout_amount' => $request->minimum_payout_amount,
                    'currency' => $request->currency ?? 'USD',
                ]);

                // Set default payout method if one exists
                $defaultPayoutMethod = $user->payoutMethods()->where('is_default', true)->first();
                if ($defaultPayoutMethod) {
                    $payoutPreference->default_payout_method_id = $defaultPayoutMethod->id;
                }

                $payoutPreference->save();
            }

            DB::commit();

            return redirect()->route('vendor.settings.payment')
                ->with('success', 'Payout preferences updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating payout preferences: ' . $e->getMessage());

            return redirect()->route('vendor.settings.payment')
                ->with('error', 'An error occurred while updating the payout preferences. Please try again.');
        }
    }
}
