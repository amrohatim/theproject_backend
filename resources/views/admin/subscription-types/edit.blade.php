@extends('layouts.dashboard')

@section('title', 'Edit Subscription Plan')
@section('page-title', 'Edit Subscription Plan')

@section('content')
<div class="container mx-auto">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Subscription Plan</h2>
                <p class="mt-1 text-gray-600 dark:text-gray-400">Update subscription plan details</p>
            </div>
            <div>
                <a href="{{ route('admin.subscription-types.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Error!</strong>
        <ul class="mt-2 list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Error!</strong>
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <form action="{{ route('admin.subscription-types.update', $subscriptionType) }}" method="POST" class="p-6" id="subscriptionForm">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- User Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        User Type <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <select name="type" 
                                id="type" 
                                class="focus:ring-indigo-500 border p-2 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('type') border-red-500 @enderror" 
                                required>
                            <option value="">Select User Type</option>
                            <option value="vendor" {{ old('type', $subscriptionType->type) == 'vendor' ? 'selected' : '' }}>Vendor</option>
                            <option value="merchant" {{ old('type', $subscriptionType->type) == 'merchant' ? 'selected' : '' }}>Merchant</option>
                            <option value="provider" {{ old('type', $subscriptionType->type) == 'provider' ? 'selected' : '' }}>Provider</option>
                        </select>
                    </div>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Select the user type for this subscription plan
                    </p>
                </div>

                <!-- Period -->
                <div>
                    <label for="period" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Billing Period <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <select name="period" 
                                id="period" 
                                class="focus:ring-indigo-500 p-2 border focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('period') border-red-500 @enderror" 
                                required>
                            <option value="">Select Billing Period</option>
                            <option value="monthly" {{ old('period', $subscriptionType->period) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="yearly" {{ old('period', $subscriptionType->period) == 'yearly' ? 'selected' : '' }}>Yearly</option>
                        </select>
                    </div>
                    @error('period')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Select the billing period for this subscription plan
                    </p>
                </div>

                <!-- Charge -->
                <div>
                    <label for="charge" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Subscription Charge (AED) <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">AED</span>
                        </div>
                        <input type="number" 
                               name="charge" 
                               id="charge" 
                               value="{{ old('charge', $subscriptionType->charge) }}"
                               step="0.01"
                               min="0"
                               max="999999.99"
                               class="focus:ring-indigo-500 pl-3 focus:border-indigo-500 block w-full pl-12 pr-12 shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('charge') border-red-500 @enderror" 
                               placeholder="0.00"
                               required>
                    </div>
                    @error('charge')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Enter the subscription amount to be paid (e.g., 99.00)
                    </p>
                </div>

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Plan Title
                    </label>
                    <div class="mt-1">
                        <input type="text" 
                               name="title" 
                               id="title" 
                               value="{{ old('title', $subscriptionType->title) }}"
                               class="focus:ring-indigo-500 pl-3 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('title') border-red-500 @enderror" 
                               placeholder="e.g., Basic Plan, Premium Plan">
                    </div>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Optional: Enter a descriptive title for this subscription plan
                    </p>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Plan Description
                    </label>
                    <div class="mt-1">
                        <textarea name="description" 
                                  id="description" 
                                  rows="4"
                                  class="focus:ring-indigo-500 p-3 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('description') border-red-500 @enderror" 
                                >{{ old('description', $subscriptionType->description) }}</textarea>
                    </div>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Optional: Provide details about features, benefits, or limitations of this plan
                    </p>
                </div>

                <!-- Alert Message -->
                <div>
                    <label for="alert_message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Alert Message
                    </label>
                    <div class="mt-1">
                        <input type="text" 
                               name="alert_message" 
                               id="alert_message" 
                               value="{{ old('alert_message', $subscriptionType->alert_message) }}"
                               maxlength="255"
                               class="focus:ring-indigo-500 pl-3 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('alert_message') border-red-500 @enderror" 
                               placeholder="e.g., Limited time offer!">
                    </div>
                    @error('alert_message')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Optional: Enter a special message or alert to display to users (max 255 characters)
                    </p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.subscription-types.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 active:bg-gray-500 dark:active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-save mr-2"></i> Update Subscription Plan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Client-side validation
    document.getElementById('subscriptionForm').addEventListener('submit', function(e) {
        const charge = parseFloat(document.getElementById('charge').value);
        
        if (isNaN(charge) || charge < 0) {
            e.preventDefault();
            alert('Please enter a valid charge amount (must be 0 or greater).');
            return false;
        }
        
        if (charge > 999999.99) {
            e.preventDefault();
            alert('Charge amount cannot exceed 999,999.99 AED.');
            return false;
        }
    });

    // Format charge input on blur
    document.getElementById('charge').addEventListener('blur', function() {
        if (this.value) {
            const value = parseFloat(this.value);
            if (!isNaN(value)) {
                this.value = value.toFixed(2);
            }
        }
    });
</script>
@endsection

