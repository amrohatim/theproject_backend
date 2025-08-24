@extends('layouts.dashboard')

@section('title', __('vendor.edit_products_manager'))
@section('page-title', __('vendor.edit_products_manager'))

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('vendor.edit_products_manager') }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('vendor.edit_products_manager_description') }}</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('vendor.settings.products-managers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left {{ app()->getLocale() === 'ar' ? 'ml-2 rtl:rotate-180' : 'mr-2' }}"></i> {{ __('vendor.back_to_products_managers') }}
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
        <form method="POST" action="{{ route('vendor.settings.products-managers.update', $productsManager) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('vendor.basic_information') }}</h3>
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('vendor.full_name') }}</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $productsManager->user->name) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('name') border-red-300 @enderror {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                           dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('vendor.email_address') }}</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $productsManager->user->email) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('email') border-red-300 @enderror {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                           dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('vendor.phone_number') }}</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $productsManager->user->phone) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('phone') border-red-300 @enderror {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                           dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                    @error('phone')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('vendor.account_status') }}</label>
                    <select name="status" id="status" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('status') border-red-300 @enderror {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                            dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                        <option value="active" {{ old('status', $productsManager->user->status) === 'active' ? 'selected' : '' }}>{{ __('vendor.active') }}</option>
                        <option value="inactive" {{ old('status', $productsManager->user->status) === 'inactive' ? 'selected' : '' }}>{{ __('vendor.inactive') }}</option>
                    </select>
                    @error('status')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('vendor.new_password') }}</label>
                    <input type="password" name="password" id="password"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('password') border-red-300 @enderror {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                           dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('vendor.new_password_help') }}</p>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('vendor.confirm_password') }}</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}"
                           dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                </div>

                <!-- Access Information -->
                <div class="md:col-span-2 mt-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('vendor.access_and_permissions') }}</h3>

                    <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-4">
                        <div class="flex items-start {{ app()->getLocale() === 'ar' ? 'flex-row-reverse' : '' }}">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-orange-600 dark:text-orange-400 mt-1"></i>
                            </div>
                            <div class="{{ app()->getLocale() === 'ar' ? 'mr-3' : 'ml-3' }}">
                                <h4 class="text-sm font-medium text-orange-800 dark:text-orange-200 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('vendor.products_manager_access') }}</h4>
                                <div class="mt-2 text-sm text-orange-700 dark:text-orange-300 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                                    <p>{{ __('vendor.this_products_manager_has_access') }}</p>
                                    <ul class="mt-2 list-disc {{ app()->getLocale() === 'ar' ? 'list-inside text-right' : 'list-inside' }} space-y-1">
                                        <li>{{ __('vendor.all_company_products_access') }}</li>
                                        <li>{{ __('vendor.add_products_any_branch') }}</li>
                                        <li>{{ __('vendor.update_product_information') }}</li>
                                        <li>{{ __('vendor.manage_product_categories') }}</li>
                                        <li>{{ __('vendor.update_order_statuses') }}</li>
                                        <li>{{ __('vendor.view_product_analytics') }}</li>
                                    </ul>
                                    <p class="mt-3 font-medium">{{ __('vendor.access_permissions_auto_managed') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex {{ app()->getLocale() === 'ar' ? 'justify-start' : 'justify-end' }} space-x-3 {{ app()->getLocale() === 'ar' ? 'rtl:space-x-reverse' : '' }}">
                <a href="{{ route('vendor.settings.products-managers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    {{ __('vendor.cancel') }}
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 active:bg-orange-900 focus:outline-none focus:border-orange-900 focus:ring ring-orange-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i> {{ __('vendor.update_products_manager_button') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
