@extends('layouts.dashboard')

@section('title', 'Edit Branch')
@section('page-title', 'Edit Branch')

@section('content')
<div class="container mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Branch</h2>
        <p class="mt-1 text-gray-600 dark:text-gray-400">Update branch information</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
        <form action="{{ route('vendor.branches.update', $branch->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Branch Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $branch->name) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="company_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Company</label>
                    <select id="company_id" name="company_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" required>
                        <option value="">Select Company</option>
                        @foreach($companies ?? [] as $company)
                            <option value="{{ $company->id }}" {{ old('company_id', $branch->company_id) == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                        @endforeach
                    </select>
                    @error('company_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone Number</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $branch->phone) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $branch->email) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <textarea id="description" name="description" rows="3" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">{{ old('description', $branch->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="text" name="address" id="address" value="{{ old('address', $branch->address) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-100 dark:text-gray-600 rounded-md cursor-not-allowed" readonly required>
                        <div id="address-loading" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                            <svg class="animate-spin h-4 w-4 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Address will be automatically filled when you select a location on the map</p>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="emirate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Emirate <span class="text-red-500">*</span></label>
                    <select id="emirate" name="emirate" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" required>
                        <option value="">Select Emirate</option>
                        <option value="Dubai" {{ old('emirate', $branch->emirate ?? '') == 'Dubai' ? 'selected' : '' }}>Dubai</option>
                        <option value="Abu Dhabi" {{ old('emirate', $branch->emirate ?? '') == 'Abu Dhabi' ? 'selected' : '' }}>Abu Dhabi</option>
                        <option value="Sharjah" {{ old('emirate', $branch->emirate ?? '') == 'Sharjah' ? 'selected' : '' }}>Sharjah</option>
                        <option value="Ajman" {{ old('emirate', $branch->emirate ?? '') == 'Ajman' ? 'selected' : '' }}>Ajman</option>
                        <option value="Umm Al Quwain" {{ old('emirate', $branch->emirate ?? '') == 'Umm Al Quwain' ? 'selected' : '' }}>Umm Al Quwain</option>
                        <option value="Ras Al Khaimah" {{ old('emirate', $branch->emirate ?? '') == 'Ras Al Khaimah' ? 'selected' : '' }}>Ras Al Khaimah</option>
                        <option value="Fujairah" {{ old('emirate', $branch->emirate ?? '') == 'Fujairah' ? 'selected' : '' }}>Fujairah</option>
                    </select>
                    @error('emirate')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Location on Map <span class="text-red-500">*</span></label>
                    <div class="mb-2">
                        <input id="pac-input" type="text" placeholder="Search for a location" class="w-full p-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                    </div>
                    <div id="map" style="height: 400px; width: 100%; border-radius: 0.375rem;" class="border border-gray-300 dark:border-gray-600"></div>
                    <input type="hidden" name="lat" id="lat" value="{{ old('lat', $branch->lat) }}">
                    <input type="hidden" name="lng" id="lng" value="{{ old('lng', $branch->lng) }}">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="active" {{ old('status', $branch->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $branch->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Branch Image</h3>
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md border border-gray-200 dark:border-gray-600">
                        <div class="flex flex-col space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="use_company_image" id="use_company_image" value="1" class="form-checkbox h-5 w-5 text-indigo-600 dark:text-indigo-400" {{ old('use_company_image', $branch->use_company_image) ? 'checked' : '' }}>
                                <label for="use_company_image" class="ml-2 text-gray-700 dark:text-gray-300">Use company image</label>
                            </div>

                            <div id="branch_image_container" class="{{ old('use_company_image', $branch->use_company_image) ? 'hidden' : '' }}">
                                @php
                                    // Get branch image using the accessor method
                                    $branchImage = !$branch->use_company_image ? $branch->getBranchImageAttribute() : null;
                                @endphp
                                @if($branchImage)
                                <div class="mb-3">
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Branch Image:</p>
                                    <img src="{{ $branchImage }}" alt="{{ $branch->name }}" class="w-40 h-40 object-cover rounded-md border border-gray-300 dark:border-gray-600">
                                </div>
                                @endif

                                <label for="branch_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ $branch->branch_image ? 'Change Branch Image' : 'Upload Branch Image' }}</label>
                                <div class="mt-1 flex items-center">
                                    <input type="file" name="branch_image" id="branch_image" accept="image/jpeg,image/png,image/webp" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                </div>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Recommended size: 400x400px. Max file size: 2MB. Supported formats: JPG, PNG, WebP</p>
                                @error('branch_image')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Business Hours Section -->
            <div class="mt-8 col-span-1 md:col-span-2">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Business Hours</h3>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md border border-gray-200 dark:border-gray-600">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @php
                            $openingHours = $branch->opening_hours ?? [];
                        @endphp

                        @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                            @php
                                $dayData = $openingHours[$day] ?? ['is_open' => true, 'open' => '09:00', 'close' => '17:00'];
                                $isOpen = $dayData['is_open'] ?? true;
                                $openTime = $dayData['open'] ?? '09:00';
                                $closeTime = $dayData['close'] ?? '17:00';
                            @endphp
                            <div class="flex flex-col space-y-2 p-3 bg-white dark:bg-gray-800 rounded-md shadow-sm">
                                <div class="flex items-center justify-between">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="days_open[{{ $day }}]" value="1" class="form-checkbox h-5 w-5 text-indigo-600 dark:text-indigo-400" {{ $isOpen ? 'checked' : '' }}>
                                        <span class="ml-2 text-gray-700 dark:text-gray-300 capitalize">{{ $day }}</span>
                                    </label>
                                    <span class="text-xs {{ $isOpen ? 'text-green-500' : 'text-red-500' }} dark:text-gray-400" id="{{ $day }}_status">{{ $isOpen ? 'Open' : 'Closed' }}</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2 mt-2 {{ $isOpen ? '' : 'hidden' }}" id="{{ $day }}_hours">
                                    <div>
                                        <label for="{{ $day }}_open" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Opening Time</label>
                                        <input type="time" name="opening_hours[{{ $day }}][open]" id="{{ $day }}_open" value="{{ $openTime }}" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label for="{{ $day }}_close" class="block text-xs font-medium text-gray-700 dark:text-gray-300">Closing Time</label>
                                        <input type="time" name="opening_hours[{{ $day }}][close]" id="{{ $day }}_close" value="{{ $closeTime }}" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end">
                <a href="{{ route('vendor.branches.show', $branch->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Update Branch
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Global error handler for Google Maps authentication
    window.gm_authFailure = function() {
        console.error('Google Maps authentication failed. Please check your API key.');
        const mapElement = document.getElementById('map');
        if (mapElement) {
            mapElement.innerHTML = '<div style="padding: 20px; text-align: center; color: red; background: #fee; border: 1px solid #fcc; border-radius: 8px;">Google Maps authentication failed. Please check your API key.</div>';
        }
    };

    // Global error handler for script loading
    function handleGoogleMapsError() {
        console.error('Failed to load Google Maps API script');
        const mapElement = document.getElementById('map');
        if (mapElement) {
            mapElement.innerHTML = '<div style="padding: 20px; text-align: center; color: red; background: #fee; border: 1px solid #fcc; border-radius: 8px;">Failed to load Google Maps. Please check your internet connection and try again.</div>';
        }
    }

</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initMap" async defer onerror="handleGoogleMapsError()"></script>
<script>
    // Google Maps functionality
    let map;
    let marker;
    let searchBox;

    function initMap() {
        // Get existing lat/lng values
        const defaultLat = parseFloat(document.getElementById('lat').value) || 25.2048;
        const defaultLng = parseFloat(document.getElementById('lng').value) || 55.2708;
        const center = { lat: defaultLat, lng: defaultLng };

        // Create the map
        map = new google.maps.Map(document.getElementById('map'), {
            center: center,
            zoom: 12,
            styles: [
                { elementType: "geometry", stylers: [{ color: "#242f3e" }] },
                { elementType: "labels.text.stroke", stylers: [{ color: "#242f3e" }] },
                { elementType: "labels.text.fill", stylers: [{ color: "#746855" }] },
                {
                    featureType: "administrative.locality",
                    elementType: "labels.text.fill",
                    stylers: [{ color: "#d59563" }],
                },
                {
                    featureType: "poi",
                    elementType: "labels.text.fill",
                    stylers: [{ color: "#d59563" }],
                },
                {
                    featureType: "poi.park",
                    elementType: "geometry",
                    stylers: [{ color: "#263c3f" }],
                },
                {
                    featureType: "poi.park",
                    elementType: "labels.text.fill",
                    stylers: [{ color: "#6b9a76" }],
                },
                {
                    featureType: "road",
                    elementType: "geometry",
                    stylers: [{ color: "#38414e" }],
                },
                {
                    featureType: "road",
                    elementType: "geometry.stroke",
                    stylers: [{ color: "#212a37" }],
                },
                {
                    featureType: "road",
                    elementType: "labels.text.fill",
                    stylers: [{ color: "#9ca5b3" }],
                },
                {
                    featureType: "road.highway",
                    elementType: "geometry",
                    stylers: [{ color: "#746855" }],
                },
                {
                    featureType: "road.highway",
                    elementType: "geometry.stroke",
                    stylers: [{ color: "#1f2835" }],
                },
                {
                    featureType: "road.highway",
                    elementType: "labels.text.fill",
                    stylers: [{ color: "#f3d19c" }],
                },
                {
                    featureType: "transit",
                    elementType: "geometry",
                    stylers: [{ color: "#2f3948" }],
                },
                {
                    featureType: "transit.station",
                    elementType: "labels.text.fill",
                    stylers: [{ color: "#d59563" }],
                },
                {
                    featureType: "water",
                    elementType: "geometry",
                    stylers: [{ color: "#17263c" }],
                },
                {
                    featureType: "water",
                    elementType: "labels.text.fill",
                    stylers: [{ color: "#515c6d" }],
                },
                {
                    featureType: "water",
                    elementType: "labels.text.stroke",
                    stylers: [{ color: "#17263c" }],
                },
            ],
        });

        // Add initial marker
        marker = new google.maps.Marker({
            position: center,
            map: map,
            draggable: true,
            animation: google.maps.Animation.DROP
        });

        // Update lat/lng when marker is dragged
        marker.addListener('dragend', function() {
            const position = marker.getPosition();
            document.getElementById('lat').value = position.lat();
            document.getElementById('lng').value = position.lng();

            // Reverse geocode to update address
            reverseGeocode(position);
        });

        // Add click listener to map
        map.addListener('click', function(event) {
            marker.setPosition(event.latLng);
            document.getElementById('lat').value = event.latLng.lat();
            document.getElementById('lng').value = event.latLng.lng();

            // Reverse geocode to update address
            reverseGeocode(event.latLng);
        });

        // Initialize search box
        const input = document.getElementById('pac-input');
        searchBox = new google.maps.places.SearchBox(input);

        // Bias the SearchBox results towards current map's viewport
        map.addListener('bounds_changed', function() {
            searchBox.setBounds(map.getBounds());
        });

        // Listen for the event fired when the user selects a prediction and retrieve
        // more details for that place
        searchBox.addListener('places_changed', function() {
            const places = searchBox.getPlaces();

            if (places.length === 0) {
                return;
            }

            // For each place, get the location and update the marker
            const bounds = new google.maps.LatLngBounds();
            places.forEach(function(place) {
                if (!place.geometry || !place.geometry.location) {
                    console.log("Returned place contains no geometry");
                    return;
                }

                // Update marker position
                marker.setPosition(place.geometry.location);

                // Update form fields
                document.getElementById('lat').value = place.geometry.location.lat();
                document.getElementById('lng').value = place.geometry.location.lng();
                document.getElementById('address').value = place.formatted_address || '';

                if (place.geometry.viewport) {
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
            });
            map.fitBounds(bounds);
        });
    }

    // Reverse geocode a position to get address
    function reverseGeocode(position) {
        const geocoder = new google.maps.Geocoder();
        const addressField = document.getElementById('address');
        const loadingIndicator = document.getElementById('address-loading');

        // Show loading indicator
        if (loadingIndicator) {
            loadingIndicator.classList.remove('hidden');
        }

        // Clear current address
        addressField.value = 'Resolving address...';
        addressField.style.color = '#9CA3AF'; // Gray color for loading text

        geocoder.geocode({ location: position }, function(results, status) {
            // Hide loading indicator
            if (loadingIndicator) {
                loadingIndicator.classList.add('hidden');
            }

            if (status === 'OK' && results[0]) {
                // Success - update address field
                addressField.value = results[0].formatted_address;
                addressField.style.color = ''; // Reset to default color

                // Try to extract emirate from address components
                for (const component of results[0].address_components) {
                    if (component.types.includes('administrative_area_level_1')) {
                        const emirateSelect = document.getElementById('emirate');
                        const emirateName = component.long_name;

                        // Find matching option in select
                        for (let i = 0; i < emirateSelect.options.length; i++) {
                            if (emirateSelect.options[i].text.includes(emirateName)) {
                                emirateSelect.selectedIndex = i;
                                break;
                            }
                        }
                        break;
                    }
                }

                console.log('Address resolved successfully:', results[0].formatted_address);
            } else {
                // Error handling
                let errorMessage = 'Unable to resolve address';

                switch (status) {
                    case 'ZERO_RESULTS':
                        errorMessage = 'No address found for this location';
                        break;
                    case 'OVER_QUERY_LIMIT':
                        errorMessage = 'Address lookup limit exceeded. Please try again later.';
                        break;
                    case 'REQUEST_DENIED':
                        errorMessage = 'Address lookup denied. Please check API configuration.';
                        break;
                    case 'INVALID_REQUEST':
                        errorMessage = 'Invalid address lookup request';
                        break;
                    case 'UNKNOWN_ERROR':
                        errorMessage = 'Unknown error occurred during address lookup';
                        break;
                }

                addressField.value = errorMessage;
                addressField.style.color = '#EF4444'; // Red color for error

                console.error('Geocoding failed:', status, errorMessage);

                // Show user-friendly error notification
                if (typeof showNotification === 'function') {
                    showNotification('Address resolution failed: ' + errorMessage, 'error');
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize business hours functionality
        const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        days.forEach(day => {
            const checkbox = document.querySelector(`input[name="days_open[${day}]"]`);
            const hoursDiv = document.getElementById(`${day}_hours`);
            const statusSpan = document.getElementById(`${day}_status`);

            // Set initial state
            updateHoursVisibility(checkbox, hoursDiv, statusSpan);

            // Add event listener for changes
            checkbox.addEventListener('change', function() {
                updateHoursVisibility(this, hoursDiv, statusSpan);
            });
        });

        function updateHoursVisibility(checkbox, hoursDiv, statusSpan) {
            if (checkbox.checked) {
                hoursDiv.classList.remove('hidden');
                statusSpan.textContent = 'Open';
                statusSpan.classList.remove('text-red-500');
                statusSpan.classList.add('text-green-500');
            } else {
                hoursDiv.classList.add('hidden');
                statusSpan.textContent = 'Closed';
                statusSpan.classList.remove('text-green-500');
                statusSpan.classList.add('text-red-500');
            }
        }

        // Branch image toggle functionality
        const useCompanyImageCheckbox = document.getElementById('use_company_image');
        const branchImageContainer = document.getElementById('branch_image_container');

        useCompanyImageCheckbox.addEventListener('change', function() {
            if (this.checked) {
                branchImageContainer.classList.add('hidden');
            } else {
                branchImageContainer.classList.remove('hidden');
            }
        });
    });
</script>
@endsection
