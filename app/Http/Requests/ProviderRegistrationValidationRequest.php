<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Provider;

class ProviderRegistrationValidationRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Required field validation
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'business_name' => 'required|string|min:2|max:255',
            
            // Optional fields
            'business_type' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'delivery_capability' => 'boolean',
            'delivery_fee_by_emirate' => 'nullable|string', // JSON string from frontend
            'delivery_fees' => 'nullable|array',
            'stock_locations' => 'nullable|array',
            'stock_locations.*.name' => 'required_with:stock_locations|string|max:255',
            'stock_locations.*.address' => 'required_with:stock_locations|string|max:500',
            'stock_locations.*.latitude' => 'required_with:stock_locations|numeric|between:-90,90',
            'stock_locations.*.longitude' => 'required_with:stock_locations|numeric|between:-180,180',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Phone format validation (UAE format) - must be first
            $this->validatePhoneFormat($validator);

            // Full name uniqueness check
            $this->validateFullNameUniqueness($validator);

            // Business name uniqueness check
            $this->validateBusinessNameUniqueness($validator);

            // Email registration status checks
            $this->validateEmailRegistrationStatus($validator);

            // Phone registration status checks
            $this->validatePhoneRegistrationStatus($validator);

            // Delivery fees validation
            $this->validateDeliveryFees($validator);
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Normalize phone number before validation
        if ($this->has('phone')) {
            $this->merge([
                'phone' => $this->normalizePhoneNumber($this->input('phone'))
            ]);
        }

        // Parse delivery fees JSON string if provided
        if ($this->has('delivery_fee_by_emirate') && is_string($this->input('delivery_fee_by_emirate'))) {
            $deliveryFeesJson = $this->input('delivery_fee_by_emirate');
            $deliveryFees = json_decode($deliveryFeesJson, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($deliveryFees)) {
                $this->merge([
                    'delivery_fees' => $deliveryFees
                ]);
            }
        }
    }

    /**
     * Validate full name uniqueness in users table.
     */
    protected function validateFullNameUniqueness($validator)
    {
        $fullName = $this->input('name');

        if ($fullName) {
            $existingUser = User::where('name', $fullName)->first();

            if ($existingUser) {
                $validator->errors()->add('name', 'Full name is already taken');
            }
        }
    }

    /**
     * Validate business name uniqueness in providers table.
     */
    protected function validateBusinessNameUniqueness($validator)
    {
        $businessName = $this->input('business_name');

        if ($businessName) {
            $existingProvider = Provider::where('business_name', $businessName)->first();

            if ($existingProvider) {
                $validator->errors()->add('business_name', 'Business name is already taken');
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
                        'You have a registered company with this email you cannot create two accounts with the same email');
                }
                
                // Check if email has registration step 'license_completed'
                if ($existingUser->registration_step === 'license_completed') {
                    $validator->errors()->add('email', 
                        'You have a submit company information wait for admin approval you will receive an email or a call from our support team , Thank you for your patience.');
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
            // Normalize phone number for comparison
            $normalizedPhone = $this->normalizePhoneNumber($phone);
            
            $existingUser = User::where('phone', $normalizedPhone)
                               ->orWhere('phone', $phone)
                               ->first();
            
            if ($existingUser && $existingUser->registration_step === 'verified') {
                $validator->errors()->add('phone', 
                    'You have a registered company with this phone you cannot create two accounts with the same phone');
            }
        }
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
     * Validate delivery fees structure and values.
     */
    protected function validateDeliveryFees($validator)
    {
        $deliveryCapability = $this->input('delivery_capability');
        $deliveryFees = $this->input('delivery_fees');

        // If delivery capability is enabled, validate delivery fees
        if ($deliveryCapability && $deliveryFees) {
            if (!is_array($deliveryFees)) {
                $validator->errors()->add('delivery_fees', 'Delivery fees must be an array');
                return;
            }

            $validEmirates = [
                'abu_dhabi', 'dubai', 'sharjah', 'ajman', 'uaq', 'rak', 'fujairah'
            ];

            foreach ($deliveryFees as $emirate => $fee) {
                // Validate emirate name
                if (!in_array($emirate, $validEmirates)) {
                    $validator->errors()->add('delivery_fees', "Invalid emirate: {$emirate}");
                }

                // Validate fee value
                if (!is_numeric($fee) || $fee < 0) {
                    $validator->errors()->add('delivery_fees', "Delivery fee for {$emirate} must be a positive number");
                }
            }
        }
    }

    /**
     * Normalize phone number for database storage and comparison.
     */
    protected function normalizePhoneNumber($phone)
    {
        // Remove spaces and normalize UAE phone format
        $cleanPhone = preg_replace('/\s/', '', $phone);

        // Convert to standard format (+971XXXXXXXXX)
        if (preg_match('/^0([0-9]{9})$/', $cleanPhone, $matches)) {
            // 0XXXXXXXXX -> +971XXXXXXXXX
            return '+971' . $matches[1];
        } elseif (preg_match('/^971([0-9]{9})$/', $cleanPhone, $matches)) {
            // 971XXXXXXXXX -> +971XXXXXXXXX
            return '+971' . $matches[1];
        } elseif (preg_match('/^\+971([0-9]{9})$/', $cleanPhone, $matches)) {
            // +971XXXXXXXXX -> +971XXXXXXXXX (already normalized)
            return '+971' . $matches[1];
        } elseif (preg_match('/^([0-9]{9})$/', $cleanPhone, $matches)) {
            // XXXXXXXXX -> +971XXXXXXXXX (9 digits without prefix)
            return '+971' . $matches[1];
        }

        return $cleanPhone;
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Company/supplier name is required',
            'name.min' => 'Company/supplier name must be at least 2 characters',
            'name.max' => 'Company/supplier name cannot exceed 255 characters',
            
            'email.required' => 'Email address is required',
            'email.email' => 'Please enter a valid email address',
            'email.max' => 'Email address cannot exceed 255 characters',
            
            'phone.required' => 'Phone number is required',
            'phone.max' => 'Phone number cannot exceed 20 characters',
            
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',
            
            'business_name.required' => 'Business name is required',
            'business_name.min' => 'Business name must be at least 2 characters',
            'business_name.max' => 'Business name cannot exceed 255 characters',
            
            'business_type.max' => 'Business type cannot exceed 255 characters',
            'description.max' => 'Description cannot exceed 1000 characters',
            
            'logo.image' => 'Logo must be an image file',
            'logo.mimes' => 'Logo must be a JPEG, PNG, JPG, or GIF file',
            'logo.max' => 'Logo file size cannot exceed 5MB',
            
            'delivery_capability.boolean' => 'Delivery capability must be true or false',

            'delivery_fees.array' => 'Delivery fees must be an array',
            'delivery_fee_by_emirate.string' => 'Delivery fees data must be a valid JSON string',

            'stock_locations.array' => 'Stock locations must be an array',
            'stock_locations.*.name.required_with' => 'Stock location name is required',
            'stock_locations.*.name.max' => 'Stock location name cannot exceed 255 characters',
            'stock_locations.*.address.required_with' => 'Stock location address is required',
            'stock_locations.*.address.max' => 'Stock location address cannot exceed 500 characters',
            'stock_locations.*.latitude.required_with' => 'Stock location latitude is required',
            'stock_locations.*.latitude.numeric' => 'Stock location latitude must be a number',
            'stock_locations.*.latitude.between' => 'Stock location latitude must be between -90 and 90',
            'stock_locations.*.longitude.required_with' => 'Stock location longitude is required',
            'stock_locations.*.longitude.numeric' => 'Stock location longitude must be a number',
            'stock_locations.*.longitude.between' => 'Stock location longitude must be between -180 and 180',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'company/supplier name',
            'email' => 'email address',
            'phone' => 'phone number',
            'business_name' => 'business name',
            'business_type' => 'business type',
            'logo' => 'logo',
            'delivery_capability' => 'delivery capability',
            'stock_locations.*.name' => 'stock location name',
            'stock_locations.*.address' => 'stock location address',
            'stock_locations.*.latitude' => 'stock location latitude',
            'stock_locations.*.longitude' => 'stock location longitude',
        ];
    }

    /**
     * Check if user should skip email verification step.
     */
    public function shouldSkipEmailVerification(): bool
    {
        $email = $this->input('email');
        $phone = $this->input('phone');
        
        if ($email && $phone) {
            $existingUser = User::where('email', $email)->first();
            
            if ($existingUser && !$existingUser->phone_verified) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if user should skip both email and phone verification steps.
     */
    public function shouldSkipBothVerifications(): bool
    {
        $email = $this->input('email');
        $phone = $this->input('phone');
        
        if ($email && $phone) {
            $normalizedPhone = $this->normalizePhoneNumber($phone);
            
            $emailUser = User::where('email', $email)->first();
            $phoneUser = User::where('phone', $normalizedPhone)->orWhere('phone', $phone)->first();
            
            if ($emailUser && $phoneUser && 
                $emailUser->email_verified_at && 
                $phoneUser->phone_verified &&
                !in_array($emailUser->registration_step, ['verified', 'license_completed'])) {
                return true;
            }
        }
        
        return false;
    }
}
