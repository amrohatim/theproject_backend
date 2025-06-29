@extends('layouts.dashboard')

@section('title', 'Edit Booking')
@section('page-title', 'Edit Booking')

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Booking #{{ $booking->booking_number ?? 'N/A' }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $booking->created_at ? $booking->created_at->format('F d, Y h:i A') : 'N/A' }}</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('vendor.bookings.show', $booking->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i> Back to Booking
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
        <form action="{{ route('vendor.bookings.update', $booking->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Booking Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Booking Status <span class="text-red-500">*</span></label>
                    <select id="status" name="status" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="no_show" {{ $booking->status == 'no_show' ? 'selected' : '' }}>No Show</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Notes -->
                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                    <textarea id="notes" name="notes" rows="4" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">{{ $booking->notes }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-save mr-2"></i> Update Booking
                </button>
            </div>
        </form>
    </div>

    <!-- Booking Details (Read-only) -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mt-6 border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Booking Details</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Customer Information</h4>
                    <div class="mt-2 space-y-2">
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Name:</span>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $booking->user->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Email:</span>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $booking->user->email ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Phone:</span>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $booking->user->phone ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Booking Information</h4>
                    <div class="mt-2 space-y-2">
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Date:</span>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $booking->booking_date ? $booking->booking_date->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Time:</span>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $booking->booking_time ? date('h:i A', strtotime($booking->booking_time)) : 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Duration:</span>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $booking->duration ?? 0 }} minutes</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Price:</span>
                            <p class="text-sm text-gray-900 dark:text-white">${{ number_format($booking->price, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6">
                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Service Information</h4>
                <div class="mt-2 flex items-center">
                    @if($booking->service && $booking->service->image)
                    <div class="flex-shrink-0 h-16 w-16">
                        <img class="h-16 w-16 rounded-md object-cover" src="{{ $booking->service->image }}" alt="{{ $booking->service->name }}">
                    </div>
                    @endif
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $booking->service->name ?? 'Unknown Service' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $booking->service->description ?? 'No description available.' }}</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-6">
                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Branch Information</h4>
                <div class="mt-2 space-y-2">
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Branch:</span>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $booking->branch->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Address:</span>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $booking->branch->address ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
