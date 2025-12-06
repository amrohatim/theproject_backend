@extends('layouts.dashboard')

@section('title', 'Add Size Category')
@section('page-title', 'Add Size Category')

@section('content')
@php
    $sizes = old('sizes', [
        ['name' => '', 'value' => '', 'additional_info' => '', 'display_order' => 0, 'is_active' => 1],
    ]);
@endphp

<div class="container mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Create Size Category</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">Define a new size grouping for products or services</p>
        </div>
        <div>
            <a href="{{ route('admin.size-categories.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i> Back to List
            </a>
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
        <form action="{{ route('admin.size-categories.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Internal Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('name') border-red-500 @enderror" placeholder="clothes, shoes, hats" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Used in code and API responses.</p>
                </div>

                <div>
                    <label for="display_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Display Name <span class="text-red-500">*</span></label>
                    <input type="text" name="display_name" id="display_name" value="{{ old('display_name') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('display_name') border-red-500 @enderror" placeholder="Clothes, Shoes, Hats" required>
                    @error('display_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Shown to users in English.</p>
                </div>

                <div>
                    <label for="name_arabic" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Internal Name (Arabic)</label>
                    <input type="text" name="name_arabic" id="name_arabic" value="{{ old('name_arabic') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('name_arabic') border-red-500 @enderror" placeholder="ملابس، أحذية">
                    @error('name_arabic')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="display_name_arabic" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Display Name (Arabic)</label>
                    <input type="text" name="display_name_arabic" id="display_name_arabic" value="{{ old('display_name_arabic') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('display_name_arabic') border-red-500 @enderror" placeholder="الملابس، الأحذية">
                    @error('display_name_arabic')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                <textarea name="description" id="description" rows="4" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('description') border-red-500 @enderror" placeholder="Optional details about where to use this size category.">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="display_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Display Order</label>
                    <input type="number" name="display_order" id="display_order" value="{{ old('display_order', 0) }}" min="0" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md @error('display_order') border-red-500 @enderror">
                    @error('display_order')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Lower numbers appear first.</p>
                </div>

                <div class="flex items-center space-x-3 mt-6 md:mt-8">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label for="is_active" class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</label>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Standardized Sizes</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Add size options that belong to this category.</p>
                        @error('sizes')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="button" id="add-size-row" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-plus mr-2"></i> Add Size
                    </button>
                </div>

                <div class="space-y-4" id="sizes-container">
                    @foreach($sizes as $index => $size)
                        <div class="size-row grid grid-cols-1 md:grid-cols-5 gap-4 bg-gray-50 dark:bg-gray-900 p-4 rounded-md">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                                <input type="text" name="sizes[{{ $index }}][name]" value="{{ $size['name'] ?? '' }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Value</label>
                                <input type="text" name="sizes[{{ $index }}][value]" value="{{ $size['value'] ?? '' }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="EU/US equivalent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Additional Info</label>
                                <input type="text" name="sizes[{{ $index }}][additional_info]" value="{{ $size['additional_info'] ?? '' }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="Foot length, age, etc.">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Display Order</label>
                                <input type="number" name="sizes[{{ $index }}][display_order]" value="{{ $size['display_order'] ?? $index }}" min="0" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                            </div>
                            <div class="flex items-center space-x-2 mt-6">
                                <input type="hidden" name="sizes[{{ $index }}][is_active]" value="0">
                                <input type="checkbox" name="sizes[{{ $index }}][is_active]" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" {{ ($size['is_active'] ?? true) ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700 dark:text-gray-300">Active</span>
                                <button type="button" class="remove-size text-red-600 hover:text-red-800 ml-auto" title="Remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <template id="size-row-template">
                <div class="size-row grid grid-cols-1 md:grid-cols-5 gap-4 bg-gray-50 dark:bg-gray-900 p-4 rounded-md">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                        <input type="text" name="__NAME__[name]" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Value</label>
                        <input type="text" name="__NAME__[value]" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="EU/US equivalent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Additional Info</label>
                        <input type="text" name="__NAME__[additional_info]" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="Foot length, age, etc.">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Display Order</label>
                        <input type="number" name="__NAME__[display_order]" value="0" min="0" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                    </div>
                    <div class="flex items-center space-x-2 mt-6">
                        <input type="hidden" name="__NAME__[is_active]" value="0">
                        <input type="checkbox" name="__NAME__[is_active]" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" checked>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Active</span>
                        <button type="button" class="remove-size text-red-600 hover:text-red-800 ml-auto" title="Remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </template>

            <div class="mt-8 flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.size-categories.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 active:bg-gray-500 dark:active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-save mr-2"></i> Create Size Category
                </button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const container = document.getElementById('sizes-container');
        const template = document.getElementById('size-row-template').innerHTML;
        let index = {{ count($sizes) }};

        document.getElementById('add-size-row').addEventListener('click', () => {
            const html = template.replace(/__NAME__/g, `sizes[${index}]`);
            const wrapper = document.createElement('div');
            wrapper.innerHTML = html.trim();
            container.appendChild(wrapper.firstChild);
            index++;
        });

        container.addEventListener('click', (e) => {
            if (e.target.closest('.remove-size')) {
                const row = e.target.closest('.size-row');
                row.remove();
            }
        });
    });
</script>
@endpush
@endsection
