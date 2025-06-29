@extends('layouts.dashboard')

@section('title', 'Booking Invoice')
@section('page-title', 'Booking Invoice')

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Invoice #{{ $booking->booking_number ?? 'N/A' }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $booking->created_at ? $booking->created_at->format('F d, Y') : 'N/A' }}</p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-2">
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-print mr-2"></i> Print Invoice
            </button>
            <a href="{{ route('vendor.bookings.show', $booking->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i> Back to Booking
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700 print:shadow-none print:border-none">
        <!-- Invoice Header -->
        <div class="flex flex-col md:flex-row justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">INVOICE</h1>
                <p class="text-gray-600 dark:text-gray-400">Booking #{{ $booking->booking_number ?? 'N/A' }}</p>
                <p class="text-gray-600 dark:text-gray-400">Date: {{ $booking->created_at ? $booking->created_at->format('M d, Y') : 'N/A' }}</p>
            </div>
            <div class="mt-4 md:mt-0">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $booking->branch->company->name ?? 'Company Name' }}</h2>
                <p class="text-gray-600 dark:text-gray-400">{{ $booking->branch->address ?? 'Branch Address' }}</p>
                <p class="text-gray-600 dark:text-gray-400">{{ $booking->branch->phone ?? 'Phone' }}</p>
                <p class="text-gray-600 dark:text-gray-400">{{ $booking->branch->email ?? 'Email' }}</p>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Bill To:</h3>
            <p class="text-gray-800 dark:text-gray-200">{{ $booking->user->name ?? 'Customer Name' }}</p>
            <p class="text-gray-600 dark:text-gray-400">{{ $booking->user->email ?? 'Email' }}</p>
            <p class="text-gray-600 dark:text-gray-400">{{ $booking->user->phone ?? 'Phone' }}</p>
        </div>

        <!-- Booking Details -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Booking Details:</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Service</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date & Time</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Duration</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $booking->service->name ?? 'Unknown Service' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $booking->booking_date ? $booking->booking_date->format('M d, Y') : 'N/A' }} at 
                                {{ $booking->booking_time ? date('h:i A', strtotime($booking->booking_time)) : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $booking->duration ?? 0 }} minutes
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white text-right">
                                ${{ number_format($booking->price, 2) }}
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">Subtotal:</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white text-right">${{ number_format($booking->price, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">Tax:</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white text-right">$0.00</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right text-sm font-bold text-gray-900 dark:text-white">Total:</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white text-right">${{ number_format($booking->price, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Branch Information -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Branch Information:</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-600 dark:text-gray-400">Branch: <span class="font-medium text-gray-900 dark:text-white">{{ $booking->branch->name ?? 'N/A' }}</span></p>
                    <p class="text-gray-600 dark:text-gray-400">Address: <span class="font-medium text-gray-900 dark:text-white">{{ $booking->branch->address ?? 'N/A' }}</span></p>
                </div>
                <div>
                    <p class="text-gray-600 dark:text-gray-400">Phone: <span class="font-medium text-gray-900 dark:text-white">{{ $booking->branch->phone ?? 'N/A' }}</span></p>
                    <p class="text-gray-600 dark:text-gray-400">Email: <span class="font-medium text-gray-900 dark:text-white">{{ $booking->branch->email ?? 'N/A' }}</span></p>
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Payment Information:</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-600 dark:text-gray-400">Payment Status: <span class="font-medium text-gray-900 dark:text-white">{{ ucfirst($booking->payment_status ?? 'pending') }}</span></p>
                    <p class="text-gray-600 dark:text-gray-400">Payment Method: <span class="font-medium text-gray-900 dark:text-white">{{ ucfirst($booking->payment_method ?? 'N/A') }}</span></p>
                </div>
                <div>
                    <p class="text-gray-600 dark:text-gray-400">Booking Status: <span class="font-medium text-gray-900 dark:text-white">{{ ucfirst($booking->status ?? 'pending') }}</span></p>
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($booking->notes)
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Notes:</h3>
            <p class="text-gray-600 dark:text-gray-400">{{ $booking->notes }}</p>
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
