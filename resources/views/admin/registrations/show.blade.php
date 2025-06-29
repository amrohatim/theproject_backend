@extends('layouts.dashboard')

@section('title', 'Registration Details')
@section('page-title', 'Registration Details')

@section('styles')
<style>
    .info-card {
        @apply bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6;
    }
    .status-badge {
        @apply px-3 py-1 rounded-full text-xs font-semibold;
    }
    .status-pending {
        @apply bg-yellow-100 text-yellow-800;
    }
    .status-approved {
        @apply bg-green-100 text-green-800;
    }
    .status-rejected {
        @apply bg-red-100 text-red-800;
    }
    .vendor-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .provider-gradient {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
</style>
@endsection

@section('content')
<div class="container mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.registrations.index') }}"
                   class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Registration Details</h2>
                    <p class="text-gray-600 dark:text-gray-400">Review {{ ucfirst($user->role) }} registration information</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <span class="status-badge {{ $user->registration_status === 'pending' ? 'status-pending' : ($user->registration_status === 'approved' ? 'status-approved' : 'status-rejected') }}">
                    {{ ucfirst($user->registration_status) }}
                </span>
                <span class="status-badge {{ $user->role === 'vendor' ? 'bg-purple-100 text-purple-800' : 'bg-pink-100 text-pink-800' }}">
                    {{ ucfirst($user->role) }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Personal Information -->
            <div class="info-card">
                <div class="flex items-center mb-6">
                    <div class="w-16 h-16 rounded-full {{ $user->role === 'vendor' ? 'vendor-gradient' : 'provider-gradient' }} flex items-center justify-center text-white mr-4">
                        <i class="fas {{ $user->role === 'vendor' ? 'fa-store' : 'fa-hands-helping' }} text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $user->name }}</h3>
                        <p class="text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
                        <p class="text-gray-600 dark:text-gray-400">{{ $user->phone }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Registration Date</label>
                        <p class="text-gray-900 dark:text-white">{{ $user->created_at->format('M d, Y \a\t H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Updated</label>
                        <p class="text-gray-900 dark:text-white">{{ $user->updated_at->format('M d, Y \a\t H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Verified</label>
                        <p class="flex items-center {{ $user->email_verified_at ? 'text-green-600' : 'text-red-600' }}">
                            <i class="fas {{ $user->email_verified_at ? 'fa-check-circle' : 'fa-times-circle' }} mr-2"></i>
                            {{ $user->email_verified_at ? $user->email_verified_at->format('M d, Y') : 'Not verified' }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone Verified</label>
                        <p class="flex items-center {{ $user->phone_verified_at ? 'text-green-600' : 'text-red-600' }}">
                            <i class="fas {{ $user->phone_verified_at ? 'fa-check-circle' : 'fa-times-circle' }} mr-2"></i>
                            {{ $user->phone_verified_at ? $user->phone_verified_at->format('M d, Y') : 'Not verified' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Business Information -->
            @if($user->role === 'vendor' && $user->company)
                <div class="info-card">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Company Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company Name</label>
                            <p class="text-gray-900 dark:text-white">{{ $user->company->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company Email</label>
                            <p class="text-gray-900 dark:text-white">{{ $user->company->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Primary Contact</label>
                            <p class="text-gray-900 dark:text-white">{{ $user->company->contact_number_1 }}</p>
                        </div>
                        @if($user->company->contact_number_2)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Secondary Contact</label>
                                <p class="text-gray-900 dark:text-white">{{ $user->company->contact_number_2 }}</p>
                            </div>
                        @endif
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address</label>
                            <p class="text-gray-900 dark:text-white">{{ $user->company->address }}</p>
                            <p class="text-gray-600 dark:text-gray-400">{{ $user->company->city }}, {{ $user->company->emirate }}</p>
                            @if($user->company->street)
                                <p class="text-gray-600 dark:text-gray-400">{{ $user->company->street }}</p>
                            @endif
                        </div>
                        @if($user->company->description)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                                <p class="text-gray-900 dark:text-white">{{ $user->company->description }}</p>
                            </div>
                        @endif
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Delivery Services</label>
                            <p class="flex items-center {{ $user->company->delivery_capability ? 'text-green-600' : 'text-gray-600' }}">
                                <i class="fas {{ $user->company->delivery_capability ? 'fa-check-circle' : 'fa-times-circle' }} mr-2"></i>
                                {{ $user->company->delivery_capability ? 'Available' : 'Not available' }}
                            </p>
                        </div>
                    </div>
                </div>
            @elseif($user->role === 'provider' && $user->provider)
                <div class="info-card">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Provider Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($user->provider->business_name)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Business Name</label>
                                <p class="text-gray-900 dark:text-white">{{ $user->provider->business_name }}</p>
                            </div>
                        @endif
                        @if($user->provider->description)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Service Description</label>
                                <p class="text-gray-900 dark:text-white">{{ $user->provider->description }}</p>
                            </div>
                        @endif
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Delivery to Vendors</label>
                            <p class="flex items-center {{ $user->provider->deliver_to_vendor_capability ? 'text-green-600' : 'text-gray-600' }}">
                                <i class="fas {{ $user->provider->deliver_to_vendor_capability ? 'fa-check-circle' : 'fa-times-circle' }} mr-2"></i>
                                {{ $user->provider->deliver_to_vendor_capability ? 'Available' : 'Not available' }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection