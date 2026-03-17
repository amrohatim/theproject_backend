@extends('layouts.dashboard')

@section('title', 'Moderate Service')
@section('page-title', 'Moderate Service')

@section('content')
<div class="container mx-auto">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Service Moderation</h2>
                <p class="mt-1 text-gray-600 dark:text-gray-400">Review service details and update moderation settings</p>
            </div>
            <div>
                <a href="{{ route('admin.services.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Services
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700 space-y-8">
        <div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Service Information (Read Only)</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Name</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $service->name }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Category</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $service->category->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Branch</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $service->branch->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Company</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $service->branch->company->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Price</p>
                    <p class="font-medium text-gray-900 dark:text-white">${{ number_format((float) $service->price, 2) }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Duration</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $service->duration }} minutes</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Availability</p>
                    <p class="font-medium {{ $service->is_available ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ $service->is_available ? 'Available' : 'Unavailable' }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Current Status</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ ucfirst((string) $service->status) }}</p>
                </div>
            </div>

            <div class="mt-4">
                <p class="text-gray-500 dark:text-gray-400 text-sm">Description</p>
                <p class="font-medium text-gray-900 dark:text-white whitespace-pre-line">{{ $service->description ?: 'N/A' }}</p>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Service Images</h3>

            @php
                $hasMainImage = !empty($service->image);
                $hasAdditionalImages = $service->serviceImages && $service->serviceImages->isNotEmpty();
            @endphp

            @if(!$hasMainImage && !$hasAdditionalImages)
                <p class="text-sm text-gray-500 dark:text-gray-400">No images available for this service.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @if($hasMainImage)
                        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-3">
                            <div class="w-full aspect-[4/3] rounded-md overflow-hidden bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                <img src="{{ $service->image }}" alt="{{ $service->name }} main image" class="w-full h-full object-cover">
                            </div>
                            <p class="mt-3 text-sm font-semibold text-gray-900 dark:text-white text-center">Main Image</p>
                        </div>
                    @endif

                    @foreach($service->serviceImages as $image)
                        @php
                            $additionalImageUrl = \App\Helpers\ImageHelper::getFullImageUrl($image->image_path);
                        @endphp
                        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-3">
                            <div class="w-full aspect-[4/3] rounded-md overflow-hidden bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                @if($additionalImageUrl)
                                    <img src="{{ $additionalImageUrl }}" alt="{{ $service->name }} additional image" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-xs text-gray-400">Image unavailable</div>
                                @endif
                            </div>
                            <p class="mt-3 text-sm font-semibold text-gray-900 dark:text-white text-center">Additional Image</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <form action="{{ route('admin.services.update', $service->id) }}" method="POST" class="border-t border-gray-200 dark:border-gray-700 pt-6">
            @csrf
            @method('PUT')

            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Moderation Controls</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex items-start mt-1">
                    <div class="flex items-center h-5">
                        <input id="featured" name="featured" type="checkbox" value="1" {{ old('featured', $service->featured) ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="featured" class="font-medium text-gray-700 dark:text-gray-300">Featured Service</label>
                        <p class="text-gray-500 dark:text-gray-400">Enable to highlight this service across listings.</p>
                    </div>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status <span class="text-red-500">*</span></label>
                    <select id="status" name="status" required class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Select Status</option>
                        <option value="approved" {{ old('status', $service->status) === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ old('status', $service->status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-save mr-2"></i> Save Moderation Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
