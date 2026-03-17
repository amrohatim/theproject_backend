@extends('layouts.dashboard')

@section('title', 'Moderate Provider Product')
@section('page-title', 'Moderate Provider Product')

@section('content')
<div class="container mx-auto">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Provider Product Moderation</h2>
                <p class="mt-1 text-gray-600 dark:text-gray-400">Review all provider product data and update approval status</p>
            </div>
            <div>
                <a href="{{ route('admin.provider-products.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Provider Products
                </a>
            </div>
        </div>
    </div>

    @php
        $imageUrl = \App\Helpers\ImageHelper::getFullImageUrl($providerProduct->image);
    @endphp

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700 space-y-8">
        <div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Provider Product Data (Read Only)</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500 dark:text-gray-400">ID</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $providerProduct->id }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Provider</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $providerProduct->provider->business_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Provider Email</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $providerProduct->provider->user->email ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Category</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $providerProduct->category->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Product Name</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $providerProduct->product_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Product Name (Arabic)</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $providerProduct->product_name_arabic ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">SKU</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $providerProduct->sku ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Price</p>
                    <p class="font-medium text-gray-900 dark:text-white">
                        {{ $providerProduct->price !== null ? '$' . number_format((float) $providerProduct->price, 2) : 'N/A' }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Original Price</p>
                    <p class="font-medium text-gray-900 dark:text-white">
                        {{ $providerProduct->original_price !== null ? '$' . number_format((float) $providerProduct->original_price, 2) : 'N/A' }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Stock</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $providerProduct->stock ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Minimum Order</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $providerProduct->min_order ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">is_active</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ is_null($providerProduct->is_active) ? 'N/A' : ($providerProduct->is_active ? 'true' : 'false') }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Rating</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $providerProduct->rating ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Total Ratings</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $providerProduct->total_ratings ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Created At</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $providerProduct->created_at?->format('Y-m-d H:i:s') ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Updated At</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $providerProduct->updated_at?->format('Y-m-d H:i:s') ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="mt-4">
                <p class="text-gray-500 dark:text-gray-400 text-sm">Description</p>
                <p class="font-medium text-gray-900 dark:text-white whitespace-pre-line">{{ $providerProduct->description ?? 'N/A' }}</p>
            </div>

            <div class="mt-4">
                <p class="text-gray-500 dark:text-gray-400 text-sm">Description (Arabic)</p>
                <p class="font-medium text-gray-900 dark:text-white whitespace-pre-line">{{ $providerProduct->product_description_arabic ?? 'N/A' }}</p>
            </div>

            <div class="mt-4">
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-2">Image</p>
                @if($imageUrl)
                    <img src="{{ $imageUrl }}" alt="{{ $providerProduct->product_name }}" class="h-48 w-48 object-cover rounded-lg border border-gray-200 dark:border-gray-700">
                @else
                    <div class="h-48 w-48 rounded-lg border border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center text-gray-400 text-sm">
                        No image
                    </div>
                @endif
            </div>
        </div>

        <form action="{{ route('admin.provider-products.update', $providerProduct->id) }}" method="POST" class="border-t border-gray-200 dark:border-gray-700 pt-6">
            @csrf
            @method('PUT')

            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Moderation Controls</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status <span class="text-red-500">*</span></label>
                    <select id="status" name="status" required class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Select Status</option>
                        <option value="approved" {{ old('status', $providerProduct->status) === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ old('status', $providerProduct->status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-save mr-2"></i> Save Status
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
