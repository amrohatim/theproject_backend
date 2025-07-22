@extends('layouts.provider')

@section('title', 'Dashboard')

@section('header', 'Provider Dashboard')

@section('content')
<!-- Modern Dashboard Container -->
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100 p-4 md:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto space-y-8">

        @if(isset($message))
        <div class="bg-white border-l-4 border-l-blue-500 shadow-lg rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-blue-100 p-2 rounded-full mr-3">
                    <i class="fas fa-info-circle text-blue-600"></i>
                </div>
                <p class="text-gray-800 font-medium">{{ $message }}</p>
            </div>
        </div>
        @endif

        <!-- Header Section -->
        <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Provider Dashboard</h1>
                <p class="text-gray-600 mt-1">Welcome back! Here's what's happening with your store.</p>
            </div>
            <a href="{{ route('provider.provider-products.create') }}"
               class="flex items-center bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 px-4 py-2 rounded-lg">
                <span class="mr-2">＋</span>
                Add New Product
            </a>
        </header>

        <!-- Statistics Cards -->
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Products -->
            <div class="bg-white border-0 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 border-l-4 border-l-blue-500 rounded-lg">
                <div class="p-6 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Total Products</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalProducts }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-box text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Orders -->
            <div class="bg-white border-0 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 border-l-4 border-l-emerald-500 rounded-lg">
                <div class="p-6 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Total Orders</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalOrders }}</p>
                    </div>
                    <div class="bg-emerald-100 p-3 rounded-full">
                        <i class="fas fa-shopping-cart text-emerald-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Revenue -->
            <div class="bg-white border-0 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 border-l-4 border-l-yellow-500 rounded-lg">
                <div class="p-6 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Revenue</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">${{ isset($totalRevenue) ? number_format($totalRevenue, 2) : '0.00' }}</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-dollar-sign text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Customers -->
            <div class="bg-white border-0 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 border-l-4 border-l-red-500 rounded-lg">
                <div class="p-6 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Customers</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ isset($totalCustomers) ? $totalCustomers : '0' }}</p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <i class="fas fa-users text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content Area -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Products -->
            <section class="bg-white border-0 shadow-lg hover:shadow-xl transition-all duration-300 rounded-lg">
                <header class="p-6 pb-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="flex items-center text-gray-900 text-lg font-semibold">
                            <i class="fas fa-box mr-2 text-blue-600"></i>
                            Recent Products
                        </h2>
                        <a href="{{ route('provider.provider-products.index') }}"
                           class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 hover:bg-blue-50 border border-blue-200 px-3 py-1 rounded-lg transition-all duration-200">
                            <i class="fas fa-eye mr-1"></i> View All
                        </a>
                    </div>
                </header>
                <div class="p-6 space-y-4">
                    @foreach($recentProducts as $product)
                    <div class="flex items-center space-x-4 p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        @if($product->image)
                            <img src="@providerProductImage($product->image)" alt="{{ $product->product_name }}"
                                 class="w-12 h-12 rounded-lg object-cover">
                        @else
                            <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-content-center">
                                <i class="fas fa-image text-gray-400"></i>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $product->product_name }}</p>
                            <p class="text-sm text-emerald-600 font-semibold">${{ number_format($product->price, 2) }}</p>
                        </div>
                        @if($product->is_active)
                            <span class="bg-emerald-100 text-emerald-800 px-2 py-1 rounded-xl text-xs font-medium">Available</span>
                        @else
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded-xl text-xs font-medium">Unavailable</span>
                        @endif
                    </div>
                    @endforeach

                    @if(count($recentProducts) == 0)
                    <div class="text-center py-8">
                        <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-box-open text-gray-400 text-2xl"></i>
                        </div>
                        <p class="text-gray-500 mb-4">No products yet</p>
                        <a href="{{ route('provider.provider-products.create') }}"
                           class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i> Add Product
                        </a>
                    </div>
                    @endif
                </div>
            </section>

            <!-- Recent Orders -->
            <section class="bg-white border-0 shadow-lg hover:shadow-xl transition-all duration-300 rounded-lg">
                <header class="p-6 pb-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="flex items-center text-gray-900 text-lg font-semibold">
                            <i class="fas fa-shopping-cart mr-2 text-emerald-600"></i>
                            Recent Orders
                        </h2>
                        <a href="{{ route('provider.orders.index') }}"
                           class="inline-flex items-center text-sm font-medium text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-lg transition-all duration-200">
                            <i class="fas fa-eye mr-1"></i> View All
                        </a>
                    </div>
                </header>
                <div class="p-6 space-y-4">
                    @foreach($recentOrders as $order)
                    <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex-1">
                            <div class="flex items-center space-x-4">
                                <div>
                                    <a href="{{ route('provider.orders.show', $order->id) }}"
                                       class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                        {{ $order->order_number }}
                                    </a>
                                    <p class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $order->customer_name }}</p>
                                    <p class="text-sm text-emerald-600 font-semibold">${{ number_format($order->total, 2) }}</p>
                                </div>
                            </div>
                        </div>
                        <div>
                            @if($order->status == 'completed')
                                <span class="bg-emerald-100 text-emerald-800 px-2 py-1 rounded-xl text-xs font-medium">Completed</span>
                            @elseif($order->status == 'processing')
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-xl text-xs font-medium">Processing</span>
                            @elseif($order->status == 'pending')
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-xl text-xs font-medium">Pending</span>
                            @elseif($order->status == 'cancelled')
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded-xl text-xs font-medium">Cancelled</span>
                            @endif
                        </div>
                    </div>
                    @endforeach

                    @if(count($recentOrders) == 0)
                    <div class="text-center py-8">
                        <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-shopping-cart text-gray-400 text-2xl"></i>
                        </div>
                        <p class="text-gray-500">No orders yet</p>
                    </div>
                    @endif
                </div>
            </section>
        </div>

        <!-- Activity Overview -->
        <section class="bg-gradient-to-r from-blue-600 to-purple-600 border-0 shadow-xl text-white rounded-lg">
            <header class="p-6 border-b border-white/20 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="bg-white/20 p-3 rounded-xl">
                        <i class="fas fa-rocket text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold">Welcome to your Dashboard!</h3>
                        <p class="text-blue-100">Manage your products, track orders, and grow your business</p>
                    </div>
                </div>
                <a href="{{ route('provider.provider-products.create') }}"
                   class="inline-flex items-center bg-white text-blue-600 hover:bg-blue-50 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 px-4 py-2 rounded-lg">
                    <span class="mr-2">＋</span>
                    Add New Product
                </a>
            </header>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Views -->
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center hover:bg-white/20 transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-eye text-3xl mb-3"></i>
                    <p class="text-2xl font-bold mb-1">{{ isset($totalViews) ? $totalViews : '0' }}</p>
                    <p class="text-blue-100 text-sm">Product Views</p>
                </div>
                <!-- Conversion Rate -->
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center hover:bg-white/20 transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-shopping-bag text-3xl mb-3"></i>
                    <p class="text-2xl font-bold mb-1">{{ isset($conversionRate) ? $conversionRate : '0' }}%</p>
                    <p class="text-blue-100 text-sm">Conversion Rate</p>
                </div>
                <!-- Avg Rating -->
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center hover:bg-white/20 transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-star text-3xl mb-3"></i>
                    <p class="text-2xl font-bold mb-1">{{ isset($avgRating) ? number_format($avgRating, 1) : '0.0' }}</p>
                    <p class="text-blue-100 text-sm">Avg. Rating</p>
                </div>
                <!-- Return Rate -->
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center hover:bg-white/20 transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-redo text-3xl mb-3"></i>
                    <p class="text-2xl font-bold mb-1">{{ isset($returnRate) ? $returnRate : '0' }}%</p>
                    <p class="text-blue-100 text-sm">Return Rate</p>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
