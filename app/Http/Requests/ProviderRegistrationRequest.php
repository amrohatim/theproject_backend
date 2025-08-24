<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProviderRegistrationRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:users,name',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stock_locations' => 'nullable|array',
            'stock_locations.*.name' => 'required_with:stock_locations|string|max:255',
            'stock_locations.*.address' => 'required_with:stock_locations|string|max:500',
            'stock_locations.*.latitude' => 'required_with:stock_locations|numeric|between:-90,90',
            'stock_locations.*.longitude' => 'required_with:stock_locations|numeric|between:-180,180',
            'delivery_capability' => 'required|boolean',
            'delivery_fee_by_emirate' => 'nullable|array',
            'delivery_fee_by_emirate.*.emirate' => 'required_with:delivery_fee_by_emirate|string|max:255',
            'delivery_fee_by_emirate.*.fee' => 'required_with:delivery_fee_by_emirate|numeric|min:0',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Full name is required.',
            'name.unique' => 'This name is already registered. Please choose a different name.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered. Please use a different email.',
            'phone.required' => 'Phone number is required.',
            'phone.unique' => 'This phone number is already registered. Please use a different number.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
            'logo.image' => 'Logo must be an image file.',
            'logo.mimes' => 'Logo must be a JPEG, PNG, JPG, or GIF file.',
            'logo.max' => 'Logo file size must not exceed 2MB.',
            'stock_locations.*.name.required_with' => 'Location name is required when adding stock locations.',
            'stock_locations.*.address.required_with' => 'Location address is required when adding stock locations.',
            'stock_locations.*.latitude.required_with' => 'Location latitude is required when adding stock locations.',
            'stock_locations.*.longitude.required_with' => 'Location longitude is required when adding stock locations.',
            'stock_locations.*.latitude.between' => 'Latitude must be between -90 and 90.',
            'stock_locations.*.longitude.between' => 'Longitude must be between -180 and 180.',
            'delivery_capability.required' => 'Please specify delivery capability.',
            'delivery_fee_by_emirate.*.emirate.required_with' => 'Emirate name is required when setting delivery fees.',
            'delivery_fee_by_emirate.*.fee.required_with' => 'Delivery fee is required when setting delivery fees.',
            'delivery_fee_by_emirate.*.fee.min' => 'Delivery fee must be a positive number.',
        ];
    }
}
