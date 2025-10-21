@extends('layouts.dashboard')

@section('title', __('messages.booking_details'))
@section('page-title', __('messages.booking_details'))

@section('content')
@php
    $customerLocation = $booking->customer_location instanceof \Illuminate\Support\Fluent
        ? $booking->customer_location->toArray()
        : (is_string($booking->customer_location)
            ? json_decode($booking->customer_location, true)
            : (is_array($booking->customer_location) ? $booking->customer_location : null));

    $customerLatitude = data_get($customerLocation, 'latitude');
    $customerLongitude = data_get($customerLocation, 'longitude');
    $customerAddress = data_get($customerLocation, 'address') ?? data_get($customerLocation, 'name');
    $customerLocationJson = $customerLocation
        ? json_encode($customerLocation, JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION)
        : null;
@endphp
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

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
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

    <!-- Booking Place -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('messages.booking_place') }}</h3>
        </div>
        <div class="p-6">
            @if($booking->is_home_service == 1)
                <div class="flex flex-col lg:flex-row lg:items-start lg:space-x-6">
                    <div class="flex-1 mb-4 lg:mb-0">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-home text-blue-500 mr-2"></i>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ __('messages.home_service') }}
                            </span>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-900 dark:text-white">{{ __('messages.service_will_be_provided_at_customer_location') }}</p>
                            {{-- <div class="text-xs text-gray-500 dark:text-gray-400">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                {{ $customerAddress ?? __('messages.address_not_available') }}
                            </div> --}}
                            @if($customerLocation)
                            <div class="flex items-center space-x-2 mt-2">
                                <button onclick="copyLocation()" class="inline-flex items-center px-2 py-1 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900 dark:hover:bg-blue-800 text-blue-700 dark:text-blue-300 text-xs font-medium rounded-md transition-colors duration-200">
                                    <i class="fas fa-copy mr-1"></i>
                                    {{ __('messages.copy_location') }}
                                </button>
                                <button onclick="shareLocation()" class="inline-flex items-center px-2 py-1 bg-green-100 hover:bg-green-200 dark:bg-green-900 dark:hover:bg-green-800 text-green-700 dark:text-green-300 text-xs font-medium rounded-md transition-colors duration-200">
                                    <i class="fas fa-share-alt mr-1"></i>
                                    {{ __('messages.share_location') }}
                                </button>
                                @if($customerLatitude && $customerLongitude)
                                <button onclick="openInMaps()" class="inline-flex items-center px-2 py-1 bg-purple-100 hover:bg-purple-200 dark:bg-purple-900 dark:hover:bg-purple-800 text-purple-700 dark:text-purple-300 text-xs font-medium rounded-md transition-colors duration-200">
                                    <i class="fas fa-external-link-alt mr-1"></i>
                                    {{ __('messages.open_in_maps') }}
                                </button>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                    @if($customerLatitude && $customerLongitude)
                        <div class="flex-1">
                            <div id="map-{{ $booking->id }}" class="w-full h-64 rounded-lg border border-gray-300 dark:border-gray-600"></div>
                        </div>
                    @endif
                </div>
            @else
                <div class="flex items-center mb-3">
                    <i class="fas fa-store text-green-500 mr-2"></i>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ __('messages.store_location') }}
                    </span>
                </div>
                <div class="space-y-2">
                    <p class="text-sm text-gray-900 dark:text-white">{{ __('messages.service_will_be_provided_at_store') }}</p>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        <i class="fas fa-store mr-1"></i>
                        {{ __('messages.please_visit_our_store_for_service') }}
                    </div>
                </div>
            @endif
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

@push('scripts')
<script>
    // Initialize Google Maps for customer locations
    function initMap() {
        @if($booking->is_home_service == 1 && $customerLatitude && $customerLongitude)
            const customerLocation = {
                lat: {{ $customerLatitude }},
                lng: {{ $customerLongitude }}
            };
            
            const map = new google.maps.Map(document.getElementById("map-{{ $booking->id }}"), {
                zoom: 15,
                center: customerLocation,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: false,
                styles: [
                    {
                        featureType: "poi",
                        elementType: "labels",
                        stylers: [{ visibility: "off" }]
                    }
                ]
            });
            
            const marker = new google.maps.Marker({
                position: customerLocation,
                map: map,
                title: "{{ __('messages.customer_location') }}",
                icon: {
                    url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="#3B82F6">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                    `),
                    scaledSize: new google.maps.Size(32, 32),
                    anchor: new google.maps.Point(16, 32)
                }
            });
            
            const infoWindow = new google.maps.InfoWindow({
                content: `
                    <div class="p-2">
                        <h4 class="font-semibold text-gray-900">{{ __('messages.customer_location') }}</h4>
                        <p class="text-sm text-gray-600">{{ $customerAddress ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $customerLatitude }}, {{ $customerLongitude }}</p>
                    </div>
                `
            });
            
            marker.addListener("click", () => {
                infoWindow.open(map, marker);
            });
        @endif
    }
    
    // Location utility functions
    function copyLocation() {
        const customerLocation = {!! $customerLocationJson ?? 'null' !!};
        
        if (customerLocation) {
            let locationText = '';
            
            // If we have coordinates, create a Google Maps link
            if (customerLocation.latitude && customerLocation.longitude) {
                locationText = `https://maps.google.com/?q=${customerLocation.latitude},${customerLocation.longitude}`;
            } else if (customerLocation.address || customerLocation.name) {
                // If no coordinates but we have address, use address for Google Maps search
                const addressQuery = customerLocation.address || customerLocation.name;
                locationText = `https://maps.google.com/?q=${encodeURIComponent(addressQuery)}`;
            }
            
            if (locationText.trim() !== '') {
                navigator.clipboard.writeText(locationText).then(function() {
                    // Show success message
                    showNotification('{{ __('messages.location_copied') }}', 'success');
                }).catch(function(err) {
                    console.error('Could not copy text: ', err);
                    showNotification('{{ __('messages.copy_failed') }}', 'error');
                });
            } else {
                showNotification('{{ __('messages.no_address_available') }}', 'error');
            }
        } else {
            showNotification('{{ __('messages.no_address_available') }}', 'error');
        }
    }
    
    function shareLocation() {
        const customerLocation = {!! $customerLocationJson ?? 'null' !!};
        
        if (customerLocation) {
            if (customerLocation.latitude && customerLocation.longitude) {
                const url = `https://maps.google.com/?q=${customerLocation.latitude},${customerLocation.longitude}`;

                if (navigator.share) {
                    navigator.share({
                        title: '{{ __('messages.customer_location') }}',
                        url,
                    }).catch(console.error);
                } else {
                    navigator.clipboard.writeText(url)
                        .then(() => showNotification('{{ __('messages.location_copied') }}', 'success'))
                        .catch(() => showNotification('{{ __('messages.copy_failed') }}', 'error'));
                }
            } else {
                showNotification('{{ __('messages.no_address_available') }}', 'error');
            }
        } else {
            showNotification('{{ __('messages.no_address_available') }}', 'error');
        }
    }
    
    function openInMaps() {
        const customerLocation = {!! $customerLocationJson ?? 'null' !!};
        
        if (customerLocation && customerLocation.latitude && customerLocation.longitude) {
            const url = `https://maps.google.com/?q=${customerLocation.latitude},${customerLocation.longitude}`;
            window.open(url, '_blank');
        } else {
            showNotification('{{ __('messages.no_address_available') }}', 'error');
        }
    }
    
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-4 py-2 rounded-md text-white z-50 ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 'bg-blue-500'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
    
    // Load Google Maps API
    function loadGoogleMaps() {
        if (typeof google === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://maps.googleapis.com/maps/api/js?key={{ config("services.google.maps_api_key", "YOUR_GOOGLE_MAPS_API_KEY") }}&callback=initMap';
            script.async = true;
            script.defer = true;
            document.head.appendChild(script);
        } else {
            initMap();
        }
    }
    
    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        @if($booking->is_home_service == 1 && $customerLatitude && $customerLongitude)
            loadGoogleMaps();
        @endif
    });
</script>
@endpush
@endsection
