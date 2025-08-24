<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Models\User;

class MerchantRegistrationValidationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'emirate' => 'required|string|in:abu_dhabi,dubai,sharjah,ajman,umm_al_quwain,ras_al_khaimah,fujairah',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'store_location_lat' => 'nullable|numeric|between:-90,90',
            'store_location_lng' => 'nullable|numeric|between:-180,180',
            'store_location_address' => 'nullable|string|max:500',
            'uae_id_front' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'uae_id_back' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'delivery_capability' => 'boolean',
            'delivery_fees' => 'nullable|array',
            'delivery_fees.*' => 'nullable|numeric|min:0',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Normalize phone number to UAE format
        if ($this->has('phone')) {
            $phone = $this->input('phone');
            $normalizedPhone = $this->normalizePhoneNumber($phone);
            $this->merge(['phone' => $normalizedPhone]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Phone format validation (UAE format) - must be first
            $this->validatePhoneFormat($validator);

            // Business name uniqueness check
            $this->validateBusinessNameUniqueness($validator);

            // Email registration status checks
            $this->validateEmailRegistrationStatus($validator);

            // Phone registration status checks
            $this->validatePhoneRegistrationStatus($validator);

            // Delivery fees validation
            $this->validateDeliveryFees($validator);

            // Check for skip verification scenario
            $this->checkSkipVerificationScenario($validator);
        });
    }

    /**
     * Normalize phone number to UAE format.
     */
    private function normalizePhoneNumber($phone)
    {
        // Remove all non-digit characters
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        // Handle different input formats
        if (strlen($cleanPhone) === 9) {
            // 9 digits: assume it's without country code
            return '+971' . $cleanPhone;
        } elseif (strlen($cleanPhone) === 10 && substr($cleanPhone, 0, 1) === '0') {
            // 10 digits starting with 0: remove leading 0 and add country code
            return '+971' . substr($cleanPhone, 1);
        } elseif (strlen($cleanPhone) === 12 && substr($cleanPhone, 0, 3) === '971') {
            // 12 digits starting with 971: add + prefix
            return '+' . $cleanPhone;
        } elseif (strlen($cleanPhone) === 13 && substr($cleanPhone, 0, 4) === '971') {
            // 13 digits starting with +971: already correct format
            return $phone;
        }

        // Return as-is if format is unclear
        return $phone;
    }

    /**
     * Validate UAE phone number format.
     */
    protected function validatePhoneFormat($validator)
    {
        $phone = $this->input('phone');

        if ($phone) {
            // After normalization, phone should be in +971XXXXXXXXX format
            if (!preg_match('/^\+971[0-9]{9}$/', $phone)) {
                $validator->errors()->add('phone', 'Please enter a valid 9-digit UAE phone number');
            }
        }
    }

    /**
     * Validate business name uniqueness in users table.
     */
    protected function validateBusinessNameUniqueness($validator)
    {
        $businessName = $this->input('name');
        
        if ($businessName) {
            $existingUser = User::where('name', $businessName)->first();
            
            if ($existingUser) {
                $validator->errors()->add('name', 'Business name is already taken');
            }
        }
    }

    /**
     * Validate email registration status and business logic.
     */
    protected function validateEmailRegistrationStatus($validator)
    {
        $email = $this->input('email');
        
        if ($email) {
            $existingUser = User::where('email', $email)->first();
            
            if ($existingUser) {
                // Check if email has registration step 'verified'
                if ($existingUser->registration_step === 'verified') {
                    $validator->errors()->add('email', 
                        'You have a registered company with this email you cannot create two accounts with the same email , please log in');
                    // Set a flag to show login dialog
                    $this->merge(['show_login_dialog' => true]);
                }
                
                // Check if email has registration step 'license_completed'
                if ($existingUser->registration_step === 'license_completed') {
                    $validator->errors()->add('email', 
                        'You have a submit company information wait for admin approval you will receive an email or a call from our support team , Thank you for your patience.');
                    // Set a flag to show login dialog
                    $this->merge(['show_login_dialog' => true]);
                }
            }
        }
    }

    /**
     * Validate phone registration status and business logic.
     */
    protected function validatePhoneRegistrationStatus($validator)
    {
        $phone = $this->input('phone');
        
        if ($phone) {
            $existingUser = User::where('phone', $phone)->first();
            
            if ($existingUser) {
                // Check if phone has registration step 'verified'
                if ($existingUser->registration_step === 'verified') {
                    $validator->errors()->add('phone', 
                        'You have a registered company with this phone you cannot create two accounts with the same phone');
                    // Set a flag to show login dialog
                    $this->merge(['show_login_dialog' => true]);
                }
            }
        }
    }

    /**
     * Check for skip verification scenario.
     */
    protected function checkSkipVerificationScenario($validator)
    {
        $email = $this->input('email');
        $phone = $this->input('phone');
        
        if ($email && $phone) {
            $emailUser = User::where('email', $email)->first();
            $phoneUser = User::where('phone', $phone)->first();
            
            // Check if both email and phone exist and are verified but registration step is not 'verified' or 'license_completed'
            if ($emailUser && $phoneUser && 
                $emailUser->email_verified_at && $phoneUser->phone_verified_at &&
                !in_array($emailUser->registration_step, ['verified', 'license_completed']) &&
                !in_array($phoneUser->registration_step, ['verified', 'license_completed'])) {
                
                // Set a flag to skip verification steps
                $this->merge(['skip_verification' => true]);
            }
        }
    }

    /**
     * Validate delivery fees structure and values.
     */
    protected function validateDeliveryFees($validator)
    {
        $deliveryCapability = $this->input('delivery_capability');
        $deliveryFees = $this->input('delivery_fees');

        if ($deliveryCapability && $deliveryFees) {
            $requiredEmirates = ['dubai', 'abu_dhabi', 'sharjah', 'ajman', 'ras_al_khaimah', 'fujairah', 'umm_al_quwain'];
            
            foreach ($requiredEmirates as $emirate) {
                if (!isset($deliveryFees[$emirate]) || $deliveryFees[$emirate] === '' || $deliveryFees[$emirate] === null) {
                    $validator->errors()->add("delivery_fees.{$emirate}", 
                        'Delivery fee for ' . ucwords(str_replace('_', ' ', $emirate)) . ' is required when delivery capability is enabled');
                }
            }
        }
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Business name is required',
            'name.unique' => 'Business name is already taken',
            'email.required' => 'Email address is required',
            'email.email' => 'Please enter a valid email address',
            'phone.required' => 'Phone number is required',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',
            'uae_id_front.required' => 'UAE ID front side is required',
            'uae_id_front.image' => 'UAE ID front side must be an image',
            'uae_id_back.required' => 'UAE ID back side is required',
            'uae_id_back.image' => 'UAE ID back side must be an image',
            'logo.image' => 'Logo must be an image file',
            'logo.mimes' => 'Logo must be a JPEG, PNG, or JPG file',
            'logo.max' => 'Logo file size cannot exceed 2MB',
            'uae_id_front.mimes' => 'UAE ID front side must be a JPEG, PNG, or JPG file',
            'uae_id_front.max' => 'UAE ID front side file size cannot exceed 2MB',
            'uae_id_back.mimes' => 'UAE ID back side must be a JPEG, PNG, or JPG file',
            'uae_id_back.max' => 'UAE ID back side file size cannot exceed 2MB',
        ];
    }
}
