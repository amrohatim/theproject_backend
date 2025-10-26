<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'firebase' => [
        'project_id' => env('FIREBASE_PROJECT_ID'),
        'credentials_file' => value(function () {
            $path = env('FIREBASE_CREDENTIALS');

            if (!$path) {
                return null;
            }

            if (str_starts_with($path, DIRECTORY_SEPARATOR) || preg_match('/^[A-Za-z]:[\\\\\\/]/', $path) === 1) {
                return $path;
            }

            return base_path($path);
        }),
        'private_key_id' => env('FIREBASE_PRIVATE_KEY_ID'),
        'private_key' => env('FIREBASE_PRIVATE_KEY'),
        'client_email' => env('FIREBASE_CLIENT_EMAIL'),
        'client_id' => env('FIREBASE_CLIENT_ID'),
        'auth_uri' => env('FIREBASE_AUTH_URI', 'https://accounts.google.com/o/oauth2/auth'),
        'token_uri' => env('FIREBASE_TOKEN_URI', 'https://oauth2.googleapis.com/token'),
        'auth_provider_x509_cert_url' => env('FIREBASE_AUTH_PROVIDER_X509_CERT_URL', 'https://www.googleapis.com/oauth2/v1/certs'),
        'client_x509_cert_url' => env('FIREBASE_CLIENT_X509_CERT_URL'),
        'web_api_key' => env('FIREBASE_WEB_API_KEY'),
        'disable_ssl_verification' => env('FIREBASE_DISABLE_SSL_VERIFICATION', false),
        'web_push' => [
            'vapid_public_key' => env('FIREBASE_WEB_PUSH_CERTIFICATE'),
        ],
    ],

    

    'cisco_duo' => [
        'client_id' => env('CISCO_DUO_CLIENT_ID', 'DI7CHCVGLUVT0KKNIOBO'),
        'client_secret' => env('CISCO_DUO_CLIENT_SECRET', 'Vde7mXhRxULzB9QY0ASiXNAXMf2J2zMWRWNJumQu'),
        'issuer' => env('CISCO_DUO_ISSUER', 'https://sso-1453a9f5.sso.duosecurity.com/oidc/DI7CHCVGLUVT0KKNIOBO'),
        'discovery_url' => env('CISCO_DUO_DISCOVERY_URL', 'https://sso-1453a9f5.sso.duosecurity.com/oidc/DI7CHCVGLUVT0KKNIOBO'),
        'authorization_url' => env('CISCO_DUO_AUTHORIZATION_URL', 'https://sso-1453a9f5.sso.duosecurity.com/oidc/DI7CHCVGLUVT0KKNIOBO/authorize'),
        'token_url' => env('CISCO_DUO_TOKEN_URL', 'https://sso-1453a9f5.sso.duosecurity.com/oidc/DI7CHCVGLUVT0KKNIOBO/token'),
        'jwks_url' => env('CISCO_DUO_JWKS_URL', 'https://sso-1453a9f5.sso.duosecurity.com/oidc/DI7CHCVGLUVT0KKNIOBO/jwks'),
        'userinfo_url' => env('CISCO_DUO_USERINFO_URL', 'https://sso-1453a9f5.sso.duosecurity.com/oidc/DI7CHCVGLUVT0KKNIOBO/userinfo'),
    ],

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN', 'www.dala3chic.com'),
        'secret' => env('MAILGUN_SECRET', 'c849b1292f390404b598f60aca06c703-a1dad75f-b9d83449'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    // Remove SmartVision configuration - replaced with Cisco DUO for OTP
    // 'smartvision' => [
    //     'api_url' => env('SMARTVISION_API_URL', 'https://api.smartvision.ae'),
    //     'api_key' => env('SMARTVISION_API_KEY'),
    //     'sender_id' => env('SMARTVISION_SENDER_ID', 'YourAppName'),
    // ],

    'google' => [
        'maps_api_key' => env('GOOGLE_MAPS_API_KEY'),
    ],

    'smsala' => [
        'api_id' => env('SMSALA_API_ID', 'SMSALA_DALA3_3862_SMS'),
        'api_password' => env('SMSALA_API_PASSWORD', 'Jf8gMgERPiorWrAr'),
        'sender_id' => env('SMSALA_SENDER_ID', 'DALA3CHIC'),
        'base_url' => env('SMSALA_BASE_URL', 'https://api.smsala.com/api'),
        'rate_limit_per_hour' => env('SMSALA_RATE_LIMIT_HOUR', 5),
        'rate_limit_per_day' => env('SMSALA_RATE_LIMIT_DAY', 20),
    ],

    'aramex' => [
        // API credentials
        'account_number' => env('ARAMEX_ACCOUNT_NUMBER', 'test_account'),
        'username' => env('ARAMEX_USERNAME', 'test_user'),
        'password' => env('ARAMEX_PASSWORD', 'test_password'),
        'account_pin' => env('ARAMEX_ACCOUNT_PIN', 'test_pin'),
        'entity' => env('ARAMEX_ENTITY', 'AE'),
        'country_code' => env('ARAMEX_COUNTRY_CODE', 'AE'),

        // API endpoints
        'wsdl_create_shipments' => env('ARAMEX_WSDL_CREATE_SHIPMENTS', 'https://ws.aramex.net/ShippingAPI.V2/Shipping/Service_1_0.svc?wsdl'),
        'wsdl_track_shipments' => env('ARAMEX_WSDL_TRACK_SHIPMENTS', 'https://ws.aramex.net/ShippingAPI.V2/Tracking/Service_1_0.svc?wsdl'),

        // Shipper information
        'shipper_name' => env('ARAMEX_SHIPPER_NAME', 'Store Manager'),
        'shipper_company' => env('ARAMEX_SHIPPER_COMPANY', 'My Store'),
        'shipper_phone' => env('ARAMEX_SHIPPER_PHONE', '+971501234567'),
        'shipper_email' => env('ARAMEX_SHIPPER_EMAIL', 'info@mystore.com'),
        'shipper_address_line1' => env('ARAMEX_SHIPPER_ADDRESS_LINE1', '123 Market St'),
        'shipper_address_line2' => env('ARAMEX_SHIPPER_ADDRESS_LINE2', ''),
        'shipper_city' => env('ARAMEX_SHIPPER_CITY', 'Dubai'),
        'shipper_state' => env('ARAMEX_SHIPPER_STATE', ''),
        'shipper_postal_code' => env('ARAMEX_SHIPPER_POSTAL_CODE', ''),
        'shipper_country_code' => env('ARAMEX_SHIPPER_COUNTRY_CODE', 'AE'),
    ],

];
