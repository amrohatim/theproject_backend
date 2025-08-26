@extends('layouts.dashboard')

@section('title', 'Edit Business Type')
@section('page-title', 'Edit Business Type')

@section('content')
<div class="container mx-auto">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Business Type</h2>
                <p class="mt-1 text-gray-600 dark:text-gray-400">Update business type information</p>
            </div>
            <div>
                <a href="{{ route('admin.business-types.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
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

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <form action="{{ route('admin.business-types.update', $businessType) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <div>
                    <label for="business_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Business Name <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <input type="text" 
                               name="business_name" 
                               id="business_name" 
                               value="{{ old('business_name', $businessType->business_name) }}"
                               class="focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('business_name') border-red-500 @enderror" 
                               placeholder="Enter business type name"
                               required>
                    </div>
                    @error('business_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Enter a unique business type name (e.g., Restaurant, Retail Store, Beauty Salon, etc.)
                    </p>
                </div>

                <!-- Display creation and update information -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Record Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-400">
                        <div>
                            <span class="font-medium">Created:</span> {{ $businessType->created_at->format('M d, Y \a\t g:i A') }}
                        </div>
                        <div>
                            <span class="font-medium">Last Updated:</span> {{ $businessType->updated_at->format('M d, Y \a\t g:i A') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('admin.business-types.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-save mr-2"></i>
                    Update Business Type
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Focus on the business name input and select all text for easy editing
        const businessNameInput = document.getElementById('business_name');
        businessNameInput.focus();
        businessNameInput.select();
        
        // Add client-side validation
        const form = document.querySelector('form');
        
        form.addEventListener('submit', function(e) {
            const businessName = businessNameInput.value.trim();
            
            if (!businessName) {
                e.preventDefault();
                alert('Please enter a business name.');
                businessNameInput.focus();
                return false;
            }
            
            if (businessName.length < 2) {
                e.preventDefault();
                alert('Business name must be at least 2 characters long.');
                businessNameInput.focus();
                return false;
            }
            
            if (businessName.length > 255) {
                e.preventDefault();
                alert('Business name must not exceed 255 characters.');
                businessNameInput.focus();
                return false;
            }
        });
        
        // Real-time character count
        businessNameInput.addEventListener('input', function() {
            const length = this.value.length;
            const maxLength = 255;
            
            // Remove existing character count if any
            const existingCount = document.getElementById('char-count');
            if (existingCount) {
                existingCount.remove();
            }
            
            // Add character count
            const charCount = document.createElement('p');
            charCount.id = 'char-count';
            charCount.className = 'mt-1 text-sm text-gray-500 dark:text-gray-400';
            charCount.textContent = `${length}/${maxLength} characters`;
            
            if (length > maxLength) {
                charCount.className = 'mt-1 text-sm text-red-600';
            }
            
            this.parentNode.appendChild(charCount);
        });
    });
</script>
@endsection
