@extends('layouts.dashboard')

@section('title', 'Update Order Item Status')
@section('page-title', 'Update Order Item Status')

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Update Item Status</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">Order #{{ $orderItem->order->order_number ?? 'N/A' }}</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('vendor.orders.show', $orderItem->order_id) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i> Back to Order
            </a>
        </div>
    </div>

    <!-- Product Information -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Product Information</h3>
        </div>
        <div class="p-6">
            <div class="flex items-center mb-4">
                @if($orderItem->product && $orderItem->product->image)
                <div class="flex-shrink-0 h-20 w-20 mr-4">
                    <img class="h-20 w-20 rounded-md object-cover" src="{{ $orderItem->product->image }}" alt="{{ $orderItem->product->name }}">
                </div>
                @endif
                <div>
                    <h4 class="text-lg font-medium text-gray-900 dark:text-white">{{ $orderItem->product->name ?? 'Unknown Product' }}</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Quantity: {{ $orderItem->quantity }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Price: ${{ number_format($orderItem->price, 2) }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total: ${{ number_format($orderItem->price * $orderItem->quantity, 2) }}</p>
                </div>
            </div>

            <!-- Product Specifications -->
            @if($orderItem->specifications && count($orderItem->specifications) > 0)
            <div class="mt-4">
                <h5 class="text-md font-medium text-gray-900 dark:text-white mb-2">Specifications</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($orderItem->specifications as $spec)
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $spec['key'] }}:</span>
                        <span class="text-sm text-gray-900 dark:text-white">{{ $spec['value'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Color and Size Information -->
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($orderItem->hasColorInfo())
                <div>
                    <h5 class="text-md font-medium text-gray-900 dark:text-white mb-2">Color</h5>
                    <div class="flex items-center">
                        @if($orderItem->color_image)
                        <div class="flex-shrink-0 h-10 w-10 mr-2">
                            <img class="h-10 w-10 rounded-md object-cover" src="{{ $orderItem->color_image }}" alt="{{ $orderItem->color_name }}">
                        </div>
                        @elseif($orderItem->color_value)
                        <div class="flex-shrink-0 h-6 w-6 mr-2 rounded-full" style="background-color: {{ $orderItem->color_value }};"></div>
                        @endif
                        <span class="text-sm text-gray-900 dark:text-white">{{ $orderItem->color_name }}</span>
                    </div>
                </div>
                @endif

                @if($orderItem->hasSizeInfo())
                <div>
                    <h5 class="text-md font-medium text-gray-900 dark:text-white mb-2">Size</h5>
                    <span class="text-sm text-gray-900 dark:text-white">{{ $orderItem->size_name }}</span>
                    @if($orderItem->size_value)
                    <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">({{ $orderItem->size_value }})</span>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Update Status Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Update Status</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('vendor.order-items.update-status', $orderItem->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="pending" {{ $orderItem->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $orderItem->status == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ $orderItem->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ $orderItem->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ $orderItem->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                    <textarea id="notes" name="notes" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"></textarea>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Status History -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Status History</h3>
        </div>
        <div class="p-6">
            @if($orderItem->statusHistory && $orderItem->statusHistory->count() > 0)
                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        @foreach($orderItem->statusHistory as $history)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white dark:ring-gray-800
                                                @if($history->status == 'delivered') bg-green-500
                                                @elseif($history->status == 'shipped') bg-blue-500
                                                @elseif($history->status == 'processing') bg-yellow-500
                                                @elseif($history->status == 'cancelled') bg-red-500
                                                @else bg-gray-500 @endif">
                                                <i class="fas fa-circle text-white text-xs"></i>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    Status changed to <span class="font-medium text-gray-900 dark:text-white">{{ ucfirst($history->status) }}</span>
                                                    @if($history->previous_status)
                                                        from <span class="font-medium text-gray-900 dark:text-white">{{ ucfirst($history->previous_status) }}</span>
                                                    @endif
                                                </p>
                                                @if($history->notes)
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $history->notes }}</p>
                                                @endif
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                {{ $history->created_at->format('M d, Y H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No status history available.</p>
            @endif
        </div>
    </div>
</div>
@endsection
