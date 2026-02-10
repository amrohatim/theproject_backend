@extends('layouts.dashboard')

@section('title', 'Provider Dashboard')
@section('page-title', 'Provider Dashboard')

@section('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
</style>
@endsection

@section('content')
<div class="container mx-auto">
    @if(isset($message))
        <div class="bg-blue-50 dark:bg-blue-900/30 border-l-4 border-blue-400 p-4 mb-8">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700 dark:text-blue-200">
                        {{ $message }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Stats cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="stat-card bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                    <i class="fas fa-box text-blue-500 dark:text-blue-400 text-xl"></i>
                </div>
                <div class="flex items-center">
                    <span class="text-green-500 text-sm font-semibold mr-1">+5%</span>
                    <i class="fas fa-arrow-up text-green-500 text-xs"></i>
                </div>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-gray-800 dark:text-white">{{ $totalProducts ?? 0 }}</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">{{ __('provider.total_products') }}</p>
            </div>
        </div>

        <div class="stat-card bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                    <i class="fas fa-shopping-cart text-green-500 dark:text-green-400 text-xl"></i>
                </div>
                <div class="flex items-center">
                    <span class="text-green-500 text-sm font-semibold mr-1">+12%</span>
                    <i class="fas fa-arrow-up text-green-500 text-xs"></i>
                </div>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-gray-800 dark:text-white">{{ $totalOrders ?? 0 }}</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">{{ __('provider.total_orders') }}</p>
            </div>
        </div>

        <div class="stat-card bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                    <i class="fas fa-dollar-sign text-purple-500 dark:text-purple-400 text-xl"></i>
                </div>
                <div class="flex items-center">
                    <span class="text-green-500 text-sm font-semibold mr-1">+8%</span>
                    <i class="fas fa-arrow-up text-green-500 text-xs"></i>
                </div>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-gray-800 dark:text-white">${{ isset($totalRevenue) ? number_format($totalRevenue, 2) : '0.00' }}</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">{{ __('provider.revenue') }}</p>
            </div>
        </div>

        <div class="stat-card bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 rounded-full bg-orange-100 dark:bg-orange-900">
                    <i class="fas fa-users text-orange-500 dark:text-orange-400 text-xl"></i>
                </div>
                <div class="flex items-center">
                    <span class="text-green-500 text-sm font-semibold mr-1">+15%</span>
                    <i class="fas fa-arrow-up text-green-500 text-xs"></i>
                </div>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-gray-800 dark:text-white">{{ $totalCustomers ?? 0 }}</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">{{ __('provider.customers') }}</p>
            </div>
        </div>
    </div>

    <!-- Recent products -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ __('provider.recent_products') }}</h3>
            <a href="{{ route('provider.provider-products.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 text-sm font-medium">{{ __('provider.view_all') }}</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('provider.product_name') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('provider.price') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('provider.status') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($recentProducts ?? [] as $product)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @php
                                        $imageUrl = \App\Helpers\ProviderImageHelper::getProviderProductImageUrl($product->image ?? null);
                                        $placeholderUrl = asset('images/placeholder.jpg');
                                    @endphp
                                    <img
                                        src="{{ $imageUrl }}"
                                        alt="{{ $product->product_name }}"
                                        class="h-10 w-10 rounded-md object-cover"
                                        onerror="this.onerror=null; this.src='{{ $placeholderUrl }}'; if (!this.src) this.parentElement.innerHTML='<div class=\\'h-10 w-10 rounded-md bg-gray-200 dark:bg-gray-700 flex items-center justify-center\\'><i class=\\'fas fa-image text-gray-400\\'></i></div>';"
                                    >
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->product_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($product->price, 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($product->is_active) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                {{ $product->is_active ? __('provider.available') : __('provider.unavailable') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center py-4">
                                <i class="fas fa-box-open text-gray-300 dark:text-gray-600 text-5xl mb-4"></i>
                                <p>{{ __('provider.no_products_yet') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent orders -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ __('provider.recent_orders') }}</h3>
            <a href="{{ route('provider.orders.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 text-sm font-medium">{{ __('provider.view_all') }}</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('provider.order_number') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('provider.customer') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('provider.total') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('provider.status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('provider.date') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($recentOrders ?? [] as $order)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('provider.orders.show', $order->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 text-sm font-medium">
                                {{ $order->order_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $order->customer_name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($order->total, 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusClasses = [
                                    'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                    'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                    'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                ];
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                                {{ __('provider.' . $order->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $order->created_at->format('M d, Y') }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center py-4">
                                <i class="fas fa-shopping-cart text-gray-300 dark:text-gray-600 text-5xl mb-4"></i>
                                <p>{{ __('provider.no_orders_yet') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Activity overview -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ __('provider.store_activity') }}</h3>
            <a href="{{ route('provider.provider-products.create') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 text-sm font-medium">
                <i class="fas fa-plus mr-1"></i> {{ __('provider.add_new_product') }}
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 p-6">
            <div class="stat-card bg-gray-50 dark:bg-gray-900/30 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2 rounded-full bg-blue-100 dark:bg-blue-900">
                        <i class="fas fa-eye text-blue-500 dark:text-blue-400"></i>
                    </div>
                </div>
                <div class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalViews ?? 0 }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('provider.product_views') }}</div>
            </div>
            <div class="stat-card bg-gray-50 dark:bg-gray-900/30 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2 rounded-full bg-green-100 dark:bg-green-900">
                        <i class="fas fa-shopping-bag text-green-500 dark:text-green-400"></i>
                    </div>
                </div>
                <div class="text-2xl font-bold text-gray-800 dark:text-white">{{ $conversionRate ?? 0 }}%</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('provider.conversion_rate') }}</div>
            </div>
            <div class="stat-card bg-gray-50 dark:bg-gray-900/30 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2 rounded-full bg-yellow-100 dark:bg-yellow-900">
                        <i class="fas fa-star text-yellow-500 dark:text-yellow-400"></i>
                    </div>
                </div>
                <div class="text-2xl font-bold text-gray-800 dark:text-white">{{ isset($avgRating) ? number_format($avgRating, 1) : '0.0' }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('provider.avg_rating') }}</div>
            </div>
            <div class="stat-card bg-gray-50 dark:bg-gray-900/30 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2 rounded-full bg-red-100 dark:bg-red-900">
                        <i class="fas fa-redo text-red-500 dark:text-red-400"></i>
                    </div>
                </div>
                <div class="text-2xl font-bold text-gray-800 dark:text-white">{{ $returnRate ?? 0 }}%</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('provider.return_rate') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
