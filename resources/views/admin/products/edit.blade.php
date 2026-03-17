@extends('layouts.dashboard')

@section('title', 'Moderate Product')
@section('page-title', 'Moderate Product')

@section('content')
<div class="container mx-auto">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Product Moderation</h2>
                <p class="mt-1 text-gray-600 dark:text-gray-400">Review product details and update moderation settings</p>
            </div>
            <div>
                <a href="{{ route('admin.products.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Products
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700 space-y-8">
        <div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Product Information (Read Only)</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Name</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $product->name }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Category</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $product->category->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Branch</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $product->branch->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Company</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $product->branch->company->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Price</p>
                    <p class="font-medium text-gray-900 dark:text-white">${{ number_format((float) $product->price, 2) }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Original Price</p>
                    <p class="font-medium text-gray-900 dark:text-white">
                        {{ $product->original_price !== null ? '$' . number_format((float) $product->original_price, 2) : 'N/A' }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Stock</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $product->stock }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Availability</p>
                    <p class="font-medium {{ $product->is_available ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ $product->is_available ? 'Available' : 'Unavailable' }}
                    </p>
                </div>
            </div>

            <div class="mt-4">
                <p class="text-gray-500 dark:text-gray-400 text-sm">Description</p>
                <p class="font-medium text-gray-900 dark:text-white whitespace-pre-line">{{ $product->description ?: 'N/A' }}</p>
            </div>

            <div class="mt-4">
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-2">Main Image</p>
                @if($product->image)
                    <img src="{{ $product->image }}" alt="{{ $product->name }}" class="h-40 w-40 object-cover rounded-lg border border-gray-200 dark:border-gray-700">
                @else
                    <div class="h-40 w-40 rounded-lg border border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center text-gray-400 text-sm">
                        No image
                    </div>
                @endif
            </div>
        </div>

        <div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Color Variants</h3>

            @if($product->colors->isEmpty())
                <p class="text-sm text-gray-500 dark:text-gray-400">No colors added for this product.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($product->colors as $color)
                        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-3">
                            <div class="w-full aspect-[3/4] rounded-md overflow-hidden bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 flex items-center justify-center">
                                @if($color->image)
                                    <img src="{{ $color->image }}" alt="{{ $color->name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-xs text-gray-400">No image</span>
                                @endif
                            </div>

                            <p class="mt-3 text-sm font-semibold text-gray-900 dark:text-white text-center">{{ $color->name ?: 'Unnamed Color' }}</p>

                            @if($color->color_code)
                                <div class="mt-2 flex items-center justify-center gap-2 text-xs text-gray-600 dark:text-gray-300">
                                    <span class="inline-block w-4 h-4 rounded-full border border-gray-300 dark:border-gray-600" style="background-color: {{ $color->color_code }};"></span>
                                    <span>{{ $color->color_code }}</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Specifications</h3>

            @if($product->specifications->isEmpty())
                <p class="text-sm text-gray-500 dark:text-gray-400">No specifications added for this product.</p>
            @else
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Key</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Value</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($product->specifications as $spec)
                                <tr class="bg-white dark:bg-gray-800">
                                    <td class="px-4 py-3 text-gray-900 dark:text-white font-medium">{{ $spec->key ?: 'N/A' }}</td>
                                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $spec->value ?: 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" class="border-t border-gray-200 dark:border-gray-700 pt-6">
            @csrf
            @method('PUT')

            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Moderation Controls</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex items-start mt-1">
                    <div class="flex items-center h-5">
                        <input id="featured" name="featured" type="checkbox" value="1" {{ old('featured', $product->featured) ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="featured" class="font-medium text-gray-700 dark:text-gray-300">Featured Product</label>
                        <p class="text-gray-500 dark:text-gray-400">Enable to highlight this product across listings.</p>
                    </div>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status <span class="text-red-500">*</span></label>
                    <select id="status" name="status" required class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Select Status</option>
                        <option value="approved" {{ old('status', $product->status) === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ old('status', $product->status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
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
