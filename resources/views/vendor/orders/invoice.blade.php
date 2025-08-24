@extends('layouts.dashboard')

@section('title', 'Order Invoice')
@section('page-title', 'Order Invoice')

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Invoice #{{ $order->order_number ?? 'N/A' }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $order->created_at ? $order->created_at->format('F d, Y') : 'N/A' }}</p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-2">
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-print mr-2"></i> Print Invoice
            </button>
            <a href="{{ route('vendor.orders.show', $order->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i> Back to Order
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700 print:shadow-none print:border-none">
        <!-- Invoice Header -->
        <div class="flex flex-col md:flex-row justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">INVOICE</h1>
                <p class="text-gray-600 dark:text-gray-400">Order #{{ $order->order_number ?? 'N/A' }}</p>
                <p class="text-gray-600 dark:text-gray-400">Date: {{ $order->created_at ? $order->created_at->format('M d, Y') : 'N/A' }}</p>
            </div>
            <div class="mt-4 md:mt-0">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $order->branch->company->name ?? 'Company Name' }}</h2>
                <p class="text-gray-600 dark:text-gray-400">{{ $order->branch->address ?? 'Branch Address' }}</p>
                <p class="text-gray-600 dark:text-gray-400">{{ $order->branch->phone ?? 'Phone' }}</p>
                <p class="text-gray-600 dark:text-gray-400">{{ $order->branch->email ?? 'Email' }}</p>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Bill To:</h3>
            <p class="text-gray-800 dark:text-gray-200">{{ $order->user->name ?? 'Customer Name' }}</p>
            <p class="text-gray-600 dark:text-gray-400">{{ $order->user->email ?? 'Email' }}</p>
            <p class="text-gray-600 dark:text-gray-400">{{ $order->user->phone ?? 'Phone' }}</p>
            @if(isset($order->shipping_address) && is_array($order->shipping_address))
                <p class="text-gray-600 dark:text-gray-400">
                    {{ $order->shipping_address['address'] ?? '' }},
                    {{ $order->shipping_address['city'] ?? '' }},
                    {{ $order->shipping_address['state'] ?? '' }},
                    {{ $order->shipping_address['zip'] ?? '' }},
                    {{ $order->shipping_address['country'] ?? '' }}
                </p>
            @endif
        </div>

        <!-- Order Items -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                @if(isset($companyId))
                    Your Items in This Order:
                @else
                    Order Items:
                @endif
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Item</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantity</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @php
                            // Filter items to only show those belonging to this vendor if companyId is set
                            $itemsToShow = isset($companyId) ? $vendorItems : $order->items;
                            $subtotal = 0;
                        @endphp

                        @forelse($itemsToShow ?? [] as $item)
                        @php
                            $subtotal += $item->price * $item->quantity;
                        @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $item->product->name ?? 'Unknown Product' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white text-right">
                                ${{ number_format($item->price, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white text-right">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white text-right">
                                ${{ number_format($item->price * $item->quantity, 2) }}
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
                    <tfoot>
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">
                                @if(isset($companyId) && isset($vendorSubtotal))
                                    Your Subtotal:
                                @else
                                    Subtotal:
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white text-right">
                                @if(isset($companyId) && isset($vendorSubtotal))
                                    ${{ number_format($vendorSubtotal, 2) }}
                                @else
                                    ${{ number_format($subtotal, 2) }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">Tax:</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white text-right">$0.00</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right text-sm font-bold text-gray-900 dark:text-white">
                                @if(isset($companyId) && isset($vendorSubtotal))
                                    Your Total:
                                @else
                                    Total:
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white text-right">
                                @if(isset($companyId) && isset($vendorSubtotal))
                                    ${{ number_format($vendorSubtotal, 2) }}
                                @else
                                    ${{ number_format($order->total, 2) }}
                                @endif
                            </td>
                        </tr>
                        @if(isset($companyId) && isset($vendorSubtotal))
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Order Total (All Vendors):</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500 dark:text-gray-400 text-right">${{ number_format($order->total, 2) }}</td>
                        </tr>
                        @endif
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Payment Information:</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-600 dark:text-gray-400">Payment Status: <span class="font-medium text-gray-900 dark:text-white">{{ ucfirst($order->payment_status ?? 'pending') }}</span></p>
                    <p class="text-gray-600 dark:text-gray-400">Payment Method: <span class="font-medium text-gray-900 dark:text-white">{{ ucfirst($order->payment_method ?? 'N/A') }}</span></p>
                </div>
                <div>
                    <p class="text-gray-600 dark:text-gray-400">Order Status: <span class="font-medium text-gray-900 dark:text-white">{{ ucfirst($order->status ?? 'pending') }}</span></p>
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($order->notes)
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Notes:</h3>
            <p class="text-gray-600 dark:text-gray-400">{{ $order->notes }}</p>
        </div>
        @endif

        <!-- Thank You Message -->
        <div class="text-center mt-12 mb-6">
            <p class="text-gray-600 dark:text-gray-400">Thank you for your business!</p>
        </div>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .container, .container * {
            visibility: visible;
        }
        .container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .print\:shadow-none {
            box-shadow: none !important;
        }
        .print\:border-none {
            border: none !important;
        }
        button, a {
            display: none !important;
        }
    }
</style>
@endsection
