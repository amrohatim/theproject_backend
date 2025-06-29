@extends('layouts.vendor')

@section('title', 'Branch Image Management')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $branch->name }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">Branch Image Management</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('vendor.branches.show', $branch->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-arrow-left mr-2"></i> Back to Branch
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg mb-6">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                Branch Image
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                Manage the image for this branch. You can upload a custom image or use the company image.
            </p>
        </div>
        <div class="border-t border-gray-200 dark:border-gray-700">
            <form action="{{ route('vendor.branches.update', $branch->id) }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <div class="flex items-center">
                        <input id="use_company_image" name="use_company_image" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" {{ $branch->use_company_image ? 'checked' : '' }}>
                        <label for="use_company_image" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                            Use company image
                        </label>
                    </div>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        If checked, the branch will use the company image instead of its own image.
                    </p>
                </div>

                <div id="branch_image_section" class="{{ $branch->use_company_image ? 'hidden' : '' }}">
                    <div class="mb-6">
                        <label for="branch_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Branch Image</label>
                        <div class="mt-1 flex items-center">
                            <div class="w-full">
                                <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md">
                                    <div class="space-y-1 text-center">
                                        <div class="flex flex-col items-center">
                                            @php
                                                // Get branch image using the accessor method
                                                $branchImage = !$branch->use_company_image ? $branch->getBranchImageAttribute() : null;
                                            @endphp
                                            @if($branchImage)
                                                <img src="{{ $branchImage }}" alt="{{ $branch->name }}" class="h-40 w-auto object-cover mb-4">
                                            @else
                                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            @endif
                                            <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                                <label for="branch_image" class="relative cursor-pointer bg-white dark:bg-gray-700 rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                    <span>Upload a file</span>
                                                    <input id="branch_image" name="branch_image" type="file" class="sr-only" accept="image/*">
                                                </label>
                                                <p class="pl-1">or drag and drop</p>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                PNG, JPG, GIF up to 2MB
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="company_image_section" class="{{ $branch->use_company_image ? '' : 'hidden' }}">
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Company Image</label>
                        <div class="mt-1 flex items-center">
                            <div class="w-full">
                                <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md">
                                    <div class="space-y-1 text-center">
                                        <div class="flex flex-col items-center">
                                            @php
                                                // Get company logo using ImageHelper
                                                $companyLogo = $company && $company->logo ? \App\Helpers\ImageHelper::getFullImageUrl($company->logo) : null;
                                            @endphp
                                            @if($companyLogo)
                                                <img src="{{ $companyLogo }}" alt="{{ $company->name }}" class="h-40 w-auto object-cover mb-4">
                                            @else
                                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    No company image available
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            This is the image from your company profile. To change it, go to the company settings.
                        </p>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="button" onclick="window.history.back()" class="bg-white dark:bg-gray-700 py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </button>
                    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const useCompanyImageCheckbox = document.getElementById('use_company_image');
        const branchImageSection = document.getElementById('branch_image_section');
        const companyImageSection = document.getElementById('company_image_section');

        useCompanyImageCheckbox.addEventListener('change', function() {
            if (this.checked) {
                branchImageSection.classList.add('hidden');
                companyImageSection.classList.remove('hidden');
            } else {
                branchImageSection.classList.remove('hidden');
                companyImageSection.classList.add('hidden');
            }
        });
    });
</script>
@endsection
