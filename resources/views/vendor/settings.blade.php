@extends('layouts.dashboard')

@section('title', 'Vendor Settings')
@section('page-title', 'Vendor Settings')

@section('content')
<div class="container mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Vendor Settings</h2>
        <p class="mt-1 text-gray-600 dark:text-gray-400">Manage your account settings and preferences</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Profile Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center mb-4">
                <div class="rounded-md bg-indigo-100 dark:bg-indigo-900 p-3 mr-4">
                    <i class="fas fa-user text-indigo-600 dark:text-indigo-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Profile Settings</h3>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Update your personal information and profile picture.</p>
            <a href="{{ route('vendor.settings.profile') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                Manage
            </a>
        </div>

        <!-- Security Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center mb-4">
                <div class="rounded-md bg-green-100 dark:bg-green-900 p-3 mr-4">
                    <i class="fas fa-lock text-green-600 dark:text-green-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Security Settings</h3>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Update your password and security preferences.</p>
            <a href="{{ route('vendor.settings.security') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                Manage
            </a>
        </div>

        <!-- Notification Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center mb-4">
                <div class="rounded-md bg-blue-100 dark:bg-blue-900 p-3 mr-4">
                    <i class="fas fa-bell text-blue-600 dark:text-blue-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Notification Settings</h3>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Configure email and push notification preferences.</p>
            <a href="{{ route('vendor.settings.notifications') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                Manage
            </a>
        </div>

        <!-- Payment Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center mb-4">
                <div class="rounded-md bg-purple-100 dark:bg-purple-900 p-3 mr-4">
                    <i class="fas fa-credit-card text-purple-600 dark:text-purple-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Payment Settings</h3>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Manage your payment methods and payout preferences.</p>
            <a href="{{ route('vendor.settings.payment') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-900 focus:outline-none focus:border-purple-900 focus:ring ring-purple-300 disabled:opacity-25 transition ease-in-out duration-150">
                Manage
            </a>
        </div>

        <!-- Business Hours -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center mb-4">
                <div class="rounded-md bg-yellow-100 dark:bg-yellow-900 p-3 mr-4">
                    <i class="fas fa-clock text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Business Hours</h3>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Set your business hours and availability for bookings.</p>
            <a href="{{ route('vendor.settings.hours') }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:border-yellow-900 focus:ring ring-yellow-300 disabled:opacity-25 transition ease-in-out duration-150">
                Manage
            </a>
        </div>

        <!-- Tax Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center mb-4">
                <div class="rounded-md bg-red-100 dark:bg-red-900 p-3 mr-4">
                    <i class="fas fa-file-invoice-dollar text-red-600 dark:text-red-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Tax Settings</h3>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Configure tax rates and tax-related information.</p>
            <a href="{{ route('vendor.settings.tax') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                Manage
            </a>
        </div>
    </div>

    <!-- Account Information -->
    <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Account Information</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Account Type</div>
                <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ ucfirst(auth()->user()->role) }}</div>
            </div>
            
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Member Since</div>
                <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ auth()->user()->created_at->format('M d, Y') }}</div>
            </div>
            
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Login</div>
                <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ auth()->user()->last_login_at ? auth()->user()->last_login_at->format('M d, Y H:i') : 'N/A' }}</div>
            </div>
            
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Account Status</div>
                <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        @if(auth()->user()->status == 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                        @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                        {{ ucfirst(auth()->user()->status ?? 'active') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-red-200 dark:border-red-700">
        <h3 class="text-lg font-medium text-red-600 dark:text-red-400 mb-4">Danger Zone</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">These actions are irreversible. Please proceed with caution.</p>
        
        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-red-50 dark:bg-red-900/20 rounded-md">
                <div>
                    <h4 class="text-sm font-medium text-red-800 dark:text-red-300">Deactivate Account</h4>
                    <p class="text-xs text-red-600 dark:text-red-400">Temporarily disable your account. You can reactivate it later.</p>
                </div>
                <button type="button" onclick="confirmDeactivate()" class="inline-flex items-center px-3 py-1.5 border border-red-300 dark:border-red-700 rounded-md text-xs font-medium text-red-700 dark:text-red-300 bg-white dark:bg-gray-800 hover:bg-red-50 dark:hover:bg-red-900/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Deactivate
                </button>
            </div>
            
            <div class="flex items-center justify-between p-4 bg-red-50 dark:bg-red-900/20 rounded-md">
                <div>
                    <h4 class="text-sm font-medium text-red-800 dark:text-red-300">Delete Account</h4>
                    <p class="text-xs text-red-600 dark:text-red-400">Permanently delete your account and all associated data.</p>
                </div>
                <button type="button" onclick="confirmDelete()" class="inline-flex items-center px-3 py-1.5 border border-red-300 dark:border-red-700 rounded-md text-xs font-medium text-red-700 dark:text-red-300 bg-white dark:bg-gray-800 hover:bg-red-50 dark:hover:bg-red-900/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDeactivate() {
        if (confirm('Are you sure you want to deactivate your account? You can reactivate it later.')) {
            // Submit form to deactivate account
            window.location.href = "{{ route('vendor.settings.deactivate') }}";
        }
    }
    
    function confirmDelete() {
        if (confirm('Are you sure you want to delete your account? This action cannot be undone and all your data will be permanently deleted.')) {
            // Submit form to delete account
            window.location.href = "{{ route('vendor.settings.delete') }}";
        }
    }
</script>
@endsection
