@extends('layouts.products-manager')

@section('title', __('products_manager.dashboard_title'))
@section('page-title', __('products_manager.dashboard_title'))

@section('content')
<div class="container mx-auto">
    <!-- Welcome Section -->
    <div class="mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('products_manager.welcome_message', ['name' => $user->name]) }}</h2>
                    <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('products_manager.manage_products_across', ['company' => $company->name]) }}</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                        {{ __('products_manager.products_manager_role') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total Products -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="rounded-md bg-purple-100 dark:bg-purple-900 p-3">
                        <i class="fas fa-box text-purple-600 dark:text-purple-400 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('products_manager.total_products') }}</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_products'] }}</div>
                </div>
            </div>
        </div>

        <!-- Available Products -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="rounded-md bg-green-100 dark:bg-green-900 p-3">
                        <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('products_manager.available') }}</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['available_products'] }}</div>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="rounded-md bg-blue-100 dark:bg-blue-900 p-3">
                        <i class="fas fa-shopping-cart text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('products_manager.total_orders') }}</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_orders'] }}</div>
                </div>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="rounded-md bg-yellow-100 dark:bg-yellow-900 p-3">
                        <i class="fas fa-clock text-yellow-600 dark:text-yellow-400 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('products_manager.pending_orders_stat') }}</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['pending_orders'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Out of Stock -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="rounded-md bg-red-100 dark:bg-red-900 p-3">
                        <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('products_manager.out_of_stock') }}</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['out_of_stock'] }}</div>
                </div>
            </div>
        </div>

        <!-- Low Stock -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="rounded-md bg-orange-100 dark:bg-orange-900 p-3">
                        <i class="fas fa-exclamation text-orange-600 dark:text-orange-400 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('products_manager.low_stock') }}</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['low_stock'] }}</div>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="rounded-md bg-green-100 dark:bg-green-900 p-3">
                        <i class="fas fa-dollar-sign text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('products_manager.total_revenue') }}</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($stats['total_revenue'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Products -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('products_manager.top_selling_products') }}</h3>
                    <a href="{{ route('products-manager.products.index') }}" class="text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300 text-sm font-medium">
                        {{ __('products_manager.view_all') }}
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if($topProducts->count() > 0)
                    <div class="space-y-4">
                        @foreach($topProducts as $product)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        @if($product->image)
                                            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="h-10 w-10 rounded-lg object-cover">
                                        @else
                                            <div class="h-10 w-10 rounded-lg bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                                                <i class="fas fa-box text-purple-600 dark:text-purple-400"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->name }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $product->branch->name ?? __('products_manager.no_branch') }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($product->price, 2) }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $product->order_items_count ?? 0 }} {{ __('products_manager.sold') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <div class="mx-auto h-12 w-12 text-gray-400">
                            <i class="fas fa-box text-4xl"></i>
                        </div>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('products_manager.no_products_found') }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('products_manager.start_adding_products') }}</p>
                        <div class="mt-4">
                            <a href="{{ route('products-manager.products.create') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-900 focus:outline-none focus:border-purple-900 focus:ring ring-purple-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <i class="fas fa-plus mr-2"></i> {{ __('products_manager.add_product') }}
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('products_manager.recent_orders') }}</h3>
                    <a href="{{ route('products-manager.orders.index') }}" class="text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300 text-sm font-medium">
                        {{ __('products_manager.view_all') }}
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if($recentOrders->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentOrders as $order)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                            <i class="fas fa-user text-blue-600 dark:text-blue-400"></i>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->user->name ?? __('products_manager.unknown_customer') }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('products_manager.order_number', ['id' => $order->id]) }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($order->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @elseif($order->status === 'processing') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                        @elseif($order->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">${{ number_format($order->total_amount, 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <div class="mx-auto h-12 w-12 text-gray-400">
                            <i class="fas fa-shopping-cart text-4xl"></i>
                        </div>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('products_manager.no_recent_orders') }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('products_manager.orders_appear_here') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Active Deals -->
    @if($activeDeals->count() > 0)
        <div class="mt-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('products_manager.active_deals') }}</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($activeDeals as $deal)
                            <div class="p-4 bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 rounded-lg border border-green-200 dark:border-green-700">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-medium text-green-900 dark:text-green-100">{{ $deal->title }}</h4>
                                        <p class="text-sm text-green-700 dark:text-green-300">{{ $deal->product->name ?? __('products_manager.product') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-lg font-bold text-green-900 dark:text-green-100">{{ $deal->discount_percentage }}% {{ __('products_manager.off') }}</span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <p class="text-xs text-green-600 dark:text-green-400">
                                        {{ __('products_manager.valid_until', ['date' => $deal->end_date->format('M d, Y')]) }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh statistics every 5 minutes
    setInterval(function() {
        fetch('{{ route("products-manager.dashboard.stats") }}')
            .then(response => response.json())
            .then(data => {
                // Update statistics if needed
                console.log('Statistics updated:', data);
            })
            .catch(error => console.error('Error updating statistics:', error));
    }, 300000); // 5 minutes
});
</script>
@endsection
