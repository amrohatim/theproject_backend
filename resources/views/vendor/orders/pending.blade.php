@extends('layouts.dashboard')

@section('title', __('messages.pending_orders'))
@section('page-title', __('messages.pending_orders'))

@section('styles')
<style>
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .status-pending {
        background-color: #FEF3C7;
        color: #92400E;
    }
    .status-processing {
        background-color: #DBEAFE;
        color: #1E40AF;
    }
    .status-shipped {
        background-color: #E0E7FF;
        color: #3730A3;
    }
    .status-delivered {
        background-color: #D1FAE5;
        color: #065F46;
    }
    .status-cancelled {
        background-color: #FEE2E2;
        color: #B91C1C;
    }

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
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('messages.pending_orders') }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('messages.manage_pending_orders') }}</p>
        </div>
    </div>

    <!-- Bulk Actions Form -->
    <form id="bulk-actions-form" action="{{ route('vendor.orders.update-multiple-status') }}" method="POST" class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 p-4 border border-gray-200 dark:border-gray-700">
        @csrf
        <div class="flex flex-col md:flex-row md:items-end space-y-4 md:space-y-0 md:space-x-4">
            <div class="flex-grow">
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.update_status') }}</label>
                <select name="status" id="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">{{ __('messages.select_status') }}</option>
                    <option value="processing">{{ __('messages.processing') }}</option>
                    <option value="shipped">{{ __('messages.shipped') }}</option>
                    <option value="delivered">{{ __('messages.delivered') }}</option>
                    <option value="cancelled">{{ __('messages.cancelled') }}</option>
                </select>
            </div>
           
            <div>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50" id="update-status-btn" disabled>
                    {{ __('messages.update_selected_items') }}
                </button>
            </div>
        </div>
    </form>

    <!-- Order Items List -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
        <div class="overflow-x-auto">
            <table class="responsive-table min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left">
                            <div class="flex items-center">
                                <input type="checkbox" id="select-all" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-700 dark:bg-gray-800 rounded">
                                <label for="select-all" class="sr-only">{{ __('messages.select_all') }}</label>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.order_number') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.customer') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.product') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.date') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.qty') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.total') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.status') }}</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($orderItems as $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap" data-label="{{ __('messages.select_all') }}">
                            <div class="flex items-center">
                                <input type="checkbox" name="item_ids[]" form="bulk-actions-form" value="{{ $item->id }}" class="item-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-700 dark:bg-gray-800 rounded">
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-label="{{ __('messages.order_number') }}">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                <a href="{{ route('vendor.orders.show', $item->order_id) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                    {{ $item->order->order_number ?? 'N/A' }}
                                </a>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-label="{{ __('messages.customer') }}">
                            <div class="text-sm text-gray-900 dark:text-white">{{ $item->order->user->name ?? __('messages.guest') }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $item->order->user->email ?? __('messages.no_email') }}</div>
                        </td>
                        <td class="px-6 py-4" data-label="{{ __('messages.product') }}">
                            <div class="text-sm text-gray-900 dark:text-white">{{ $item->product->name ?? __('messages.unknown_product') }}</div>
                            @if($item->color_name || $item->size_name)
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    @if($item->color_name)
                                        <span class="inline-flex items-center mr-2">
                                            <span class="w-3 h-3 inline-block mr-1 rounded-full" style="background-color: {{ $item->color_value ?? '#ccc' }}"></span>
                                            {{ $item->color_name }}
                                        </span>
                                    @endif
                                    @if($item->size_name)
                                        <span>{{ $item->size_name }}</span>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-label="{{ __('messages.date') }}">
                            <div class="text-sm text-gray-900 dark:text-white">
                                {{ $item->created_at ? $item->created_at->format('M d, Y') : 'N/A' }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $item->created_at ? $item->created_at->format('h:i A') : '' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white" data-label="{{ __('messages.qty') }}">
                            {{ $item->quantity }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white" data-label="{{ __('messages.total') }}">
                            ${{ number_format($item->total, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap" data-label="{{ __('messages.status') }}">
                            <span class="status-badge status-{{ strtolower($item->status) }}">
                                {{ __('messages.' . $item->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" data-label="{{ __('messages.actions') }}">
                            <a href="{{ route('vendor.order-items.edit', $item->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('vendor.orders.show', $item->order_id) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center py-4">
                                <i class="fas fa-check-circle text-green-500 dark:text-green-400 text-5xl mb-4"></i>
                                <p>{{ __('messages.no_pending_orders_found') }}</p>
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
        {{ $orderItems->links() }}
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('select-all');
        const itemCheckboxes = document.querySelectorAll('.item-checkbox');
        const updateStatusBtn = document.getElementById('update-status-btn');
        const statusSelect = document.getElementById('status');

        // Select all checkbox functionality
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            updateButtonState();
        });

        // Individual checkbox change
        itemCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateButtonState();
                
                // Update select all checkbox state
                const allChecked = Array.from(itemCheckboxes).every(cb => cb.checked);
                const someChecked = Array.from(itemCheckboxes).some(cb => cb.checked);
                
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
            });
        });

        // Status select change
        statusSelect.addEventListener('change', function() {
            updateButtonState();
        });

        // Update button state based on selections
        function updateButtonState() {
            const anyCheckboxChecked = Array.from(itemCheckboxes).some(cb => cb.checked);
            const statusSelected = statusSelect.value !== '';
            
            updateStatusBtn.disabled = !(anyCheckboxChecked && statusSelected);
        }

        // Initial button state
        updateButtonState();
    });
</script>
@endsection
