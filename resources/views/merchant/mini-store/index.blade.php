@extends('layouts.merchant')

@section('title', __('merchant.mini_store_management'))

@section('styles')
<style>
    :root {
        --discord-primary: #5865F2;
        --discord-secondary: #4752C4;
        --discord-success: #57F287;
        --discord-danger: #ED4245;
        --discord-warning: #FEE75C;
        --discord-info: #5DADE2;
        --discord-light: #99AAB5;
        --discord-dark: #2C2F33;
        --discord-darker: #23272A;
        --discord-lightest: #FFFFFF;
        --discord-blue: #7289DA;
    }

    /* RTL Support */
    [dir="rtl"] {
        text-align: right;
    }

    [dir="rtl"] .me-1,
    [dir="rtl"] .me-2 {
        margin-right: 0 !important;
        margin-left: 0.25rem !important;
    }

    [dir="rtl"] .me-2 {
        margin-left: 0.5rem !important;
    }

    [dir="rtl"] .d-flex {
        flex-direction: row-reverse;
    }

    [dir="rtl"] .gap-2 > * {
        margin-left: 0.5rem;
        margin-right: 0;
    }

    [dir="rtl"] .gap-2 > *:first-child {
        margin-left: 0;
    }

    [dir="rtl"] .text-end {
        text-align: left !important;
    }

    [dir="rtl"] .form-label {
        text-align: right;
    }

    [dir="rtl"] .alert {
        text-align: right;
    }

    /* Mobile responsive adjustments for mini-store */
    @media (max-width: 768px) {
        .discord-card-body .row .col-md-6 {
            margin-bottom: 1rem;
        }

        #google-map {
            height: 300px !important;
        }

        .d-flex.gap-2 {
            flex-direction: column;
            gap: 0.5rem !important;
        }

        .d-flex.gap-2 .discord-btn {
            width: 100%;
            text-align: center;
        }

        .discord-card-header .d-flex {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 1rem;
        }

        .discord-card-header .discord-btn {
            width: 100%;
        }
    }

    /* Ensure map container is responsive */
    #google-map {
        width: 100%;
        min-height: 400px;
    }

    /* Loading state for map */
    .map-loading {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 400px;
        background-color: var(--discord-dark);
        border-radius: 8px;
        color: var(--discord-light);
    }
</style>
@endsection

@section('content')
<div dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<!-- Success/Error Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Store Location Information Card -->
<div class="discord-card">
    <div class="discord-card-header d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-map-marker-alt me-2" style="color: var(--discord-primary);"></i>
            {{ __('merchant.store_location_management') }}
        </div>
        @if($merchant)
            <button type="button" class="discord-btn" onclick="toggleEditMode()">
                <i class="fas fa-edit me-1"></i> {{ __('merchant.edit_location') }}
            </button>
        @else
            <button type="button" class="discord-btn" onclick="showCreateForm()">
                <i class="fas fa-plus me-1"></i> {{ __('merchant.set_location') }}
            </button>
        @endif
    </div>
    <div class="discord-card-body">
        @if($merchant)
            <!-- Display Mode -->
            <div id="display-mode">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ __('merchant.store_location_address') }}</label>
                            <p class="form-control-plaintext">{{ $merchant->store_location_address ?? __('merchant.not_set') }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('merchant.coordinates') }}</label>
                            <p class="form-control-plaintext">
                                @if($merchant->store_location_lat && $merchant->store_location_lng)
                                    {{ number_format($merchant->store_location_lat, 6) }}, {{ number_format($merchant->store_location_lng, 6) }}
                                @else
                                    {{ __('merchant.not_set') }}
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ __('merchant.location_status') }}</label>
                            <p class="form-control-plaintext">
                                @if($merchant->store_location_address)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>{{ __('merchant.location_set') }}
                                    </span>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>{{ __('merchant.location_not_set') }}
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div class="mb-3">
                            <div class="alert alert-info" style="background-color: var(--discord-blue); border: none; color: white;">
                                <i class="fas fa-info-circle me-2"></i>
                                <small>{{ __('merchant.for_business_details_visit') }} <a href="{{ route('merchant.settings.global') }}" style="color: white; text-decoration: underline;">{{ __('merchant.global_settings') }}</a></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Mode Form -->
            <div id="edit-mode" style="display: none;">
                <form id="mini-store-form" action="{{ route('merchant.mini-store.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="store_location_address" class="form-label">{{ __('merchant.store_location_address') }}</label>
                                <input type="text" class="form-control @error('store_location_address') is-invalid @enderror"
                                       id="store_location_address" name="store_location_address"
                                       value="{{ old('store_location_address', $merchant->store_location_address) }}"
                                       placeholder="{{ __('merchant.enter_store_address') }}">
                                @error('store_location_address')
                                    <div class="invalid-feedback" data-server-error>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Hidden fields for coordinates -->
                    <input type="hidden" id="store_location_lat" name="store_location_lat"
                           value="{{ old('store_location_lat', $merchant->store_location_lat) }}">
                    <input type="hidden" id="store_location_lng" name="store_location_lng"
                           value="{{ old('store_location_lng', $merchant->store_location_lng) }}">

                    <div class="alert alert-info mb-3" style="background-color: var(--discord-blue); border: none; color: white;">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>{{ __('merchant.note') }}:</strong> {{ __('merchant.location_note_text') }}
                        <a href="{{ route('merchant.settings.global') }}" style="color: white; text-decoration: underline;">{{ __('merchant.global_settings') }}</a>.
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="discord-btn">
                            <i class="fas fa-save me-1"></i> {{ __('merchant.save_location') }}
                        </button>
                        <button type="button" class="discord-btn discord-btn-secondary" onclick="cancelEdit()">
                            <i class="fas fa-times me-1"></i> {{ __('merchant.cancel') }}
                        </button>
                    </div>
                </form>
            </div>
        @else
            <!-- No Location Set Message -->
            <div id="no-store-message" class="text-center py-5">
                <i class="fas fa-map-marker-alt fa-3x mb-3" style="color: var(--discord-light);"></i>
                <h4 style="color: var(--discord-lightest);">{{ __('merchant.store_location_not_set') }}</h4>
                <p style="color: var(--discord-light);">{{ __('merchant.set_store_location_help') }}</p>
                <div class="alert alert-info mt-3" style="background-color: var(--discord-blue); border: none; color: white;">
                    <i class="fas fa-info-circle me-2"></i>
                    <small>{{ __('merchant.for_business_details_visit') }} <a href="{{ route('merchant.settings.global') }}" style="color: white; text-decoration: underline;">{{ __('merchant.global_settings') }}</a></small>
                </div>
            </div>

            <!-- Create Form (Initially Hidden) -->
            <div id="create-form" style="display: none;">
                <form action="{{ route('merchant.mini-store.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="create_store_location_address" class="form-label">{{ __('merchant.store_location_address') }}</label>
                                <input type="text" class="form-control @error('store_location_address') is-invalid @enderror"
                                       id="create_store_location_address" name="store_location_address"
                                       value="{{ old('store_location_address') }}"
                                       placeholder="{{ __('merchant.enter_store_address') }}">
                                @error('store_location_address')
                                    <div class="invalid-feedback" data-server-error>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Hidden fields for coordinates -->
                    <input type="hidden" id="create_store_location_lat" name="store_location_lat"
                           value="{{ old('store_location_lat') }}">
                    <input type="hidden" id="create_store_location_lng" name="store_location_lng"
                           value="{{ old('store_location_lng') }}">

                    <div class="alert alert-info mb-3" style="background-color: var(--discord-blue); border: none; color: white;">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> This will only set your store's physical location.
                        To add business details like name, description, logo, etc., please visit
                        <a href="{{ route('merchant.settings.global') }}" style="color: white; text-decoration: underline;">Global Settings</a> after setting your location.
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="discord-btn">
                            <i class="fas fa-save me-1"></i> {{ __('merchant.set_location') }}
                        </button>
                        <button type="button" class="discord-btn discord-btn-secondary" onclick="cancelCreate()">
                            <i class="fas fa-times me-1"></i> {{ __('merchant.cancel') }}
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>

<!-- Google Maps Card -->
<div class="discord-card mt-4">
    <div class="discord-card-header">
        <i class="fas fa-map-marker-alt me-2" style="color: var(--discord-primary);"></i>
        {{ __('merchant.store_location') }}
    </div>
    <div class="discord-card-body">
        <div class="mb-3">
            <input type="text" id="map-search" class="form-control"
                   placeholder="{{ __('merchant.search_store_location') }}">
        </div>
        <div id="google-map" style="height: 400px; border-radius: 8px; overflow: hidden;">
            <div class="map-loading">
                <div class="text-center">
                    <div class="spinner-border text-primary mb-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div>{{ __('merchant.loading_google_maps') }}</div>
                </div>
            </div>
        </div>
        <small class="form-text text-muted mt-2">
            <i class="fas fa-info-circle me-1"></i>
            {{ __('merchant.map_instructions') }}
        </small>
    </div>
</div>
</div>
@endsection

@section('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('googlemaps.api_key') }}&libraries=places&callback=initMap" async defer onerror="handleGoogleMapsError()"></script>
<script>
    // Global variables
    let map;
    let marker;
    let autocomplete;
    let geocoder;

    // Initialize Google Maps
    function initMap() {
        let center;

        try {
            // Remove loading state
            const mapContainer = document.getElementById('google-map');
            mapContainer.innerHTML = '';

            // Default center (Dubai)
            const defaultCenter = {
                lat: {{ config('googlemaps.default_center.lat') }},
                lng: {{ config('googlemaps.default_center.lng') }}
            };

            // Get existing coordinates if available
            const existingLatField = document.getElementById('store_location_lat');
            const existingLngField = document.getElementById('store_location_lng');
            const existingLat = existingLatField ? existingLatField.value : null;
            const existingLng = existingLngField ? existingLngField.value : null;

            center = (existingLat && existingLng) ?
                { lat: parseFloat(existingLat), lng: parseFloat(existingLng) } :
                defaultCenter;

            // Create map
            map = new google.maps.Map(mapContainer, {
                zoom: {{ config('googlemaps.default_zoom') }},
                center: center,
                mapTypeControl: false,
                streetViewControl: true,
                fullscreenControl: true,
                zoomControl: true,
                styles: [
                    {
                        featureType: 'poi',
                        elementType: 'labels',
                        stylers: [{ visibility: 'on' }]
                    }
                ]
            });

            // Initialize geocoder
            geocoder = new google.maps.Geocoder();

            // Create marker
            marker = new google.maps.Marker({
                position: center,
                map: map,
                draggable: true,
                title: 'Store Location'
            });
        } catch (error) {
            console.error('Error initializing Google Maps:', error);
            document.getElementById('google-map').innerHTML =
                '<div class="alert alert-danger">{{ __('merchant.google_maps_error_loading') }}</div>';
            return;
        }

        // Set up autocomplete
        const searchInput = document.getElementById('map-search');
        autocomplete = new google.maps.places.Autocomplete(searchInput, {
            componentRestrictions: { country: 'ae' },
            types: ['establishment', 'geocode']
        });

        // Autocomplete place changed
        autocomplete.addListener('place_changed', function() {
            const place = autocomplete.getPlace();
            if (!place.geometry) return;

            const location = place.geometry.location;
            updateLocation(location.lat(), location.lng(), place.formatted_address);

            map.setCenter(location);
            map.setZoom(15);
        });

        // Map click event
        map.addListener('click', function(event) {
            updateLocation(event.latLng.lat(), event.latLng.lng());
        });

        // Marker drag event
        marker.addListener('dragend', function() {
            const position = marker.getPosition();
            updateLocation(position.lat(), position.lng());
        });

        // If we have existing coordinates but no address, reverse geocode
        const addressField = document.getElementById('store_location_address');
        if (existingLat && existingLng && addressField && !addressField.value) {
            reverseGeocode(parseFloat(existingLat), parseFloat(existingLng));
        }
    }

    // Update location coordinates and address
    function updateLocation(lat, lng, address = null) {
        // Update marker position
        marker.setPosition({ lat: lat, lng: lng });

        // Update hidden fields for both edit and create modes
        const latField = document.getElementById('store_location_lat') || document.getElementById('create_store_location_lat');
        const lngField = document.getElementById('store_location_lng') || document.getElementById('create_store_location_lng');
        const addressField = document.getElementById('store_location_address') || document.getElementById('create_store_location_address');

        if (latField) latField.value = lat;
        if (lngField) lngField.value = lng;

        // Update address if provided, otherwise reverse geocode
        if (address && addressField) {
            addressField.value = address;
        } else {
            reverseGeocode(lat, lng);
        }
    }

    // Reverse geocode coordinates to get address
    function reverseGeocode(lat, lng) {
        geocoder.geocode({ location: { lat: lat, lng: lng } }, function(results, status) {
            if (status === 'OK' && results[0]) {
                const addressField = document.getElementById('store_location_address') || document.getElementById('create_store_location_address');
                if (addressField) {
                    addressField.value = results[0].formatted_address;
                }
            }
        });
    }

    // Toggle edit mode
    function toggleEditMode() {
        document.getElementById('display-mode').style.display = 'none';
        document.getElementById('edit-mode').style.display = 'block';
    }

    // Cancel edit
    function cancelEdit() {
        document.getElementById('edit-mode').style.display = 'none';
        document.getElementById('display-mode').style.display = 'block';
    }

    // Show create form
    function showCreateForm() {
        document.getElementById('no-store-message').style.display = 'none';
        document.getElementById('create-form').style.display = 'block';
    }

    // Cancel create
    function cancelCreate() {
        document.getElementById('create-form').style.display = 'none';
        document.getElementById('no-store-message').style.display = 'block';
    }

    // Handle Google Maps loading errors
    window.gm_authFailure = function() {
        console.error('Google Maps authentication failed');
        const mapElement = document.getElementById('google-map');
        if (mapElement) {
            mapElement.innerHTML =
                '<div class="alert alert-danger">' +
                '<i class="fas fa-exclamation-triangle me-2"></i>' +
                '{{ __('merchant.google_maps_auth_failed') }}' +
                '</div>';
        }
    };

    // Handle script loading errors
    function handleGoogleMapsError() {
        console.error('Failed to load Google Maps API script');
        const mapElement = document.getElementById('google-map');
        if (mapElement) {
            mapElement.innerHTML =
                '<div class="alert alert-warning">' +
                '<i class="fas fa-exclamation-triangle me-2"></i>' +
                '{{ __('merchant.google_maps_load_failed') }}' +
                '</div>';
        }
    }

    // Form validation
    document.addEventListener('DOMContentLoaded', function() {
        // Add form validation for both edit and create forms
        const forms = document.querySelectorAll('form');
        forms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                const businessName = form.querySelector('input[name="business_name"]');
                if (businessName && !businessName.value.trim()) {
                    e.preventDefault();
                    businessName.classList.add('is-invalid');
                    if (!businessName.nextElementSibling || !businessName.nextElementSibling.classList.contains('invalid-feedback')) {
                        const feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        feedback.textContent = '{{ __('merchant.business_name_required') }}';
                        businessName.parentNode.appendChild(feedback);
                    }
                    businessName.focus();
                    return false;
                }
            });
        });

        // Remove validation errors on input
        document.querySelectorAll('input, select, textarea').forEach(function(field) {
            field.addEventListener('input', function() {
                this.classList.remove('is-invalid');
                const feedback = this.parentNode.querySelector('.invalid-feedback');
                // Only remove client-side validation feedback, keep server-side validation messages
                if (feedback && !feedback.hasAttribute('data-server-error')) {
                    feedback.remove();
                }
            });
        });

        // Show create form if there are validation errors and no merchant exists
        @if(!$merchant && $errors->any())
            showCreateForm();
        @endif
    });
</script>
@endsection
