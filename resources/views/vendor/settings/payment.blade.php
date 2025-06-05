@extends('layouts.dashboard')

@section('title', 'Payment Settings')
@section('page-title', 'Payment Settings')

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Payment Settings</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">Manage your payment methods and payout preferences</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('vendor.settings') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Settings
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
        <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
            <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
            </svg>
        </button>
    </div>
    @endif

    <!-- Payment Methods -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700 mb-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Payment Methods</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Add and manage your payment methods.</p>

        <div class="flex justify-end mb-4">
            <button type="button" onclick="toggleModal('addPaymentMethodModal')" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-900 focus:outline-none focus:border-purple-900 focus:ring ring-purple-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Payment Method
            </button>
        </div>

        <div class="space-y-4">
            @if(count($paymentMethods) > 0)
                @foreach($paymentMethods as $method)
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="rounded-md bg-white dark:bg-gray-800 p-2 mr-4">
                                    @if($method->type === 'credit_card')
                                        <svg class="w-8 h-8 text-blue-600" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9.112 8.262L5.97 15.758H3.92L2.374 9.775C2.374 9.775 2.234 9.18 1.73 8.966C1.73 8.966 0.762 8.561 0 8.262H0.042L3.36 15.758H5.635L9.112 8.262ZM10.173 8.262L9.112 15.758H11.244L12.306 8.262H10.173ZM20.56 15.758H22.51L20.56 8.262H18.764C18.764 8.262 17.949 8.22 17.574 8.966L14.297 15.758H16.598L17.032 14.627H20.126L20.56 15.758ZM17.69 12.918L18.848 10.146L19.497 12.918H17.69ZM15.514 10.146L16.04 8.262L13.547 8.262L12.306 14.118C12.306 14.118 12.166 14.755 12.79 15.12C12.79 15.12 13.266 15.464 14.297 15.758L15.514 10.146Z"/>
                                        </svg>
                                    @elseif($method->type === 'paypal')
                                        <svg class="w-8 h-8 text-blue-800" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M20.067 8.478c.492.315.844.897.844 1.589 0 .186-.027.366-.08.536-.374 1.587-2.081 2.374-4.009 2.374h-.375a.563.563 0 0 0-.555.479l-.057.279-.399 2.543-.026.142a.562.562 0 0 1-.555.479h-2.979l-.131-.007a.365.365 0 0 1-.36-.372c0-.027 0-.054.007-.08l.732-4.636.052-.293a.56.56 0 0 1 .555-.48h1.727c2.364 0 4.188-.956 4.728-3.718.16-.827.124-1.516-.12-2.055-.038-.08-.08-.153-.126-.22.137.02.268.047.392.08.36.1.677.259.935.44zM18.032 0c.267 0 .553.027.845.087.08.013.16.033.24.053.086.02.166.047.246.073.08.027.153.06.226.093.373.167.706.4.972.7.4.447.632 1.033.632 1.723 0 .22-.027.447-.08.68-.4 1.693-2.174 2.536-4.242 2.536h-.325a.544.544 0 0 0-.538.462l-.06.287-.393 2.493-.026.14a.563.563 0 0 1-.555.48H11.97l-.132-.007a.365.365 0 0 1-.359-.373c0-.026 0-.053.007-.08l.732-4.636.052-.293a.56.56 0 0 1 .555-.479h1.727c2.364 0 4.188-.957 4.728-3.718.16-.828.124-1.517-.12-2.055a2.012 2.012 0 0 0-.632-.7 2.17 2.17 0 0 0-.972-.399 3.384 3.384 0 0 0-.486-.067A5.795 5.795 0 0 0 16.3 0h1.731z" />
                                            <path d="M8.118 8.478c.492.315.844.897.844 1.589 0 .186-.027.366-.08.536-.374 1.587-2.081 2.374-4.009 2.374h-.375a.563.563 0 0 0-.555.479l-.057.279-.399 2.543-.026.142a.562.562 0 0 1-.555.479H.022l-.131-.007a.365.365 0 0 1-.36-.372c0-.027 0-.054.007-.08l.732-4.636.052-.293a.56.56 0 0 1 .555-.48h1.727c2.364 0 4.188-.956 4.728-3.718.16-.827.124-1.516-.12-2.055-.038-.08-.08-.153-.126-.22.137.02.268.047.392.08.36.1.677.259.935.44z" />
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $method->name }}</h4>
                                    @if($method->type === 'credit_card')
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ ucfirst($method->card_type) }} ending in {{ $method->last_four }}
                                            @if($method->expiry_month && $method->expiry_year)
                                                • Expires {{ $method->expiry_month }}/{{ $method->expiry_year }}
                                            @endif
                                        </p>
                                    @elseif($method->type === 'paypal')
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $method->email }}</p>
                                    @endif
                                    @if($method->is_default)
                                        <div class="mt-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                Default
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button type="button" onclick="editPaymentMethod({{ $method->id }})" class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-md text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Edit
                                </button>
                                <form action="{{ route('vendor.settings.payment.methods.remove', $method->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Are you sure you want to remove this payment method?')" class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-md text-xs font-medium text-red-700 dark:text-red-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md text-center">
                    <p class="text-gray-500 dark:text-gray-400">You don't have any payment methods yet.</p>
                </div>
            @endif
        </div>

        <!-- Add Payment Method Modal -->
        <div id="addPaymentMethodModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="toggleModal('addPaymentMethodModal')"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                    Add Payment Method
                                </h3>
                                <div class="mt-4">
                                    <form action="{{ route('vendor.settings.payment.methods.add') }}" method="POST" id="addPaymentMethodForm">
                                        @csrf
                                        <div class="space-y-4">
                                            <div>
                                                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Method Type</label>
                                                <select id="type" name="type" onchange="togglePaymentMethodFields()" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                                    <option value="credit_card">Credit Card</option>
                                                    <option value="paypal">PayPal</option>
                                                </select>
                                            </div>

                                            <div>
                                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                                                <input type="text" name="name" id="name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="e.g. My Personal Card">
                                            </div>

                                            <div id="creditCardFields">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Card Type</label>
                                                    <div class="mt-2">
                                                        <div class="flex items-center mb-2">
                                                            <input type="radio" id="card_type_visa" name="card_type" value="visa" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600" checked>
                                                            <label for="card_type_visa" class="ml-3 flex items-center">
                                                                <svg class="h-8 w-auto text-blue-600 mr-2" viewBox="0 0 780 500" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M40,0h700c22.1,0,40,17.9,40,40v420c0,22.1-17.9,40-40,40H40c-22.1,0-40-17.9-40-40V40C0,17.9,17.9,0,40,0z"/>
                                                                    <path fill="#fff" d="M293.2,348.7l33.4-195.8h53.3l-33.4,195.8H293.2L293.2,348.7z M536.6,158.9c-11.1-4.2-28.5-8.7-50.2-8.7
                                                                        c-55.3,0-94.2,27.8-94.5,67.7c-0.2,29.5,27.8,45.9,49,55.7c21.8,10,29.1,16.5,29,25.4c0,13.7-17.4,20-33.5,20
                                                                        c-22.4,0-34.3-3.1-52.6-10.7l-7.2-3.3l-7.8,46c13.1,5.7,37.1,10.7,62.1,10.9c58.6,0,96.7-27.5,97.1-70.1
                                                                        c0.2-23.3-14.7-41.1-47.1-55.8c-19.6-9.5-31.6-15.8-31.5-25.4c0-8.5,10.2-17.6,32.2-17.6c18.3-0.3,31.6,3.7,41.9,7.9l5,2.4
                                                                        L536.6,158.9L536.6,158.9z M674.8,152.9h-41.2c-12.8,0-22.4,3.5-28,16.2l-79.6,179.6h56.3c0,0,9.2-24.3,11.3-29.6
                                                                        c6.1,0,60.9,0.1,68.7,0.1c1.6,6.9,6.5,29.5,6.5,29.5h49.8L674.8,152.9L674.8,152.9z M596.2,276.3c4.4-11.3,21.3-54.9,21.3-54.9
                                                                        c-0.3,0.5,4.4-11.3,7.1-18.7l3.6,16.9c0,0,10.2,46.7,12.4,56.7L596.2,276.3L596.2,276.3z M232.2,152.9l-52.8,133.5l-5.7-27.1
                                                                        c-9.8-31.7-40.5-66-74.8-83.1l48.4,173.6l57.2-0.1l85.1-196.8H232.2L232.2,152.9z M131.4,152.9H45.6l-0.7,4.2
                                                                        c66.7,16.1,110.9,55,129.2,101.7l-18.6-89.9C152.4,156.3,142.7,153.4,131.4,152.9L131.4,152.9z"/>
                                                                </svg>
                                                                <span class="font-medium">Visa</span>
                                                            </label>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <input type="radio" id="card_type_mastercard" name="card_type" value="mastercard" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600">
                                                            <label for="card_type_mastercard" class="ml-3 flex items-center">
                                                                <svg class="h-8 w-auto mr-2" viewBox="0 0 780 500" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M40,0h700c22.1,0,40,17.9,40,40v420c0,22.1-17.9,40-40,40H40c-22.1,0-40-17.9-40-40V40C0,17.9,17.9,0,40,0z" fill="#16366F"/>
                                                                    <path d="M449.8,250c0,99.4-80.6,180-180,180s-180-80.6-180-180s80.6-180,180-180S449.8,150.6,449.8,250L449.8,250z" fill="#D9222A"/>
                                                                    <path d="M510.2,70c-46.9,0-89.2,19-119.9,49.7c-8,8-15.2,16.8-21.5,26.3h43c5.3,8.1,10,16.7,13.9,25.8h-70.7
                                                                        c-3.5,8.4-6.4,17.1-8.6,26h87.9c2.1,8.5,3.6,17.3,4.3,26.3h-96.5c-0.6,8.6-0.6,17.3,0,26h96.5c-0.7,9-2.2,17.7-4.3,26.3h-87.9
                                                                        c2.2,8.9,5.1,17.6,8.6,26h70.7c-3.9,9.1-8.6,17.7-13.9,25.8h-43c6.3,9.5,13.5,18.3,21.5,26.3c30.7,30.7,73,49.7,119.9,49.7
                                                                        c93.8,0,169.9-76.1,169.9-169.9C680.1,146.1,604,70,510.2,70z" fill="#EE9F2D"/>
                                                                    <path d="M666.1,350.3v-6.1h2.5v-1.2h-6.3v1.2h2.5v6.1H666.1z M680,350.3v-7.3h-1.9l-2.2,5.2l-2.2-5.2h-1.9v7.3h1.3v-5.5
                                                                        l2.1,4.8h1.4l2.1-4.8v5.5H680z" fill="#16366F"/>
                                                                </svg>
                                                                <span class="font-medium">Mastercard</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mt-4">
                                                    <label for="last_four" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last 4 Digits</label>
                                                    <input type="text" name="last_four" id="last_four" maxlength="4" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="1234">
                                                </div>

                                                <div class="mt-4 grid grid-cols-2 gap-4">
                                                    <div>
                                                        <label for="expiry_month" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expiry Month</label>
                                                        <select id="expiry_month" name="expiry_month" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                                            @for($i = 1; $i <= 12; $i++)
                                                                <option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option>
                                                            @endfor
                                                        </select>
                                                    </div>

                                                    <div>
                                                        <label for="expiry_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expiry Year</label>
                                                        <select id="expiry_year" name="expiry_year" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                                            @for($i = date('Y'); $i <= date('Y') + 10; $i++)
                                                                <option value="{{ $i }}">{{ $i }}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="paypalFields" class="hidden">
                                                <div>
                                                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">PayPal Email</label>
                                                    <input type="email" name="email" id="email" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="your-email@example.com">
                                                </div>
                                            </div>

                                            <div class="flex items-center">
                                                <input id="is_default" name="is_default" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded">
                                                <label for="is_default" class="ml-2 block text-sm text-gray-900 dark:text-white">
                                                    Set as default payment method
                                                </label>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" onclick="document.getElementById('addPaymentMethodForm').submit()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Add
                        </button>
                        <button type="button" onclick="toggleModal('addPaymentMethodModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payout Methods -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700 mb-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Payout Methods</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Add and manage your payout methods.</p>

        <div class="flex justify-end mb-4">
            <button type="button" onclick="toggleModal('addPayoutMethodModal')" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-900 focus:outline-none focus:border-purple-900 focus:ring ring-purple-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Payout Method
            </button>
        </div>

        <div class="space-y-4">
            @if(count($payoutMethods) > 0)
                @foreach($payoutMethods as $method)
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="rounded-md bg-white dark:bg-gray-800 p-2 mr-4">
                                    @if($method->type === 'bank_account')
                                        <svg class="w-8 h-8 text-gray-600 dark:text-gray-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3 21H21M3 18H21M5 18V13M9 18V13M15 18V13M19 18V13M3 10L12 3L21 10H3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    @elseif($method->type === 'paypal')
                                        <svg class="w-8 h-8 text-blue-800" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M20.067 8.478c.492.315.844.897.844 1.589 0 .186-.027.366-.08.536-.374 1.587-2.081 2.374-4.009 2.374h-.375a.563.563 0 0 0-.555.479l-.057.279-.399 2.543-.026.142a.562.562 0 0 1-.555.479h-2.979l-.131-.007a.365.365 0 0 1-.36-.372c0-.027 0-.054.007-.08l.732-4.636.052-.293a.56.56 0 0 1 .555-.48h1.727c2.364 0 4.188-.956 4.728-3.718.16-.827.124-1.516-.12-2.055-.038-.08-.08-.153-.126-.22.137.02.268.047.392.08.36.1.677.259.935.44zM18.032 0c.267 0 .553.027.845.087.08.013.16.033.24.053.086.02.166.047.246.073.08.027.153.06.226.093.373.167.706.4.972.7.4.447.632 1.033.632 1.723 0 .22-.027.447-.08.68-.4 1.693-2.174 2.536-4.242 2.536h-.325a.544.544 0 0 0-.538.462l-.06.287-.393 2.493-.026.14a.563.563 0 0 1-.555.48H11.97l-.132-.007a.365.365 0 0 1-.359-.373c0-.026 0-.053.007-.08l.732-4.636.052-.293a.56.56 0 0 1 .555-.479h1.727c2.364 0 4.188-.957 4.728-3.718.16-.828.124-1.517-.12-2.055a2.012 2.012 0 0 0-.632-.7 2.17 2.17 0 0 0-.972-.399 3.384 3.384 0 0 0-.486-.067A5.795 5.795 0 0 0 16.3 0h1.731z" />
                                            <path d="M8.118 8.478c.492.315.844.897.844 1.589 0 .186-.027.366-.08.536-.374 1.587-2.081 2.374-4.009 2.374h-.375a.563.563 0 0 0-.555.479l-.057.279-.399 2.543-.026.142a.562.562 0 0 1-.555.479H.022l-.131-.007a.365.365 0 0 1-.36-.372c0-.027 0-.054.007-.08l.732-4.636.052-.293a.56.56 0 0 1 .555-.48h1.727c2.364 0 4.188-.956 4.728-3.718.16-.827.124-1.516-.12-2.055-.038-.08-.08-.153-.126-.22.137.02.268.047.392.08.36.1.677.259.935.44z" />
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $method->name }}</h4>
                                    @if($method->type === 'bank_account')
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $method->bank_name }} • {{ ucfirst($method->account_type) }} • Ending in {{ $method->last_four }}
                                        </p>
                                    @elseif($method->type === 'paypal')
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $method->email }}</p>
                                    @endif
                                    @if($method->is_default)
                                        <div class="mt-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                Default
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button type="button" onclick="editPayoutMethod({{ $method->id }})" class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-md text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Edit
                                </button>
                                <form action="{{ route('vendor.settings.payment.payout-methods.remove', $method->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Are you sure you want to remove this payout method?')" class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-md text-xs font-medium text-red-700 dark:text-red-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md text-center">
                    <p class="text-gray-500 dark:text-gray-400">You don't have any payout methods yet.</p>
                </div>
            @endif
        </div>

        <!-- Add Payout Method Modal -->
        <div id="addPayoutMethodModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="toggleModal('addPayoutMethodModal')"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                    Add Payout Method
                                </h3>
                                <div class="mt-4">
                                    <form action="{{ route('vendor.settings.payment.payout-methods.add') }}" method="POST" id="addPayoutMethodForm">
                                        @csrf
                                        <div class="space-y-4">
                                            <div>
                                                <label for="payout_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payout Method Type</label>
                                                <select id="payout_type" name="type" onchange="togglePayoutMethodFields()" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                                    <option value="bank_account">Bank Account</option>
                                                    <option value="paypal">PayPal</option>
                                                </select>
                                            </div>

                                            <div>
                                                <label for="payout_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                                                <input type="text" name="name" id="payout_name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="e.g. My Bank Account">
                                            </div>

                                            <div id="bankAccountFields">
                                                <div>
                                                    <label for="bank_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bank Name</label>
                                                    <input type="text" name="bank_name" id="bank_name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="e.g. Chase Bank">
                                                </div>

                                                <div class="mt-4">
                                                    <label for="account_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Account Number</label>
                                                    <input type="text" name="account_number" id="account_number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="Your account number">
                                                </div>

                                                <div class="mt-4">
                                                    <label for="routing_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Routing Number</label>
                                                    <input type="text" name="routing_number" id="routing_number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="Your routing number">
                                                </div>

                                                <div class="mt-4">
                                                    <label for="account_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Account Type</label>
                                                    <select id="account_type" name="account_type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                                        <option value="checking">Checking</option>
                                                        <option value="savings">Savings</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div id="payoutPaypalFields" class="hidden">
                                                <div>
                                                    <label for="payout_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">PayPal Email</label>
                                                    <input type="email" name="email" id="payout_email" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="your-email@example.com">
                                                </div>
                                            </div>

                                            <div class="flex items-center">
                                                <input id="payout_is_default" name="is_default" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded">
                                                <label for="payout_is_default" class="ml-2 block text-sm text-gray-900 dark:text-white">
                                                    Set as default payout method
                                                </label>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" onclick="document.getElementById('addPayoutMethodForm').submit()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Add
                        </button>
                        <button type="button" onclick="toggleModal('addPayoutMethodModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payout Schedule -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700 mb-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Payout Schedule</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Configure when you want to receive your payouts.</p>

        <form action="{{ route('vendor.settings.payment.preferences.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <!-- Payout Frequency -->
                <div>
                    <label for="payout_frequency" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payout Frequency</label>
                    <select id="payout_frequency" name="payout_frequency" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="daily" {{ $payoutPreference->payout_frequency === 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="weekly" {{ $payoutPreference->payout_frequency === 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="biweekly" {{ $payoutPreference->payout_frequency === 'biweekly' ? 'selected' : '' }}>Bi-weekly</option>
                        <option value="monthly" {{ $payoutPreference->payout_frequency === 'monthly' ? 'selected' : '' }}>Monthly</option>
                    </select>
                </div>

                <!-- Minimum Payout Amount -->
                <div>
                    <label for="minimum_payout_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Minimum Payout Amount</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                        </div>
                        <input type="text" name="minimum_payout_amount" id="minimum_payout_amount" value="{{ $payoutPreference->minimum_payout_amount }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 dark:text-gray-400 sm:text-sm">{{ $payoutPreference->currency }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-900 focus:outline-none focus:border-purple-900 focus:ring ring-purple-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Payment History -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Payment History</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">View your recent payment transactions.</p>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @if(count($transactions) > 0)
                        @foreach($transactions as $transaction)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $transaction->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $transaction->description }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($transaction->status === 'completed')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Completed
                                        </span>
                                    @elseif($transaction->status === 'pending')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                            Pending
                                        </span>
                                    @elseif($transaction->status === 'failed')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            Failed
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white text-right">
                                    {{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                No transactions found.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        @if(count($transactions) > 0)
            <div class="mt-4 flex justify-center">
                <a href="#" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-900 focus:outline-none focus:border-purple-900 focus:ring ring-purple-300 disabled:opacity-25 transition ease-in-out duration-150">
                    View All Transactions
                </a>
            </div>
        @endif
    </div>
</div>
<!-- Edit Payment Method Modal -->
<div id="editPaymentMethodModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="toggleModal('editPaymentMethodModal')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="edit-payment-modal-title">
                            Edit Payment Method
                        </h3>
                        <div class="mt-4">
                            <form id="editPaymentMethodForm" method="POST" action="" onsubmit="return validatePaymentMethodForm(this)">
                                @csrf
                                @method('PUT')
                                <div class="space-y-4">
                                    <input type="hidden" name="type" id="edit_type" value="credit_card">
                                    <input type="hidden" name="_method" value="PUT">
                                    <div>
                                        <label for="edit_name" id="edit_name_label" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name on Card <span class="text-red-500">*</span></label>
                                        <input type="text" name="name" id="edit_name" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    </div>

                                    <div id="editCreditCardFields">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Card Type <span class="text-red-500">*</span></label>
                                            <div class="mt-2 space-y-2">
                                                <div class="flex items-center p-3 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer" onclick="document.getElementById('edit_card_type_visa').checked = true">
                                                    <input type="radio" id="edit_card_type_visa" name="card_type" value="visa" class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600">
                                                    <label for="edit_card_type_visa" class="ml-3 flex items-center w-full cursor-pointer">
                                                        <svg class="h-8 w-auto text-blue-600 mr-2" viewBox="0 0 780 500" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M40,0h700c22.1,0,40,17.9,40,40v420c0,22.1-17.9,40-40,40H40c-22.1,0-40-17.9-40-40V40C0,17.9,17.9,0,40,0z"/>
                                                            <path fill="#fff" d="M293.2,348.7l33.4-195.8h53.3l-33.4,195.8H293.2L293.2,348.7z M536.6,158.9c-11.1-4.2-28.5-8.7-50.2-8.7
                                                                c-55.3,0-94.2,27.8-94.5,67.7c-0.2,29.5,27.8,45.9,49,55.7c21.8,10,29.1,16.5,29,25.4c0,13.7-17.4,20-33.5,20
                                                                c-22.4,0-34.3-3.1-52.6-10.7l-7.2-3.3l-7.8,46c13.1,5.7,37.1,10.7,62.1,10.9c58.6,0,96.7-27.5,97.1-70.1
                                                                c0.2-23.3-14.7-41.1-47.1-55.8c-19.6-9.5-31.6-15.8-31.5-25.4c0-8.5,10.2-17.6,32.2-17.6c18.3-0.3,31.6,3.7,41.9,7.9l5,2.4
                                                                L536.6,158.9L536.6,158.9z M674.8,152.9h-41.2c-12.8,0-22.4,3.5-28,16.2l-79.6,179.6h56.3c0,0,9.2-24.3,11.3-29.6
                                                                c6.1,0,60.9,0.1,68.7,0.1c1.6,6.9,6.5,29.5,6.5,29.5h49.8L674.8,152.9L674.8,152.9z M596.2,276.3c4.4-11.3,21.3-54.9,21.3-54.9
                                                                c-0.3,0.5,4.4-11.3,7.1-18.7l3.6,16.9c0,0,10.2,46.7,12.4,56.7L596.2,276.3L596.2,276.3z M232.2,152.9l-52.8,133.5l-5.7-27.1
                                                                c-9.8-31.7-40.5-66-74.8-83.1l48.4,173.6l57.2-0.1l85.1-196.8H232.2L232.2,152.9z M131.4,152.9H45.6l-0.7,4.2
                                                                c66.7,16.1,110.9,55,129.2,101.7l-18.6-89.9C152.4,156.3,142.7,153.4,131.4,152.9L131.4,152.9z"/>
                                                        </svg>
                                                        <span class="font-medium">Visa</span>
                                                    </label>
                                                </div>
                                                <div class="flex items-center p-3 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer" onclick="document.getElementById('edit_card_type_mastercard').checked = true">
                                                    <input type="radio" id="edit_card_type_mastercard" name="card_type" value="mastercard" class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600">
                                                    <label for="edit_card_type_mastercard" class="ml-3 flex items-center w-full cursor-pointer">
                                                        <svg class="h-8 w-auto mr-2" viewBox="0 0 780 500" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M40,0h700c22.1,0,40,17.9,40,40v420c0,22.1-17.9,40-40,40H40c-22.1,0-40-17.9-40-40V40C0,17.9,17.9,0,40,0z" fill="#16366F"/>
                                                            <path d="M449.8,250c0,99.4-80.6,180-180,180s-180-80.6-180-180s80.6-180,180-180S449.8,150.6,449.8,250L449.8,250z" fill="#D9222A"/>
                                                            <path d="M510.2,70c-46.9,0-89.2,19-119.9,49.7c-8,8-15.2,16.8-21.5,26.3h43c5.3,8.1,10,16.7,13.9,25.8h-70.7
                                                                c-3.5,8.4-6.4,17.1-8.6,26h87.9c2.1,8.5,3.6,17.3,4.3,26.3h-96.5c-0.6,8.6-0.6,17.3,0,26h96.5c-0.7,9-2.2,17.7-4.3,26.3h-87.9
                                                                c2.2,8.9,5.1,17.6,8.6,26h70.7c-3.9,9.1-8.6,17.7-13.9,25.8h-43c6.3,9.5,13.5,18.3,21.5,26.3c30.7,30.7,73,49.7,119.9,49.7
                                                                c93.8,0,169.9-76.1,169.9-169.9C680.1,146.1,604,70,510.2,70z" fill="#EE9F2D"/>
                                                            <path d="M666.1,350.3v-6.1h2.5v-1.2h-6.3v1.2h2.5v6.1H666.1z M680,350.3v-7.3h-1.9l-2.2,5.2l-2.2-5.2h-1.9v7.3h1.3v-5.5
                                                                l2.1,4.8h1.4l2.1-4.8v5.5H680z" fill="#16366F"/>
                                                        </svg>
                                                        <span class="font-medium">Mastercard</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4 grid grid-cols-2 gap-4">
                                            <div>
                                                <label for="edit_expiry_month" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expiry Month <span class="text-red-500">*</span></label>
                                                <select id="edit_expiry_month" name="expiry_month" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                                    @for($i = 1; $i <= 12; $i++)
                                                        <option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option>
                                                    @endfor
                                                </select>
                                            </div>

                                            <div>
                                                <label for="edit_expiry_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expiry Year <span class="text-red-500">*</span></label>
                                                <select id="edit_expiry_year" name="expiry_year" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                                    @for($i = date('Y'); $i <= date('Y') + 10; $i++)
                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="editPaypalFields" class="hidden">
                                        <div>
                                            <label for="edit_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">PayPal Email <span class="text-red-500">*</span></label>
                                            <input type="email" name="email" id="edit_email" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                        </div>
                                    </div>

                                    <div class="flex items-center">
                                        <input id="edit_is_default" name="is_default" type="checkbox" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded">
                                        <label for="edit_is_default" class="ml-2 block text-sm text-gray-900 dark:text-white">
                                            Set as default payment method
                                        </label>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="submit" form="editPaymentMethodForm" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Save
                </button>
                <button type="button" onclick="toggleModal('editPaymentMethodModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Payout Method Modal -->
<div id="editPayoutMethodModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="toggleModal('editPayoutMethodModal')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="edit-payout-modal-title">
                            Edit Payout Method
                        </h3>
                        <div class="mt-4">
                            <form id="editPayoutMethodForm" method="POST" action="">
                                @csrf
                                @method('PUT')
                                <div class="space-y-4">
                                    <input type="hidden" name="type" id="edit_payout_type" value="bank_account">
                                    <input type="hidden" name="_method" value="PUT">
                                    <div>
                                        <label for="edit_payout_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                                        <input type="text" name="name" id="edit_payout_name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    </div>

                                    <div id="editBankAccountFields">
                                        <div>
                                            <label for="edit_bank_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bank Name</label>
                                            <input type="text" name="bank_name" id="edit_bank_name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                        </div>

                                        <div class="mt-4">
                                            <label for="edit_account_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Account Type</label>
                                            <select id="edit_account_type" name="account_type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                                <option value="checking">Checking</option>
                                                <option value="savings">Savings</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div id="editPayoutPaypalFields" class="hidden">
                                        <div>
                                            <label for="edit_payout_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">PayPal Email</label>
                                            <input type="email" name="email" id="edit_payout_email" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                        </div>
                                    </div>

                                    <div class="flex items-center">
                                        <input id="edit_payout_is_default" name="is_default" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded">
                                        <label for="edit_payout_is_default" class="ml-2 block text-sm text-gray-900 dark:text-white">
                                            Set as default payout method
                                        </label>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="submit" form="editPayoutMethodForm" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Save
                </button>
                <button type="button" onclick="toggleModal('editPayoutMethodModal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle modal visibility
    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            } else {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        } else {
            console.error(`Modal with ID ${modalId} not found`);
        }
    }

    // Toggle payment method fields based on selected type
    function togglePaymentMethodFields() {
        const type = document.getElementById('type');
        if (!type) return;

        const creditCardFields = document.getElementById('creditCardFields');
        const paypalFields = document.getElementById('paypalFields');

        if (!creditCardFields || !paypalFields) return;

        console.log('Toggling payment method fields for type:', type.value);

        if (type.value === 'credit_card') {
            creditCardFields.classList.remove('hidden');
            paypalFields.classList.add('hidden');

            // Make credit card fields required
            const lastFour = document.getElementById('last_four');
            if (lastFour) lastFour.setAttribute('required', 'required');

            // Make PayPal fields not required
            const email = document.getElementById('email');
            if (email) email.removeAttribute('required');
        } else if (type.value === 'paypal') {
            creditCardFields.classList.add('hidden');
            paypalFields.classList.remove('hidden');

            // Make PayPal fields required
            const email = document.getElementById('email');
            if (email) email.setAttribute('required', 'required');

            // Make credit card fields not required
            const lastFour = document.getElementById('last_four');
            if (lastFour) lastFour.removeAttribute('required');
        }
    }

    // Toggle payout method fields based on selected type
    function togglePayoutMethodFields() {
        const type = document.getElementById('payout_type');
        if (!type) return;

        const bankAccountFields = document.getElementById('bankAccountFields');
        const paypalFields = document.getElementById('payoutPaypalFields');

        if (!bankAccountFields || !paypalFields) return;

        if (type.value === 'bank_account') {
            bankAccountFields.classList.remove('hidden');
            paypalFields.classList.add('hidden');
        } else if (type.value === 'paypal') {
            bankAccountFields.classList.add('hidden');
            paypalFields.classList.remove('hidden');
        }
    }

    // Edit payment method
    function editPaymentMethod(id) {
        console.log('Editing payment method with ID:', id);

        // Show loading indicator
        const loadingIndicator = document.createElement('div');
        loadingIndicator.id = 'loadingIndicator';
        loadingIndicator.className = 'fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50';
        loadingIndicator.innerHTML = `
            <div class="bg-white p-5 rounded-lg shadow-lg">
                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-purple-500 mx-auto"></div>
                <p class="mt-3 text-center">Loading payment method...</p>
            </div>
        `;
        document.body.appendChild(loadingIndicator);

        try {
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Fetch payment method data
            fetch(`/api/payment-methods/${id}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    // Remove loading indicator
                    const indicator = document.getElementById('loadingIndicator');
                    if (indicator) indicator.remove();

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Payment method data received:', data);

                    // Map API response fields to form fields
                    // The API uses payment_type, card_brand, billing_email
                    // The form uses type, card_type, email
                    const mappedData = {
                        id: data.id,
                        type: data.payment_type || data.type || 'credit_card',
                        name: data.name || '',
                        card_type: data.card_brand || data.card_type || 'visa',
                        expiry_month: data.expiry_month || '01',
                        expiry_year: data.expiry_year || new Date().getFullYear().toString(),
                        email: data.billing_email || data.email || '',
                        is_default: data.is_default || false
                    };

                    console.log('Mapped data:', mappedData);

                    // Set form action
                    const form = document.getElementById('editPaymentMethodForm');
                    if (!form) {
                        console.error('Edit payment method form not found');
                        return;
                    }

                    // Set the form action directly with the correct URL
                    form.action = `/vendor/settings/payment/methods/${id}`;

                    // Set form fields
                    const nameField = document.getElementById('edit_name');
                    if (nameField) nameField.value = mappedData.name;

                    // Set type field
                    const typeField = document.getElementById('edit_type');
                    if (typeField) typeField.value = mappedData.type;

                    const editCreditCardFields = document.getElementById('editCreditCardFields');
                    const editPaypalFields = document.getElementById('editPaypalFields');

                    // Update the name label based on the payment method type
                    const nameLabel = document.getElementById('edit_name_label');
                    if (nameLabel) {
                        nameLabel.textContent = mappedData.type === 'credit_card' ? 'Name on Card ' : 'Account Name ';
                        const requiredSpan = document.createElement('span');
                        requiredSpan.className = 'text-red-500';
                        requiredSpan.textContent = '*';
                        nameLabel.appendChild(requiredSpan);
                    }

                    if (mappedData.type === 'credit_card' && editCreditCardFields && editPaypalFields) {
                        editCreditCardFields.classList.remove('hidden');
                        editPaypalFields.classList.add('hidden');

                        // Set credit card fields
                        const expiryMonth = document.getElementById('edit_expiry_month');
                        const expiryYear = document.getElementById('edit_expiry_year');

                        // Set card type - default to visa if not set
                        const cardType = mappedData.card_type;

                        console.log('Setting card type to:', cardType);

                        // Make sure the email field is not required for credit cards
                        const emailField = document.getElementById('edit_email');
                        if (emailField) {
                            emailField.removeAttribute('required');
                            emailField.value = '';  // Clear email field
                        }

                        // Try to set the radio button
                        try {
                            const visaRadio = document.getElementById('edit_card_type_visa');
                            const mastercardRadio = document.getElementById('edit_card_type_mastercard');

                            // Reset both radio buttons
                            if (visaRadio) visaRadio.checked = false;
                            if (mastercardRadio) mastercardRadio.checked = false;

                            // Set the correct one
                            if (cardType === 'visa' && visaRadio) {
                                visaRadio.checked = true;
                            } else if (cardType === 'mastercard' && mastercardRadio) {
                                mastercardRadio.checked = true;
                            } else {
                                // Default to visa if the card type is not recognized
                                if (visaRadio) visaRadio.checked = true;
                            }
                        } catch (e) {
                            console.error('Error setting card type:', e);
                            // Default to visa as fallback
                            const visaRadio = document.getElementById('edit_card_type_visa');
                            if (visaRadio) visaRadio.checked = true;
                        }

                        if (expiryMonth) expiryMonth.value = mappedData.expiry_month;
                        if (expiryYear) expiryYear.value = mappedData.expiry_year;
                    } else if (mappedData.type === 'paypal' && editCreditCardFields && editPaypalFields) {
                        console.log('Setting up PayPal fields');

                        // Make sure the type field is set to paypal
                        if (typeField) {
                            typeField.value = 'paypal';
                            console.log('Type field set to:', typeField.value);
                        }

                        editCreditCardFields.classList.add('hidden');
                        editPaypalFields.classList.remove('hidden');

                        // Set PayPal fields
                        const email = document.getElementById('edit_email');
                        if (email) {
                            email.value = mappedData.email;
                            email.setAttribute('required', 'required');
                        }

                        // Make sure card type radios are not required for PayPal
                        const visaRadio = document.getElementById('edit_card_type_visa');
                        if (visaRadio) visaRadio.removeAttribute('required');

                        // Clear credit card fields
                        const expiryMonth = document.getElementById('edit_expiry_month');
                        const expiryYear = document.getElementById('edit_expiry_year');
                        if (expiryMonth) expiryMonth.value = '01';
                        if (expiryYear) expiryYear.value = new Date().getFullYear().toString();
                    }

                    // Set default checkbox
                    const isDefault = document.getElementById('edit_is_default');
                    if (isDefault) {
                        // Convert to boolean explicitly
                        isDefault.checked = mappedData.is_default === true || mappedData.is_default === 1 || mappedData.is_default === "1";
                    }

                    // Show modal
                    toggleModal('editPaymentMethodModal');
                })
                .catch(error => {
                    // Remove loading indicator
                    const indicator = document.getElementById('loadingIndicator');
                    if (indicator) indicator.remove();

                    console.error('Error fetching payment method:', error);
                    alert('An error occurred while fetching the payment method. Please try again.');
                });
        } catch (error) {
            // Remove loading indicator
            const indicator = document.getElementById('loadingIndicator');
            if (indicator) indicator.remove();

            console.error('Error in editPaymentMethod function:', error);
            alert('An error occurred while preparing to edit the payment method. Please try again.');
        }
    }

    // Edit payout method
    function editPayoutMethod(id) {
        // Fetch payout method data
        fetch(`/api/payout-methods/${id}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                // Set form action
                const form = document.getElementById('editPayoutMethodForm');
                if (!form) {
                    console.error('Edit payout method form not found');
                    return;
                }

                // Set the form action directly with the correct URL
                form.action = `/vendor/settings/payment/payout-methods/${id}`;

                // Set form fields
                const nameField = document.getElementById('edit_payout_name');
                if (nameField) nameField.value = data.name || '';

                // Set type field
                const typeField = document.getElementById('edit_payout_type');
                if (typeField) typeField.value = data.type || 'bank_account';

                const editBankAccountFields = document.getElementById('editBankAccountFields');
                const editPayoutPaypalFields = document.getElementById('editPayoutPaypalFields');

                if (data.type === 'bank_account' && editBankAccountFields && editPayoutPaypalFields) {
                    editBankAccountFields.classList.remove('hidden');
                    editPayoutPaypalFields.classList.add('hidden');

                    // Set bank account fields
                    const bankName = document.getElementById('edit_bank_name');
                    const accountType = document.getElementById('edit_account_type');

                    if (bankName) bankName.value = data.bank_name || '';
                    if (accountType) accountType.value = data.account_type || 'checking';
                } else if (data.type === 'paypal' && editBankAccountFields && editPayoutPaypalFields) {
                    editBankAccountFields.classList.add('hidden');
                    editPayoutPaypalFields.classList.remove('hidden');

                    // Set PayPal fields
                    const email = document.getElementById('edit_payout_email');
                    if (email) email.value = data.email || '';
                }

                // Set default checkbox
                const isDefault = document.getElementById('edit_payout_is_default');
                if (isDefault) isDefault.checked = data.is_default || false;

                // Show modal
                toggleModal('editPayoutMethodModal');
            })
            .catch(error => {
                console.error('Error fetching payout method:', error);
                alert('An error occurred while fetching the payout method. Please try again.');
            });
    }

    // Validate payment method form before submission
    function validatePaymentMethodForm(form) {
        console.log('Validating payment method form...');

        try {
            const type = form.querySelector('#edit_type').value;
            console.log('Payment method type:', type);

            // Double check that the type field is set correctly
            if (type !== 'credit_card' && type !== 'paypal') {
                console.error('Invalid payment method type:', type);
                alert('Invalid payment method type. Please try again.');
                return false;
            }

            // Validate name field
            const nameField = form.querySelector('#edit_name');
            if (!nameField || !nameField.value.trim()) {
                alert('Please enter a name for this payment method.');
                if (nameField) nameField.focus();
                return false;
            }

            // Validate credit card fields
            if (type === 'credit_card') {
                console.log('Validating credit card fields');

                // Validate card type
                const cardTypeVisa = form.querySelector('#edit_card_type_visa');
                const cardTypeMastercard = form.querySelector('#edit_card_type_mastercard');

                if ((!cardTypeVisa || !cardTypeVisa.checked) && (!cardTypeMastercard || !cardTypeMastercard.checked)) {
                    alert('Please select a card type (Visa or Mastercard).');
                    return false;
                }

                // Validate expiry date
                const expiryMonthField = form.querySelector('#edit_expiry_month');
                const expiryYearField = form.querySelector('#edit_expiry_year');

                if (!expiryMonthField || !expiryYearField) {
                    alert('Expiry date fields are missing.');
                    return false;
                }

                const expiryMonth = expiryMonthField.value;
                const expiryYear = expiryYearField.value;

                if (!expiryMonth || !expiryYear) {
                    alert('Please select an expiry date.');
                    return false;
                }

                // Check if expiry date is in the past
                const today = new Date();
                const expiryDate = new Date(expiryYear, parseInt(expiryMonth) - 1, 1);
                const lastDayOfMonth = new Date(expiryYear, parseInt(expiryMonth), 0);

                // Compare with the last day of the expiry month
                if (new Date(today.getFullYear(), today.getMonth(), today.getDate()) > lastDayOfMonth) {
                    alert('The expiry date cannot be in the past.');
                    return false;
                }

                console.log('Credit card validation passed');
            }

            // Validate PayPal fields
            if (type === 'paypal') {
                console.log('Validating PayPal fields');

                const emailField = form.querySelector('#edit_email');

                if (!emailField) {
                    alert('Email field is missing.');
                    return false;
                }

                if (!emailField.value.trim()) {
                    alert('Please enter a PayPal email address.');
                    emailField.focus();
                    return false;
                }

                // Simple email validation
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailField.value.trim())) {
                    alert('Please enter a valid email address.');
                    emailField.focus();
                    return false;
                }

                console.log('PayPal validation passed');
            }

            // Log the form data before submission
            console.log('Form data to be submitted:');
            console.log('Type:', type);
            console.log('Name:', nameField.value);

            if (type === 'credit_card') {
                console.log('Card Type:', form.querySelector('input[name="card_type"]:checked')?.value);
                console.log('Expiry Month:', form.querySelector('#edit_expiry_month').value);
                console.log('Expiry Year:', form.querySelector('#edit_expiry_year').value);
            } else if (type === 'paypal') {
                console.log('Email:', form.querySelector('#edit_email').value);
            }

            console.log('Is Default:', form.querySelector('#edit_is_default').checked);

            console.log('Form validation passed');
            return true;
        } catch (error) {
            console.error('Error during form validation:', error);
            alert('An error occurred during form validation. Please try again.');
            return false;
        }
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        try {
            // Initialize payment method fields
            togglePaymentMethodFields();

            // Initialize payout method fields
            togglePayoutMethodFields();

            console.log('Payment settings page initialized successfully');
        } catch (error) {
            console.error('Error initializing payment settings page:', error);
        }
    });
</script>
@endsection
