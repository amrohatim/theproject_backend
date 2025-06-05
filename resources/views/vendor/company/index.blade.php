@extends('layouts.dashboard')

@section('title', 'Company Management')
@section('page-title', 'Company Management')

@section('content')
<div class="container mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Company Management</h2>
        <p class="mt-1 text-gray-600 dark:text-gray-400">Manage your company information and settings</p>
    </div>

    <!-- Company Information -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
        <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1 p-6 border-r border-gray-200 dark:border-gray-700">
                <div class="px-4 sm:px-0">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Company Information</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Basic information about your company that will be displayed to customers.
                    </p>
                </div>
            </div>
            <div class="mt-5 md:mt-0 md:col-span-2 p-6">
                <form action="{{ route('vendor.company.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6 sm:col-span-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Company Name</label>
                            <input type="text" name="name" id="name" value="{{ $company->name ?? old('name') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="business_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Business Type</label>
                            <select id="business_type" name="business_type" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Select Business Type</option>
                                <option value="retail" {{ ($company->business_type ?? old('business_type')) == 'retail' ? 'selected' : '' }}>Retail</option>
                                <option value="service" {{ ($company->business_type ?? old('business_type')) == 'service' ? 'selected' : '' }}>Service</option>
                                <option value="manufacturing" {{ ($company->business_type ?? old('business_type')) == 'manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                                <option value="food" {{ ($company->business_type ?? old('business_type')) == 'food' ? 'selected' : '' }}>Food & Beverage</option>
                                <option value="technology" {{ ($company->business_type ?? old('business_type')) == 'technology' ? 'selected' : '' }}>Technology</option>
                                <option value="other" {{ ($company->business_type ?? old('business_type')) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="registration_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Registration Number</label>
                            <input type="text" name="registration_number" id="registration_number" value="{{ $company->registration_number ?? old('registration_number') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>

                        <div class="col-span-6 sm:col-span-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
                            <input type="email" name="email" id="email" value="{{ $company->email ?? old('email') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone Number</label>
                            <input type="text" name="phone" id="phone" value="{{ $company->phone ?? old('phone') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="website" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Website</label>
                            <input type="text" name="website" id="website" value="{{ $company->website ?? old('website') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="can_deliver" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Delivery Capability</label>
                            <select id="can_deliver" name="can_deliver" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="1" {{ ($company->can_deliver ?? old('can_deliver', '1')) == '1' || ($company->can_deliver ?? old('can_deliver', true)) === true ? 'selected' : '' }}>Yes, we can handle our own deliveries</option>
                                <option value="0" {{ ($company->can_deliver ?? old('can_deliver')) == '0' || ($company->can_deliver ?? old('can_deliver')) === false ? 'selected' : '' }}>No, we need a third-party delivery service</option>
                            </select>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">This determines if your company can deliver products to customers directly.</p>
                        </div>

                        <div class="col-span-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                            <textarea id="description" name="description" rows="3" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">{{ $company->description ?? old('description') }}</textarea>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Brief description of your company for your customers.</p>
                        </div>

                        <div class="col-span-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Company Logo</label>
                            <div class="mt-1 flex items-center">
                                @if(isset($company) && $company->logo)
                                    @php
                                        // Check both storage URL and direct public path
                                        $storageUrl = $company->logo;
                                        $imagePath = 'images/companies/' . basename($company->logo ?? '');
                                        $fileExistsInPublic = file_exists(public_path($imagePath));
                                        $fileExistsInStorage = file_exists(storage_path('app/public/companies/' . basename($company->logo ?? '')));

                                        // Log for debugging
                                        \Illuminate\Support\Facades\Log::info('Company logo display check', [
                                            'company_id' => $company->id,
                                            'logo_path' => $company->logo,
                                            'storage_path' => storage_path('app/public/companies/' . basename($company->logo ?? '')),
                                            'public_path' => public_path($imagePath),
                                            'exists_in_storage' => $fileExistsInStorage,
                                            'exists_in_public' => $fileExistsInPublic
                                        ]);
                                    @endphp
                                    <div class="mr-4">
                                        @if($fileExistsInPublic)
                                            <img src="/{{ $imagePath }}" alt="{{ $company->name }}" class="h-16 w-16 object-cover rounded-md">
                                        @elseif($fileExistsInStorage)
                                            <img src="{{ $storageUrl }}" alt="{{ $company->name }}" class="h-16 w-16 object-cover rounded-md">
                                        @else
                                            <img src="{{ $storageUrl }}" alt="{{ $company->name }}" class="h-16 w-16 object-cover rounded-md">
                                        @endif
                                    </div>
                                @else
                                    <div class="mr-4">
                                        <div class="h-16 w-16 rounded-md bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                            <i class="fas fa-building text-gray-400"></i>
                                        </div>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                                <label for="logo" class="relative cursor-pointer bg-white dark:bg-gray-700 rounded-md font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                    <span>Upload a file</span>
                                                    <input id="logo" name="logo" type="file" class="sr-only" onchange="showFileName(this)">
                                                </label>
                                                <p class="pl-1" id="file-name">or drag and drop</p>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                PNG, JPG, GIF up to 2MB
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <i class="fas fa-save mr-2"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Address Information -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
        <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1 p-6 border-r border-gray-200 dark:border-gray-700">
                <div class="px-4 sm:px-0">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Address Information</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Your company's headquarters or main address.
                    </p>
                </div>
            </div>
            <div class="mt-5 md:mt-0 md:col-span-2 p-6">
                <form action="{{ route('vendor.company.address.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6">
                            <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Street Address</label>
                            <input type="text" name="address" id="address" value="{{ $company->address ?? old('address') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>

                        <div class="col-span-6 sm:col-span-6 lg:col-span-2">
                            <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">City</label>
                            <input type="text" name="city" id="city" value="{{ $company->city ?? old('city') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>

                        <div class="col-span-6 sm:col-span-3 lg:col-span-2">
                            <label for="state" class="block text-sm font-medium text-gray-700 dark:text-gray-300">State / Province</label>
                            <input type="text" name="state" id="state" value="{{ $company->state ?? old('state') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>

                        <div class="col-span-6 sm:col-span-3 lg:col-span-2">
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ZIP / Postal Code</label>
                            <input type="text" name="postal_code" id="postal_code" value="{{ $company->zip_code ?? old('postal_code') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Country</label>
                            <select id="country" name="country" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Select Country</option>
                                <option value="US" {{ ($company->country ?? old('country')) == 'US' ? 'selected' : '' }}>United States</option>
                                <option value="CA" {{ ($company->country ?? old('country')) == 'CA' ? 'selected' : '' }}>Canada</option>
                                <option value="UK" {{ ($company->country ?? old('country')) == 'UK' ? 'selected' : '' }}>United Kingdom</option>
                                <option value="AU" {{ ($company->country ?? old('country')) == 'AU' ? 'selected' : '' }}>Australia</option>
                                <!-- Add more countries as needed -->
                            </select>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <i class="fas fa-save mr-2"></i> Save Address
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function showFileName(input) {
        if (input.files && input.files[0]) {
            document.getElementById('file-name').textContent = input.files[0].name;

            // Preview image if possible
            var reader = new FileReader();
            reader.onload = function(e) {
                // If there's an existing image, update it, otherwise create a new one
                var existingImg = document.querySelector('.mr-4 img');
                if (existingImg) {
                    existingImg.src = e.target.result;
                } else {
                    var imgContainer = document.createElement('div');
                    imgContainer.className = 'mr-4';

                    var img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'h-16 w-16 object-cover rounded-md';
                    img.alt = 'Company Logo Preview';

                    imgContainer.appendChild(img);
                    document.querySelector('.mt-1.flex.items-center').prepend(imgContainer);
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
