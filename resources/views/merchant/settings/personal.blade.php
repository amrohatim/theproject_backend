@extends('layouts.merchant')

@section('title', __('merchant.personal_settings'))
@section('header', __('merchant.personal_settings'))

@section('content')
<!-- Main Content Container -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 personal-settings-container">
    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex">
                <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="ml-3 text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Personal Information Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('merchant.personal_information') }}</h3>
                            <p class="text-sm text-gray-500">{{ __('merchant.personal_information_subtitle') }}</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('merchant.settings.personal.update') }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Full Name -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('merchant.full_name') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $user->name) }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('name') border-red-300 @enderror"
                                   placeholder="{{ __('merchant.enter_full_name') }}">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('merchant.email_address') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="{{ old('email', $user->email) }}"
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('email') border-red-300 @enderror"
                                   placeholder="{{ __('merchant.enter_email_address') }}">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('merchant.phone_number') }}
                            </label>
                            <input type="tel"
                                   id="phone"
                                   name="phone"
                                   value="{{ old('phone', $user->phone) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('phone') border-red-300 @enderror"
                                   placeholder="{{ __('merchant.enter_phone_number') }}">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            {{ __('merchant.save_changes') }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('merchant.change_password') }}</h3>
                            <p class="text-sm text-gray-500">{{ __('merchant.change_password_subtitle') }}</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('merchant.settings.personal.update') }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Current Password -->
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('merchant.current_password') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="password"
                                   id="current_password"
                                   name="current_password"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('current_password') border-red-300 @enderror"
                                   placeholder="{{ __('merchant.enter_current_password') }}">
                            @error('current_password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- New Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('merchant.new_password') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="password"
                                       id="password"
                                       name="password"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('password') border-red-300 @enderror"
                                       placeholder="{{ __('merchant.enter_new_password') }}">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirm New Password -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('merchant.confirm_new_password') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="password"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                       placeholder="{{ __('merchant.confirm_new_password_placeholder') }}">
                                @error('password_confirmation')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Password Requirements -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-blue-900 mb-2">{{ __('merchant.password_requirements') }}:</h4>
                            <ul class="text-sm text-blue-700 space-y-1">
                                <li>• {{ __('merchant.password_min_length') }}</li>
                                <li>• {{ __('merchant.password_uppercase') }}</li>
                                <li>• {{ __('merchant.password_lowercase') }}</li>
                                <li>• {{ __('merchant.password_number') }}</li>
                                <li>• {{ __('merchant.password_special_char') }}</li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                            {{ __('merchant.update_password') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>


        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Account Information Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('merchant.account_information') }}</h3>
                            <p class="text-sm text-gray-500">{{ __('merchant.account_details_verification_status') }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    <!-- Account Type -->
                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ __('merchant.account_type') }}</p>
                            <p class="text-sm text-gray-500">{{ __('merchant.merchant') }}</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ __('merchant.merchant') }}
                        </span>
                    </div>

                    <!-- Member Since -->
                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ __('merchant.member_since') }}</p>
                            <p class="text-sm text-gray-500">{{ $user->created_at->format('M d, Y') }}</p>
                        </div>
                        <p class="text-sm text-gray-600">{{ $user->created_at->format('M d, Y') }}</p>
                    </div>

                    <!-- Email Status -->
                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ __('merchant.email_status') }}</p>
                            <p class="text-sm text-gray-500">{{ __('merchant.last_updated') }}: {{ $user->updated_at->format('M d, Y') }}</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->email_verified_at ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            @if($user->email_verified_at)
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                {{ __('merchant.verified') }}
                            @else
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                {{ __('merchant.not_verified') }}
                            @endif
                        </span>
                    </div>

                    <!-- Phone Status -->
                    <div class="flex items-center justify-between py-3">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ __('merchant.phone_status') }}</p>
                            @if($merchant)
                                <p class="text-sm text-gray-500">{{ __('merchant.merchant_status') }}</p>
                            @else
                                <p class="text-sm text-gray-500">{{ __('merchant.not_available') }}</p>
                            @endif
                        </div>
                        @if($merchant)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $merchant->is_verified ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                @if($merchant->is_verified)
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    {{ __('merchant.verified') }}
                                @else
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ __('merchant.pending') }}
                                @endif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ __('merchant.not_applicable') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('merchant.quick_actions') }}</h3>
                </div>

                <div class="p-6 space-y-3">
                    <a href="{{ route('merchant.settings.global') }}" class="flex items-center p-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        {{ __('merchant.global_settings') }}
                    </a>

                    <a href="#" class="flex items-center p-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ __('merchant.help_support') }}
                    </a>

                    <a href="#" class="flex items-center p-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        {{ __('merchant.privacy_policy') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* RTL Layout Support for Personal Settings */
[dir="rtl"] .personal-settings-container {
    direction: rtl;
    text-align: right;
}

[dir="rtl"] .personal-settings-container .form-label {
    text-align: right;
}

[dir="rtl"] .personal-settings-container .form-input {
    text-align: right;
    direction: rtl;
}

[dir="rtl"] .personal-settings-container .btn {
    direction: ltr;
}

[dir="rtl"] .personal-settings-container .flex {
    flex-direction: row-reverse;
}

[dir="rtl"] .personal-settings-container .ml-4 {
    margin-left: 0;
    margin-right: 1rem;
}

[dir="rtl"] .personal-settings-container .mr-3 {
    margin-right: 0;
    margin-left: 0.75rem;
}

[dir="rtl"] .personal-settings-container .mr-2 {
    margin-right: 0;
    margin-left: 0.5rem;
}

[dir="rtl"] .personal-settings-container .justify-end {
    justify-content: flex-start;
}

[dir="rtl"] .personal-settings-container .text-left {
    text-align: right;
}

[dir="rtl"] .personal-settings-container .border-l {
    border-left: none;
    border-right: 1px solid #e5e7eb;
}

[dir="rtl"] .personal-settings-container .pl-8 {
    padding-left: 0;
    padding-right: 2rem;
}

[dir="rtl"] .personal-settings-container .space-y-6 > * + * {
    margin-top: 1.5rem;
}

/* Quick Actions RTL */
[dir="rtl"] .quick-actions-item {
    flex-direction: row-reverse;
}

[dir="rtl"] .quick-actions-item .mr-3 {
    margin-right: 0;
    margin-left: 0.75rem;
}

/* Account Information RTL */
[dir="rtl"] .account-info-item {
    flex-direction: row-reverse;
}

[dir="rtl"] .account-info-item .justify-between {
    flex-direction: row-reverse;
}

/* Password Requirements RTL */
[dir="rtl"] .password-requirements ul {
    text-align: right;
    direction: rtl;
}

/* Form Grid RTL */
[dir="rtl"] .form-grid {
    direction: rtl;
}

[dir="rtl"] .form-grid .grid {
    direction: rtl;
}

/* Responsive RTL */
@media (max-width: 768px) {
    [dir="rtl"] .personal-settings-container {
        padding: 1rem;
    }
    
    [dir="rtl"] .personal-settings-container .lg\:grid-cols-3 {
        grid-template-columns: 1fr;
    }
    
    [dir="rtl"] .personal-settings-container .lg\:col-span-2 {
        grid-column: span 1;
    }
}
</style>
@endpush
