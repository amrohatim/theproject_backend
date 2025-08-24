{{-- Products Manager Products Index Content - For AJAX Loading --}}
<div class="products-manager-theme">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ __('products_manager.products_title') }}</h1>
                <p class="text-gray-600 dark:text-gray-400">{{ __('products_manager.manage_products_description') }}</p>
            </div>
            <a href="{{ route('products-manager.products.create') }}"
               class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-md transition-colors ajax-nav">
                <i class="fas fa-plus mr-2"></i>
                {{ __('products_manager.add_product') }}
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4" id="products-filter-form">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('products_manager.search') }}</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="{{ __('products_manager.search_products') }}"
                           class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('products_manager.category') }}</label>
                <select name="category" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white">
                    <option value="">{{ __('products_manager.all_categories') }}</option>
                    @foreach(($categories ?? []) as $category)
                        <option value="{{ $category->id }}" @selected(request('category') == $category->id)>
                            {{ $category->name }}
                        </option>
                        @if($category->children)
                            @foreach($category->children as $child)
                                <option value="{{ $child->id }}" @selected(request('category') == $child->id)>
                                    &nbsp;&nbsp;{{ $child->name }}
                                </option>
                            @endforeach
                        @endif
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('products_manager.branch') }}</label>
                <select name="branch" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white">
                    <option value="">{{ __('products_manager.all_branches') }}</option>
                    @foreach(($branches ?? []) as $branch)
                        <option value="{{ $branch->id }}" @selected(request('branch') == $branch->id)>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-md transition-colors">
                    <i class="fas fa-filter mr-2"></i>
                    {{ __('products_manager.filter') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Products List -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        @if(($products ?? collect())->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('products_manager.product') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('products_manager.category') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('products_manager.branch') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('products_manager.price') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('products_manager.stock') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('products_manager.status') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('products_manager.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($products as $product)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($product->image)
                                            <img class="h-10 w-10 rounded-lg object-cover mr-3" src="{{ $product->image }}" alt="{{ $product->name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-lg bg-gray-200 dark:bg-gray-600 flex items-center justify-center mr-3">
                                                <i class="fas fa-box text-gray-400"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->name }}</div>
                                            @if($product->product_name_arabic)
                                                <div class="text-sm text-gray-500 dark:text-gray-400" dir="rtl">{{ $product->product_name_arabic }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $product->category->name ?? 'No Category' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $product->branch->name ?? 'No Branch' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    ${{ number_format($product->price, 2) }}
                                    @if($product->original_price && $product->original_price > $product->price)
                                        <span class="text-xs text-gray-500 line-through ml-1">${{ number_format($product->original_price, 2) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($product->stock > 10) bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                        @elseif($product->stock > 0) bg-orange-200 text-orange-900 dark:bg-orange-800 dark:text-orange-100
                                        @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                        {{ $product->stock }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
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
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('products-manager.products.show', $product) }}" 
                                           class="text-orange-600 hover:text-orange-900 dark:text-orange-400 dark:hover:text-orange-300 ajax-nav">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('products-manager.products.edit', $product) }}" 
                                           class="text-orange-600 hover:text-orange-900 dark:text-orange-400 dark:hover:text-orange-300 ajax-nav">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="deleteProduct({{ $product->id }})" 
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
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
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="mx-auto h-16 w-16 rounded-full bg-orange-100 dark:bg-orange-900/20 flex items-center justify-center mb-4">
                    <i class="fas fa-box text-orange-500 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ __('products_manager.no_products_found') }}</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">{{ __('products_manager.start_adding_products') }}</p>
                <a href="{{ route('products-manager.products.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-md transition-colors ajax-nav">
                    <i class="fas fa-plus mr-2"></i>
                    {{ __('products_manager.add_product') }}
                </a>
            </div>
        @endif
    </div>
</div>

<script>
// Handle delete product
function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        fetch(`/products-manager/products/${productId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message and reload the products list
                alert(data.message || 'Product deleted successfully');
                window.location.reload();
            } else {
                alert(data.message || 'Failed to delete product');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to delete product');
        });
    }
}

// Handle filter form submission via AJAX
document.getElementById('products-filter-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const params = new URLSearchParams(formData);
    const url = `{{ route('products-manager.products.index') }}?${params.toString()}`;
    
    // Use the global AJAX navigation system
    if (window.productsManagerAjaxNav) {
        window.productsManagerAjaxNav.loadContent(url, true);
    } else {
        window.location.href = url;
    }
});
</script>
