@extends('layouts.vendor')

@section('title', 'Payment Settings')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Payment Settings</h1>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
        <p>{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
        <p>{{ session('error') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Payment Methods Section -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                <div class="flex justify-between items-center p-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Payment Methods</h2>
                    <a href="{{ route('vendor.payment.methods.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Method
                    </a>
                </div>
                <div class="p-4">
                    @if($paymentMethods->isEmpty())
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No payment methods</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by adding a new payment method.</p>
                        <div class="mt-6">
                            <a href="{{ route('vendor.payment.methods.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Payment Method
                            </a>
                        </div>
                    </div>
                    @else
                    <div class="space-y-4">
                        @foreach($paymentMethods as $method)
                        <div class="border rounded-lg p-4 flex items-center justify-between {{ $method->is_default ? 'bg-indigo-50 dark:bg-indigo-900/20 border-indigo-200 dark:border-indigo-700' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700' }}">
                            <div class="flex items-center space-x-4">
                                @if($method->payment_type === 'credit_card')
                                    @if($method->card_brand === 'visa')
                                    <div class="w-12 h-8 bg-blue-600 rounded flex items-center justify-center text-white">
                                        <span class="font-bold text-sm">VISA</span>
                                    </div>
                                    @elseif($method->card_brand === 'mastercard')
                                    <div class="w-12 h-8 bg-red-600 rounded flex items-center justify-center text-white">
                                        <span class="font-bold text-sm">MC</span>
                                    </div>
                                    @else
                                    <div class="w-12 h-8 bg-gray-600 rounded flex items-center justify-center text-white">
                                        <span class="font-bold text-sm">CARD</span>
                                    </div>
                                    @endif
                                @elseif($method->payment_type === 'paypal')
                                    <div class="w-12 h-8 bg-blue-500 rounded flex items-center justify-center text-white">
                                        <span class="font-bold text-sm">PP</span>
                                    </div>
                                @endif
                                <div>
                                    <h3 class="font-medium text-gray-900 dark:text-white">{{ $method->name }}</h3>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        @if($method->payment_type === 'credit_card')
                                            {{ ucfirst($method->card_brand) }} •••• {{ $method->last_four }}
                                            <span class="ml-2">Expires {{ $method->expiry_month }}/{{ $method->expiry_year }}</span>
                                        @elseif($method->payment_type === 'paypal')
                                            {{ $method->billing_email }}
                                        @endif
                                    </div>
                                    @if($method->is_default)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 mt-1">
                                        Default
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('vendor.payment.methods.edit', $method->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('vendor.payment.methods.destroy', $method->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this payment method?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            <!-- Payout Methods Section -->
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden mt-6">
                <div class="flex justify-between items-center p-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Payout Methods</h2>
                    <a href="{{ route('vendor.payment.payout-methods.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Method
                    </a>
                </div>
                <div class="p-4">
                    @if($payoutMethods->isEmpty())
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No payout methods</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by adding a new payout method.</p>
                        <div class="mt-6">
                            <a href="{{ route('vendor.payment.payout-methods.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Payout Method
                            </a>
                        </div>
                    </div>
                    @else
                    <div class="space-y-4">
                        @foreach($payoutMethods as $method)
                        <div class="border rounded-lg p-4 flex items-center justify-between {{ $method->is_default ? 'bg-indigo-50 dark:bg-indigo-900/20 border-indigo-200 dark:border-indigo-700' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700' }}">
                            <div class="flex items-center space-x-4">
                                @if($method->payout_type === 'bank_account')
                                <div class="w-12 h-8 bg-green-600 rounded flex items-center justify-center text-white">
                                    <span class="font-bold text-sm">BANK</span>
                                </div>
                                @elseif($method->payout_type === 'paypal')
                                <div class="w-12 h-8 bg-blue-500 rounded flex items-center justify-center text-white">
                                    <span class="font-bold text-sm">PP</span>
                                </div>
                                @endif
                                <div>
                                    <h3 class="font-medium text-gray-900 dark:text-white">{{ $method->name }}</h3>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        @if($method->payout_type === 'bank_account')
                                            {{ $method->bank_name }} •••• {{ $method->last_four }}
                                            <span class="ml-2">{{ ucfirst($method->account_type) }}</span>
                                        @elseif($method->payout_type === 'paypal')
                                            {{ $method->payout_email }}
                                        @endif
                                    </div>
                                    @if($method->is_default)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 mt-1">
                                        Default
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('vendor.payment.payout-methods.edit', $method->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('vendor.payment.payout-methods.destroy', $method->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this payout method?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Payout Preferences -->
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Payout Preferences</h2>
                </div>
                <div class="p-4">
                    <form action="{{ route('vendor.payment.preferences.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label for="payout_frequency" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payout Frequency</label>
                                <select id="payout_frequency" name="payout_frequency" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="daily" {{ $payoutPreference->payout_frequency === 'daily' ? 'selected' : '' }}>Daily</option>
                                    <option value="weekly" {{ $payoutPreference->payout_frequency === 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="biweekly" {{ $payoutPreference->payout_frequency === 'biweekly' ? 'selected' : '' }}>Bi-weekly</option>
                                    <option value="monthly" {{ $payoutPreference->payout_frequency === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                </select>
                            </div>
                            <div>
                                <label for="minimum_payout_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Minimum Payout Amount</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" name="minimum_payout_amount" id="minimum_payout_amount" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="0.00" step="0.01" min="0" value="{{ $payoutPreference->minimum_payout_amount }}">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 dark:text-gray-400 sm:text-sm">USD</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Save Preferences
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden mt-6">
                <div class="flex justify-between items-center p-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Transactions</h2>
                    <a href="{{ route('vendor.payment.history') }}" class="text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">View All</a>
                </div>
                <div class="p-4">
                    @if($transactions->isEmpty())
                    <div class="text-center py-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">No recent transactions</p>
                    </div>
                    @else
                    <div class="space-y-4">
                        @foreach($transactions as $transaction)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $transaction->description }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $transaction->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="text-sm font-medium {{ $transaction->transaction_type === 'payment' ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                {{ $transaction->transaction_type === 'payment' ? '-' : '+' }}{{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
