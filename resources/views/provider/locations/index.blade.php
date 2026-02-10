@extends('layouts.dashboard')

@section('title', __('provider.locations'))
@section('page-title', __('provider.locations'))

@section('styles')
<style>
    #map {
        height: 480px;
        width: 100%;
        border-radius: 12px;
    }
    .location-item + .location-item {
        border-top: 1px solid #e5e7eb;
    }
    .dark .location-item + .location-item {
        border-top-color: #374151;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ __('provider.stock_distribution_points') }}</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('provider.add_manage_locations') }}</p>
        </div>
        <button id="save-locations" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <i class="fas fa-save mr-2"></i> {{ __('provider.save_locations') }}
        </button>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="emirate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('provider.emirate') }}</label>
                <select id="emirate" class="mt-2 block w-full p-3 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">{{ __('provider.select_emirate') }}</option>
                    <option value="Dubai">{{ __('provider.dubai') }}</option>
                    <option value="Abu Dhabi">{{ __('provider.abu_dhabi') }}</option>
                    <option value="Sharjah">{{ __('provider.sharjah') }}</option>
                    <option value="Ajman">{{ __('provider.ajman') }}</option>
                    <option value="Umm Al Quwain">{{ __('provider.umm_al_quwain') }}</option>
                    <option value="Ras Al Khaimah">{{ __('provider.ras_al_khaimah') }}</option>
                    <option value="Fujairah">{{ __('provider.fujairah') }}</option>
                </select>
            </div>
            <div>
                <label for="pac-input" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('provider.search_location') }}</label>
                <input id="pac-input" type="text" placeholder="{{ __('provider.search_location') }}"
                       class="mt-2 block px-2 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <div id="map"></div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide">{{ __('provider.saved_locations') }}</h3>
        </div>
        <div id="locations-container">
            @if(count($locations) > 0)
                @foreach($locations as $location)
                <div class="location-item px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4" data-id="{{ $location->id }}" data-lat="{{ $location->latitude }}" data-lng="{{ $location->longitude }}">
                    <div>
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $location->label ?? 'Location ' . $loop->iteration }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $location->emirate }}</div>
                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $location->latitude }}, {{ $location->longitude }}</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button class="edit-location inline-flex items-center justify-center h-9 w-9 rounded-md border border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-300" data-id="{{ $location->id }}" title="{{ __('provider.edit') }}">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button class="delete-location inline-flex items-center justify-center h-9 w-9 rounded-md border border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 hover:border-red-300" data-id="{{ $location->id }}" title="{{ __('provider.delete') }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            @else
                <div class="px-6 py-12 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 text-gray-400">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">{{ __('provider.no_locations_saved') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('googlemaps.api_key') }}&libraries=places&callback=initMap" async defer></script>
<script>
    // Global variables
    let map;
    let markers = [];
    let searchBox;
    let editingMarker = null;

    // Translation strings for JavaScript
    const translations = {
        location_details: @json(__('provider.location_details')),
        label: @json(__('provider.label')),
        emirate: @json(__('provider.emirate')),
        latitude: @json(__('provider.latitude')),
        longitude: @json(__('provider.longitude')),
        remove: @json(__('provider.remove')),
        locations_saved_successfully: @json(__('provider.locations_saved_successfully')),
        error_saving_locations: @json(__('provider.error_saving_locations')),
        unknown_error: @json(__('provider.unknown_error')),
        error_saving_locations_try_again: @json(__('provider.error_saving_locations_try_again')),
        confirm_delete_location: @json(__('provider.confirm_delete_location')),
        location_deleted_successfully: @json(__('provider.location_deleted_successfully')),
        error_deleting_location: @json(__('provider.error_deleting_location')),
        error_deleting_location_try_again: @json(__('provider.error_deleting_location_try_again')),
        edit: @json(__('provider.edit')),
        delete: @json(__('provider.delete'))
    };

    // Initialize the map
    function initMap() {
        // Default center (Dubai)
        const center = { lat: 25.2048, lng: 55.2708 };

        // Create the map
        map = new google.maps.Map(document.getElementById('map'), {
            center: center,
            zoom: 10,
            styles: [
                { elementType: "geometry", stylers: [{ color: "#f5f5f5" }] },
                { elementType: "labels.icon", stylers: [{ visibility: "off" }] },
                { elementType: "labels.text.fill", stylers: [{ color: "#6b7280" }] },
                { elementType: "labels.text.stroke", stylers: [{ color: "#ffffff" }] },
                { featureType: "administrative", elementType: "geometry", stylers: [{ color: "#d1d5db" }] },
                { featureType: "administrative.country", elementType: "labels.text.fill", stylers: [{ color: "#4b5563" }] },
                { featureType: "administrative.locality", elementType: "labels.text.fill", stylers: [{ color: "#6b7280" }] },
                { featureType: "poi", elementType: "geometry", stylers: [{ color: "#e5e7eb" }] },
                { featureType: "poi", elementType: "labels.text.fill", stylers: [{ color: "#9ca3af" }] },
                { featureType: "poi.park", elementType: "geometry", stylers: [{ color: "#d1fae5" }] },
                { featureType: "road", elementType: "geometry", stylers: [{ color: "#ffffff" }] },
                { featureType: "road", elementType: "geometry.stroke", stylers: [{ color: "#e5e7eb" }] },
                { featureType: "road", elementType: "labels.text.fill", stylers: [{ color: "#6b7280" }] },
                { featureType: "road.highway", elementType: "geometry", stylers: [{ color: "#f3f4f6" }] },
                { featureType: "road.highway", elementType: "geometry.stroke", stylers: [{ color: "#d1d5db" }] },
                { featureType: "transit", elementType: "geometry", stylers: [{ color: "#e5e7eb" }] },
                { featureType: "transit.station", elementType: "labels.text.fill", stylers: [{ color: "#9ca3af" }] },
                { featureType: "water", elementType: "geometry", stylers: [{ color: "#dbeafe" }] },
                { featureType: "water", elementType: "labels.text.fill", stylers: [{ color: "#6b7280" }] }
            ]
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

            // For each place, get the location and add a marker
            const bounds = new google.maps.LatLngBounds();
            places.forEach(function(place) {
                if (!place.geometry || !place.geometry.location) {
                    console.log("Returned place contains no geometry");
                    return;
                }

                // Add a marker for the selected place
                addMarker(place.geometry.location);

                if (place.geometry.viewport) {
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
            });
            map.fitBounds(bounds);
        });

        // Add click listener to map
        map.addListener('click', function(event) {
            addMarker(event.latLng);
        });

        // Load existing markers
        loadExistingMarkers();
    }

    // Add a marker to the map
    function addMarker(location) {
        const marker = new google.maps.Marker({
            position: location,
            map: map,
            draggable: true,
            animation: google.maps.Animation.DROP
        });

        // Generate a unique ID for the marker and initialize data
        marker.markerId = 'marker_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
        marker.locationLabel = '';
        marker.locationEmirate = document.getElementById('emirate').value || 'Dubai';

        markers.push(marker);

        // Add info window for the marker
        const infoWindow = new google.maps.InfoWindow({
            content: createInfoWindowContent(marker, null)
        });

        // Store the info window with the marker for later reference
        marker.infoWindow = infoWindow;

        marker.addListener('click', function() {
            infoWindow.open(map, marker);

            // Add event listeners after the info window is opened
            setTimeout(() => {
                const removeButton = document.querySelector(`button.remove-marker[data-marker-id="${marker.markerId}"]`);
                const labelInput = document.querySelector(`input.marker-label[data-marker-id="${marker.markerId}"]`);
                const emirateSelect = document.querySelector(`select.marker-emirate[data-marker-id="${marker.markerId}"]`);

                if (removeButton) {
                    removeButton.addEventListener('click', function() {
                        removeMarker(marker);
                    });
                }

                // Add event listeners to update marker data when inputs change
                if (labelInput) {
                    labelInput.addEventListener('input', function() {
                        marker.locationLabel = this.value;
                    });
                }

                if (emirateSelect) {
                    emirateSelect.addEventListener('change', function() {
                        marker.locationEmirate = this.value;
                    });
                }
            }, 100);
        });

        // Update info window content when marker is dragged
        marker.addListener('dragend', function() {
            infoWindow.setContent(createInfoWindowContent(marker, {
                label: marker.locationLabel || '',
                emirate: marker.locationEmirate || 'Dubai'
            }));
        });
    }

    // Remove a marker from the map
    function removeMarker(marker) {
        // Remove the marker from the map
        marker.setMap(null);

        // Close the info window if it's open
        if (marker.infoWindow) {
            marker.infoWindow.close();
        }

        // Remove the marker from the markers array
        markers = markers.filter(m => m !== marker);
    }

    // Create info window content
    function createInfoWindowContent(marker, locationData) {
        const position = marker.getPosition();
        const lat = position.lat();
        const lng = position.lng();

        // Use existing data if provided, otherwise use stored data on marker or defaults
        const label = locationData ? locationData.label : (marker.locationLabel || '');
        const emirate = locationData ? locationData.emirate : (marker.locationEmirate || document.getElementById('emirate').value || 'Dubai');
        const markerId = marker.markerId || 'marker_' + Date.now() + '_' + Math.floor(Math.random() * 1000);

        // If the marker doesn't have an ID yet, assign one
        if (!marker.markerId) {
            marker.markerId = markerId;
        }

        return `
            <div style="padding: 12px; min-width: 220px; font-family: sans-serif;">
                <h3 style="margin-top: 0; font-size: 14px;">${translations.location_details}</h3>
                <div style="margin-bottom: 10px;">
                    <label style="display: block; margin-bottom: 6px; font-size: 12px; color: #6b7280;">${translations.label}:</label>
                    <input type="text" class="marker-label" data-marker-id="${marker.markerId}" value="${label}" style="width: 100%; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 6px;">
                </div>
                <div style="margin-bottom: 10px;">
                    <label style="display: block; margin-bottom: 6px; font-size: 12px; color: #6b7280;">${translations.emirate}:</label>
                    <select class="marker-emirate" data-marker-id="${marker.markerId}" style="width: 100%; padding: 6px 8px; border: 1px solid #d1d5db; border-radius: 6px;">
                        <option value="Dubai" ${emirate === 'Dubai' ? 'selected' : ''}>Dubai</option>
                        <option value="Abu Dhabi" ${emirate === 'Abu Dhabi' ? 'selected' : ''}>Abu Dhabi</option>
                        <option value="Sharjah" ${emirate === 'Sharjah' ? 'selected' : ''}>Sharjah</option>
                        <option value="Ajman" ${emirate === 'Ajman' ? 'selected' : ''}>Ajman</option>
                        <option value="Umm Al Quwain" ${emirate === 'Umm Al Quwain' ? 'selected' : ''}>Umm Al Quwain</option>
                        <option value="Ras Al Khaimah" ${emirate === 'Ras Al Khaimah' ? 'selected' : ''}>Ras Al Khaimah</option>
                        <option value="Fujairah" ${emirate === 'Fujairah' ? 'selected' : ''}>Fujairah</option>
                    </select>
                </div>
                <div style="margin-bottom: 10px; font-size: 12px; color: #6b7280;">
                    <p style="margin: 0;">${translations.latitude}: ${lat.toFixed(6)}</p>
                    <p style="margin: 0;">${translations.longitude}: ${lng.toFixed(6)}</p>
                </div>
                <button class="remove-marker" data-marker-id="${marker.markerId}" style="background-color: #ef4444; color: white; border: none; padding: 6px 10px; border-radius: 6px; cursor: pointer; font-size: 12px;">${translations.remove}</button>
            </div>
        `;
    }

    // Load existing markers from the database
    function loadExistingMarkers() {
        const locationItems = document.querySelectorAll('.location-item');

        locationItems.forEach(function(item) {
            const id = item.dataset.id;
            const lat = parseFloat(item.dataset.lat);
            const lng = parseFloat(item.dataset.lng);
            const label = item.querySelector('.location-label')?.textContent || item.querySelector('div')?.textContent || '';
            const emirate = item.querySelector('.location-emirate')?.textContent || '';

            const position = new google.maps.LatLng(lat, lng);
            const marker = new google.maps.Marker({
                position: position,
                map: map,
                draggable: true,
                animation: google.maps.Animation.DROP
            });

            // Generate a unique ID for the marker and store data
            marker.markerId = 'marker_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
            marker.locationId = id;
            marker.locationLabel = label;
            marker.locationEmirate = emirate;
            markers.push(marker);

            // Add info window for the marker
            const infoWindow = new google.maps.InfoWindow({
                content: createInfoWindowContent(marker, { label, emirate })
            });

            // Store the info window with the marker for later reference
            marker.infoWindow = infoWindow;

            marker.addListener('click', function() {
                infoWindow.open(map, marker);

                // Add event listeners after the info window is opened
                setTimeout(() => {
                    const removeButton = document.querySelector(`button.remove-marker[data-marker-id="${marker.markerId}"]`);
                    const labelInput = document.querySelector(`input.marker-label[data-marker-id="${marker.markerId}"]`);
                    const emirateSelect = document.querySelector(`select.marker-emirate[data-marker-id="${marker.markerId}"]`);

                    if (removeButton) {
                        removeButton.addEventListener('click', function() {
                            removeMarker(marker);
                        });
                    }

                    // Add event listeners to update marker data when inputs change
                    if (labelInput) {
                        labelInput.addEventListener('input', function() {
                            marker.locationLabel = this.value;
                        });
                    }

                    if (emirateSelect) {
                        emirateSelect.addEventListener('change', function() {
                            marker.locationEmirate = this.value;
                        });
                    }
                }, 100);
            });

            // Update info window content when marker is dragged
            marker.addListener('dragend', function() {
                infoWindow.setContent(createInfoWindowContent(marker, {
                    label: marker.locationLabel || '',
                    emirate: marker.locationEmirate || 'Dubai'
                }));
            });
        });
    }

    // Save locations to the database
    document.getElementById('save-locations').addEventListener('click', function() {
        const locationsData = [];

        // Only save markers that haven't been removed
        markers.forEach(function(marker) {
            // Skip markers that have been removed from the map
            if (!marker.getMap()) return;

            const position = marker.getPosition();

            // Get label and emirate from stored marker data
            let label = marker.locationLabel || '';
            let emirate = marker.locationEmirate || 'Dubai';

            // If the info window is currently open, get the current values from the inputs
            const currentLabelInput = document.querySelector(`input.marker-label[data-marker-id="${marker.markerId}"]`);
            const currentEmirateSelect = document.querySelector(`select.marker-emirate[data-marker-id="${marker.markerId}"]`);

            if (currentLabelInput) {
                label = currentLabelInput.value;
                marker.locationLabel = label; // Update stored value
            }

            if (currentEmirateSelect) {
                emirate = currentEmirateSelect.value;
                marker.locationEmirate = emirate; // Update stored value
            }

            locationsData.push({
                id: marker.locationId || null,
                label: label,
                emirate: emirate,
                latitude: position.lat(),
                longitude: position.lng()
            });
        });

        // Send data to the server
        fetch('{{ route("provider.locations.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ locations: locationsData })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(translations.locations_saved_successfully);
                window.location.reload();
            } else {
                alert(translations.error_saving_locations + ': ' + (data.error || translations.unknown_error));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(translations.error_saving_locations_try_again);
        });
    });

    // Edit location
    document.querySelectorAll('.edit-location').forEach(function(button) {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const locationItem = document.querySelector(`.location-item[data-id="${id}"]`);
            const lat = parseFloat(locationItem.dataset.lat);
            const lng = parseFloat(locationItem.dataset.lng);

            // Find the marker for this location
            const marker = markers.find(m => m.locationId === id);

            if (marker) {
                // Center the map on this marker
                map.setCenter(marker.getPosition());
                map.setZoom(15);

                // Open the info window for this marker
                google.maps.event.trigger(marker, 'click');
            }
        });
    });

    // Delete location
    document.querySelectorAll('.delete-location').forEach(function(button) {
        button.addEventListener('click', function() {
            if (confirm(translations.confirm_delete_location)) {
                const id = this.dataset.id;

                fetch(`{{ url('provider/locations') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the marker from the map
                        const marker = markers.find(m => m.locationId === id);
                        if (marker) {
                            marker.setMap(null);
                            markers = markers.filter(m => m.locationId !== id);
                        }

                        // Remove the location item from the list
                        const locationItem = document.querySelector(`.location-item[data-id="${id}"]`);
                        if (locationItem) {
                            locationItem.remove();
                        }

                        alert(translations.location_deleted_successfully);
                    } else {
                        alert(translations.error_deleting_location + ': ' + (data.error || translations.unknown_error));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(translations.error_deleting_location_try_again);
                });
            }
        });
    });
</script>
@endsection
