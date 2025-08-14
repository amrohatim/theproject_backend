@extends('layouts.dashboard')

@section('title', 'Create Service Provider')
@section('page-title', 'Create Service Provider')

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Create Service Provider</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">Add a new service provider to manage your services</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('vendor.settings.service-providers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i> Back to Service Providers
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
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Basic Information</h3>
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Full Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('name') border-red-300 @enderror">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('email') border-red-300 @enderror">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone Number</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('phone') border-red-300 @enderror">
                    @error('phone')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                    <input type="password" name="password" id="password" required
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('password') border-red-300 @enderror">
                    @error('password')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <!-- Permissions Section -->
                <div class="md:col-span-2 mt-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Permissions & Access</h3>
                </div>

                <!-- Branch Access -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Branch Access</label>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Select which branches this service provider can manage</p>
                    
                    @if($branches->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($branches as $branch)
                                <div class="flex items-center">
                                    <input type="checkbox" name="branch_ids[]" value="{{ $branch->id }}" id="branch_{{ $branch->id }}"
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                           {{ in_array($branch->id, old('branch_ids', [])) ? 'checked' : '' }}>
                                    <label for="branch_{{ $branch->id }}" class="ml-2 block text-sm text-gray-900 dark:text-white">
                                        {{ $branch->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No branches available. Please create branches first.</p>
                    @endif
                    
                    @error('branch_ids')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Service Access -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Service Access</label>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Select which services this service provider can manage</p>
                    
                    @if($services->count() > 0)
                        <div class="max-h-48 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-3">
                            <div class="grid grid-cols-1 gap-2">
                                @foreach($services as $service)
                                    <div class="flex items-center">
                                        <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" id="service_{{ $service->id }}"
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                               {{ in_array($service->id, old('service_ids', [])) ? 'checked' : '' }}>
                                        <label for="service_{{ $service->id }}" class="ml-2 block text-sm text-gray-900 dark:text-white">
                                            {{ $service->name }}
                                            <span class="text-gray-500 dark:text-gray-400">({{ $service->branch->name }})</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No services available. Please create services first.</p>
                    @endif
                    
                    @error('service_ids')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('vendor.settings.service-providers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-save mr-2"></i> Create Service Provider
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
