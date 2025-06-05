@extends('layouts.dashboard')

@section('title', 'Notification Settings')
@section('page-title', 'Notification Settings')

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Notification Settings</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">Configure email and push notification preferences</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('vendor.settings') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i> Back to Settings
            </a>
        </div>
    </div>

    <!-- Notification Preferences -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700 mb-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Notification Preferences</h3>
        <form action="#" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <!-- Email Notifications -->
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Email Notifications</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Receive email notifications for important updates.</p>
                        </div>
                        <div class="flex items-center">
                            <span class="mr-3 text-sm font-medium text-gray-900 dark:text-white">Enabled</span>
                            <button type="button" class="bg-indigo-600 relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" role="switch" aria-checked="true">
                                <span class="translate-x-5 pointer-events-none relative inline-block h-5 w-5 rounded-full bg-white dark:bg-gray-800 shadow transform ring-0 transition ease-in-out duration-200">
                                    <span class="absolute inset-0 h-full w-full flex items-center justify-center transition-opacity opacity-0 ease-out duration-100">
                                        <i class="fas fa-times text-gray-400 text-xs"></i>
                                    </span>
                                    <span class="absolute inset-0 h-full w-full flex items-center justify-center transition-opacity opacity-100 ease-in duration-200">
                                        <i class="fas fa-check text-indigo-600 text-xs"></i>
                                    </span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Push Notifications -->
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Push Notifications</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Receive push notifications on your device.</p>
                        </div>
                        <div class="flex items-center">
                            <span class="mr-3 text-sm font-medium text-gray-900 dark:text-white">Enabled</span>
                            <button type="button" class="bg-indigo-600 relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" role="switch" aria-checked="true">
                                <span class="translate-x-5 pointer-events-none relative inline-block h-5 w-5 rounded-full bg-white dark:bg-gray-800 shadow transform ring-0 transition ease-in-out duration-200">
                                    <span class="absolute inset-0 h-full w-full flex items-center justify-center transition-opacity opacity-0 ease-out duration-100">
                                        <i class="fas fa-times text-gray-400 text-xs"></i>
                                    </span>
                                    <span class="absolute inset-0 h-full w-full flex items-center justify-center transition-opacity opacity-100 ease-in duration-200">
                                        <i class="fas fa-check text-indigo-600 text-xs"></i>
                                    </span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- SMS Notifications -->
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">SMS Notifications</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Receive text message notifications for important updates.</p>
                        </div>
                        <div class="flex items-center">
                            <span class="mr-3 text-sm font-medium text-gray-900 dark:text-white">Disabled</span>
                            <button type="button" class="bg-gray-200 dark:bg-gray-600 relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" role="switch" aria-checked="false">
                                <span class="translate-x-0 pointer-events-none relative inline-block h-5 w-5 rounded-full bg-white dark:bg-gray-800 shadow transform ring-0 transition ease-in-out duration-200">
                                    <span class="absolute inset-0 h-full w-full flex items-center justify-center transition-opacity opacity-100 ease-in duration-200">
                                        <i class="fas fa-times text-gray-400 text-xs"></i>
                                    </span>
                                    <span class="absolute inset-0 h-full w-full flex items-center justify-center transition-opacity opacity-0 ease-out duration-100">
                                        <i class="fas fa-check text-indigo-600 text-xs"></i>
                                    </span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-save mr-2"></i> Save Preferences
                </button>
            </div>
        </form>
    </div>

    <!-- Notification Types -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Notification Types</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Choose which types of notifications you want to receive.</p>
        
        <form action="#" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <!-- New Orders -->
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">New Orders</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Get notified when you receive a new order.</p>
                        </div>
                        <div class="flex items-center">
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center">
                                    <input id="new_orders_email" name="new_orders_email" type="checkbox" checked class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded">
                                    <label for="new_orders_email" class="ml-2 text-xs text-gray-700 dark:text-gray-300">Email</label>
                                </div>
                                <div class="flex items-center">
                                    <input id="new_orders_push" name="new_orders_push" type="checkbox" checked class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded">
                                    <label for="new_orders_push" class="ml-2 text-xs text-gray-700 dark:text-gray-300">Push</label>
                                </div>
                                <div class="flex items-center">
                                    <input id="new_orders_sms" name="new_orders_sms" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded">
                                    <label for="new_orders_sms" class="ml-2 text-xs text-gray-700 dark:text-gray-300">SMS</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- New Bookings -->
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">New Bookings</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Get notified when you receive a new booking.</p>
                        </div>
                        <div class="flex items-center">
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center">
                                    <input id="new_bookings_email" name="new_bookings_email" type="checkbox" checked class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded">
                                    <label for="new_bookings_email" class="ml-2 text-xs text-gray-700 dark:text-gray-300">Email</label>
                                </div>
                                <div class="flex items-center">
                                    <input id="new_bookings_push" name="new_bookings_push" type="checkbox" checked class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded">
                                    <label for="new_bookings_push" class="ml-2 text-xs text-gray-700 dark:text-gray-300">Push</label>
                                </div>
                                <div class="flex items-center">
                                    <input id="new_bookings_sms" name="new_bookings_sms" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded">
                                    <label for="new_bookings_sms" class="ml-2 text-xs text-gray-700 dark:text-gray-300">SMS</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Order Status Updates -->
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Order Status Updates</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Get notified when an order status changes.</p>
                        </div>
                        <div class="flex items-center">
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center">
                                    <input id="order_status_email" name="order_status_email" type="checkbox" checked class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded">
                                    <label for="order_status_email" class="ml-2 text-xs text-gray-700 dark:text-gray-300">Email</label>
                                </div>
                                <div class="flex items-center">
                                    <input id="order_status_push" name="order_status_push" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded">
                                    <label for="order_status_push" class="ml-2 text-xs text-gray-700 dark:text-gray-300">Push</label>
                                </div>
                                <div class="flex items-center">
                                    <input id="order_status_sms" name="order_status_sms" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded">
                                    <label for="order_status_sms" class="ml-2 text-xs text-gray-700 dark:text-gray-300">SMS</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Notifications -->
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Payment Notifications</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Get notified about payment updates.</p>
                        </div>
                        <div class="flex items-center">
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center">
                                    <input id="payment_email" name="payment_email" type="checkbox" checked class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded">
                                    <label for="payment_email" class="ml-2 text-xs text-gray-700 dark:text-gray-300">Email</label>
                                </div>
                                <div class="flex items-center">
                                    <input id="payment_push" name="payment_push" type="checkbox" checked class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded">
                                    <label for="payment_push" class="ml-2 text-xs text-gray-700 dark:text-gray-300">Push</label>
                                </div>
                                <div class="flex items-center">
                                    <input id="payment_sms" name="payment_sms" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded">
                                    <label for="payment_sms" class="ml-2 text-xs text-gray-700 dark:text-gray-300">SMS</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- System Notifications -->
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">System Notifications</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Get notified about system updates and maintenance.</p>
                        </div>
                        <div class="flex items-center">
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center">
                                    <input id="system_email" name="system_email" type="checkbox" checked class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded">
                                    <label for="system_email" class="ml-2 text-xs text-gray-700 dark:text-gray-300">Email</label>
                                </div>
                                <div class="flex items-center">
                                    <input id="system_push" name="system_push" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded">
                                    <label for="system_push" class="ml-2 text-xs text-gray-700 dark:text-gray-300">Push</label>
                                </div>
                                <div class="flex items-center">
                                    <input id="system_sms" name="system_sms" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded">
                                    <label for="system_sms" class="ml-2 text-xs text-gray-700 dark:text-gray-300">SMS</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-save mr-2"></i> Save Preferences
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
