@php
    use App\Helpers\LanguageHelper;

    $currentLocale = LanguageHelper::getCurrentLocale();
    $isRtl = LanguageHelper::isRtl();
    $direction = LanguageHelper::getDirection();

    $loginSlides = [
        [
            'image' => asset('assets/banner1.webp'),
            'top_text' => __('messages.modern_login_slide1_top'),
            'bottom_text' => __('messages.modern_login_slide1_bottom'),
        ],
        [
            'image' => asset('assets/banner2.webp'),
            'top_text' => __('messages.modern_login_slide2_top'),
            'bottom_text' => __('messages.modern_login_slide2_bottom'),
        ],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ $currentLocale }}" dir="{{ $direction }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login to your glowlabs account - Access your dashboard, orders, and more">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('messages.sign_in') }} - {{ __('messages.dala3chic') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500&family=Noto+Serif:wght@400&family=Playfair+Display:wght@700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css'])

    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
        }

        .modern-login-v2 {
            height: 100vh;
            width: 100%;
            display: flex;
            background: #ffffff;
            font-family: 'Poppins', sans-serif;
            color: #121212;
            overflow: hidden;
        }

        .modern-login-v2__form-panel {
            width: 35%;
            min-height: 100vh;
            background: #ffffff;
            display: flex;
            justify-content: center;
            padding: 1px 76px;
        }

        .modern-login-v2__form-content {
            width: 448px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
        }

        .modern-login-v2__brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            margin-top: 80px;
        }

        .modern-login-v2__brand img {
            width: 120px;
            height: 88px;
            object-fit: contain;
        }

        .modern-login-v2__brand-title {
            margin: 0;
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 30px;
            line-height: 1.1;
            color: #6d3b93;
            letter-spacing: 0.02em;
        }

        .modern-login-v2__brand-subtitle {
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            font-weight: 300;
            font-size: 16px;
            line-height: 20px;
            color: #231916;
        }

        .modern-login-v2__header {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .modern-login-v2__title {
            margin: 0;
            font-weight: 400;
            font-size: 48px;
            line-height: 56px;
            letter-spacing: -0.06em;
            color: #121212;
            text-align: center;
        }

        .modern-login-v2__subtitle {
            margin: 0;
            font-weight: 400;
            font-size: 14px;
            line-height: 20px;
            color: #3d3d3d;
            text-align: center;
        }

        .modern-login-v2__messages {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .modern-login-v2__message {
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 13px;
            line-height: 18px;
        }

        .modern-login-v2__message--success {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #86efac;
        }

        .modern-login-v2__form {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .modern-login-v2__fields {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .modern-login-v2__field {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .modern-login-v2__label {
            font-weight: 500;
            font-size: 14px;
            line-height: 20px;
            color: #121212;
        }

        .modern-login-v2__input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .modern-login-v2__input {
            width: 100%;
            height: 52px;
            border: 1px solid #ededed;
            background: #ffffff;
            padding: 16px;
            padding-inline-end: 44px;
            font-size: 14px;
            line-height: 20px;
            color: #121212;
            border-radius: 0;
            outline: none;
            border-radius:4px;
        }

        .modern-login-v2__input::placeholder {
            color: #6b6b6b;
        }

        .modern-login-v2__input:focus {
            border-color: #6c3d97;
        }

        .modern-login-v2__input--error {
            border-color: #dc2626;
        }

        .modern-login-v2__password-toggle {
            position: absolute;
            top: 50%;
            inset-inline-end: 14px;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #5b5b5b;
            cursor: pointer;
            padding: 0;
            line-height: 1;
        }

        .modern-login-v2__error {
            min-height: 20px;
            font-size: 12px;
            line-height: 18px;
            color: #dc2626;
        }

        .modern-login-v2__links {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
        }

        .modern-login-v2__remember {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            line-height: 20px;
            color: #3d3d3d;
            cursor: pointer;
        }

        .modern-login-v2__remember input {
            appearance: none;
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            display: inline-grid;
            place-content: center;
            margin: 0;
            cursor: pointer;
        }

        .modern-login-v2__remember input:checked {
            border-color: #6c3d97;
            background: #6c3d97;
        }

        .modern-login-v2__remember input:checked::after {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 2px;
            background: #fff;
        }

        .modern-login-v2__link {
            font-weight: 500;
            font-size: 14px;
            line-height: 20px;
            color: #3d3d3d;
            text-decoration: none;
        }

        .modern-login-v2__link:hover {
            color: #6c3d97;
        }

        .modern-login-v2__submit {
            width: 100%;
            height: 52px;
            border: none;
            background: #6c3d97;
            color: #ffffff;
            font-weight: 600;
            font-size: 16px;
            line-height: 24px;
            cursor: pointer;
            transition: background-color 0.2s ease;
            border-radius:4px;
        }

        .modern-login-v2__submit:hover {
            background: #5f3586;
        }

        .modern-login-v2__submit:disabled {
            opacity: 0.75;
            cursor: not-allowed;
        }

        .modern-login-v2__register {
            margin: 0;
            text-align: center;
            font-size: 14px;
            line-height: 28px;
            color: #3e3e3e;
        }

        .modern-login-v2__register a {
            color: #121212;
            font-weight: 600;
            text-decoration: none;
        }

        .modern-login-v2__register a:hover {
            color: #6c3d97;
        }

        .modern-login-v2__banner {
            position: relative;
            width: 65%;
            min-height: 100vh;
            overflow: hidden;
            background: #000;
        }

        .modern-login-v2__banner-image {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top;
            transition: opacity 0.35s ease;
        }

        .modern-login-v2__banner-gradient {
            position: absolute;
            inset-inline: 0;
            bottom: 0;
            height: 408px;
            background: linear-gradient(180deg, rgba(5, 5, 5, 0) 0%, rgba(5, 5, 5, 0.88) 100%);
            pointer-events: none;
        }

        .modern-login-v2__banner-top {
            position: absolute;
            top: 48px;
            left: 48px;
            width: 600px;
            margin: 0;
            font-family: 'Noto Serif', serif;
            font-weight: 400;
            font-size: 80px;
            line-height: 130px;
            letter-spacing: -0.04em;
            background: linear-gradient(108.91deg, #ffffff 0%, rgba(255, 255, 255, 0.24) 124.45%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-transform: uppercase;
        }

        .modern-login-v2__banner-bottom {
            position: absolute;
            left: 48px;
            bottom: 54px;
            width: 381px;
            margin: 0;
            font-size: 14px;
            line-height: 22px;
            color: rgba(255, 255, 255, 0.72);
        }

        .modern-login-v2__slider-controls {
            position: absolute;
            right: 48px;
            bottom: 48px;
            display: flex;
            gap: 12px;
        }

        .modern-login-v2__arrow {
            width: 56px;
            height: 56px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.24);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            background: transparent;
            cursor: pointer;
        }

        .modern-login-v2__arrow:hover {
            background: rgba(255, 255, 255, 0.12);
        }

        .modern-login-v2__arrow i {
            font-size: 18px;
        }

        .modern-login-v2__back-home {
            position: fixed;
            top: 20px;
            z-index: 10;
            {{ $isRtl ? 'right: 20px;' : 'left: 20px;' }}
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #3d3d3d;
            text-decoration: none;
            font-size: 14px;
            line-height: 20px;
        }

        .modern-login-v2__back-home:hover {
            color: #121212;
        }

        .modern-login-v2__spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.55);
            border-top-color: #ffffff;
            border-radius: 50%;
            display: inline-block;
            margin-inline-end: 8px;
            animation: modernLoginSpin 0.9s linear infinite;
            vertical-align: middle;
        }

        @keyframes modernLoginSpin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        [dir="rtl"] .modern-login-v2__links {
            flex-direction: row-reverse;
        }

        [dir="rtl"] .modern-login-v2__input {
            text-align: right;
        }

        [dir="rtl"] .modern-login-v2__banner-top {
            left: auto;
            right: 48px;
            text-align: right;
        }

        [dir="rtl"] .modern-login-v2__banner-bottom {
            left: auto;
            right: 48px;
            text-align: right;
        }

        [dir="rtl"] .modern-login-v2__slider-controls {
            right: auto;
            left: 48px;
        }

        @media (max-width: 1200px) {
            .modern-login-v2__form-panel {
                width: 100%;
                padding: 56px 24px;
            }

            .modern-login-v2__banner {
                display: none;
            }

            .modern-login-v2__form-content {
                width: 100%;
                max-width: 448px;
            }

            .modern-login-v2__title {
                font-size: 46px;
                line-height: 52px;
            }
        }

        @media (max-width: 560px) {
            .modern-login-v2__title {
                font-size: 40px;
                line-height: 0px;
                visibility:hidden;
            }
             .modern-login-v2__brand {
            display: flex;
            flex-direction: column;
        
            align-items: center;
            gap: 0px;
            margin-top: 0px;
        }
        }
    </style>
</head>
<body>
    <a href="{{ url('/') }}" class="modern-login-v2__back-home">
        <i class="fas {{ $isRtl ? 'fa-arrow-right' : 'fa-arrow-left' }}"></i>
        <span>{{ __('messages.back_to_home') }}</span>
    </a>

    <main class="modern-login-v2">
        <section class="modern-login-v2__form-panel" aria-label="Login form">
            <div class="modern-login-v2__form-content">
                <div class="modern-login-v2__brand">
                    <img src="{{ asset('assets/logo.png') }}" alt="Glow Labs">
                    <h2 class="modern-login-v2__brand-title">Glow Labs</h2>
                    <p class="modern-login-v2__brand-subtitle">{{ __('messages.modern_login_brand_subtitle') }}</p>
                </div>

                <div class="modern-login-v2__header">
                    <h1 class="modern-login-v2__title">{{ __('messages.modern_login_welcome_back') }}</h1>
                    <p class="modern-login-v2__subtitle">{{ __('messages.modern_login_subtitle') }}</p>
                </div>

                <div class="modern-login-v2__messages">
                    @if (session('success'))
                        <div class="modern-login-v2__message modern-login-v2__message--success">
                            <i class="fas fa-circle-check" aria-hidden="true"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif
                </div>

                <form class="modern-login-v2__form" action="{{ route('login.attempt') }}" method="POST" novalidate>
                    @csrf

                    <div class="modern-login-v2__fields">
                        <div class="modern-login-v2__field">
                            <label for="email" class="modern-login-v2__label">{{ __('messages.email') }}</label>
                            <div class="modern-login-v2__input-wrap">
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    class="modern-login-v2__input @error('email') modern-login-v2__input--error @enderror"
                                    placeholder="{{ __('messages.enter_email_address') }}"
                                    value="{{ old('email') }}"
                                    autocomplete="email"
                                    required
                                    autofocus
                                >
                            </div>
                            <div class="modern-login-v2__error">
                                @error('email')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="modern-login-v2__field">
                            <label for="password" class="modern-login-v2__label">{{ __('messages.password') }}</label>
                            <div class="modern-login-v2__input-wrap">
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="modern-login-v2__input @error('password') modern-login-v2__input--error @enderror"
                                    placeholder="{{ __('messages.enter_password') }}"
                                    autocomplete="current-password"
                                    required
                                >
                                <button type="button" class="modern-login-v2__password-toggle" id="passwordToggle" aria-label="Toggle password visibility">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                            <div class="modern-login-v2__error">
                                @error('password')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="modern-login-v2__links">
                        <label for="remember" class="modern-login-v2__remember">
                            <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span>{{ __('messages.remember_me') }}</span>
                        </label>

                        <a href="{{ route('password.request') }}" class="modern-login-v2__link">{{ __('messages.forgot_password') }}</a>
                    </div>

                    <button type="submit" class="modern-login-v2__submit" id="submitButton">{{ __('messages.sign_in') }}</button>
                </form>

                <p class="modern-login-v2__register">
                    {{ __('messages.dont_have_account') }}
                    <a href="{{ route('register') }}">{{ __('messages.create_one_now') }}</a>
                </p>
            </div>
        </section>

        <aside class="modern-login-v2__banner" aria-label="Promotional banner">
            <img id="sliderImage" class="modern-login-v2__banner-image" src="{{ $loginSlides[0]['image'] }}" alt="Promotional banner">
            <div class="modern-login-v2__banner-gradient"></div>

            <h2 id="sliderTopText" class="modern-login-v2__banner-top">{{ $loginSlides[0]['top_text'] }}</h2>
            <p id="sliderBottomText" class="modern-login-v2__banner-bottom">{{ $loginSlides[0]['bottom_text'] }}</p>

            <div class="modern-login-v2__slider-controls">
                <button id="prevSlide" type="button" class="modern-login-v2__arrow" aria-label="Previous slide">
                   <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-right' : 'fa-arrow-left' }}"></i> 
                </button>
                <button id="nextSlide" type="button" class="modern-login-v2__arrow" aria-label="Next slide">
                    <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right' }}"></i> 
                </button>
            </div>
        </aside>
    </main>

    @vite(['resources/js/app.js'])

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('.modern-login-v2__form');
            const submitButton = document.getElementById('submitButton');
            const passwordToggle = document.getElementById('passwordToggle');
            const passwordInput = document.getElementById('password');

            if (passwordToggle && passwordInput) {
                passwordToggle.addEventListener('click', function () {
                    const icon = this.querySelector('i');
                    const reveal = passwordInput.type === 'password';
                    passwordInput.type = reveal ? 'text' : 'password';
                    icon.className = reveal ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
                });
            }

            if (form && submitButton) {
                form.addEventListener('submit', function () {
                    submitButton.disabled = true;
                    submitButton.innerHTML = `<span class="modern-login-v2__spinner"></span>{{ __('messages.signing_in') }}`;
                });
            }

            const slides = @json($loginSlides);
            const sliderImage = document.getElementById('sliderImage');
            const sliderTopText = document.getElementById('sliderTopText');
            const sliderBottomText = document.getElementById('sliderBottomText');
            const prevSlide = document.getElementById('prevSlide');
            const nextSlide = document.getElementById('nextSlide');
            const shouldRunSlider = slides.length > 0 && sliderImage && sliderTopText && sliderBottomText && prevSlide && nextSlide;

            if (!shouldRunSlider) {
                return;
            }

            let currentIndex = 0;
            let timerId = null;

            function renderSlide(index) {
                currentIndex = (index + slides.length) % slides.length;
                const activeSlide = slides[currentIndex];
                sliderImage.style.opacity = '0.25';

                setTimeout(function () {
                    sliderImage.src = activeSlide.image;
                    sliderTopText.textContent = activeSlide.top_text;
                    sliderBottomText.textContent = activeSlide.bottom_text;
                    sliderImage.style.opacity = '1';
                }, 120);
            }

            function restartAutoSlide() {
                if (timerId) {
                    clearInterval(timerId);
                }

                timerId = setInterval(function () {
                    renderSlide(currentIndex + 1);
                }, 8000);
            }

            prevSlide.addEventListener('click', function () {
                renderSlide(currentIndex - 1);
                restartAutoSlide();
            });

            nextSlide.addEventListener('click', function () {
                renderSlide(currentIndex + 1);
                restartAutoSlide();
            });

            restartAutoSlide();
        });
    </script>
</body>
</html>
