<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data3Chic - Merchant Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        amber: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .step-content {
            display: none;
        }
        .step-content.active {
            display: block;
        }
        .progress-line {
            transition: all 0.3s ease;
        }
        .step-circle {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }
        .step-circle:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }
        .step-circle.completed {
            animation: checkmark-bounce 0.6s ease-in-out;
        }
        @keyframes checkmark-bounce {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .step-circle.clickable:hover {
            background-color: rgba(245, 158, 11, 0.1);
        }
        .step-circle:focus {
            outline: 2px solid #f59e0b;
            outline-offset: 2px;
        }
        .upload-area {
            transition: border-color 0.3s ease;
        }
        .upload-area:hover {
            border-color: #f59e0b;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .loading {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s ease-in-out infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Google Maps initialization */
        window.initGoogleMaps = function() {
            window.googleMapsLoaded = true;
            console.log('Google Maps API loaded successfully');
            window.dispatchEvent(new CustomEvent('google-maps-loaded'));
        };

        window.handleGoogleMapsError = function() {
            console.error('Failed to load Google Maps API script');
            window.googleMapsLoaded = false;
            window.dispatchEvent(new CustomEvent('google-maps-failed'));
        };

        setTimeout(() => {
            if (!window.googleMapsLoaded) {
                console.warn('Google Maps failed to load within timeout. Map functionality will be disabled.');
                window.googleMapsLoaded = false;
                window.dispatchEvent(new CustomEvent('google-maps-failed'));
            }
        }, 10000);
    </style>

    <!-- Google Maps API -->
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key={{ config('googlemaps.api_key') }}&libraries=places&callback=initGoogleMaps"
        onerror="handleGoogleMapsError()">
    </script>

    @vite(['resources/js/merchant-registration.js'])
</head>
<body class="min-h-screen bg-gray-50">
    <!-- Vue.js App Container -->
    <div id="merchant-registration-app"></div>
</body>
</html>
