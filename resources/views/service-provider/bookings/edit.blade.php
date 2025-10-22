@extends('layouts.service-provider')

@section('title', __('messages.edit_booking'))
@section('page-title', __('messages.edit_booking'))

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('messages.edit_booking') }} #{{ $booking->booking_number ?? 'N/A' }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $booking->created_at ? $booking->created_at->format('F d, Y h:i A') : 'N/A' }}</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('service-provider.bookings.show', $booking) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i> {{ __('messages.back_to_booking') }}
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
        <form action="{{ route('service-provider.bookings.update-status', $booking) }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Booking Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.status') }} <span class="text-red-500">*</span></label>
                    <select id="status" name="status" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white bg-white rounded-md shadow-sm focus:outline-none focus:ring-[#53D2DC] focus:border-[#53D2DC] sm:text-sm" required>
                        <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>{{ __('messages.pending') }}</option>
                        <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>{{ __('messages.confirmed') }}</option>
                        <option value="in_progress" {{ $booking->status == 'in_progress' ? 'selected' : '' }}>{{ __('service_provider.booking_status_in_progress') }}</option>
                        <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>{{ __('messages.completed') }}</option>
                        <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>{{ __('messages.cancelled') }}</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.notes') }}</label>
                    <textarea id="notes" name="notes" rows="4" class="mt-1 focus:ring-[#53D2DC] focus:border-[#53D2DC] block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="{{ __('messages.add_notes_placeholder') }}">{{ old('notes', $booking->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#53D2DC] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:opacity-90 active:opacity-80 focus:outline-none focus:ring ring-[#53D2DC]/30 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-save mr-2"></i> {{ __('messages.update_booking') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Booking Details (Read-only) -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mt-6 border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('messages.booking_details') }}</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.customer_information') }}</h4>
                    <div class="mt-2 space-y-2">
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.name') }}:</span>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $booking->user->name ?? __('messages.guest') }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.email') }}:</span>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $booking->user->email ?? __('messages.no_email') }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.phone') }}:</span>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $booking->user->phone ?? __('messages.no_phone') }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.booking_information') }}</h4>
                    <div class="mt-2 space-y-2">
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.date') }}:</span>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $booking->booking_date ? $booking->booking_date->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.time') }}:</span>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $booking->booking_time ? date('h:i A', strtotime($booking->booking_time)) : 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.duration') }}:</span>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $booking->duration ?? 0 }} {{ __('messages.minutes') }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.price') }}:</span>
                            <p class="text-sm text-gray-900 dark:text-white">${{ number_format($booking->price, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.service_information') }}</h4>
                <div class="mt-2 flex items-center">
                    @if($booking->service && $booking->service->image)
                        <div class="flex-shrink-0 h-16 w-16">
                            <img class="h-16 w-16 rounded-md object-cover" src="{{ $booking->service->image }}" alt="{{ $booking->service->name }}">
                        </div>
                    @endif
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $booking->service->name ?? __('messages.unknown_service') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $booking->service->description ?? __('messages.no_description_available') }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.branch_information') }}</h4>
                <div class="mt-2 space-y-2">
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.branch') }}:</span>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $booking->branch->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.address') }}:</span>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $booking->branch->address ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
