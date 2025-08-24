@extends('layouts.dashboard')

@section('title', __('messages.booking_details'))
@section('page-title', __('messages.booking_details'))

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('messages.booking') }} #{{ $booking->booking_number ?? 'N/A' }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $booking->created_at ? $booking->created_at->format('F d, Y h:i A') : 'N/A' }}</p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-2">
            <a href="{{ route('vendor.bookings.edit', $booking->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-edit mr-2"></i> {{ __('messages.edit_booking') }}
            </a>
            <a href="{{ route('vendor.bookings.invoice', $booking->id) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-file-invoice mr-2"></i> {{ __('messages.invoice') }}
            </a>
            <a href="{{ route('vendor.bookings.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i> {{ __('messages.back') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Booking Status -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('messages.booking_status') }}</h3>
            <div class="flex items-center mb-4">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                    @if($booking->status == 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                    @elseif($booking->status == 'confirmed') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                    @elseif($booking->status == 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                    @elseif($booking->status == 'no_show') bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                    @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @endif">
                    {{ ucfirst($booking->status ?? 'pending') }}
                </span>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.booking_date') }}:</span>
                    <span class="text-sm text-gray-900 dark:text-white">{{ $booking->booking_date ? $booking->booking_date->format('M d, Y') : 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.booking_time') }}:</span>
                    <span class="text-sm text-gray-900 dark:text-white">{{ $booking->booking_time ? date('h:i A', strtotime($booking->booking_time)) : 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.duration') }}:</span>
                    <span class="text-sm text-gray-900 dark:text-white">{{ $booking->duration ?? 0 }} {{ __('messages.minutes') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.payment_status') }}:</span>
                    <span class="text-sm text-gray-900 dark:text-white">{{ ucfirst($booking->payment_status ?? 'pending') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.payment_method') }}:</span>
                    <span class="text-sm text-gray-900 dark:text-white">{{ ucfirst($booking->payment_method ?? 'N/A') }}</span>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('messages.customer_information') }}</h3>
            <div class="space-y-2">
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.name') }}:</span>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $booking->user->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.email') }}:</span>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $booking->user->email ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.phone') }}:</span>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $booking->user->phone ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Branch Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('messages.branch_information') }}</h3>
            <div class="space-y-2">
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.branch') }}:</span>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $booking->branch->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.address') }}:</span>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $booking->branch->address ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Phone:</span>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $booking->branch->phone ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Details -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('messages.service_details') }}</h3>
        </div>
        <div class="p-6">
            <div class="flex items-center">
                @if($booking->service && $booking->service->image)
                <div class="flex-shrink-0 h-20 w-20">
                    <img class="h-20 w-20 rounded-md object-cover" src="{{ $booking->service->image }}" alt="{{ $booking->service->name }}">
                </div>
                @endif
                <div class="ml-4">
                    <h4 class="text-lg font-medium text-gray-900 dark:text-white">{{ $booking->service->name ?? __('messages.unknown_service') }}</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $booking->service->description ?? __('messages.no_description_available') }}</p>
                    <div class="mt-2 flex items-center">
                        <span class="text-sm font-medium text-gray-900 dark:text-white mr-4">${{ number_format($booking->price, 2) }}</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $booking->duration ?? 0 }} {{ __('messages.minutes') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('messages.notes') }}</h3>
        </div>
        <div class="p-6">
            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $booking->notes ?? __('messages.no_notes_available') }}</p>
        </div>
    </div>
</div>
@endsection
