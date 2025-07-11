@extends('layouts.merchant')

@section('title', 'Merchant Dashboard')
@section('header', 'Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50 p-4 sm:p-6 lg:p-8">
<!-- Welcome Section -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center space-x-3 mb-2">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}!</h2>
            </div>
            <p class="text-gray-600">Here's what's happening with your merchant business</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('merchant.products.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Product
            </a>
            <a href="{{ route('merchant.services.create') }}" class="inline-flex items-center px-4 py-2 bg-violet-600 text-white text-sm font-medium rounded-lg hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Service
            </a>
        </div>
    </div>
</div>

<!-- Search and Quick Actions -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0 sm:space-x-4">
        <div class="flex-1 max-w-lg">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input
                    type="text"
                    placeholder="Search across all your products and services..."
                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                >
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('merchant.products.index') }}" class="inline-flex items-center px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"></path>
                </svg>
                Browse Products
            </a>
            <a href="{{ route('merchant.services.index') }}" class="inline-flex items-center px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                Browse Services
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-4 sm:gap-6 mb-8">
    <!-- Products Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 cursor-pointer group">
        <div class="flex items-center justify-between h-full">
            <div class="flex-1 min-w-0">
                <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1 sm:mb-2 truncate">Products</p>
                <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 leading-tight">{{ $totalProducts }}</p>
            </div>
            <div class="flex-shrink-0 ml-3 sm:ml-4">
                <div class="p-2 sm:p-3 bg-blue-100 rounded-lg group-hover:bg-blue-200 transition-colors duration-300">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 cursor-pointer group">
        <div class="flex items-center justify-between h-full">
            <div class="flex-1 min-w-0">
                <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1 sm:mb-2 truncate">Services</p>
                <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 leading-tight">{{ $totalServices }}</p>
            </div>
            <div class="flex-shrink-0 ml-3 sm:ml-4">
                <div class="p-2 sm:p-3 bg-green-100 rounded-lg group-hover:bg-green-200 transition-colors duration-300">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 cursor-pointer group">
        <div class="flex items-center justify-between h-full">
            <div class="flex-1 min-w-0">
                <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1 sm:mb-2 truncate">Orders</p>
                <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 leading-tight">{{ $totalOrders }}</p>
            </div>
            <div class="flex-shrink-0 ml-3 sm:ml-4">
                <div class="p-2 sm:p-3 bg-yellow-100 rounded-lg group-hover:bg-yellow-200 transition-colors duration-300">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Customers Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 cursor-pointer group">
        <div class="flex items-center justify-between h-full">
            <div class="flex-1 min-w-0">
                <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1 sm:mb-2 truncate">Customers</p>
                <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 leading-tight">{{ $totalCustomers }}</p>
            </div>
            <div class="flex-shrink-0 ml-3 sm:ml-4">
                <div class="p-2 sm:p-3 bg-purple-100 rounded-lg group-hover:bg-purple-200 transition-colors duration-300">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Rating Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 cursor-pointer group">
        <div class="flex items-center justify-between h-full">
            <div class="flex-1 min-w-0">
                <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1 sm:mb-2 truncate">Average Rating</p>
                <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 leading-tight">{{ number_format($averageRating, 1) }}</p>
                <p class="text-xs text-gray-500 mt-1 truncate">{{ $totalRatings }} reviews</p>
            </div>
            <div class="flex-shrink-0 ml-3 sm:ml-4">
                <div class="p-2 sm:p-3 bg-orange-100 rounded-lg group-hover:bg-orange-200 transition-colors duration-300">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Views Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 cursor-pointer group">
        <div class="flex items-center justify-between h-full">
            <div class="flex-1 min-w-0">
                <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1 sm:mb-2 truncate">Profile Views</p>
                <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 leading-tight">{{ $viewCount }}</p>
            </div>
            <div class="flex-shrink-0 ml-3 sm:ml-4">
                <div class="p-2 sm:p-3 bg-indigo-100 rounded-lg group-hover:bg-indigo-200 transition-colors duration-300">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Area -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Recent Products -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-md">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-blue-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <div class="p-2 bg-blue-500 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"></path>
                        </svg>
                    </div>
                    Recent Products
                </h3>
                <a href="{{ route('merchant.products.index') }}" class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    View All
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentProducts as $product)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($product->image)
                                        <img class="h-10 w-10 rounded-lg object-cover border border-gray-200" src="{{ $product->image }}" alt="{{ $product->name }}">
                                    @else
                                        <div class="h-10 w-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($product->description, 30) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">${{ number_format($product->price, 2) }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $product->is_available ? 'bg-green-400' : 'bg-red-400' }}"></span>
                                {{ $product->is_available ? 'Available' : 'Unavailable' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No products</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating your first product.</p>
                            <div class="mt-6">
                                <a href="{{ route('merchant.products.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Add Your First Product
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Services -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-md">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-green-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <div class="p-2 bg-green-500 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    Recent Services
                </h3>
                <a href="{{ route('merchant.services.index') }}" class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    View All
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentServices as $service)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($service->image)
                                        <img class="h-10 w-10 rounded-lg object-cover border border-gray-200" src="{{ $service->image }}" alt="{{ $service->name }}">
                                    @else
                                        <div class="h-10 w-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $service->name }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($service->description, 30) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">${{ number_format($service->price, 2) }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($service->status ?? 'active') === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ ($service->status ?? 'active') === 'active' ? 'bg-green-400' : 'bg-red-400' }}"></span>
                                {{ ucfirst($service->status ?? 'active') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No services</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating your first service.</p>
                            <div class="mt-6">
                                <a href="{{ route('merchant.services.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Add Your First Service
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Account Summary -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-md">
    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-indigo-100">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <div class="p-2 bg-indigo-500 rounded-lg mr-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            Account Summary
        </h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Merchant Score -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-500 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-blue-700">{{ $merchant->merchant_score ?? 0 }}</div>
                        <div class="text-xs text-blue-600 font-medium">/ 100</div>
                    </div>
                </div>
                <div class="text-sm font-medium text-blue-700">Merchant Score</div>
                
            </div>

            <!-- Account Status -->
            <div class="bg-gradient-to-br {{ $merchant->status === 'active' ? 'from-green-50 to-green-100' : 'from-red-50 to-red-100' }} rounded-xl p-6 border {{ $merchant->status === 'active' ? 'border-green-200' : 'border-red-200' }}">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 {{ $merchant->status === 'active' ? 'bg-orange-500' : 'bg-red-500' }} rounded-lg">
                        @if($merchant->status === 'active')
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        @else
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        @endif
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold {{ $merchant->status === 'active' ? 'text-orange-700' : 'text-red-700' }}">
                            {{ $merchant->status === 'active' ? 'Active' : ucfirst($merchant->status) }}
                        </div>
                    </div>
                </div>
                <div class="text-sm font-medium {{ $merchant->status === 'active' ? 'text-orange-700' : 'text-red-700' }}">Account Status</div>
            </div>

            <!-- Verification Status -->
            <div class="bg-gradient-to-br {{ $merchant->is_verified ? 'from-green-50 to-green-100' : 'from-yellow-50 to-yellow-100' }} rounded-xl p-6 border {{ $merchant->is_verified ? 'border-green-200' : 'border-yellow-200' }}">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 {{ $merchant->is_verified ? 'bg-green-500' : 'bg-yellow-500' }} rounded-lg">
                        @if($merchant->is_verified)
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        @else
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @endif
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold {{ $merchant->is_verified ? 'text-green-700' : 'text-yellow-700' }}">
                            {{ $merchant->is_verified ? 'Verified' : 'Pending' }}
                        </div>
                    </div>
                </div>
                <div class="text-sm font-medium {{ $merchant->is_verified ? 'text-green-700' : 'text-yellow-700' }}">Verification Status</div>
            </div>

            <!-- Location -->
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 border border-purple-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-purple-500 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-purple-700">
                            {{ $merchant->emirate ?? 'Not Set' }}
                        </div>
                    </div>
                </div>
                <div class="text-sm font-medium text-purple-700">Location</div>
            </div>
        </div>

        @if(!$merchant->is_verified)
        <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <div>
                    <h4 class="text-sm font-medium text-yellow-800">Account Verification Pending</h4>
                    <p class="mt-1 text-sm text-yellow-700">
                        Your merchant account is pending admin verification. You can still manage your products and services, but some features may be limited until verification is complete.
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
</div>
@endsection

@section('scripts')
<script>
// Dashboard-specific search handling
document.addEventListener('DOMContentLoaded', function() {
    // Handle search results display on dashboard
    document.addEventListener('merchantSearch', function(e) {
        const query = e.detail.query;
        console.log('Dashboard search triggered for:', query);

        // The search component will handle displaying results
        // Additional dashboard-specific logic can be added here
    });

    // Auto-focus search input on page load
    const searchInput = document.querySelector('.merchant-search-input');
    if (searchInput && !window.location.search) {
        // Only auto-focus if there are no URL parameters (not coming from a search)
        setTimeout(() => {
            searchInput.focus();
        }, 500);
    }
});
</script>
@endsection
