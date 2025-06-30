<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Merchant Registration - Dala3Chic</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/js/merchant-registration.js'])

    <script>
        // Global Google Maps initialization function (must be defined before API script loads)
        window.initGoogleMaps = function() {
            window.googleMapsLoaded = true;
            console.log('Google Maps API loaded successfully');

            // Dispatch event to notify Vue components that Google Maps is ready
            window.dispatchEvent(new CustomEvent('google-maps-loaded'));
        };

        // Global error handler for Google Maps script loading
        window.handleGoogleMapsError = function() {
            console.error('Failed to load Google Maps API script');
            window.googleMapsLoaded = false;
            window.dispatchEvent(new CustomEvent('google-maps-failed'));
        };

        // Fallback if Google Maps fails to load
        setTimeout(() => {
            if (!window.googleMapsLoaded) {
                console.warn('Google Maps failed to load within timeout. Map functionality will be disabled.');
                window.googleMapsLoaded = false;
                window.dispatchEvent(new CustomEvent('google-maps-failed'));
            }
        }, 10000); // 10 second timeout
    </script>

    <!-- Google Maps API -->
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key={{ config('googlemaps.api_key') }}&libraries=places&callback=initGoogleMaps"
        onerror="handleGoogleMapsError()">
    </script>
    
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Loading spinner for initial load */
        .loading-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Hide loading when Vue app is mounted */
        #merchant-registration-app:not(:empty) + .loading-container {
            display: none;
        }
    </style>
</head>
<body>
    <!-- Vue.js App Container -->
    <div id="merchant-registration-app"></div>
    
    <!-- Loading Fallback -->
    <div class="loading-container">
        <div class="loading-spinner"></div>
    </div>


</body>
</html>
