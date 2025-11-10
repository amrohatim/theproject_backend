@php
    $currentLocale = app()->getLocale();
    $isRtl = in_array($currentLocale, ['ar', 'he', 'fa', 'ur']);
    $direction = $isRtl ? 'rtl' : 'ltr';
@endphp
<!DOCTYPE html>
<html lang="{{ $currentLocale }}" dir="{{ $direction }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data3Chic - {{ __('messages.merchant_registration') }}</title>
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

    <!-- RTL Support -->
    @if($isRtl)
    <style>
        body {
            direction: rtl;
            text-align: right;
        }
        .rtl-flip {
            transform: scaleX(-1);
        }
        .rtl-margin-left {
            margin-right: auto;
            margin-left: 0;
        }
        .rtl-margin-right {
            margin-left: auto;
            margin-right: 0;
        }
        .rtl-text-left {
            text-align: right;
        }
        .rtl-text-right {
            text-align: left;
        }
        .rtl-float-left {
            float: right;
        }
        .rtl-float-right {
            float: left;
        }
        .rtl-border-left {
            border-right: 1px solid;
            border-left: none;
        }
        .rtl-border-right {
            border-left: 1px solid;
            border-right: none;
        }
        .rtl-padding-left {
            padding-right: 1rem;
            padding-left: 0;
        }
        .rtl-padding-right {
            padding-left: 1rem;
            padding-right: 0;
        }
    </style>
    @endif

    <!-- Pass translations and locale data to Vue -->
    <script>
        window.appLocale = '{{ $currentLocale }}';
        window.appDirection = '{{ $direction }}';
        window.appIsRtl = {{ $isRtl ? 'true' : 'false' }};
        window.appTranslations = {
            'business_info': '{{ __('messages.business_info') }}',
            'email_verification': '{{ __('messages.email_verification') }}',
            'phone_verification': '{{ __('messages.phone_verification') }}',
            'license_upload': '{{ __('messages.license_upload') }}',
            'business_name': '{{ __('messages.business_name') }}',
            'enter_business_name': '{{ __('messages.enter_business_name') }}',
            'email_address': '{{ __('messages.email_address') }}',
            'enter_email_address': '{{ __('messages.enter_email_address') }}',
            'phone_number': '{{ __('messages.phone_number') }}',
            'enter_phone_number': '{{ __('messages.enter_phone_number') }}',
            'password': '{{ __('messages.password') }}',
            'enter_password': '{{ __('messages.enter_password') }}',
            'confirm_password': '{{ __('messages.confirm_password') }}',
            'enter_confirm_password': '{{ __('messages.enter_confirm_password') }}',
            'continue_to_email_verification': '{{ __('messages.continue_to_email_verification') }}',
            'continue_to_phone_verification': '{{ __('messages.continue_to_phone_verification') }}',
            'continue_to_license_upload': '{{ __('messages.continue_to_license_upload') }}',
            'verify_email': '{{ __('messages.verify_email') }}',
            'verify_phone': '{{ __('messages.verify_phone') }}',
            'resend_code': '{{ __('messages.resend_code') }}',
            'resend_otp': '{{ __('messages.resend_otp') }}',
            'complete_registration': '{{ __('messages.complete_registration') }}',
            'processing': '{{ __('messages.processing') }}',
            'saving': '{{ __('messages.saving') }}',
            'verification_code_sent': '{{ __('messages.verification_code_sent') }}',
            'enter_verification_code': '{{ __('messages.enter_verification_code') }}',
            'enter_6_digit_code': '{{ __('messages.enter_6_digit_code') }}',
            'verification_code': '{{ __('messages.verification_code') }}',
            'otp_code': '{{ __('messages.otp_code') }}',
            'check_spam_folder': '{{ __('messages.check_spam_folder') }}',
            'code_expires_in_10_minutes': '{{ __('messages.code_expires_in_10_minutes') }}',
            'make_sure_phone_receives_sms': '{{ __('messages.make_sure_phone_receives_sms') }}',
            'upload_business_license': '{{ __('messages.upload_business_license') }}',
            'business_license_pdf': '{{ __('messages.business_license_pdf') }}',
            'drop_license_here': '{{ __('messages.drop_license_here') }}',
            'pdf_files_only': '{{ __('messages.pdf_files_only') }}',
            'license_start_date': '{{ __('messages.license_start_date') }}',
            'license_end_date': '{{ __('messages.license_end_date') }}',
            'notes': '{{ __('messages.notes') }}',
            'optional_notes': '{{ __('messages.optional_notes') }}',
            'license_reviewed_24_48_hours': '{{ __('messages.license_reviewed_24_48_hours') }}',
            'documents_securely_stored': '{{ __('messages.documents_securely_stored') }}',
            'email_confirmation_once_approved': '{{ __('messages.email_confirmation_once_approved') }}',
            'field_required': '{{ __('messages.field_required') }}',
            'invalid_email_format': '{{ __('messages.invalid_email_format') }}',
            'password_min_8_characters': '{{ __('messages.password_min_8_characters') }}',
            'passwords_do_not_match': '{{ __('messages.passwords_do_not_match') }}',
            'invalid_phone_format': '{{ __('messages.invalid_phone_format') }}',
            'file_too_large': '{{ __('messages.file_too_large') }}',
            'invalid_file_type': '{{ __('messages.invalid_file_type') }}',
            'pdf_files_only_message': '{{ __('messages.pdf_files_only_message') }}',
            'merchant_registration': '{{ __('messages.merchant_registration') }}',
            'merchant_registration_desc': '{{ __('messages.merchant_registration_desc') }}',
            'basic_information': '{{ __('messages.basic_information') }}',
            'verify_your_email': '{{ __('messages.verify_your_email') }}',
            'verify_your_phone': '{{ __('messages.verify_your_phone') }}',
            'business_details': '{{ __('messages.business_details') }}',
            'upload_documents': '{{ __('messages.upload_documents') }}',
            // Missing keys from MerchantRegistrationApp.vue
            'join_our_merchant_community': '{{ __('messages.join_our_merchant_community') }}',
            'expand_business_reach': '{{ __('messages.expand_business_reach') }}',
            'grow_your_sales': '{{ __('messages.grow_your_sales') }}',
            'access_large_customer_base': '{{ __('messages.access_large_customer_base') }}',
            'powerful_tools': '{{ __('messages.powerful_tools') }}',
            'easy_dashboard_manage_products': '{{ __('messages.easy_dashboard_manage_products') }}',
            'dedicated_support': '{{ __('messages.dedicated_support') }}',
            'our_team_help_succeed': '{{ __('messages.our_team_help_succeed') }}',
            'create_merchant_account': '{{ __('messages.create_merchant_account') }}',
            'complete_all_steps': '{{ __('messages.complete_all_steps') }}',
            'step_of': '{{ __('messages.step_of') }}',
            'percent_complete': '{{ __('messages.percent_complete') }}',
            'registration_complete': '{{ __('messages.registration_complete') }}',
            'registration_submitted_successfully': '{{ __('messages.registration_submitted_successfully') }}',
            'go_to_login': '{{ __('messages.go_to_login') }}',
            'previous': '{{ __('messages.previous') }}',
            'already_have_account': '{{ __('messages.already_have_account') }}',
            'uploading_license' : '{{ __('messages.uploading_license') }}',
            'registration_co'
            'log_in': '{{ __('messages.log_in') }}',
            'continue_to_verification': '{{ __('messages.continue_to_verification') }}',
            // Additional missing keys
            'business_logo_optional': '{{ __('messages.business_logo_optional') }}',
            'click_to_upload_logo': '{{ __('messages.click_to_upload_logo') }}',
            'uae_id_front_side_required': '{{ __('messages.uae_id_front_side_required') }}',
            'upload_front_side_uae_id': '{{ __('messages.upload_front_side_uae_id') }}',
            'uae_id_back_side_required': '{{ __('messages.uae_id_back_side_required') }}',
            'upload_back_side_uae_id': '{{ __('messages.upload_back_side_uae_id') }}',
            'store_location_optional': '{{ __('messages.store_location_optional') }}',
            'i_can_deliver_to_customers': '{{ __('messages.i_can_deliver_to_customers') }}',
            'business_information': '{{ __('messages.business_information') }}',
            'phone_placeholder': '{{ __('messages.phone_placeholder') }}',
            'search_for_store_location': '{{ __('messages.search_for_store_location') }}',
            'delivery_fees_by_emirate_aed': '{{ __('messages.delivery_fees_by_emirate_aed') }}',
            'emirate': '{{ __('messages.emirate') }}',
            'select_emirate': '{{ __('messages.select_emirate') }}',
            'verify_your_email_step': '{{ __('messages.verify_your_email_step') }}',
            // Missing email verification keys
            'verify_your_email_title': '{{ __('messages.verify_your_email_title') }}',
            'weve_sent_verification_code_to': '{{ __('messages.weve_sent_verification_code_to') }}',
            'please_check_email_enter_code': '{{ __('messages.please_check_email_enter_code') }}',
            'didnt_receive_code': '{{ __('messages.didnt_receive_code') }}',
            'check_spam_junk_folder': '{{ __('messages.check_spam_junk_folder') }}',
            'verification_code_expires_10_minutes': '{{ __('messages.verification_code_expires_10_minutes') }}',
            'resend_verification_code': '{{ __('messages.resend_verification_code') }}',
            'enter_code_below': '{{ __('messages.enter_code_below') }}',
            'code_sent_to': '{{ __('messages.code_sent_to') }}',
            // Missing phone verification keys
            'verification_code_sent_to': '{{ __('messages.verification_code_sent_to') }}',
            'enter_6_digit_code_below': '{{ __('messages.enter_6_digit_code_below') }}',
            'phone_sms_check': '{{ __('messages.phone_sms_check') }}',
            'otp_expires_10_minutes': '{{ __('messages.otp_expires_10_minutes') }}',
            'verifying': '{{ __('messages.verifying') }}',
            'resend_in': '{{ __('messages.resend_in') }}',
            'enter_6_digit_code_placeholder': '{{ __('messages.enter_6_digit_code_placeholder') }}',
            'upload_license_description_merchant': '{{ __('messages.upload_license_description_merchant') }}',
            'drop_license_or_browse': '{{ __('messages.drop_license_or_browse') }}',
            'pdf_files_max_10mb': '{{ __('messages.pdf_files_max_10mb') }}',
            'additional_notes_optional': '{{ __('messages.additional_notes_optional') }}',
            'license_additional_info_placeholder': '{{ __('messages.license_additional_info_placeholder') }}',
            'license_review_24_48_hours': '{{ __('messages.license_review_24_48_hours') }}',
            'documents_securely_encrypted': '{{ __('messages.documents_securely_encrypted') }}',
            'sharjah': '{{ __('messages.sharjah') }}',
            'dubai': '{{ __('messages.dubai') }}',
            'abu_dhabi': '{{ __('messages.abu_dhabi') }}',
            'ajman': '{{ __('messages.ajman') }}',
            'fujairah': '{{ __('messages.fujairah') }}',
            'ras_al_khaimah': '{{ __('messages.ras_al_khaimah') }}',
            'umm_al_quwain': '{{ __('messages.umm_al_quwain') }}',
            'creating_account': '{{ __('messages.creating_account') }}',
            'map_instructions': '{{ __('messages.map_instructions') }}',
            'close': '{{ __('messages.close') }}',
            'account_already_exists':'{{ __('messages.account_already_exists') }}',
            'please_fix_the_following_errors_before_submitting': '{{ __('messages.please_fix_the_following_errors_before_submitting') }}',
            'correct_the_errors': '{{ __('messages.correct_the_errors') }}',
        };
    </script>

    <!-- Dynamics.js for modal animations -->
    <script src="https://cdn.jsdelivr.net/npm/dynamics.js@1.1.5/lib/dynamics.min.js"></script>

    @vite(['resources/js/merchant-registration.js'])
</head>
<body class="min-h-screen bg-gray-50 {{ $isRtl ? 'rtl' : '' }}">
    <!-- Vue.js App Container -->
    <div id="merchant-registration-app"></div>
</body>
</html>
