@extends('layouts.products-manager')

@section('title', __('products_manager.products_title'))
@section('page-title', __('products_manager.products_title'))

@section('content')
<div class="container mx-auto px-0 sm:px-0 md:px-6 lg:px-8">
    <div class="mb-6 px-4 sm:px-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('products_manager.products_title') }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('products_manager.manage_products_description') }}</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('products-manager.products.create') }}" class="inline-flex w-full items-center justify-center px-4 py-2 bg-orange-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-600 active:bg-orange-700 focus:outline-none focus:border-orange-700 focus:ring ring-orange-300 disabled:opacity-25 transition ease-in-out duration-150 md:w-auto">
                <i class="fas fa-plus mr-2"></i> {{ __('products_manager.add_product') }}
            </a>
        </div>
    </div>

    <!-- Search and filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 p-4 border border-gray-200 dark:border-gray-700">
        <form action="{{ route('products-manager.products.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('products_manager.search') }}</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" class="focus:ring-orange-500 focus:border-orange-500 block w-full pl-10 sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="{{ __('products_manager.search_products') }}">
                    </div>
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('products_manager.category') }}</label>
                    <select id="category" name="category" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-md">
                        <option value="">{{ __('products_manager.all_categories') }}</option>
                        @foreach(($categories ?? []) as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @if($category->children->count() > 0)
                                @foreach($category->children as $child)
                                    <option value="{{ $child->id }}" {{ request('category') == $child->id ? 'selected' : '' }}>
                                        &nbsp;&nbsp;&nbsp;&nbsp;{{ $child->name }}
                                    </option>
                                @endforeach
                            @endif
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="branch" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('products_manager.branch') }}</label>
                    <select id="branch" name="branch" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm rounded-md">
                        <option value="">{{ __('products_manager.all_branches') }}</option>
                        @foreach(($branches ?? []) as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex w-full items-center justify-center px-4 py-2 bg-orange-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-600 active:bg-orange-700 focus:outline-none focus:border-orange-700 focus:ring ring-orange-300 disabled:opacity-25 transition ease-in-out duration-150 sm:w-auto">
                    <i class="fas fa-filter mr-2"></i> {{ __('products_manager.filter') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Products List -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        @if(($products ?? collect())->count())
            <div class="block md:hidden space-y-4 px-4 py-4">
                @foreach($products as $product)
                    <x-mobile-product-card
                        :product="$product"
                        :edit-url="route('products-manager.products.edit', $product)"
                        :delete-url="route('products-manager.products.destroy', $product)"
                        :delete-confirm="__('products_manager.confirm_delete_product')"
                    />
                @endforeach
            </div>

            <div class="hidden md:block overflow-x-auto">
                <table class="pm-responsive-table min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('products_manager.product') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('products_manager.category') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('products_manager.branch') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('products_manager.price') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('products_manager.stock') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('products_manager.status') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('products_manager.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($products as $product)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 md:whitespace-nowrap" data-label="{{ __('products_manager.product') }}">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if($product->colors->first() && $product->colors->first()->image)
                                                <img class="h-10 w-10 rounded-lg object-cover" src="{{ $product->colors->first()->image }}" alt="{{ $product->name }}">
                                            @else
                                                <div class="h-10 w-10 rounded-lg bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                                    <i class="fas fa-image text-gray-400"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->name }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $product->name_ar }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 md:whitespace-nowrap text-sm text-gray-900 dark:text-white" data-label="{{ __('products_manager.category') }}">{{ $product->category->name ?? '-' }}</td>
                                <td class="px-6 py-4 md:whitespace-nowrap text-sm text-gray-900 dark:text-white" data-label="{{ __('products_manager.branch') }}">{{ $product->branch->name ?? '-' }}</td>
                                <td class="px-6 py-4 md:whitespace-nowrap text-sm text-gray-900 dark:text-white" data-label="{{ __('products_manager.price') }}">${{ number_format($product->price, 2) }}</td>
                                <td class="px-6 py-4 md:whitespace-nowrap" data-label="{{ __('products_manager.stock') }}">
                                    @if($product->stock <= 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            {{ __('products_manager.out_of_stock') }}
                                        </span>
                                    @elseif($product->stock <= 10)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                            {{ __('products_manager.low_stock_with_count', ['count' => $product->stock]) }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            {{ __('products_manager.in_stock_with_count', ['count' => $product->stock]) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 md:whitespace-nowrap" data-label="{{ __('products_manager.status') }}">
                                    @if($product->is_available)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            {{ __('products_manager.active') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                            {{ __('products_manager.inactive') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 md:whitespace-nowrap text-right text-sm font-medium" data-label="{{ __('products_manager.actions') }}">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('products-manager.products.edit', $product) }}" class="text-orange-600 hover:text-orange-900 dark:text-orange-400 dark:hover:text-orange-300">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="deleteProduct({{ $product->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @include('products-manager.products.partials.pagination', ['products' => $products])
        @else
            <div class="p-8 text-center">
                <div class="mx-auto h-14 w-14 rounded-full bg-orange-100 dark:bg-orange-900 flex items-center justify-center">
                    <i class="fas fa-box text-orange-600 dark:text-orange-400"></i>
                </div>
                <h4 class="mt-3 text-gray-900 dark:text-white font-medium">{{ __('products_manager.no_products_found') }}</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('products_manager.start_adding_products') }}</p>
                <div class="mt-6">
                    <a href="{{ route('products-manager.products.create') }}" class="inline-flex items-center px-4 py-2 bg-orange-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-600 active:bg-orange-700 focus:outline-none focus:border-orange-700 focus:ring ring-orange-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-plus mr-2"></i> {{ __('products_manager.add_first_product') }}
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/vendor-autocomplete.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    if (searchInput) {
        new VendorAutoComplete(searchInput, {
            apiUrl: '{{ route('products-manager.products.search-suggestions') }}',
            placeholder: '{{ __('products_manager.search_products') }}'
        });
    }
});

function deleteProduct(productId) {
    if (confirm('{{ __('products_manager.confirm_delete_product') }}')) {
        // Handle product deletion
        fetch(`/products-manager/products/${productId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.ok) {
                location.reload();
            } else {
                alert('{{ __('products_manager.failed_to_delete') }}');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('{{ __('products_manager.failed_to_delete') }}');
        });
    }
}
</script>
@endsection
