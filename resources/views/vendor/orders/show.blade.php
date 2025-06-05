@extends('layouts.dashboard')

@section('title', 'Order Details')
@section('page-title', 'Order Details')

@push('scripts')
<script>
    function toggleProductDetails(elementId) {
        const element = document.getElementById(elementId);
        if (element) {
            if (element.classList.contains('hidden')) {
                element.classList.remove('hidden');
            } else {
                element.classList.add('hidden');
            }
        }
    }
</script>
@endpush

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Order #{{ $order->order_number ?? 'N/A' }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $order->created_at ? $order->created_at->format('F d, Y h:i A') : 'N/A' }}</p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-2">
            @if(isset($allItemsBelongToVendor) && $allItemsBelongToVendor)
            <a href="{{ route('vendor.orders.edit', $order->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-edit mr-2"></i> Edit Order
            </a>
            @endif
            <a href="{{ route('vendor.orders.invoice', $order->id) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-file-invoice mr-2"></i> Invoice
            </a>
            <a href="{{ route('vendor.orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Order Status -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Order Status</h3>

            <!-- Overall Order Status -->
            <div class="mb-4">
                <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">Overall Order Status:</div>
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                    @if($order->status == 'delivered') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                    @elseif($order->status == 'shipped') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                    @elseif($order->status == 'processing') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                    @elseif($order->status == 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                    @elseif($order->status == 'partially_shipped') bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200
                    @elseif($order->status == 'partially_delivered') bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200
                    @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                    {{ ucfirst(str_replace('_', ' ', $order->status ?? 'pending')) }}
                </span>
            </div>

            <!-- Vendor-specific Status -->
            @if(isset($vendorStatus))
            <div class="mb-4">
                <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">Your Status for This Order:</div>
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                    @if($vendorStatus->status == 'delivered') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                    @elseif($vendorStatus->status == 'shipped') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                    @elseif($vendorStatus->status == 'processing') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                    @elseif($vendorStatus->status == 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                    @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                    {{ ucfirst($vendorStatus->status ?? 'pending') }}
                </span>
                @if($vendorStatus->notes)
                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400 italic">
                    Note: {{ $vendorStatus->notes }}
                </div>
                @endif
            </div>
            @endif

            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Order Date:</span>
                    <span class="text-sm text-gray-900 dark:text-white">{{ $order->created_at ? $order->created_at->format('M d, Y') : 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Payment Status:</span>
                    <span class="text-sm text-gray-900 dark:text-white">{{ ucfirst($order->payment_status ?? 'pending') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Payment Method:</span>
                    <span class="text-sm text-gray-900 dark:text-white">{{ ucfirst($order->payment_method ?? 'N/A') }}</span>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Customer Information</h3>
            <div class="space-y-2">
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Name:</span>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $order->user->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Email:</span>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $order->user->email ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Phone:</span>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $order->user->phone ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Branch Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Branch Information</h3>
            <div class="space-y-2">
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Branch:</span>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $order->branch->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Address:</span>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $order->branch->address ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Phone:</span>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $order->branch->phone ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    @if(isset($allItemsBelongToVendor) && !$allItemsBelongToVendor)
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    <strong>Note:</strong> This order contains products from multiple vendors. You are only seeing your products below.
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Update All Vendor Items Form -->
    @if(isset($companyId) && $order->items->where('vendor_id', $companyId)->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Update All Your Items</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('vendor.order-items.update-vendor-items-status', $order->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="pending" {{ isset($vendorStatus) && $vendorStatus->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ isset($vendorStatus) && $vendorStatus->status == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ isset($vendorStatus) && $vendorStatus->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ isset($vendorStatus) && $vendorStatus->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ isset($vendorStatus) && $vendorStatus->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                        <textarea id="notes" name="notes" rows="1" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"></textarea>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Update All Your Items
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Order Items -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                @if(isset($allItemsBelongToVendor) && !$allItemsBelongToVendor)
                    Your Products in This Order
                @else
                    Order Items
                @endif
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantity</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @php
                        // Filter items to only show those belonging to this vendor if companyId is set
                        $itemsToShow = isset($companyId) ? $order->items->where('vendor_id', $companyId) : $order->items;
                        $subtotal = 0;
                    @endphp

                    @forelse($itemsToShow ?? [] as $item)
                    @php
                        $subtotal += $item->price * $item->quantity;
                    @endphp
                    <tr>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($item->product && $item->product->image)
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-md object-cover" src="{{ $item->product->image }}" alt="{{ $item->product->name }}">
                                </div>
                                @elseif($item->color_image)
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-md object-cover" src="{{ $item->color_image }}" alt="{{ $item->product->name }}">
                                </div>
                                @endif
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $item->product->name ?? 'Unknown Product' }}
                                        <a href="#" onclick="toggleProductDetails('product-details-{{ $item->id }}'); return false;" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 ml-2 text-xs">
                                            <i class="fas fa-info-circle"></i> View Details
                                        </a>
                                    </div>

                                    <!-- Display color and size if available -->
                                    @if($item->color_name || $item->size_name)
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        @if($item->color_name)
                                            <span class="inline-flex items-center">
                                                @if($item->color_value)
                                                <span class="inline-block h-3 w-3 rounded-full mr-1" style="background-color: {{ $item->color_value }};"></span>
                                                @endif
                                                Color: {{ $item->color_name }}
                                            </span>
                                        @endif

                                        @if($item->color_name && $item->size_name)
                                            <span class="mx-1">|</span>
                                        @endif

                                        @if($item->size_name)
                                            <span>Size: {{ $item->size_name }}</span>
                                        @endif
                                    </div>
                                    @endif

                                    <!-- Product Details (hidden by default) -->
                                    <div id="product-details-{{ $item->id }}" class="hidden mt-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-md text-xs">
                                        @if($item->product)
                                            <h4 class="font-semibold mb-2">Product Details</h4>

                                            @if($item->product && $item->product->description)
                                                <div class="mb-2">
                                                    <span class="font-medium">Description:</span>
                                                    <p class="text-gray-600 dark:text-gray-300">{{ $item->product->description }}</p>
                                                </div>
                                            @endif

                                            @if($item->product && $item->product->specifications()->count() > 0)
                                                <div class="mb-2">
                                                    <span class="font-medium">Specifications:</span>
                                                    <ul class="list-disc list-inside text-gray-600 dark:text-gray-300 pl-2">
                                                        @foreach($item->product->specifications as $spec)
                                                            <li><span class="font-medium">{{ $spec->key }}:</span> {{ $spec->value }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif

                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <span class="font-medium">SKU:</span>
                                                    <span class="text-gray-600 dark:text-gray-300">{{ $item->product->sku ?? 'N/A' }}</span>
                                                </div>

                                                <div>
                                                    <span class="font-medium">Category:</span>
                                                    <span class="text-gray-600 dark:text-gray-300">{{ $item->product->category->name ?? 'N/A' }}</span>
                                                </div>

                                                <div>
                                                    <span class="font-medium">Price:</span>
                                                    <span class="text-gray-600 dark:text-gray-300">{{ number_format($item->product->price ?? 0, 2) }}</span>
                                                </div>

                                                <div>
                                                    <span class="font-medium">Stock:</span>
                                                    <span class="text-gray-600 dark:text-gray-300">{{ $item->product->stock ?? 'N/A' }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <p class="text-gray-600 dark:text-gray-300">Product details not available</p>
                                        @endif
                                    </div>

                                    <!-- Item Status Badge -->
                                    <div class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($item->status == 'delivered') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @elseif($item->status == 'shipped') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                            @elseif($item->status == 'processing') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @elseif($item->status == 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                            {{ ucfirst($item->status ?? 'pending') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">${{ number_format($item->price, 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">{{ $item->quantity }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">${{ number_format($item->price * $item->quantity, 2) }}</div>

                            <!-- Item Actions -->
                            <div class="mt-2 flex space-x-2">
                                <a href="{{ route('vendor.order-items.edit', $item->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 text-sm">
                                    Update Status
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            No items found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-500 dark:text-gray-300">
                            @if(isset($allItemsBelongToVendor) && !$allItemsBelongToVendor)
                                Your Subtotal:
                            @else
                                Total:
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            @if(isset($allItemsBelongToVendor) && !$allItemsBelongToVendor)
                                ${{ number_format($subtotal, 2) }}
                            @else
                                ${{ number_format($order->total, 2) }}
                            @endif
                        </td>
                    </tr>
                    @if(isset($allItemsBelongToVendor) && !$allItemsBelongToVendor)
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-500 dark:text-gray-300">Order Total (All Vendors):</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">${{ number_format($order->total, 2) }}</td>
                    </tr>
                    @endif
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Vendor Status History -->
    @if(isset($vendorStatus) && isset($vendorStatusHistory))
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Your Status History</h3>
        </div>
        <div class="p-6">
            @if($vendorStatusHistory->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Previous Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Notes</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Updated By</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($vendorStatusHistory as $history)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $history->created_at->format('M d, Y h:i A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($history->status == 'delivered') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($history->status == 'shipped') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                        @elseif($history->status == 'processing') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @elseif($history->status == 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                        {{ ucfirst($history->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    @if($history->previous_status)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($history->previous_status == 'delivered') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($history->previous_status == 'shipped') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                        @elseif($history->previous_status == 'processing') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @elseif($history->previous_status == 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                        {{ ucfirst($history->previous_status) }}
                                    </span>
                                    @else
                                    <span class="text-gray-500 dark:text-gray-400">None</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                    {{ $history->notes ?? 'No notes' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                    {{ $history->updatedByUser->name ?? 'System' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-700 dark:text-gray-300">No status history available.</p>
            @endif
        </div>
    </div>
    @endif

    <!-- Notes -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Notes</h3>
        </div>
        <div class="p-6">
            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $order->notes ?? 'No notes available.' }}</p>
        </div>
    </div>
</div>
@endsection
