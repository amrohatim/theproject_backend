@extends('layouts.dashboard')

@section('title', __('messages.orders_management'))
@section('page-title', __('messages.orders_management'))

@section('styles')
<style>
    @media (max-width: 768px) {
        .responsive-table thead {
            display: none;
        }

        .responsive-table,
        .responsive-table tbody,
        .responsive-table tr,
        .responsive-table td {
            display: block;
            width: 100%;
        }

        .responsive-table tbody tr {
            margin-bottom: 1rem;
            border: 1px solid #3b82f6 !important;
            border-radius: 0.375rem !important;
            overflow: hidden;
            background-color: #ffffff;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06);
        }

        .dark .responsive-table tbody tr {
            background-color: #1f2937;
            border-color: #60a5fa !important;
        }

        .responsive-table td {
            position: relative;
            padding: 0.75rem 1rem 0.75rem 9.5rem;
            text-align: left;
            white-space: normal;
        }

        .responsive-table td::before {
            content: attr(data-label);
            position: absolute;
            left: 1rem;
            top: 0.75rem;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #6b7280;
        }

        .dark .responsive-table td::before {
            color: #9ca3af;
        }

        .responsive-table td:last-child {
            text-align: left;
        }
    }
</style>
@endsection

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('messages.orders_management') }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('messages.manage_product_orders_from_customers') }}</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('vendor.orders.export') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-file-export mr-2"></i> {{ __('messages.export_orders') }}
            </a>
        </div>
    </div>

    <!-- Search and filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 p-4 border border-gray-200 dark:border-gray-700">
        <form action="{{ route('vendor.orders.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.search') }}</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" id="search" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-1 sm:text-xs border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"  placeholder="{{ __('messages.search_by_order_id_or_customer_name') }}">
                    </div>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.status') }}</label>
                    <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">{{ __('messages.all_status') }}</option>
                        <option value="pending">{{ __('messages.pending') }}</option>
                        <option value="processing">{{ __('messages.processing') }}</option>
                        <option value="shipped">{{ __('messages.shipped') }}</option>
                        <option value="delivered">{{ __('messages.delivered') }}</option>
                        <option value="cancelled">{{ __('messages.cancelled') }}</option>
                    </select>
                </div>

                <div>
                    <label for="branch" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.branch') }}</label>
                    <select id="branch" name="branch" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">{{ __('messages.all_branches') }}</option>
                        @foreach($branches ?? [] as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="date_range" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.date_range') }}</label>
                    <select id="date_range" name="date_range" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">{{ __('messages.all_time') }}</option>
                        <option value="">{{ __('messages.today') }}</option>
                        <option value="">{{ __('messages.yesterday') }}</option>
                        <option value="">{{ __('messages.this_week') }}</option>
                        <option value="">{{ __('messages.last_week') }}</option>
                        <option value="">{{ __('messages.this_month') }}</option>
                        <option value="">{{ __('messages.last_month') }}</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-filter mr-2"></i> {{ __('messages.filter') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Orders Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100 dark:bg-indigo-900 mr-4">
                    <i class="fas fa-shopping-cart text-indigo-500 dark:text-indigo-400"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.total_orders') }}</p>
                    <p class="text-2xl font-semibold text-gray-800 dark:text-white">{{ $stats->total_orders ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900 mr-4">
                    <i class="fas fa-check-circle text-green-500 dark:text-green-400"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.completed_orders') }}</p>
                    <p class="text-2xl font-semibold text-gray-800 dark:text-white">{{ $stats->completed_orders ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900 mr-4">
                    <i class="fas fa-clock text-yellow-500 dark:text-yellow-400"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.pending_orders') }}</p>
                    <p class="text-2xl font-semibold text-gray-800 dark:text-white">{{ $stats->pending_orders ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 mr-4">
                    <i class="fas fa-dollar-sign text-blue-500 dark:text-blue-400"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.total_revenue') }}</p>
                    <p class="text-2xl font-semibold text-gray-800 dark:text-white">${{ number_format($stats->total_revenue ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders list -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
        <div class="overflow-x-auto">
            <table class="responsive-table min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.order_id') }}</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.customer') }}</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.branch') }}</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.date') }}</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.total') }}</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.status') }}</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($orders ?? [] as $order)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap" data-label="{{ __('messages.order_id') }}">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">#{{ $order->order_number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-label="{{ __('messages.customer') }}">
                            <div class="text-sm text-gray-900 dark:text-white">{{ $order->user->name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $order->user->email ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-label="{{ __('messages.branch') }}">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $order->branch->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-label="{{ __('messages.date') }}">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $order->created_at ? $order->created_at->format('M d, Y') : 'N/A' }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $order->created_at ? $order->created_at->format('h:i A') : '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-label="{{ __('messages.total') }}">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($order->total, 2) }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $order->items->count() ?? 0 }} {{ __('messages.items') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-label="{{ __('messages.status') }}">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($order->status == 'delivered') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($order->status == 'shipped') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @elseif($order->status == 'processing') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @elseif($order->status == 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                {{ __('messages.' . ($order->status ?? 'pending')) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" data-label="{{ __('messages.actions') }}">
                            <a href="{{ route('vendor.orders.show', $order->id) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-3" title="{{ __('messages.view') }}">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('vendor.orders.edit', $order->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 mr-3" title="{{ __('messages.edit') }}">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('vendor.orders.invoice', $order->id) }}" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300" title="{{ __('messages.invoice') }}">
                                <i class="fas fa-file-invoice"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center py-4">
                                <i class="fas fa-shopping-cart text-gray-300 dark:text-gray-600 text-5xl mb-4"></i>
                                <p>{{ __('messages.no_orders_found') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        @if(method_exists($orders, 'links'))
            {{ $orders->links() }}
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/vendor-autocomplete.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    if (searchInput) {
        new VendorAutoComplete(searchInput, {
            apiUrl: '{{ route('vendor.orders.search-suggestions') }}',
            placeholder: '{{ __('messages.search_orders_customers_order_numbers') }}'
        });
    }
});
</script>
@endpush
