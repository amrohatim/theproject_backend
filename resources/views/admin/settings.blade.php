@extends('layouts.dashboard')

@section('title', 'Admin Settings')
@section('page-title', 'Admin Settings')

@section('content')
<div class="container mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Admin Settings</h2>
        <p class="mt-1 text-gray-600 dark:text-gray-400">Manage system settings and configurations</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- General Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center mb-4">
                <div class="rounded-md bg-indigo-100 dark:bg-indigo-900 p-3 mr-4">
                    <i class="fas fa-cog text-indigo-600 dark:text-indigo-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">General Settings</h3>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Configure general system settings like site name, logo, and contact information.</p>
            <a href="{{ route('admin.settings.general') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                Manage
            </a>
        </div>

        <!-- Payment Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center mb-4">
                <div class="rounded-md bg-green-100 dark:bg-green-900 p-3 mr-4">
                    <i class="fas fa-credit-card text-green-600 dark:text-green-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Payment Settings</h3>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Configure payment gateways, transaction fees, and payment methods.</p>
            <a href="{{ route('admin.settings.payment') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                Manage
            </a>
        </div>

        <!-- Email Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center mb-4">
                <div class="rounded-md bg-blue-100 dark:bg-blue-900 p-3 mr-4">
                    <i class="fas fa-envelope text-blue-600 dark:text-blue-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Email Settings</h3>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Configure email templates, SMTP settings, and notification preferences.</p>
            <a href="{{ route('admin.settings.email') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                Manage
            </a>
        </div>

        <!-- User Roles & Permissions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center mb-4">
                <div class="rounded-md bg-purple-100 dark:bg-purple-900 p-3 mr-4">
                    <i class="fas fa-user-shield text-purple-600 dark:text-purple-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">User Roles & Permissions</h3>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Manage user roles, permissions, and access control settings.</p>
            <a href="{{ route('admin.settings.roles') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-900 focus:outline-none focus:border-purple-900 focus:ring ring-purple-300 disabled:opacity-25 transition ease-in-out duration-150">
                Manage
            </a>
        </div>

        <!-- Commission Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center mb-4">
                <div class="rounded-md bg-yellow-100 dark:bg-yellow-900 p-3 mr-4">
                    <i class="fas fa-percentage text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Commission Settings</h3>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Configure commission rates, payout schedules, and vendor fee structures.</p>
            <a href="{{ route('admin.settings.commission') }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:border-yellow-900 focus:ring ring-yellow-300 disabled:opacity-25 transition ease-in-out duration-150">
                Manage
            </a>
        </div>

        <!-- System Maintenance -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center mb-4">
                <div class="rounded-md bg-red-100 dark:bg-red-900 p-3 mr-4">
                    <i class="fas fa-tools text-red-600 dark:text-red-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">System Maintenance</h3>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Manage system backups, updates, and maintenance tasks.</p>
            <a href="{{ route('admin.settings.maintenance') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                Manage
            </a>
        </div>
    </div>

    <!-- System Information -->
    <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">System Information</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">PHP Version</div>
                <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ phpversion() }}</div>
            </div>
            
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Laravel Version</div>
                <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ app()->version() }}</div>
            </div>
            
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Environment</div>
                <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ app()->environment() }}</div>
            </div>
            
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Server Time</div>
                <div class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ now()->format('Y-m-d H:i:s') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
