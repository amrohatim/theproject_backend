@extends('layouts.dashboard')

@section('title', __('vendor.create_service_provider'))
@section('page-title', __('vendor.create_service_provider'))

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('vendor.create_service_provider') }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('vendor.create_service_provider_description') }}</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('vendor.settings.service-providers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left {{ app()->getLocale() === 'ar' ? 'ml-2 rtl:rotate-180' : 'mr-2' }}"></i> {{ __('vendor.back_to_service_providers') }}
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
        <form method="POST" action="{{ route('vendor.settings.service-providers.store') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('vendor.basic_information') }}</h3>
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('vendor.full_name') }}</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('name') border-red-300 @enderror {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                           dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('vendor.email_address') }}</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('email') border-red-300 @enderror {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                           dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('vendor.phone_number') }}</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('phone') border-red-300 @enderror {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                           dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                    @error('phone')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('vendor.password') }}</label>
                    <input type="password" name="password" id="password" required
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('password') border-red-300 @enderror {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                           dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                    @error('password')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('vendor.confirm_password') }}</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                           dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                </div>

                <!-- Permissions Section -->
                <div class="md:col-span-2 mt-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('vendor.permissions_and_access') }}</h3>
                </div>

                <!-- Branch Access -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('vendor.branch_access') }}</label>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('vendor.branch_access_description') }}</p>

                    @if($branches->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($branches as $branch)
                                <div class="flex items-center gap-2 {{ app()->getLocale() === 'ar' ? 'flex-row-reverse' : '' }}">
                                    <input type="checkbox" name="branch_ids[]" value="{{ $branch->id }}" id="branch_{{ $branch->id }}"
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                           {{ in_array($branch->id, old('branch_ids', [])) ? 'checked' : '' }}>
                                    <label for="branch_{{ $branch->id }}" class="{{ app()->getLocale() === 'ar' ? 'mr-2' : 'ml-2' }} block text-sm text-gray-900 dark:text-white">
                                        {{ $branch->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('vendor.no_branches_available') }}</p>
                    @endif

                    @error('branch_ids')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Service Access -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('vendor.service_access') }}</label>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('vendor.service_access_description') }}</p>

                    @if($services->count() > 0)
                        <div class="max-h-48 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-3">
                            <div class="grid grid-cols-1 gap-2">
                                @foreach($services as $service)
                                    <div class="flex items-center {{ app()->getLocale() === 'ar' ? 'flex-row-reverse' : '' }}">
                                        <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" id="service_{{ $service->id }}"
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                               {{ in_array($service->id, old('service_ids', [])) ? 'checked' : '' }}>
                                        <label for="service_{{ $service->id }}" class="{{ app()->getLocale() === 'ar' ? 'mr-2' : 'ml-2' }} block text-sm text-gray-900 dark:text-white">
                                            {{ $service->name }}
                                            <span class="text-gray-500 dark:text-gray-400">({{ $service->branch->name }})</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('vendor.no_services_available') }}</p>
                    @endif

                    @error('service_ids')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex {{ app()->getLocale() === 'ar' ? 'justify-start' : 'justify-end' }} space-x-3 {{ app()->getLocale() === 'ar' ? 'rtl:space-x-reverse' : '' }}">
                <a href="{{ route('vendor.settings.service-providers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    {{ __('vendor.cancel') }}
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none disabled:opacity-25 transition ease-in-out duration-150" style="background-color: #53D2DC; border-color: #53D2DC;" onmouseover="this.style.backgroundColor='#42B8C2'" onmouseout="this.style.backgroundColor='#53D2DC'" onfocus="this.style.boxShadow='0 0 0 3px rgba(83, 210, 220, 0.3)'" onblur="this.style.boxShadow='none'">
                    <i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i> {{ __('vendor.create_service_provider_button') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add select all functionality for branches
    const branchCheckboxes = document.querySelectorAll('input[name="branch_ids[]"]');
    const serviceCheckboxes = document.querySelectorAll('input[name="service_ids[]"]');
    
    // You can add additional JavaScript functionality here if needed
});
</script>
@endsection
