<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Merchant Registration - glowlabs</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .registration-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 800px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 600px;
        }

        .logo-section {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            color: white;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .logo {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
        }

        .logo i {
            font-size: 2.5rem;
        }

        .welcome-text {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            line-height: 1.2;
        }

        .subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            line-height: 1.5;
        }

        .form-section {
            padding: 60px 40px;
            overflow-y: auto;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 0.95rem;
        }

        .form-input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e1e5e9;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-input:focus {
            outline: none;
            border-color: #ff6b6b;
            background: white;
            box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.1);
        }

        .form-input::placeholder {
            color: #adb5bd;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #ff6b6b;
        }

        .file-upload {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-upload input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-upload-label {
            display: block;
            padding: 15px 20px;
            border: 2px dashed #e1e5e9;
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .file-upload-label:hover {
            border-color: #ff6b6b;
            background: white;
        }

        .file-upload-label i {
            font-size: 1.5rem;
            color: #ff6b6b;
            margin-bottom: 10px;
            display: block;
        }

        .delivery-fees {
            display: none;
            margin-top: 15px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
        }

        .delivery-fees.show {
            display: block;
        }

        .emirate-fee {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .emirate-fee label {
            font-weight: 500;
            color: #333;
        }

        .emirate-fee input {
            width: 100px;
            padding: 8px 12px;
            border: 1px solid #e1e5e9;
            border-radius: 8px;
            text-align: center;
        }

        .form-button {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
            position: relative;
            overflow: hidden;
        }

        .form-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 107, 107, 0.3);
        }

        .form-button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 5px;
            display: none;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }

        .location-input {
            position: relative;
        }

        .location-search-container {
            position: relative;
            margin-bottom: 10px;
        }

        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            pointer-events: none;
        }

        #map-container {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            background: #f9fafb;
            margin-top: 10px;
        }

        #google-map {
            border: 1px solid #d1d5db;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .map-instructions {
            margin-top: 10px;
            padding: 8px 12px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            font-size: 0.875rem;
            color: #1e40af;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .map-instructions i {
            color: #3b82f6;
        }

        .location-selected {
            background: #f0fdf4 !important;
            border-color: #22c55e !important;
        }

        .selected-location {
            position: relative;
        }

        .clear-location-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 6px 10px;
            cursor: pointer;
            font-size: 0.75rem;
            transition: background-color 0.2s;
        }

        .clear-location-btn:hover {
            background: #dc2626;
        }

        @media (max-width: 768px) {
            .registration-container {
                grid-template-columns: 1fr;
                max-width: 500px;
            }

            .logo-section {
                padding: 40px 30px;
            }

            .form-section {
                padding: 40px 30px;
            }

            .welcome-text {
                font-size: 2rem;
            }

            #google-map {
                height: 250px !important;
            }

            .map-instructions {
                font-size: 0.8rem;
                padding: 6px 10px;
            }

            .clear-location-btn {
                font-size: 0.7rem;
                padding: 4px 8px;
            }
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <!-- Logo Section -->
        <div class="logo-section">
            <div class="logo">
                <i class="fas fa-store"></i>
            </div>
            <h1 class="welcome-text">Join as Merchant</h1>
            <p class="subtitle">Start your individual business on glowlabs marketplace</p>
        </div>

        <!-- Registration Form -->
        <div class="form-section">
            @if(session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif

            <form id="merchantRegistrationForm" method="POST" action="{{ route('register.merchant.submit') }}" enctype="multipart/form-data">
                @csrf

                <!-- Business Name -->
                <div class="form-group">
                    <label for="name" class="form-label">Business Name *</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-input"
                        placeholder="Enter your business name"
                        value="{{ old('name') }}"
                        required
                    >
                    <div class="error-message" id="name-error"></div>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email" class="form-label">Email Address *</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input"
                        placeholder="Enter your business email address"
                        value="{{ old('email') }}"
                        required
                    >
                    <div class="error-message" id="email-error"></div>
                </div>

                <!-- Phone -->
                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number *</label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        class="form-input"
                        placeholder="+971 50 123 4567"
                        value="{{ old('phone') }}"
                        required
                    >
                    <div class="error-message" id="phone-error"></div>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label">Password *</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input"
                        placeholder="Create a strong password"
                        required
                    >
                    <div class="error-message" id="password-error"></div>
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm Password *</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="form-input"
                        placeholder="Confirm your password"
                        required
                    >
                    <div class="error-message" id="password_confirmation-error"></div>
                </div>

                <!-- Logo Upload -->
                <div class="form-group">
                    <label for="logo" class="form-label">Business Logo (Optional)</label>
                    <div class="file-upload">
                        <input type="file" id="logo" name="logo" accept="image/*">
                        <label for="logo" class="file-upload-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Click to upload logo</span>
                        </label>
                    </div>
                    <div class="error-message" id="logo-error"></div>
                </div>

                <!-- UAE ID Front -->
                <div class="form-group">
                    <label for="uae_id_front" class="form-label">UAE ID Front Side *</label>
                    <div class="file-upload">
                        <input type="file" id="uae_id_front" name="uae_id_front" accept="image/*" required>
                        <label for="uae_id_front" class="file-upload-label">
                            <i class="fas fa-id-card"></i>
                            <span>Upload front side of UAE ID</span>
                        </label>
                    </div>
                    <div class="error-message" id="uae_id_front-error"></div>
                </div>

                <!-- UAE ID Back -->
                <div class="form-group">
                    <label for="uae_id_back" class="form-label">UAE ID Back Side *</label>
                    <div class="file-upload">
                        <input type="file" id="uae_id_back" name="uae_id_back" accept="image/*" required>
                        <label for="uae_id_back" class="file-upload-label">
                            <i class="fas fa-id-card"></i>
                            <span>Upload back side of UAE ID</span>
                        </label>
                    </div>
                    <div class="error-message" id="uae_id_back-error"></div>
                </div>

                <!-- Store Location -->
                <div class="form-group">
                    <label for="store_location_address" class="form-label">Store Location (Optional)</label>
                    <div class="location-search-container">
                        <input
                            type="text"
                            id="location-search"
                            class="form-input"
                            placeholder="Search for your store location..."
                            autocomplete="off"
                        >
                        <div class="search-icon">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                    <div id="map-container" style="display: none;">
                        <div id="google-map" style="height: 300px; width: 100%; border-radius: 8px; margin-top: 10px;"></div>
                        <div class="map-instructions">
                            <i class="fas fa-info-circle"></i>
                            Click on the map to set your exact store location, or drag the marker to adjust.
                        </div>
                    </div>
                    <div class="selected-location" style="margin-top: 10px; display: none;" id="selected-location-container">
                        <input
                            type="text"
                            id="store_location_address"
                            name="store_location_address"
                            class="form-input location-selected"
                            placeholder="Selected address will appear here"
                            value="{{ old('store_location_address') }}"
                            readonly
                        >
                        <button type="button" class="clear-location-btn" onclick="clearLocation()">
                            <i class="fas fa-times"></i> Clear Location
                        </button>
                    </div>
                    <input type="hidden" id="store_location_lat" name="store_location_lat" value="{{ old('store_location_lat') }}">
                    <input type="hidden" id="store_location_lng" name="store_location_lng" value="{{ old('store_location_lng') }}">
                    <div class="error-message" id="store_location-error"></div>
                </div>

                <!-- Delivery Capability -->
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="delivery_capability" name="delivery_capability" value="1" {{ old('delivery_capability') ? 'checked' : '' }}>
                        <label for="delivery_capability" class="form-label">I can deliver to customers</label>
                    </div>
                </div>

                <!-- Delivery Fees -->
                <div class="delivery-fees" id="delivery-fees">
                    <h4 style="margin-bottom: 15px; color: #333;">Delivery Fees by Emirate (AED)</h4>
                    <div class="emirate-fee">
                        <label>Dubai:</label>
                        <input type="number" name="delivery_fees[dubai]" placeholder="0" min="0" step="0.01" value="{{ old('delivery_fees.dubai') }}">
                    </div>
                    <div class="emirate-fee">
                        <label>Abu Dhabi:</label>
                        <input type="number" name="delivery_fees[abu_dhabi]" placeholder="0" min="0" step="0.01" value="{{ old('delivery_fees.abu_dhabi') }}">
                    </div>
                    <div class="emirate-fee">
                        <label>Sharjah:</label>
                        <input type="number" name="delivery_fees[sharjah]" placeholder="0" min="0" step="0.01" value="{{ old('delivery_fees.sharjah') }}">
                    </div>
                    <div class="emirate-fee">
                        <label>Ajman:</label>
                        <input type="number" name="delivery_fees[ajman]" placeholder="0" min="0" step="0.01" value="{{ old('delivery_fees.ajman') }}">
                    </div>
                    <div class="emirate-fee">
                        <label>Ras Al Khaimah:</label>
                        <input type="number" name="delivery_fees[ras_al_khaimah]" placeholder="0" min="0" step="0.01" value="{{ old('delivery_fees.ras_al_khaimah') }}">
                    </div>
                    <div class="emirate-fee">
                        <label>Fujairah:</label>
                        <input type="number" name="delivery_fees[fujairah]" placeholder="0" min="0" step="0.01" value="{{ old('delivery_fees.fujairah') }}">
                    </div>
                    <div class="emirate-fee">
                        <label>Umm Al Quwain:</label>
                        <input type="number" name="delivery_fees[umm_al_quwain]" placeholder="0" min="0" step="0.01" value="{{ old('delivery_fees.umm_al_quwain') }}">
                    </div>
                </div>

                <button type="submit" class="form-button" id="submitBtn">
                    <div class="loading-spinner"></div>
                    <span class="button-text">Continue to Verification</span>
                </button>
            </form>
        </div>
    </div>

    <script>
        // Form validation and submission
        document.getElementById('merchantRegistrationForm').addEventListener('submit', function(e) {
            const form = this;
            const submitBtn = document.getElementById('submitBtn');
            const btnText = submitBtn.querySelector('.button-text');
            const spinner = submitBtn.querySelector('.loading-spinner');

            // Check form validity before preventing default
            if (!form.checkValidity()) {
                // Let browser handle validation
                return;
            }

            // Clear previous errors
            document.querySelectorAll('.error-message').forEach(el => {
                el.style.display = 'none';
                el.textContent = '';
            });

            // Show loading state
            submitBtn.disabled = true;
            if (spinner) spinner.style.display = 'inline-block';
            if (btnText) btnText.textContent = 'Creating Account...';

            // Allow natural form submission - don't prevent default for valid forms
        });

        // File upload preview
        function setupFileUpload(inputId, defaultIcon, defaultText) {
            document.getElementById(inputId).addEventListener('change', function(e) {
                const file = e.target.files[0];
                const label = e.target.nextElementSibling;

                if (file) {
                    label.innerHTML = `<i class="fas fa-check"></i><span>${file.name}</span>`;
                } else {
                    label.innerHTML = `<i class="${defaultIcon}"></i><span>${defaultText}</span>`;
                }
            });
        }

        setupFileUpload('logo', 'fas fa-cloud-upload-alt', 'Click to upload logo');
        setupFileUpload('uae_id_front', 'fas fa-id-card', 'Upload front side of UAE ID');
        setupFileUpload('uae_id_back', 'fas fa-id-card', 'Upload back side of UAE ID');

        // Delivery capability toggle
        document.getElementById('delivery_capability').addEventListener('change', function(e) {
            const deliveryFees = document.getElementById('delivery-fees');
            if (e.target.checked) {
                deliveryFees.classList.add('show');
            } else {
                deliveryFees.classList.remove('show');
            }
        });

        // Initialize delivery fees visibility
        if (document.getElementById('delivery_capability').checked) {
            document.getElementById('delivery-fees').classList.add('show');
        }

        // Google Maps Integration
        let map;
        let marker;
        let autocomplete;
        let geocoder;

        function initGoogleMaps() {
            // Initialize map centered on Dubai
            const dubaiCenter = { lat: 25.2048, lng: 55.2708 };

            map = new google.maps.Map(document.getElementById('google-map'), {
                zoom: 12,
                center: dubaiCenter,
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

            // Initialize autocomplete
            const searchInput = document.getElementById('location-search');
            autocomplete = new google.maps.places.Autocomplete(searchInput, {
                componentRestrictions: { country: 'ae' },
                fields: ['place_id', 'geometry', 'name', 'formatted_address'],
                types: ['establishment', 'geocode']
            });

            // Handle autocomplete selection
            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();
                if (!place.geometry) {
                    return;
                }

                // Center map on selected place
                map.setCenter(place.geometry.location);
                map.setZoom(16);

                // Add or update marker
                addMarker(place.geometry.location, place.formatted_address);
            });

            // Handle map clicks
            map.addListener('click', function(event) {
                addMarker(event.latLng);
                reverseGeocode(event.latLng);
            });

            // Show map container when search input is focused
            searchInput.addEventListener('focus', function() {
                showMapContainer();
            });

            // Initialize with existing location data if available
            initializeExistingLocation();
        }

        function initializeExistingLocation() {
            const existingLat = document.getElementById('store_location_lat').value;
            const existingLng = document.getElementById('store_location_lng').value;
            const existingAddress = document.getElementById('store_location_address').value;

            if (existingLat && existingLng && existingAddress) {
                const location = new google.maps.LatLng(parseFloat(existingLat), parseFloat(existingLng));

                // Show map and center on existing location
                showMapContainer();
                map.setCenter(location);
                map.setZoom(16);

                // Add marker
                addMarker(location, existingAddress);

                // Show selected location container
                document.getElementById('selected-location-container').style.display = 'block';
            }
        }

        function addMarker(location, address = null) {
            // Remove existing marker
            if (marker) {
                marker.setMap(null);
            }

            // Create new marker
            marker = new google.maps.Marker({
                position: location,
                map: map,
                draggable: true,
                animation: google.maps.Animation.DROP,
                title: 'Store Location'
            });

            // Handle marker drag
            marker.addListener('dragend', function() {
                reverseGeocode(marker.getPosition());
            });

            // Update form fields
            updateLocationFields(location, address);
        }

        function reverseGeocode(location) {
            geocoder.geocode({ location: location }, function(results, status) {
                if (status === 'OK' && results[0]) {
                    updateLocationFields(location, results[0].formatted_address);
                }
            });
        }

        function updateLocationFields(location, address) {
            const lat = location.lat();
            const lng = location.lng();

            document.getElementById('store_location_lat').value = lat;
            document.getElementById('store_location_lng').value = lng;

            if (address) {
                document.getElementById('store_location_address').value = address;
                document.getElementById('selected-location-container').style.display = 'block';
            }
        }

        function clearLocation() {
            // Clear form fields
            document.getElementById('store_location_lat').value = '';
            document.getElementById('store_location_lng').value = '';
            document.getElementById('store_location_address').value = '';
            document.getElementById('location-search').value = '';

            // Hide selected location container
            document.getElementById('selected-location-container').style.display = 'none';

            // Remove marker from map
            if (marker) {
                marker.setMap(null);
                marker = null;
            }

            // Reset map center to Dubai
            if (map) {
                map.setCenter({ lat: 25.2048, lng: 55.2708 });
                map.setZoom(12);
            }
        }

        function showMapContainer() {
            const mapContainer = document.getElementById('map-container');
            if (mapContainer.style.display === 'none') {
                mapContainer.style.display = 'block';
                // Trigger map resize to ensure proper rendering
                setTimeout(() => {
                    google.maps.event.trigger(map, 'resize');
                }, 100);
            }
        }

        // Error handling for Google Maps
        function handleGoogleMapsError() {
            console.error('Google Maps failed to load');
            const mapContainer = document.getElementById('map-container');
            const searchInput = document.getElementById('location-search');

            // Show fallback message
            mapContainer.innerHTML = `
                <div style="padding: 20px; text-align: center; color: #6b7280;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 10px; color: #f59e0b;"></i>
                    <p>Google Maps is currently unavailable.</p>
                    <p style="font-size: 0.875rem; margin-top: 5px;">You can still enter your address manually in the search field above.</p>
                </div>
            `;
            mapContainer.style.display = 'block';

            // Enable manual address entry
            searchInput.placeholder = 'Enter your store address manually';
            searchInput.addEventListener('input', function() {
                document.getElementById('store_location_address').value = this.value;
                document.getElementById('store_location_address').style.display = 'block';
            });
        }

        // Initialize Google Maps when the API is loaded
        window.initGoogleMaps = initGoogleMaps;
        window.gm_authFailure = handleGoogleMapsError;
    </script>

    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('googlemaps.api_key') }}&libraries=places&callback=initGoogleMaps" async defer></script>
</body>
</html>
