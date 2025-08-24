<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorCompanyInfoRequest extends FormRequest
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
            'company_name' => 'required|string|max:255|unique:companies,name',
            'contact_number_1' => 'required|string|max:20|unique:companies,phone',
            'contact_number_2' => 'nullable|string|max:20',
            'company_email' => 'required|string|email|max:255|unique:companies,email',
            'emirate' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'delivery_capability' => 'required|boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string|max:1000',
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
            'company_name.required' => 'Company name is required.',
            'company_name.unique' => 'This company name is already registered. Please choose a different name.',
            'contact_number_1.required' => 'Primary contact number is required.',
            'contact_number_1.unique' => 'This contact number is already registered.',
            'company_email.required' => 'Company email is required.',
            'company_email.email' => 'Please enter a valid company email address.',
            'company_email.unique' => 'This company email is already registered.',
            'emirate.required' => 'Emirate is required.',
            'city.required' => 'City is required.',
            'street.required' => 'Street address is required.',
            'delivery_capability.required' => 'Please specify delivery capability.',
            'logo.image' => 'Logo must be an image file.',
            'logo.mimes' => 'Logo must be a JPEG, PNG, JPG, or GIF file.',
            'logo.max' => 'Logo file size must not exceed 2MB.',
            'description.max' => 'Description must not exceed 1000 characters.',
        ];
    }
}
