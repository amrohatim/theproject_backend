@extends('layouts.dashboard')

@section('title', __('vendor.security_settings'))
@section('page-title', __('vendor.security_settings'))

@section('content')
<div class="container mx-auto" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('vendor.security_settings') }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('vendor.security_settings_description') }}</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('vendor.settings') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-right ml-2' : 'fa-arrow-left mr-2' }} px-2"></i> {{ __('vendor.back_to_settings') }}
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if($sessionDriver !== 'database')
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded mb-6" role="alert">
            <span class="block sm:inline">{{ __('vendor.session_listing_requires_database') }}</span>
        </div>
    @endif

    <!-- Change Password -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700 mb-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('vendor.change_password') }}</h3>
        <form action="{{ route('vendor.settings.security.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('vendor.current_password') }}</label>
                    <div class="relative mt-1">
                        <input type="password" name="current_password" id="current_password" class="focus:ring-indigo-500 p-2 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md {{ app()->getLocale() === 'ar' ? 'pl-10' : 'pr-10' }}">
                        <button type="button" class="absolute inset-y-0 {{ app()->getLocale() === 'ar' ? 'left-0 pl-3' : 'right-0 pr-3' }} flex items-center" onclick="togglePasswordVisibility('current_password')">
                            <i id="current_password_icon" class="fas fa-eye text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 cursor-pointer"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('vendor.new_password') }}</label>
                    <div class="relative mt-1">
                        <input type="password" name="password" id="password" class="p-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md {{ app()->getLocale() === 'ar' ? 'pl-10' : 'pr-10' }}">
                        <button type="button" class="absolute inset-y-0 {{ app()->getLocale() === 'ar' ? 'left-0 pl-3' : 'right-0 pr-3' }} flex items-center" onclick="togglePasswordVisibility('password')">
                            <i id="password_icon" class="fas fa-eye text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 cursor-pointer"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm New Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('vendor.confirm_password') }}</label>
                    <div class="relative mt-1">
                        <input type="password" name="password_confirmation" id="password_confirmation" class="p-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md {{ app()->getLocale() === 'ar' ? 'pl-10' : 'pr-10' }}">
                        <button type="button" class="absolute inset-y-0 {{ app()->getLocale() === 'ar' ? 'left-0 pl-3' : 'right-0 pr-3' }} flex items-center" onclick="togglePasswordVisibility('password_confirmation')">
                            <i id="password_confirmation_icon" class="fas fa-eye text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 cursor-pointer"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-save {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i> {{ __('vendor.update_password') }}
                </button>
            </div>
        </form>
    </div>

    {{-- <!-- Two-Factor Authentication -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700 mb-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Two-Factor Authentication</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Add an extra layer of security to your account by enabling two-factor authentication.</p>
        
        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">Two-Factor Authentication</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Protect your account with an additional security layer.</p>
                </div>
                <div class="flex items-center">
                    <span class="mr-3 text-sm font-medium text-gray-900 dark:text-white">Disabled</span>
                    <button type="button" class="bg-gray-200 dark:bg-gray-600 relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" role="switch" aria-checked="false">
                        <span class="translate-x-0 pointer-events-none relative inline-block h-5 w-5 rounded-full bg-white dark:bg-gray-800 shadow transform ring-0 transition ease-in-out duration-200">
                            <span class="absolute inset-0 h-full w-full flex items-center justify-center transition-opacity opacity-100 ease-in duration-200">
                                <i class="fas fa-times text-gray-400 text-xs"></i>
                            </span>
                            <span class="absolute inset-0 h-full w-full flex items-center justify-center transition-opacity opacity-0 ease-out duration-100">
                                <i class="fas fa-check text-indigo-600 text-xs"></i>
                            </span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <button type="button" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-shield-alt mr-2"></i> Enable Two-Factor Authentication
            </button>
        </div>
    </div> --}}

    <!-- Login Sessions -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700 mb-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('vendor.login_sessions') }}</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('vendor.manage_and_logout_other_sessions') }}</p>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('vendor.device') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('vendor.ip_address') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('vendor.last_activity') }}</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('vendor.action') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($activeSessions as $activeSession)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <i class="fas {{ $activeSession->device_icon }} text-gray-400 mr-2"></i>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $activeSession->device_label }}</div>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $activeSession->is_current ? __('vendor.current_device') : __('vendor.other_device') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">{{ $activeSession->ip_address }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $activeSession->is_current ? __('vendor.now') : $activeSession->last_activity_human }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if($activeSession->is_current)
                                    <span class="text-gray-400 dark:text-gray-500">{{ __('vendor.this_device') }}</span>
                                @else
                                    <span class="text-green-600 dark:text-green-400">{{ __('vendor.active') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                {{ __('vendor.no_active_sessions_found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            <form method="POST" action="{{ route('vendor.settings.security.sessions.logout-others') }}">
                @csrf
                <button
                    type="submit"
                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150"
                    {{ $sessionDriver !== 'database' || $activeSessions->count() <= 1 ? 'disabled' : '' }}
                >
                    <i class="fas fa-sign-out-alt mr-2"></i> {{ __('vendor.log_out_other_sessions') }}
                </button>
            </form>
        </div>
    </div>

    {{-- <!-- API Tokens -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">API Tokens</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Create and manage API tokens to interact with our API.</p>
        
        <div class="flex justify-end mb-4">
            <button type="button" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i> Create API Token
            </button>
        </div>
        
        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">No API tokens have been created yet.</p>
        </div>
    </div> --}}
</div>

<script>
/**
 * Toggle password visibility for password input fields
 * @param {string} fieldId - The ID of the password input field
 */
function togglePasswordVisibility(fieldId) {
    const passwordInput = document.getElementById(fieldId);
    const eyeIcon = document.getElementById(fieldId + '_icon');

    if (passwordInput && eyeIcon) {
        if (passwordInput.type === 'password') {
            // Show password
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            // Hide password
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }
}

// Ensure all password fields start as hidden (security default)
document.addEventListener('DOMContentLoaded', function() {
    const passwordFields = ['current_password', 'password', 'password_confirmation'];

    passwordFields.forEach(function(fieldId) {
        const passwordInput = document.getElementById(fieldId);
        const eyeIcon = document.getElementById(fieldId + '_icon');

        if (passwordInput && eyeIcon) {
            // Ensure password is hidden by default
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    });
});
</script>
@endsection
