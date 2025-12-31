{{-- Products Table Partial for AJAX responses --}}
@if($products->count() > 0)
<div class="block md:hidden space-y-4 px-4 py-4">
    @foreach($products as $product)
        <x-mobile-product-card :product="$product" />
    @endforeach
</div>

<div class="hidden md:block overflow-x-auto">
    <div class="inline-block min-w-full align-middle">
        <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-4 text-left">
                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                </th>
                <th class="px-3 sm:px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <button class="flex items-center space-x-1 hover:text-gray-700 sortable-header min-h-[44px] w-full justify-start" data-sort="name">
                        <span>{{ __('merchant.product') }}</span>
                        <svg class="w-4 h-4 sort-icon flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </button>
                </th>
                <th class="px-3 sm:px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <button class="flex items-center space-x-1 hover:text-gray-700 sortable-header min-h-[44px] w-full justify-start" data-sort="category">
                        <span>{{ __('merchant.category') }}</span>
                        <svg class="w-4 h-4 sort-icon flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </button>
                </th>
                <th class="px-3 sm:px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <button class="flex items-center space-x-1 hover:text-gray-700 sortable-header min-h-[44px] w-full justify-start" data-sort="price">
                        <span>{{ __('merchant.price') }}</span>
                        <svg class="w-4 h-4 sort-icon flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </button>
                </th>
                <th class="px-3 sm:px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <button class="flex items-center space-x-1 hover:text-gray-700 sortable-header min-h-[44px] w-full justify-start" data-sort="stock">
                        <span>{{ __('merchant.stock') }}</span>
                        <svg class="w-4 h-4 sort-icon flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </button>
                </th>
                <th class="px-3 sm:px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <button class="flex items-center space-x-1 hover:text-gray-700 sortable-header min-h-[44px] w-full justify-start" data-sort="status">
                        <span>{{ __('merchant.status') }}</span>
                        <svg class="w-4 h-4 sort-icon flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </button>
                </th>
                <th class="px-3 sm:px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <button class="flex items-center space-x-1 hover:text-gray-700 sortable-header min-h-[44px] w-full justify-start" data-sort="created_at">
                        <span>{{ __('merchant.sort_by_date') }}</span>
                        <svg class="w-4 h-4 sort-icon flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                        </svg>
                    </button>
                </th>
                <th class="px-3 sm:px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('merchant.actions') }}</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($products as $product)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-3 sm:px-6 py-4">
                    <input type="checkbox" class="product-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4" value="{{ $product->id }}">
                </td>
                <td class="px-3 sm:px-6 py-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12">
                            @if($product->image)
                                <img class="h-12 w-12 rounded-lg object-cover border border-gray-200" src="{{ asset($product->image) }}" alt="{{ $product->name }}">
                            @else
                                <div class="h-12 w-12 rounded-lg bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400 text-lg"></i>
                                </div>
                            @endif
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                            @if($product->sku)
                                <div class="text-sm text-gray-500">SKU: {{ $product->sku }}</div>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-3 sm:px-6 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        {{ $product->category->name ?? __('merchant.uncategorized') }}
                    </span>
                </td>
                <td class="px-3 sm:px-6 py-4 text-sm font-medium text-gray-900">${{ number_format($product->price, 2) }}</td>
                <td class="px-3 sm:px-6 py-4">
                    @if($product->stock !== null)
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-900 mr-2">{{ $product->stock }}</span>
                            @if($product->stock < 10)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20" width="12" height="12">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ __('merchant.low_stock') }}
                                </span>
                            @endif
                        </div>
                    @else
                        <span class="text-sm text-gray-500">{{ __('merchant.not_tracked') }}</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    @if($product->is_available)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-400 mr-1.5"></span>
                            {{ __('merchant.available') }}
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-400 mr-1.5"></span>
                            {{ __('merchant.unavailable') }}
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">
                    {{ $product->created_at->format('M d, Y') }}
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('merchant.products.show', $product->id) }}"
                           class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors"
                           title="{{ __('merchant.view') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </a>
                        <a href="{{ route('merchant.products.edit', $product->id) }}"
                           class="p-2 text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg transition-colors"
                           title="{{ __('merchant.edit') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                        <form action="{{ route('merchant.products.destroy', $product->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('{{ __('merchant.delete_product_confirm') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors"
                                    title="{{ __('merchant.delete') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
        </table>
    </div>
</div>
@else
<!-- Empty State -->
<div class="text-center py-16 px-6">
    <svg class="mx-auto h-16 w-16 text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="64" height="64">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
    </svg>
    <h3 class="text-lg font-semibold text-gray-900 mb-3">{{ __('merchant.no_products_found') }}</h3>
    <p class="text-gray-600 mb-6 max-w-md mx-auto">
        {{ __('merchant.adjust_search_filters') }}
    </p>
    <button type="button" class="discord-btn" onclick="clearAllFilters()">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
        {{ __('merchant.clear_search_filters') }}
    </button>
</div>
@endif
