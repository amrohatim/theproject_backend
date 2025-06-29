@extends('layouts.dashboard')

@section('title', 'Vendor Dashboard')
@section('page-title', 'Vendor Dashboard')

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
    <!-- Welcome message -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Welcome, {{ auth()->user()->name ?? 'Vendor' }}!</h2>
                <p class="text-gray-600 dark:text-gray-400">Here's what's happening with your business</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('vendor.image.test') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-image mr-2"></i> Test Images
                </a>
                <a href="{{ route('vendor.fix.images') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-wrench mr-2"></i> Fix Images
                </a>
            </div>
        </div>
    </div>

    @if(!isset($hasCompany) || !$hasCompany)
    <!-- Company Registration Alert -->
    <div class="bg-yellow-50 dark:bg-yellow-900/30 border-l-4 border-yellow-400 p-4 mb-8">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700 dark:text-yellow-200">
                    You haven't registered your company yet. Please register your company to start selling products and services.
                </p>
                <div class="mt-3">
                    <a href="{{ route('vendor.company.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-building mr-2"></i> Register Company
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Stats cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Branches stat -->
        <div class="stat-card bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                    <i class="fas fa-store text-blue-500 dark:text-blue-400 text-xl"></i>
                </div>
                <div class="flex items-center">
                    <span class="text-green-500 text-sm font-semibold mr-1">+5%</span>
                    <i class="fas fa-arrow-up text-green-500 text-xs"></i>
                </div>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-gray-800 dark:text-white">{{ $totalBranches ?? 3 }}</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Branches</p>
            </div>
        </div>

        <!-- Products stat -->
        <div class="stat-card bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                    <i class="fas fa-shopping-bag text-green-500 dark:text-green-400 text-xl"></i>
                </div>
                <div class="flex items-center">
                    <span class="text-green-500 text-sm font-semibold mr-1">+12%</span>
                    <i class="fas fa-arrow-up text-green-500 text-xs"></i>
                </div>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-gray-800 dark:text-white">{{ $totalProducts ?? 12 }}</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Products</p>
            </div>
        </div>

        <!-- Services stat -->
        <div class="stat-card bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                    <i class="fas fa-concierge-bell text-purple-500 dark:text-purple-400 text-xl"></i>
                </div>
                <div class="flex items-center">
                    <span class="text-green-500 text-sm font-semibold mr-1">+8%</span>
                    <i class="fas fa-arrow-up text-green-500 text-xs"></i>
                </div>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-gray-800 dark:text-white">{{ $totalServices ?? 8 }}</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Services</p>
            </div>
        </div>

        <!-- Orders stat -->
        <div class="stat-card bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 rounded-full bg-orange-100 dark:bg-orange-900">
                    <i class="fas fa-shopping-cart text-orange-500 dark:text-orange-400 text-xl"></i>
                </div>
                <div class="flex items-center">
                    <span class="text-green-500 text-sm font-semibold mr-1">+15%</span>
                    <i class="fas fa-arrow-up text-green-500 text-xs"></i>
                </div>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-gray-800 dark:text-white">{{ $totalOrders ?? 24 }}</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Orders</p>
            </div>
        </div>
    </div>

    <!-- Recent products -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Recent Products</h3>
            <a href="{{ route('vendor.products.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 text-sm font-medium">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Branch</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($recentProducts ?? [] as $product)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @php
                                        // Get the image URL directly from the product model
                                        // The accessor in the model will handle the path resolution
                                        $imageUrl = $product->image;

                                        // Fallback to a placeholder if no image is available
                                        $placeholderUrl = asset('images/placeholder.jpg');
                                    @endphp

                                    <img
                                        src="{{ $imageUrl }}"
                                        alt="{{ $product->name }}"
                                        class="h-10 w-10 rounded-md object-cover"
                                        onerror="this.onerror=null; this.src='{{ $placeholderUrl }}'; if (!this.src) this.parentElement.innerHTML='<div class=\'h-10 w-10 rounded-md bg-gray-200 dark:bg-gray-700 flex items-center justify-center\'><i class=\'fas fa-image text-gray-400\'></i></div>';"
                                    >
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $product->branch->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($product->price, 2) }}</div>
                            @if($product->original_price)
                                <div class="text-xs text-gray-500 dark:text-gray-400 line-through">${{ number_format($product->original_price, 2) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($product->is_available) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                {{ $product->is_available ? 'Available' : 'Out of Stock' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center py-4">
                                <i class="fas fa-shopping-bag text-gray-300 dark:text-gray-600 text-5xl mb-4"></i>
                                <p>No products found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent services -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Recent Services</h3>
            <a href="{{ route('vendor.services.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 text-sm font-medium">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Branch</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Duration</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($recentServices ?? [] as $service)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($service->image)
                                        <img src="{{ $service->image }}" alt="{{ $service->name }}" class="h-10 w-10 rounded-md object-cover"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="h-10 w-10 rounded-md bg-gray-200 dark:bg-gray-700 flex items-center justify-center" style="display: none;">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                    @else
                                        <div class="h-10 w-10 rounded-md bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $service->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $service->branch->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($service->price, 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $service->duration }} min</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center py-4">
                                <i class="fas fa-concierge-bell text-gray-300 dark:text-gray-600 text-5xl mb-4"></i>
                                <p>No services found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
