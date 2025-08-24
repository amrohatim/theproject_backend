<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google Maps API Key
    |--------------------------------------------------------------------------
    |
    | This is your Google Maps API key. You can get one from the Google Cloud
    | Console. Make sure to enable the following APIs:
    | - Maps JavaScript API
    | - Places API
    | - Geocoding API
    |
    */
    'api_key' => env('GOOGLE_MAPS_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Default Map Settings
    |--------------------------------------------------------------------------
    |
    | These are the default settings for Google Maps integration.
    |
    */
    'default_center' => [
        'lat' => 25.2048, // Dubai latitude
        'lng' => 55.2708, // Dubai longitude
    ],

    'default_zoom' => 12,

    /*
    |--------------------------------------------------------------------------
    | Map Libraries
    |--------------------------------------------------------------------------
    |
    | Google Maps libraries to load. Common libraries include:
    | - places (for Places API)
    | - geometry (for geometric calculations)
    | - drawing (for drawing tools)
    |
    */
    'libraries' => ['places'],

    /*
    |--------------------------------------------------------------------------
    | Autocomplete Settings
    |--------------------------------------------------------------------------
    |
    | Settings for Google Places Autocomplete functionality.
    |
    */
    'autocomplete' => [
        'country_restriction' => 'ae', // Restrict to UAE
        'types' => ['establishment', 'geocode'], // Types of places to search
    ],

    /*
    |--------------------------------------------------------------------------
    | Map Styling
    |--------------------------------------------------------------------------
    |
    | Default map styling options.
    |
    */
    'map_options' => [
        'zoom_control' => true,
        'map_type_control' => false,
        'scale_control' => true,
        'street_view_control' => true,
        'rotate_control' => true,
        'fullscreen_control' => true,
    ],
];
