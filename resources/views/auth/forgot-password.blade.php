@php
    use App\Helpers\LanguageHelper;
    $currentLocale = LanguageHelper::getCurrentLocale();
    $isRtl = LanguageHelper::isRtl();
    $direction = LanguageHelper::getDirection();
@endphp

<!DOCTYPE html>
<html lang="{{ $currentLocale }}" dir="{{ $direction }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Reset your glowlabs password - Request a verification code via email">
    <meta name="robots" content="noindex, nofollow">

    <title>{{ __('messages.forgot_password') }} - {{ __('messages.dala3chic') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>

    @vite(['resources/css/app.css', 'resources/css/animations.css', 'resources/css/modern-auth.css'])

    @if($isRtl)
        <link href="{{ asset('css/rtl.css') }}" rel="stylesheet">
    @endif

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="auth-container">
        <div class="auth-branding">
            <div class="scroll-reveal animate-fade-in-left">
                <img src="{{ asset('assets/logo.png') }}" alt="glowlabs Logo" style="width: 150px; height: 150px; object-fit: contain; border-radius: 14px;">

                <h1 class="auth-brand-title">
                    {{ __('messages.reset_your_password') }}
                </h1>

                <p class="auth-brand-subtitle">
                    {{ __('messages.forgot_password_intro') }}
                </p>
            </div>
        </div>

        <div class="auth-form-container">
            <div class="auth-form-card scroll-reveal animate-fade-in-right">
                <div class="auth-form-header">
                    <h2 class="auth-form-title">{{ __('messages.forgot_password') }}</h2>
                    <p class="auth-form-subtitle">{{ __('messages.forgot_password_subtitle') }}</p>
                </div>

                @if (session('status'))
                    <div class="auth-success-message mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <i class="fas fa-check-circle"></i>
                        <span class="ml-2 text-sm">{{ session('status') }}</span>
                    </div>
                @endif

                <form class="auth-form" action="{{ route('password.email') }}" method="POST" novalidate>
                    @csrf

                    <div class="auth-form-group">
                        <label for="email" class="auth-form-label">{{ __('messages.email_address') }}</label>
                        <div class="auth-input-group">
                            <i class="auth-input-icon fas fa-envelope"></i>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="auth-form-input @error('email') error @enderror"
                                placeholder="{{ __('messages.enter_email_address') }}"
                                value="{{ old('email') }}"
                                required
                                autocomplete="email"
                                autofocus
                            >
                        </div>
                        <div class="auth-error-container">
                            @error('email')
                                <div class="auth-error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="auth-submit-btn w-full">
                        <i class="fas fa-paper-plane mr-2"></i>
                        {{ __('messages.send_reset_code') }}
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="auth-link text-sm">
                        <i class="fas fa-arrow-left mr-1"></i>
                        {{ __('messages.back_to_login') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
