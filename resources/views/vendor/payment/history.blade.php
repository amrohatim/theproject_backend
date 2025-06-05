@extends('layouts.vendor')

@section('title', 'Payment History')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center mb-6">
        <a href="{{ route('vendor.payment.index') }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Payment History</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="flex items-center space-x-4 mb-4 md:mb-0">
                    <div>
                        <label for="transaction_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Transaction Type</label>
                        <select id="transaction_type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">All Types</option>
                            <option value="payment">Payments</option>
                            <option value="payout">Payouts</option>
                            <option value="refund">Refunds</option>
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <select id="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">All Statuses</option>
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label for="date_range" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date Range</label>
                    <select id="date_range" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="all">All Time</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="year">This Year</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Method</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($transactions as $transaction)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $transaction->created_at->format('M d, Y') }}
                            <div class="text-xs">{{ $transaction->created_at->format('h:i A') }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                            {{ $transaction->description }}
                            @if($transaction->provider_transaction_id)
                            <div class="text-xs text-gray-500 dark:text-gray-400">ID: {{ $transaction->provider_transaction_id }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($transaction->transaction_type === 'payment')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                Payment
                            </span>
                            @elseif($transaction->transaction_type === 'payout')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Payout
                            </span>
                            @elseif($transaction->transaction_type === 'refund')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                Refund
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                {{ ucfirst($transaction->transaction_type) }}
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            @if($transaction->paymentMethod)
                                @if($transaction->paymentMethod->payment_type === 'credit_card')
                                    {{ ucfirst($transaction->paymentMethod->card_brand) }} •••• {{ $transaction->paymentMethod->last_four }}
                                @elseif($transaction->paymentMethod->payment_type === 'paypal')
                                    PayPal ({{ $transaction->paymentMethod->billing_email }})
                                @endif
                            @elseif($transaction->payoutMethod)
                                @if($transaction->payoutMethod->payout_type === 'bank_account')
                                    Bank Account •••• {{ $transaction->payoutMethod->last_four }}
                                @elseif($transaction->payoutMethod->payout_type === 'paypal')
                                    PayPal ({{ $transaction->payoutMethod->payout_email }})
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $transaction->transaction_type === 'payment' || $transaction->transaction_type === 'refund' ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                            {{ $transaction->transaction_type === 'payment' || $transaction->transaction_type === 'refund' ? '-' : '+' }}{{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}
                            @if($transaction->fee > 0)
                            <div class="text-xs text-gray-500 dark:text-gray-400">Fee: {{ $transaction->currency }} {{ number_format($transaction->fee, 2) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($transaction->status === 'completed')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Completed
                            </span>
                            @elseif($transaction->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                Pending
                            </span>
                            @elseif($transaction->status === 'failed')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                Failed
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                {{ ucfirst($transaction->status) }}
                            </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No transactions found</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No transaction history is available for your account.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $transactions->links() }}
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const transactionTypeSelect = document.getElementById('transaction_type');
        const statusSelect = document.getElementById('status');
        const dateRangeSelect = document.getElementById('date_range');
        
        // Function to apply filters
        function applyFilters() {
            const transactionType = transactionTypeSelect.value;
            const status = statusSelect.value;
            const dateRange = dateRangeSelect.value;
            
            // Build the URL with query parameters
            let url = new URL(window.location.href);
            
            if (transactionType) {
                url.searchParams.set('type', transactionType);
            } else {
                url.searchParams.delete('type');
            }
            
            if (status) {
                url.searchParams.set('status', status);
            } else {
                url.searchParams.delete('status');
            }
            
            if (dateRange && dateRange !== 'all') {
                url.searchParams.set('date', dateRange);
            } else {
                url.searchParams.delete('date');
            }
            
            // Navigate to the filtered URL
            window.location.href = url.toString();
        }
        
        // Add event listeners to the filter controls
        transactionTypeSelect.addEventListener('change', applyFilters);
        statusSelect.addEventListener('change', applyFilters);
        dateRangeSelect.addEventListener('change', applyFilters);
        
        // Set the initial values based on URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        
        if (urlParams.has('type')) {
            transactionTypeSelect.value = urlParams.get('type');
        }
        
        if (urlParams.has('status')) {
            statusSelect.value = urlParams.get('status');
        }
        
        if (urlParams.has('date')) {
            dateRangeSelect.value = urlParams.get('date');
        }
    });
</script>
@endsection
