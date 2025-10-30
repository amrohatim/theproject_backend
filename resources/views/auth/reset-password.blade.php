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
    <meta name="description" content="Enter your verification code and choose a new password for your Dala3Chic account">
    <meta name="robots" content="noindex, nofollow">

    <title>{{ __('messages.reset_password') }} - {{ __('messages.dala3chic') }}</title>

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
                <img src="{{ asset('assets/logo.png') }}" alt="Dala3Chic Logo" style="width: 150px; height: 150px; object-fit: contain; border-radius: 14px;">

                <h1 class="auth-brand-title">
                    {{ __('messages.reset_your_password') }}
                </h1>

                <p class="auth-brand-subtitle">
                    {{ __('messages.enter_reset_code') }}
                </p>
            </div>
        </div>

        <div class="auth-form-container">
            <div class="auth-form-card scroll-reveal animate-fade-in-right">
                <div class="auth-form-header">
                    <h2 class="auth-form-title">{{ __('messages.reset_password') }}</h2>
                    <p class="auth-form-subtitle">{{ __('messages.reset_password_subtitle') }}</p>
                </div>

                @if (session('status'))
                    <div class="auth-success-message mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <i class="fas fa-check-circle"></i>
                        <span class="ml-2 text-sm">{{ session('status') }}</span>
                    </div>
                @endif

                <form class="auth-form" action="{{ route('password.update') }}" method="POST" novalidate>
                    @csrf

                    <input type="hidden" name="email" value="{{ old('email', $email) }}">

                    <div class="auth-form-group">
                        <label for="code" class="auth-form-label">{{ __('messages.verification_code') }}</label>
                        <div class="auth-input-group">
                            <i class="auth-input-icon fas fa-key"></i>
                            <input
                                type="text"
                                id="code"
                                name="code"
                                class="auth-form-input @error('code') error @enderror"
                                placeholder="{{ __('messages.enter_verification_code') }}"
                                value="{{ old('code') }}"
                                required
                                autocomplete="one-time-code"
                                inputmode="numeric"
                            >
                        </div>
                        <div class="auth-error-container">
                            @error('code')
                                <div class="auth-error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="auth-form-group">
                        <label for="password" class="auth-form-label">{{ __('messages.new_password') }}</label>
                        <div class="auth-input-group {{ $isRtl ? 'rtl-password-field' : '' }}">
                            @if($isRtl)
                                <button type="button" class="auth-password-toggle auth-password-toggle-rtl" tabindex="-1">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <input
                                   style="direction: ltr; text-align: left;"
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="auth-form-input auth-form-input-rtl @error('password') error @enderror"
                                    placeholder="{{ __('messages.enter_new_password') }}"
                                    required
                                    autocomplete="new-password"
                                >
                                <i class="auth-input-icon auth-input-icon-rtl fas fa-lock"></i>
                            @else
                                <i class="auth-input-icon fas fa-lock"></i>
                                <input
                                
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="auth-form-input @error('password') error @enderror"
                                    placeholder="{{ __('messages.enter_new_password') }}"
                                    required
                                    autocomplete="new-password"
                                >
                                <button type="button" class="auth-password-toggle" tabindex="-1">
                                    <i class="fas fa-eye"></i>
                                </button>
                            @endif
                        </div>
                        <div class="auth-error-container">
                            @error('password')
                                <div class="auth-error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="auth-form-group">
                        <label for="password_confirmation" class="auth-form-label">{{ __('messages.confirm_new_password') }}</label>
                        <div class="auth-input-group {{ $isRtl ? 'rtl-password-field' : '' }}">
                            @if($isRtl)
                                <button type="button" class="auth-password-toggle auth-password-toggle-rtl" tabindex="-1">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <input
                                style="direction: ltr; text-align: left;"
                                    type="password"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    class="auth-form-input auth-form-input-rtl"
                                    placeholder="{{ __('messages.enter_confirm_new_password') }}"
                                    required
                                    autocomplete="new-password"
                                >
                                <i class="auth-input-icon auth-input-icon-rtl fas fa-lock"></i>
                            @else
                                <i class="auth-input-icon fas fa-lock"></i>
                                <input
                                    type="password"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    class="auth-form-input"
                                    placeholder="{{ __('messages.enter_confirm_new_password') }}"
                                    required
                                    autocomplete="new-password"
                                >
                                <button type="button" class="auth-password-toggle" tabindex="-1">
                                    <i class="fas fa-eye"></i>
                                </button>
                            @endif
                        </div>
                        <div class="auth-error-container">
                            @error('password_confirmation')
                                <div class="auth-error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="auth-submit-btn w-full">
                        <i class="fas fa-unlock-alt mr-2"></i>
                        {{ __('messages.reset_password_submit') }}
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <a href="{{ route('password.request') }}" class="auth-link text-sm">
                        <i class="fas fa-arrow-left mr-1"></i>
                        {{ __('messages.back_to_email_step') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    @vite(['resources/js/app.js', 'resources/js/modern-interactions.js'])

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.auth-password-toggle').forEach(function(toggle) {
                const input = toggle.closest('.auth-input-group')?.querySelector('input');
                const icon = toggle.querySelector('i');

                if (!input || !icon) {
                    return;
                }

                toggle.addEventListener('click', function() {
                    const isPassword = input.type === 'password';
                    input.type = isPassword ? 'text' : 'password';
                    icon.classList.toggle('fa-eye', !isPassword);
                    icon.classList.toggle('fa-eye-slash', isPassword);
                });
            });
        });
    </script>
</body>
</html>
