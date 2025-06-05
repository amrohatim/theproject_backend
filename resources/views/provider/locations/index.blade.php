@extends('layouts.provider')

@section('header', 'Locations')

@section('styles')
<style>
    #map {
        height: 500px;
        width: 100%;
        border-radius: 8px;
    }
    .location-list {
        margin-top: 20px;
    }
    .location-item {
        background-color: var(--discord-dark);
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .location-info {
        flex: 1;
    }
    .location-actions {
        display: flex;
        gap: 8px;
    }
    .location-label {
        font-weight: bold;
        color: var(--discord-lightest);
    }
    .location-emirate {
        color: var(--discord-light);
        font-size: 14px;
    }
    .location-coords {
        color: var(--discord-light);
        font-size: 12px;
        margin-top: 4px;
    }
    .emirate-select {
        margin-bottom: 15px;
    }
    .map-controls {
        margin-bottom: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .search-box {
        flex: 1;
        max-width: 400px;
    }
    .search-input {
        width: 100%;
        padding: 8px 12px;
        border-radius: 4px;
        border: 1px solid var(--discord-darker);
        background-color: var(--discord-dark);
        color: var(--discord-lightest);
    }
    .action-btn {
        background-color: var(--discord-primary);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .action-btn:hover {
        background-color: var(--discord-primary-hover);
    }
    .action-btn.delete {
        background-color: var(--discord-red);
    }
    .action-btn.delete:hover {
        background-color: var(--discord-red-hover);
    }
    .action-btn.edit {
        background-color: var(--discord-yellow);
    }
    .action-btn.edit:hover {
        background-color: var(--discord-yellow-hover);
    }
</style>
@endsection

@section('content')
<div class="discord-card">
    <div class="discord-card-header">
        <h2><i class="fas fa-map-marker-alt me-2"></i> Stock Distribution Points</h2>
        <p>Add and manage your stock distribution locations</p>
    </div>
    <div class="discord-card-body">
        <div class="map-controls">
            <div class="emirate-select">
                <label for="emirate" class="form-label">Emirate:</label>
                <select id="emirate" class="form-select">
                    <option value="">Select Emirate</option>
                    <option value="Dubai">Dubai</option>
                    <option value="Abu Dhabi">Abu Dhabi</option>
                    <option value="Sharjah">Sharjah</option>
                    <option value="Ajman">Ajman</option>
                    <option value="Umm Al Quwain">Umm Al Quwain</option>
                    <option value="Ras Al Khaimah">Ras Al Khaimah</option>
                    <option value="Fujairah">Fujairah</option>
                </select>
            </div>
            <div class="search-box">
                <input id="pac-input" class="search-input" type="text" placeholder="Search for a location">
            </div>
        </div>

        <div id="map"></div>

        <div class="d-flex justify-content-end mt-3">
            <button id="save-locations" class="action-btn">
                <i class="fas fa-save me-2"></i> Save Locations
            </button>
        </div>

        <div class="location-list">
            <h3 class="mb-3">Saved Locations</h3>
            <div id="locations-container">
                @if(count($locations) > 0)
                    @foreach($locations as $location)
                    <div class="location-item" data-id="{{ $location->id }}" data-lat="{{ $location->latitude }}" data-lng="{{ $location->longitude }}">
                        <div class="location-info">
                            <div class="location-label">{{ $location->label ?? 'Location ' . $loop->iteration }}</div>
                            <div class="location-emirate">{{ $location->emirate }}</div>
                            <div class="location-coords">{{ $location->latitude }}, {{ $location->longitude }}</div>
                        </div>
                        <div class="location-actions">
                            <button class="action-btn edit edit-location" data-id="{{ $location->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="action-btn delete delete-location" data-id="{{ $location->id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                @else
                    <p>No locations saved yet. Click on the map to add locations.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initMap" async defer></script>
<script>
    // Global variables
    let map;
    let markers = [];
    let searchBox;
    let editingMarker = null;

    // Initialize the map
    function initMap() {
        // Default center (Dubai)
        const center = { lat: 25.2048, lng: 55.2708 };

        // Create the map
        map = new google.maps.Map(document.getElementById('map'), {
            center: center,
            zoom: 10,
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

        // Generate a unique ID for the marker
        marker.markerId = 'marker_' + Date.now() + '_' + Math.floor(Math.random() * 1000);

        markers.push(marker);

        // Add info window for the marker
        const infoWindow = new google.maps.InfoWindow({
            content: createInfoWindowContent(marker, null)
        });

        // Store the info window with the marker for later reference
        marker.infoWindow = infoWindow;

        marker.addListener('click', function() {
            infoWindow.open(map, marker);

            // Add event listener to the remove button after the info window is opened
            setTimeout(() => {
                const removeButton = document.querySelector(`button.remove-marker[data-marker-id="${marker.markerId}"]`);
                if (removeButton) {
                    removeButton.addEventListener('click', function() {
                        removeMarker(marker);
                    });
                }
            }, 100);
        });

        // Update info window content when marker is dragged
        marker.addListener('dragend', function() {
            infoWindow.setContent(createInfoWindowContent(marker, null));
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
        const label = locationData ? locationData.label : '';
        const emirate = locationData ? locationData.emirate : document.getElementById('emirate').value;
        const markerId = marker.markerId || 'marker_' + Date.now() + '_' + Math.floor(Math.random() * 1000);

        // If the marker doesn't have an ID yet, assign one
        if (!marker.markerId) {
            marker.markerId = markerId;
        }

        return `
            <div style="padding: 10px; min-width: 200px;">
                <h3 style="margin-top: 0;">Location Details</h3>
                <div style="margin-bottom: 10px;">
                    <label style="display: block; margin-bottom: 5px;">Label:</label>
                    <input type="text" class="marker-label" value="${label}" style="width: 100%; padding: 5px;">
                </div>
                <div style="margin-bottom: 10px;">
                    <label style="display: block; margin-bottom: 5px;">Emirate:</label>
                    <select class="marker-emirate" style="width: 100%; padding: 5px;">
                        <option value="Dubai" ${emirate === 'Dubai' ? 'selected' : ''}>Dubai</option>
                        <option value="Abu Dhabi" ${emirate === 'Abu Dhabi' ? 'selected' : ''}>Abu Dhabi</option>
                        <option value="Sharjah" ${emirate === 'Sharjah' ? 'selected' : ''}>Sharjah</option>
                        <option value="Ajman" ${emirate === 'Ajman' ? 'selected' : ''}>Ajman</option>
                        <option value="Umm Al Quwain" ${emirate === 'Umm Al Quwain' ? 'selected' : ''}>Umm Al Quwain</option>
                        <option value="Ras Al Khaimah" ${emirate === 'Ras Al Khaimah' ? 'selected' : ''}>Ras Al Khaimah</option>
                        <option value="Fujairah" ${emirate === 'Fujairah' ? 'selected' : ''}>Fujairah</option>
                    </select>
                </div>
                <div style="margin-bottom: 10px;">
                    <p>Latitude: ${lat.toFixed(6)}</p>
                    <p>Longitude: ${lng.toFixed(6)}</p>
                </div>
                <button class="remove-marker" data-marker-id="${marker.markerId}" style="background-color: #f04747; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">Remove</button>
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
            const label = item.querySelector('.location-label').textContent;
            const emirate = item.querySelector('.location-emirate').textContent;

            const position = new google.maps.LatLng(lat, lng);
            const marker = new google.maps.Marker({
                position: position,
                map: map,
                draggable: true,
                animation: google.maps.Animation.DROP
            });

            // Generate a unique ID for the marker
            marker.markerId = 'marker_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
            marker.locationId = id;
            markers.push(marker);

            // Add info window for the marker
            const infoWindow = new google.maps.InfoWindow({
                content: createInfoWindowContent(marker, { label, emirate })
            });

            // Store the info window with the marker for later reference
            marker.infoWindow = infoWindow;

            marker.addListener('click', function() {
                infoWindow.open(map, marker);

                // Add event listener to the remove button after the info window is opened
                setTimeout(() => {
                    const removeButton = document.querySelector(`button.remove-marker[data-marker-id="${marker.markerId}"]`);
                    if (removeButton) {
                        removeButton.addEventListener('click', function() {
                            removeMarker(marker);
                        });
                    }
                }, 100);
            });

            // Update info window content when marker is dragged
            marker.addListener('dragend', function() {
                infoWindow.setContent(createInfoWindowContent(marker, { label, emirate }));
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

            // Try to get label and emirate from the DOM if the info window was opened
            let label = '';
            let emirate = document.getElementById('emirate').value || 'Dubai';

            // Create a temporary div to parse the info window content
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = createInfoWindowContent(marker, null);

            // Get the label and emirate values
            const labelInput = tempDiv.querySelector('.marker-label');
            const emirateSelect = tempDiv.querySelector('.marker-emirate');

            if (labelInput) label = labelInput.value;
            if (emirateSelect) emirate = emirateSelect.value;

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
                alert('Locations saved successfully!');
                window.location.reload();
            } else {
                alert('Error saving locations: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving locations. Please try again.');
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
            if (confirm('Are you sure you want to delete this location?')) {
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

                        alert('Location deleted successfully!');
                    } else {
                        alert('Error deleting location: ' + (data.error || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting location. Please try again.');
                });
            }
        });
    });
</script>
@endsection
