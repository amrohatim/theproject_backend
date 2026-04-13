
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ __('messages.gogo_title') }}</title>
  <meta name="description" content="{{ __('messages.gogo_meta_description') }}" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="{{ asset('css/global.css') }}" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display&family=Manrope:wght@700&family=Playfair+Display:wght@700&family=Poppins:wght@400;700&display=swap" rel="stylesheet" />
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['DM Sans', 'sans-serif'],
            serif: ['DM Serif Display', 'serif'],
          },
          colors: {
            brand: {
              pink: '#d9657a',
              'pink-light': '#f8d5d0',
              'pink-bg': '#fdf0ee',
              dark: '#1e2536',
              'dark-card': '#2a3045',
              'dark-border': '#353b50',
            }
          }
        }
      }
    }
  </script>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'DM Sans', sans-serif; color: #1e2536; }
    .font-serif { font-family: 'DM Serif Display', serif; }
    .font-playfair { font-family: 'Playfair Display', serif; }
    .upper-shell {
      background-image: url('{{ asset('assets/homeBG.png') }}');
      background-position: center top;
      background-size: cover;
      background-repeat: no-repeat;
    }
    .upper-hero-title span,
    .upper-hero-title strong {
      color: #ffa006;
    }
    .upper-cta-primary {
      background: #ffa006;
      color: #f8f8f8;
      border-radius: 6px;
      box-shadow: 0 0 21px rgba(226, 142, 8, 0.65);
      transition: transform .2s ease, box-shadow .2s ease;
    }
    .upper-cta-primary:hover {
      transform: translateY(-1px);
      box-shadow: 0 0 24px rgba(226, 142, 8, 0.85);
    }
    .upper-cta-secondary {
      border: 3px solid #ffa006;
      border-radius: 6px;
      color: #fff;
      box-shadow: 0 0 18px rgba(226, 142, 8, 0.45);
      transition: transform .2s ease, background-color .2s ease;
    }
    .upper-cta-secondary:hover {
      transform: translateY(-1px);
      background: rgba(255, 160, 6, 0.12);
    }
    .upper-nav-link {
      position: relative;
      font-size: 15px;
      letter-spacing: .03em;
      color: rgba(255, 255, 255, 0.9);
      transition: color .2s ease;
    }
    .upper-nav-link:hover { color: #fff; }
    .upper-nav-link.is-active::after {
      content: '';
      position: absolute;
      left: 0;
      right: 0;
      bottom: -8px;
      border-bottom: 2px solid #ffa006;
    }
    .upper-auth-segment {
      border: 2px solid #ffa006;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 0 18px rgba(226, 142, 8, 0.34);
    }
    .upper-login-btn {
      min-width: 138px;
      color: #ffffff;
      background: rgba(43, 3, 82, 0);
      transition: background-color .2s ease;
    }
    .upper-login-btn:hover { background: #ffa006; }
    .upper-signup-btn {
      min-width: 138px;
      color: #ffffff;
      background: rgba(43, 3, 82, 0);
      transition: background-color .2s ease;
    }
    .upper-signup-btn:hover { background: #ffa006; }
    .upper-model-wrap::after {
      content: '';
      position: absolute;
      inset: 0;
      pointer-events: none;
      
    }
    .upper-emirates-strip {
      background: rgba(93, 42, 133, 0.8);
      backdrop-filter: blur(2px);
    }
    .upper-more-highlight {
      color: #ffa006;
      font-weight: 700;
    }
    .hero-model-glow {
      position: absolute;
      width: 713.44px;
      height: 723.77px;
      right: -110px;
      top: 10px;
      border-radius: 9999px;
      background: #370665;
      filter: blur(300px);
      opacity: 0.82;
      pointer-events: none;
      z-index: 20;
    }
    .about-us-figma { background: #fff; }
    .about-us-visual {
      position: relative;
      min-height: 520px;
      isolation: isolate;
      
    }
   
    .about-us-main-image {
      position: absolute;
      left: 15px;
      bottom: 0;
      width: min(560px, 90%);
      max-height: 620px;
      object-fit: contain;
      z-index: 1;
    }
   
    .about-us-pill {
      display: inline-flex;
      align-items: center;
      border: 1px solid #eee;
      border-radius: 4px;
      padding: 7px 16px;
      font-family: 'Manrope', sans-serif;
      font-size: 14px;
      font-weight: 700;
      color: #a46bc1;
    }
    .about-us-heading {
      margin-top: 14px;
      font-family: 'Poppins', sans-serif;
      font-size: 52px;
      line-height: 1.08;
      font-weight: 700;
      letter-spacing: -1.5px;
      color: #000a2d;
    }
    .about-us-copy {
      margin-top: 18px;
      max-width: 484px;
      font-family: 'Poppins', sans-serif;
      font-size: 18px;
      line-height: 1.45;
      color: #636571;
    }
    .about-us-action-primary {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 17px 36px 18px;
      border-radius: 6px;
      background: #a46bc1;
      color: #fff;
      font-family: 'Manrope', sans-serif;
      font-size: 15px;
      font-weight: 700;
    }
    .about-us-action-secondary {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 17px 37px 18px;
      border-radius: 6px;
      border: 1px solid rgba(152, 179, 255, 0.23);
      background: rgba(164, 107, 193, 0.07);
      color: #a46bc1;
      font-family: 'Manrope', sans-serif;
      font-size: 15px;
      font-weight: 700;
    }
    @media (max-width: 1023px) {
      .about-us-visual {
        min-height: 460px;
        margin-bottom: 12px;
      }
      .about-us-main-image {
        left: 50%;
        transform: translateX(-50%);
      }
      
      .about-us-heading {
        font-size: 42px;
      }
    }
    @media (max-width: 640px) {
      .about-us-visual {
        min-height: 410px;
      }
      .about-us-main-image {
        width: min(390px, 90%);
        max-height: 460px;
      }
     
     
    }
    .clients-say-section {
      background: #ffffff;
    }
    .clients-say-title {
      font-family: 'Poppins', sans-serif;
      font-weight: 700;
      font-size: 36px;
      line-height: 1.15;
      color: #0d0d14;
      text-align: center;
    }
    .clients-say-subtitle {
      max-width: 640px;
      margin: 14px auto 0;
      font-family: 'Poppins', sans-serif;
      font-weight: 400;
      font-size: 18px;
      line-height: 1.5;
      color: #4b496a;
      text-align: center;
    }
    .clients-card {
      border: 1px solid #d4d2e3;
      border-radius: 20px;
      background: #fff;
      padding: 40px 34px 28px;
      min-height: 519px;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
    }
    .clients-avatar {
      width: 194.81px;
      height: 194.81px;
      border-radius: 9999px;
      object-fit: cover;
    }
    .clients-name {
      margin-top: 30px;
      font-family: 'DM Sans', sans-serif;
      font-weight: 700;
      font-size: 28px;
      line-height: 1.2;
      color: #0a090f;
    }
    .clients-quote {
      margin-top: 14px;
      font-family: 'Poppins', sans-serif;
      font-weight: 400;
      font-size: 18px;
      line-height: 1.67;
      color: #4b496a;
    }
    .clients-icons {
      margin-top: auto;
      display: flex;
      gap: 16px;
      padding-top: 22px;
    }
    .clients-icon {
      width: 36px;
      height: 36px;
      border-radius: 8px;
      background: #f2f1fa;
      color: #4b496a;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }
    @media (max-width: 1023px) {
      .clients-say-title {
        font-size: 32px;
      }
      .clients-card {
        min-height: auto;
      }
      .clients-name {
        font-size: 24px;
      }
      .clients-quote {
        font-size: 17px;
      }
    }
    @media (max-width: 640px) {
      .clients-say-title {
        font-size: 28px;
      }
      .clients-say-subtitle {
        font-size: 15px;
      }
      .clients-avatar {
        width: 164px;
        height: 164px;
      }
      .clients-name {
        font-size: 22px;
      }
      .clients-quote {
        font-size: 16px;
      }
    }
    .benefits-themed {
      background: rgba(128, 40, 168, 0.66);
      color: #ffffff;
    }
    .benefits-themed-title {
      color: #ffffff;
      font-family: 'Poppins', sans-serif;
      font-weight: 700;
    }
    .benefits-themed-list-text {
      color: rgba(255, 255, 255, 0.86);
    }
    .benefits-themed-check {
      color: #fbbf24;
      flex-shrink: 0;
    }
    .benefits-themed-glow {
      background: var(--primary-light);
    }
    .benefits-themed-float {
      background: rgba(255, 255, 255, 0.92);
      border: 1px solid rgba(248, 233, 255, 0.65);
      color: #2a2140;
    }
    .pricing-themed {
      background: #ffffff;
    }
    .pricing-themed-title {
      color: #0d0d14;
    }
    .pricing-themed-desc {
      color: #4b496a;
    }
    .pricing-toggle-wrap {
      background: rgba(255, 255, 255, 0.35);
      border: 1px solid rgba(68, 41, 80, 0.35);
    }
    .pricing-toggle-active {
      background: var(--primary);
      color: #ffffff;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }
    .pricing-toggle-inactive {
      color: var(--primary-hover);
    }
    .pricing-card-themed {
      border: 1px solid #e5e7eb;
      background: #ffffff;
      transition: background-color .25s ease, color .25s ease;
    }
    .pricing-card-themed:hover {
      background: var(--primary);
      border-color: #e5e7eb;
    }
    .pricing-card-themed:hover h3,
    .pricing-card-themed:hover p,
    .pricing-card-themed:hover li span {
      color: rgba(255, 255, 255, 0.92) !important;
    }
    .pricing-card-themed:hover .mt-6 > span {
      color: #ffffff !important;
    }
    .pricing-card-themed:hover svg[stroke="#d9657a"] {
      stroke: #ffffff;
    }
    [dir="rtl"] .pricing-themed .pricing-card-themed,
    [dir="rtl"] .pricing-themed .pricing-card-themed-highlight,
    html[lang="ar"] .pricing-themed .pricing-card-themed,
    html[lang="ar"] .pricing-themed .pricing-card-themed-highlight {
      direction: rtl;
      text-align: right;
    }
    [dir="rtl"] .pricing-themed .pricing-card-themed li,
    [dir="rtl"] .pricing-themed .pricing-card-themed-highlight li,
    html[lang="ar"] .pricing-themed .pricing-card-themed li,
    html[lang="ar"] .pricing-themed .pricing-card-themed-highlight li {
      flex-direction: row-reverse;
      justify-content: flex-end;
    }
    [dir="rtl"] .pricing-themed .pricing-card-themed button,
    [dir="rtl"] .pricing-themed .pricing-card-themed-highlight button,
    html[lang="ar"] .pricing-themed .pricing-card-themed button,
    html[lang="ar"] .pricing-themed .pricing-card-themed-highlight button {
      text-align: center;
    }
    .contact-v20-section {
      background: #ffffff;
    }
    .contact-v20-subtitle {
      font-family: 'Poppins', sans-serif;
      font-style: normal;
      font-weight: 700;
      font-size: 16px;
      line-height: 20px;
      text-align: left;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      color: #4b496a;
    }
    .contact-v20-title {
      margin-top: 18px;
      max-width: 460px;
      font-family: 'Poppins', sans-serif;
      font-style: normal;
      font-weight: 700;
      font-size: 44px;
      line-height: 1.14;
      color: #080710;
    }
    .contact-v20-copy {
      margin-top: 16px;
      max-width: 500px;
      font-family: 'Poppins', sans-serif;
      font-style: normal;
      font-weight: 400;
      font-size: 20px;
      line-height: 1.67;
      color: #4b496a;
    }
    .contact-v20-form {
      margin-top: 34px;
      max-width: 416px;
      display: flex;
      flex-direction: column;
      gap: 16px;
    }
    .contact-v20-input,
    .contact-v20-textarea {
      width: 100%;
      border: 0;
      background: #f9f9ff;
      color: #4b496a;
      font-family: 'DM Sans', sans-serif;
      font-size: 16px;
      line-height: 1.12;
      outline: none;
    }
    .contact-v20-input {
      border-radius: 9999px;
      height: 50px;
      padding: 0 24px;
    }
    .contact-v20-textarea {
      border-radius: 20px;
      min-height: 115px;
      padding: 16px 24px;
      resize: none;
    }
    .contact-v20-input::placeholder,
    .contact-v20-textarea::placeholder {
      color: #4b496a;
      opacity: 1;
    }
    .contact-v20-btn {
      margin-top: 12px;
      width: 169px;
      height: 54px;
      border: 0;
      border-radius: 6px;
      background: #a46bc1;
      color: #fff;
      font-family: 'Poppins', sans-serif;
      font-weight: 700;
      font-size: 15px;
      line-height: 18px;
      text-align: center;
      cursor: pointer;
      transition: opacity .2s ease, transform .2s ease;
    }
    .contact-v20-btn:hover {
      opacity: .92;
      transform: translateY(-1px);
    }
    .contact-v20-visual {
      position: relative;
      min-height: 620px;
      display: flex;
      align-items: center;
      justify-content: flex-end;
      isolation: isolate;
    }
    .contact-v20-strip {
      position: absolute;
      right: 0;
      top: 0;
      bottom: 0;
      width: 36%;
      border-top-left-radius: 20px;
      border-bottom-left-radius: 20px;
      background:
        repeating-linear-gradient(
          90deg,
          #1f0f09 0px,
          #1f0f09 5px,
          #442013 5px,
          #442013 9px,
          #130804 9px,
          #130804 13px
        );
      z-index: 0;
    }
    .contact-v20-image-wrap {
      position: relative;
      width: min(618px, 100%);
      border-radius: 30px;
      overflow: hidden;
      z-index: 1;
    }
    .contact-v20-image {
      display: block;
      width: 100%;
      aspect-ratio: 618.61 / 567.67;
      object-fit: cover;
    }
    @media (max-width: 1279px) {
      .contact-v20-title {
        font-size: 40px;
      }
      .contact-v20-copy {
        font-size: 18px;
      }
      .contact-v20-input,
      .contact-v20-textarea {
        font-size: 16px;
      }
    }
    @media (max-width: 1023px) {
      .contact-v20-title {
        font-size: 34px;
      }
      .contact-v20-copy {
        font-size: 17px;
      }
      .contact-v20-form {
        max-width: 100%;
      }
      .contact-v20-input,
      .contact-v20-textarea {
        font-size: 15px;
      }
      .contact-v20-visual {
        min-height: 420px;
        justify-content: center;
      }
      .contact-v20-strip {
        width: 30%;
        border-radius: 20px;
      }
    }
    @media (max-width: 640px) {
      .contact-v20-title {
        font-size: 30px;
      }
      .contact-v20-copy {
        font-size: 16px;
      }
      .contact-v20-input,
      .contact-v20-textarea {
        font-size: 14px;
      }
      .contact-v20-btn {
        width: 100%;
      }
      .contact-v20-visual {
        min-height: 320px;
      }
      .contact-v20-strip {
        width: 24%;
      }
    }
    .demo-menu-wrap {
      position: relative;
      display: inline-flex;
      align-items: center;
    }
    .demo-trigger {
      transition: color .25s ease, transform .25s ease;
    }
    .demo-menu-panel {
      position: absolute;
      top: calc(100% + 12px);
      left: 0;
      z-index: 60;
      width: min(92vw, 360px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 14px;
      background: rgba(25, 7, 42, 0.95);
      box-shadow: 0 18px 40px rgba(0, 0, 0, 0.35);
      padding: 10px;
      opacity: 0;
      transform: translateY(10px);
      pointer-events: none;
      transition: opacity .2s ease, transform .2s ease;
    }
    .demo-menu-wrap.is-open .demo-menu-panel {
      opacity: 1;
      transform: translateY(0);
      pointer-events: auto;
    }
    .demo-menu-card {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 8px;
      border-radius: 12px;
      text-decoration: none;
      color: #fff;
      transition: transform .2s ease, background-color .2s ease;
    }
    .demo-menu-card + .demo-menu-card {
      margin-top: 6px;
    }
    .demo-menu-card:hover {
      background: rgba(255, 160, 6, 0.2);
      transform: translateY(-1px);
    }
    .demo-menu-thumb {
      width: 56px;
      height: 56px;
      border-radius: 10px;
      object-fit: cover;
      flex-shrink: 0;
    }
    .demo-menu-title {
      display: block;
      font-size: 13px;
      font-weight: 700;
      color: #fff;
      line-height: 1.2;
    }
    .demo-menu-subtitle {
      display: block;
      margin-top: 3px;
      font-size: 12px;
      color: rgba(255, 255, 255, 0.72);
      line-height: 1.2;
    }
    .upper-shell .language-switcher .language-toggle {
      border: 2px solid #ffa006;
      background: rgba(42, 3, 82, 0.5);
      color: #ffffff;
      padding:18px;
      border-radius: 8px;
      box-shadow: 0 0 18px rgba(226, 142, 8, 0.28);
    }
    .upper-shell .language-switcher .language-toggle:hover {
      background: #ffa006;
      color: #ffffff;
    }
    .upper-shell .language-switcher .language-toggle svg {
      color: rgba(255, 255, 255, 0.88);
    }
    .upper-shell .language-switcher .language-menu {
      border: 1px solid rgba(255, 255, 255, 0.15);
      background: rgba(25, 7, 42, 0.95);
      box-shadow: 0 18px 40px rgba(0, 0, 0, 0.35);
      border-radius: 12px;
    }
    .upper-shell .language-switcher .language-option {
      color: rgba(255, 255, 255, 0.92);
    }
    .upper-shell .language-switcher .language-option:hover {
      background: rgba(255, 160, 6, 0.2);
      color: #ffffff;
    }
    .upper-shell .language-switcher .language-option.active {
      background: rgba(255, 160, 6, 0.24);
      color: #ffffff;
    }
    .upper-shell .language-switcher .language-option.active .text-pink-600 {
      color: #ffa006;
    }
    @media (max-width: 768px) {
    
    .upper-shell .language-switcher .language-toggle {

      padding:10px;
    }
    }
  </style>
  @include('partials.public-shell-styles')
</head>
<body class="bg-white antialiased">
  @php
    $isAuthenticated = auth()->check();
    $getStartedUrl = $isAuthenticated ? url('/dashboard') : route('register');
  @endphp

  <section class="upper-shell relative isolate overflow-hidden pb-24 sm:pb-24">
    @include('partials.public-navbar', ['publicNavActive' => 'home'])

    <!-- HERO -->
    <div class="relative mx-auto w-full max-w-[1260px] px-5 pb-0 pt-0 sm:px-6 lg:-mt-4 lg:px-8">
      <div class="grid items-start gap-8 pt-14 lg:grid-cols-[1fr_0.95fr] lg:gap-10 lg:pb-0">
        <div class="pb-8 pt-4 lg:pb-20 lg:pt-6">
          <h1 class="upper-hero-title max-w-[620px] text-5xl font-bold leading-[1.15] text-white sm:text-6xl lg:text-[74px]">
            {!! __('messages.gogo_hero_title') !!}
          </h1>
          <p class="mt-7 max-w-[700px] text-lg leading-9 text-white/70 sm:text-2xl sm:leading-10">
            {{ __('messages.gogo_hero_description') }}
          </p>
          <div class="mt-10 flex flex-wrap items-center gap-4 sm:gap-7">
            <a
              href="{{ $isAuthenticated ? $getStartedUrl : route('register') }}"
              class="upper-cta-primary inline-flex min-w-[220px] justify-center px-10 py-4 text-xl font-bold tracking-wide"
            >
              {{ $isAuthenticated ? __('messages.go_to_dashboard') : __('messages.gogo_start_now') }}
            </a>
            <div class="demo-menu-wrap" id="demo-menu-wrap">
              <button type="button" id="demo-trigger" class="demo-trigger upper-cta-secondary inline-flex min-w-[220px] items-center justify-center px-10 py-4 text-xl font-bold tracking-wide" aria-haspopup="true" aria-expanded="false">
                {{ __('messages.gogo_view_demo') }}
              </button>
              <div class="demo-menu-panel" id="demo-menu-panel" role="menu" aria-label="Demo pages">
                <a href="{{ route('aboutmerchant') }}" class="demo-menu-card" role="menuitem">
                  <img src="{{ asset('assets/traderHero.webp') }}" alt="Merchant Hero" class="demo-menu-thumb" />
                  <span>
                    <span class="demo-menu-title">{{ __('messages.merchant') }}</span>
                    <span class="demo-menu-subtitle">{{ __('messages.aboutmerchantdemo') }}</span>
                  </span>
                </a>
                <a href="{{ route('aboutvendor') }}" class="demo-menu-card" role="menuitem">
                  <img src="{{ asset('assets/vendorHero.webp') }}" alt="Vendor Hero" class="demo-menu-thumb" />
                  <span>
                    <span class="demo-menu-title">{{ __('messages.company') }}</span>
                    <span class="demo-menu-subtitle">{{ __('messages.aboutcompanydemo') }}</span>
                  </span>
                </a>
              </div>
            </div>
          </div>
        </div>
        <div class="relative flex min-h-[440px] items-end justify-end sm:min-h-[520px] lg:min-h-[640px]">
          <div class="pointer-events-none absolute -bottom-1 right-0 h-[calc(100%+6rem)] w-full max-w-[680px]">
            <div class="hero-model-glow"></div>
            <img src="{{ asset('assets/heroModel.webp') }}" alt="{{ __('messages.gogo_alt_professional_businesswoman') }}" class="relative z-10 h-[130%] w-[130%] object-contain object-bottom sm:object-cover lg:object-contain" />
          </div>
        </div>
      </div>
    </div>

    <div class="upper-emirates-strip absolute inset-x-0 bottom-0 z-30 mx-auto w-full px-5 py-8 sm:px-6 lg:px-8">
      <div class="mx-auto max-w-[1260px] text-center">
        @php
          $gogoStoresText = __('messages.gogo_more_than_stores');
          $gogoStoresTextHighlighted = preg_replace('/25[,\\. ]?000/', '<span class="upper-more-highlight">$0</span>', e($gogoStoresText), 1);
        @endphp
        <p class="text-sm font-semibold tracking-wide text-white sm:text-base">
          {!! $gogoStoresTextHighlighted !!}
        </p>
        <div class="mt-6 flex flex-wrap items-center justify-center gap-x-7 gap-y-4 text-sm font-semibold text-white/90 sm:text-base">
          <span>{{ __('messages.gogo_city_dubai') }}</span>
          <span>{{ __('messages.gogo_city_ajman') }}</span>
          <span>{{ __('messages.gogo_city_sharjah') }}</span>
          <span>{{ __('messages.gogo_city_abu_dhabi') }}</span>
          <span>{{ __('messages.gogo_city_um_al_quwain') }}</span>
          <span>{{ __('messages.gogo_city_fujairah') }}</span>
          <span>{{ __('messages.gogo_city_ras_al_khaimah') }}</span>
        </div>
      </div>
    </div>
  </section>

  <!-- ABOUT US SECTION -->
  <section class="about-us-figma py-16 sm:py-20">
    <div class="mx-auto max-w-[1320px] px-6">
      <div class="grid items-center gap-10 lg:grid-cols-[1.2fr_1fr]">
        <div class="about-us-visual">
          <div class="about-us-shape"></div>
          <img src="{{ asset('assets/aboutUS.png') }}" alt="About us visual" class="about-us-main-image" />
        </div>

        <div class="lg:pl-2">
          <span class="about-us-pill">More About Us</span>
          <h2 class="about-us-heading">About Us</h2>
          <p class="about-us-copy">
            This is a Paragraph. Click on "Edit Text" or double click on the text box to start editing the content and make sure to add any relevant details or information that you want to share with your visitors.
          </p>
          <p class="about-us-copy mt-4">
            Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh.
          </p>
          <div class="mt-10 flex flex-wrap items-center gap-5">
            <a href="{{ route('faq') }}" class="about-us-action-primary">Learn More</a>
            <a href="{{ route('register') }}" class="about-us-action-secondary">Create Account</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- SUPPORT SECTION -->
  <section class="bg-white py-20">
    <div class="mx-auto max-w-7xl px-6">
      <div class="grid gap-12 lg:grid-cols-2">
        <div>
          <h2 class="font-serif text-3xl leading-tight text-brand-dark md:text-4xl">
            {!! __('messages.gogo_support_title') !!}
          </h2>
          <p class="mt-6 text-sm leading-relaxed text-gray-500">
            {{ __('messages.gogo_support_description') }}
          </p>
          <div class="mt-8 flex gap-10">
            <div>
              <div class="flex gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              </div>
              <p class="mt-1 text-sm font-semibold text-brand-dark">4.9 / 5 rating</p>
              <p class="text-xs text-gray-500">{{ __('messages.gogo_trustpilots') }}</p>
            </div>
            <div>
              <div class="flex gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              </div>
              <p class="mt-1 text-sm font-semibold text-brand-dark">4.8 / 5 rating</p>
              <p class="text-xs text-gray-500">{{ __('messages.gogo_clutchanlytica') }}</p>
            </div>
          </div>
        </div>
        <div class="flex flex-col gap-8">
          <div class="flex gap-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand-pink-bg">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
            </div>
            <div>
              <h3 class="text-base font-semibold text-brand-dark">{{ __('messages.gogo_business_operations_hub') }}</h3>
              <p class="mt-1 text-sm text-gray-500">{{ __('messages.gogo_business_operations_hub_description') }}</p>
            </div>
          </div>
          <div class="flex gap-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand-pink-bg">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div>
              <h3 class="text-base font-semibold text-brand-dark">{{ __('messages.gogo_commerce_revenue_control') }}</h3>
              <p class="mt-1 text-sm text-gray-500">{{ __('messages.gogo_commerce_revenue_control_description') }}</p>
            </div>
          </div>
          <div class="flex gap-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand-pink-bg">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div>
              <h3 class="text-base font-semibold text-brand-dark">{{ __('messages.gogo_engagement') }}</h3>
              <p class="mt-1 text-sm text-gray-500">{{ __('messages.gogo_engagement_description') }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FEATURES -->
  <section class="bg-white py-20">
    <div class="mx-auto max-w-7xl px-6">
      <div class="flex flex-col items-start justify-between gap-6 md:flex-row md:items-end">
        <h2 class="font-serif text-3xl leading-tight text-brand-dark md:text-4xl">{!! __('messages.gogo_features_title') !!}</h2>
        <div class="max-w-md">
          <p class="text-sm leading-relaxed text-gray-500">{{ __('messages.gogo_features_description') }}</p>
          <button class="mt-4 rounded-full bg-brand-pink px-6 py-2.5 text-sm font-medium text-white transition-opacity hover:opacity-90">{{ __('messages.gogo_get_started') }}</button>
        </div>
      </div>
      <div class="mt-14 grid gap-6 md:grid-cols-3">
        <div class="relative overflow-hidden rounded-2xl bg-[#FAA8BF]">
          <img src="{{asset('assets/FD.jpg')}}" alt="{{ __('messages.gogo_alt_free_delivery_feature') }}" width="400" height="250" class="h-48 w-full object-cover" />
        </div>
        <div class="overflow-hidden rounded-2xl">
          <div class="relative">
            <div class="absolute right-4 top-4 z-10 flex gap-2">
              <div class="rounded-lg bg-white/90 px-3 py-1.5 text-xs font-semibold text-brand-dark backdrop-blur-sm">86%</div>
              <div class="rounded-lg bg-white/90 px-3 py-1.5 text-xs font-semibold text-brand-dark backdrop-blur-sm">44%</div>
            </div>
            <img src="{{asset('assets/RC.jpg')}}" alt="{{ __('messages.gogo_alt_resolution_center_feature') }}" width="400" height="300" class="h-64 w-full rounded-2xl object-cover" />
          </div>
        </div>
        <div class="overflow-hidden rounded-2xl">
          <img src="{{asset('assets/DA.jpg')}}" alt="{{ __('messages.gogo_alt_daily_analytics_feature') }}" width="400" height="300" class="h-64 w-full rounded-2xl object-cover" />
        </div>
      </div>
      <div class="mt-8 grid gap-6 md:grid-cols-3">
        <div>
          <h3 class="text-lg font-semibold text-brand-dark">{{ __('messages.gogo_free_delivery') }}</h3>
          <p class="mt-2 text-sm text-gray-500">{{ __('messages.gogo_free_delivery_description') }}</p>
        </div>
        <div>
          <h3 class="text-lg font-semibold text-brand-dark">{{ __('messages.gogo_resolution_center') }}</h3>
          <p class="mt-2 text-sm text-gray-500">{{ __('messages.gogo_resolution_center_description') }}</p>
        </div>
        <div>
          <h3 class="text-lg font-semibold text-brand-dark">{{ __('messages.gogo_daily_analytics') }}</h3>
          <p class="mt-2 text-sm text-gray-500">{{ __('messages.gogo_daily_analytics_description') }}</p>
        </div>
      </div>
    </div>
  </section>

  <!-- BENEFITS -->
  <section class="benefits-themed py-20">
    <div class="mx-auto max-w-7xl px-6">
      <div class="grid items-center gap-12 lg:grid-cols-2">
        <div>
          <h2 class="benefits-themed-title text-4xl leading-tight md:text-5xl">{!! __('messages.gogo_benefits_title') !!}</h2>
          <div class="mt-8 flex flex-col gap-5">
            <div class="flex items-center gap-3">
              <svg class="benefits-themed-check" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              <span class="benefits-themed-list-text text-base md:text-lg">{{ __('messages.gogo_all_in_one_business_dashboard') }}</span>
            </div>
            <div class="flex items-center gap-3">
              <svg class="benefits-themed-check" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              <span class="benefits-themed-list-text text-base md:text-lg">{{ __('messages.gogo_smart_order_booking_management') }}</span>
            </div>
            <div class="flex items-center gap-3">
              <svg class="benefits-themed-check" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              <span class="benefits-themed-list-text text-base md:text-lg">{{ __('messages.gogo_business_insights_reports') }}</span>
            </div>
            <div class="flex items-center gap-3">
              <svg class="benefits-themed-check" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              <span class="benefits-themed-list-text text-base md:text-lg">{{ __('messages.gogo_secure_payments_payout_tracking') }}</span>
            </div>
            <div class="flex items-center gap-3">
              <svg class="benefits-themed-check" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              <span class="benefits-themed-list-text text-base md:text-lg">{{ __('messages.gogo_online_transaction') }}</span>
            </div>
          </div>
        </div>
        <div class="relative flex justify-center">
          <div class="benefits-themed-glow absolute left-1/2 top-1/2 h-80 w-80 -translate-x-1/2 -translate-y-1/2 rounded-full opacity-50 blur-3xl"></div>
          <div class="relative z-10 w-full max-w-sm">
            <div class="benefits-themed-float absolute -left-4 top-8 z-20 rounded-xl p-3 shadow-lg">
              <div class="flex items-center gap-2">
                <img src="{{ asset('assets/prof.png') }}" alt="Profile" class="h-12 w-12 rounded-full object-cover" />
                <div>
                  <p class="text-xs font-semibold text-brand-dark">{{ __('messages.gogo_amanda_young') }}</p>
                  <p class="text-[10px] text-gray-500">{{ __('messages.gogo_transfer') }}</p>
                </div>
              </div>
            </div>
            <div class="benefits-themed-float absolute -right-2 top-20 z-20 rounded-xl px-4 py-2 shadow-lg">
              <span class="text-sm font-semibold text-brand-dark">245.00 AED</span>
            </div>
            <img src="{{ asset('assets/mobileHome.jpg') }}" alt="{{ __('messages.gogo_alt_money_transfer_mockup') }}" width="350" height="500" class="mx-auto h-auto w-64 rounded-3xl object-cover shadow-2xl" />
            <div class="benefits-themed-float absolute -right-6 bottom-16 z-20 rounded-xl px-4 py-2 shadow-lg">
              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span class="text-xs font-medium text-brand-dark">{{ __('messages.gogo_money_transfer_successful') }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- OUR CLIENTS SECTION -->
  <section class="clients-say-section py-20 sm:py-24">
    <div class="mx-auto max-w-[1320px] px-6">
      <h2 class="clients-say-title">{{ __('messages.gogo_clients_title') }}</h2>
      <p class="clients-say-subtitle">
        {{ __('messages.gogo_clients_subtitle') }}
      </p>

      <div class="mt-14 grid gap-6 lg:grid-cols-3">
        <article class="clients-card">
          <img src="{{ asset('assets/c1.png') }}" alt="John Carter" class="clients-avatar" />
          <h3 class="clients-name">John Carter</h3>
          <p class="clients-quote">
            Our living room was completely transformed! The team captured our vision perfectly and exceeded our expectations.
          </p>
          <div class="clients-icons">
            <span class="clients-icon" aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M4 5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v.4l-8 5-8-5V5zm0 3.2V19a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8.2l-8 5-8-5z"/></svg>
            </span>
            <span class="clients-icon" aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M6.6 10.8a15.7 15.7 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.24c1.1.36 2.29.56 3.52.56a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1C11.4 21 3 12.6 3 2a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.23.2 2.42.56 3.52a1 1 0 0 1-.24 1l-2.22 2.28z"/></svg>
            </span>
            <span class="clients-icon" aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 10 10A10.01 10.01 0 0 0 12 2zm7.93 9h-3.06a15.7 15.7 0 0 0-1.05-4.62A8.03 8.03 0 0 1 19.93 11zM12 4c.94 1.36 1.66 3.22 2.03 5H9.97C10.34 7.22 11.06 5.36 12 4zM6.18 6.38A15.7 15.7 0 0 0 5.13 11H2.07a8.03 8.03 0 0 1 4.11-4.62zM2.07 13h3.06c.2 1.62.56 3.2 1.05 4.62A8.03 8.03 0 0 1 2.07 13zM12 20c-.94-1.36-1.66-3.22-2.03-5h4.06c-.37 1.78-1.09 3.64-2.03 5zm2.47-7h-4.94c-.12-.98-.18-1.99-.18-3s.06-2.02.18-3h4.94c.12.98.18 1.99.18 3s-.06 2.02-.18 3zm1.35 4.62c.49-1.42.85-3 1.05-4.62h3.06a8.03 8.03 0 0 1-4.11 4.62z"/></svg>
            </span>
          </div>
        </article>

        <article class="clients-card">
          <img src="{{ asset('assets/c2.png') }}" alt="Sophie Moore" class="clients-avatar" />
          <h3 class="clients-name">Sophie Moore</h3>
          <p class="clients-quote">
            Professional and creative! The design process was smooth, and the results are stunning. Highly recommend their services
          </p>
          <div class="clients-icons">
            <span class="clients-icon" aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M4 5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v.4l-8 5-8-5V5zm0 3.2V19a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8.2l-8 5-8-5z"/></svg>
            </span>
            <span class="clients-icon" aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M6.6 10.8a15.7 15.7 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.24c1.1.36 2.29.56 3.52.56a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1C11.4 21 3 12.6 3 2a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.23.2 2.42.56 3.52a1 1 0 0 1-.24 1l-2.22 2.28z"/></svg>
            </span>
            <span class="clients-icon" aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 10 10A10.01 10.01 0 0 0 12 2zm7.93 9h-3.06a15.7 15.7 0 0 0-1.05-4.62A8.03 8.03 0 0 1 19.93 11zM12 4c.94 1.36 1.66 3.22 2.03 5H9.97C10.34 7.22 11.06 5.36 12 4zM6.18 6.38A15.7 15.7 0 0 0 5.13 11H2.07a8.03 8.03 0 0 1 4.11-4.62zM2.07 13h3.06c.2 1.62.56 3.2 1.05 4.62A8.03 8.03 0 0 1 2.07 13zM12 20c-.94-1.36-1.66-3.22-2.03-5h4.06c-.37 1.78-1.09 3.64-2.03 5zm2.47-7h-4.94c-.12-.98-.18-1.99-.18-3s.06-2.02.18-3h4.94c.12.98.18 1.99.18 3s-.06 2.02-.18 3zm1.35 4.62c.49-1.42.85-3 1.05-4.62h3.06a8.03 8.03 0 0 1-4.11 4.62z"/></svg>
            </span>
          </div>
        </article>

        <article class="clients-card">
          <img src="{{ asset('assets/c3.png') }}" alt="Matt Cannon" class="clients-avatar" />
          <h3 class="clients-name">Matt Cannon</h3>
          <p class="clients-quote">
            Their attention to detail and commitment to quality turned our house into a home we love. Outstanding work!
          </p>
          <div class="clients-icons">
            <span class="clients-icon" aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M4 5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v.4l-8 5-8-5V5zm0 3.2V19a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8.2l-8 5-8-5z"/></svg>
            </span>
            <span class="clients-icon" aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M6.6 10.8a15.7 15.7 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.24c1.1.36 2.29.56 3.52.56a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1C11.4 21 3 12.6 3 2a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.23.2 2.42.56 3.52a1 1 0 0 1-.24 1l-2.22 2.28z"/></svg>
            </span>
            <span class="clients-icon" aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 10 10A10.01 10.01 0 0 0 12 2zm7.93 9h-3.06a15.7 15.7 0 0 0-1.05-4.62A8.03 8.03 0 0 1 19.93 11zM12 4c.94 1.36 1.66 3.22 2.03 5H9.97C10.34 7.22 11.06 5.36 12 4zM6.18 6.38A15.7 15.7 0 0 0 5.13 11H2.07a8.03 8.03 0 0 1 4.11-4.62zM2.07 13h3.06c.2 1.62.56 3.2 1.05 4.62A8.03 8.03 0 0 1 2.07 13zM12 20c-.94-1.36-1.66-3.22-2.03-5h4.06c-.37 1.78-1.09 3.64-2.03 5zm2.47-7h-4.94c-.12-.98-.18-1.99-.18-3s.06-2.02.18-3h4.94c.12.98.18 1.99.18 3s-.06 2.02-.18 3zm1.35 4.62c.49-1.42.85-3 1.05-4.62h3.06a8.03 8.03 0 0 1-4.11 4.62z"/></svg>
            </span>
          </div>
        </article>
      </div>
    </div>
  </section>

  <!-- PRICING -->
  <section class="pricing-themed py-20">
    <div class="mx-auto max-w-7xl px-6">
      <div class="text-center">
        <h2 class="pricing-themed-title font-serif text-3xl leading-tight md:text-4xl">{!! __('messages.gogo_choose_plan_title') !!}</h2>
        <p class="pricing-themed-desc mt-4 text-sm">{{ __('messages.gogo_choose_plan_description') }}</p>
        <div class="pricing-toggle-wrap mt-6 inline-flex items-center rounded-full p-1">
          <button id="btn-monthly" class="pricing-toggle-inactive rounded-full px-5 py-2 text-sm font-medium" onclick="togglePricing('monthly')">{{ __('messages.gogo_bill_monthly') }}</button>
          <button id="btn-yearly" class="pricing-toggle-active rounded-full px-5 py-2 text-sm font-medium" onclick="togglePricing('yearly')">{{ __('messages.gogo_bill_yearly') }}</button>
        </div>
      </div>
      <div class="mt-12 grid gap-6 md:grid-cols-3">
        <!-- Merchant -->
        <div class="pricing-card-themed rounded-2xl p-8">
          <h3 class="text-xl font-semibold text-brand-dark">{{ __('messages.merchant_registration') }}</h3>
          <p class="mt-2 text-sm text-gray-500">{{ __('messages.merchant_registration_desc') }}</p>
          <div class="mt-6">
            <span class="text-4xl font-bold text-brand-dark">99 AED</span>
            <p class="mt-1 text-sm text-gray-500">{{ __('messages.gogo_bill_monthly') }}</p>
          </div>
          <ul class="mt-8 flex flex-col gap-4">
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">{{ __('messages.individual_business_setup') }}</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">{{ __('messages.direct_customer_sales') }}</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">{{ __('messages.flexible_delivery_options') }}</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">{{ __('messages.small_store_management') }}</span>
            </li>
          </ul>
          <button class="mt-8 w-full rounded-full border border-gray-200 py-3 text-sm font-medium text-brand-dark transition-colors hover:bg-white">{{ __('messages.register_as_merchant') }}</button>
        </div>
        <!-- Vendor -->
        <div class="pricing-card-themed relative overflow-hidden rounded-2xl p-8">
          <div class="absolute right-4 top-4 rounded-full bg-[color:var(--towhite)] px-3 py-1 text-xs font-medium text-[color:var(--primary)]">{{ __('messages.gogo_save_63_a_year') }}</div>
          <h3 class="text-xl font-semibold text-brand-dark">{{ __('messages.vendor_registration') }}</h3>
          <p class="mt-2 text-sm text-gray-500">{{ __('messages.vendor_registration_desc') }}</p>
          <div class="mt-6">
            <span class="text-4xl font-bold text-brand-dark">99 AED</span>
            <p class="mt-1 text-sm text-gray-500">{{ __('messages.gogo_bill_monthly') }}</p>
          </div>
          <ul class="mt-8 flex flex-col gap-4">
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">{{ __('messages.product_catalog_management') }}</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">{{ __('messages.inventory_tracking_analytics') }}</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">{{ __('messages.order_management_system') }}</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">{{ __('messages.multi_channel_delivery') }}</span>
            </li>
          </ul>
          <button class="mt-8 w-full rounded-full border border-gray-200 py-3 text-sm font-medium text-brand-dark transition-colors hover:bg-white">{{ __('messages.register_as_vendor') }}</button>
        </div>
        <!-- Provider -->
        <div class="pricing-card-themed rounded-2xl p-8">
          <h3 class="text-xl font-semibold text-brand-dark">{{ __('messages.provider_registration') }}</h3>
          <p class="mt-2 text-sm text-gray-500">{{ __('messages.provider_registration_desc') }}</p>
          <div class="mt-6">
            <span class="text-4xl font-bold text-brand-dark">99 AED</span>
            <p class="mt-1 text-sm text-gray-500">{{ __('messages.gogo_bill_monthly') }}</p>
          </div>
          <ul class="mt-8 flex flex-col gap-4">
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">{{ __('messages.wholesale_product_catalog') }}</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">{{ __('messages.bulk_order_management') }}</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">{{ __('messages.vendor_relationship_management') }}</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">{{ __('messages.supply_chain_tracking') }}</span>
            </li>
          </ul>
          <button class="mt-8 w-full rounded-full border border-gray-200 py-3 text-sm font-medium text-brand-dark transition-colors hover:bg-white">{{ __('messages.register_as_provider') }}</button>
        </div>
      </div>
    </div>
  </section>

  <!-- CONTACT V20 SECTION -->
  <section class="contact-v20-section py-16 sm:py-20">
    <div class="mx-auto max-w-[1320px] px-6">
      <div class="grid items-start gap-12 lg:grid-cols-[1fr_1.15fr]">
        <div class="pt-4">
          <p class="contact-v20-subtitle">{{ __('messages.gogo_contact_v20_subtitle') }}</p>
          <h2 class="contact-v20-title">{{ __('messages.gogo_contact_v20_title') }}</h2>
          <p class="contact-v20-copy">
            {{ __('messages.gogo_contact_v20_copy') }}
          </p>

          <form class="contact-v20-form" onsubmit="event.preventDefault()">
            <input type="text" class="contact-v20-input" placeholder="{{ __('messages.gogo_contact_v20_name_placeholder') }}" />
            <input type="email" class="contact-v20-input" placeholder="{{ __('messages.gogo_contact_v20_email_placeholder') }}" />
            <textarea class="contact-v20-textarea" rows="4" placeholder="{{ __('messages.gogo_contact_v20_message_placeholder') }}"></textarea>
            <button type="submit" class="contact-v20-btn">{{ __('messages.gogo_contact_v20_send_button') }}</button>
          </form>
        </div>

        <div class="contact-v20-visual">
          
          <div class="contact-v20-image-wrap">
            <img src="{{ asset('assets/contactUS.webp') }}" alt="{{ __('messages.gogo_contact_v20_alt') }}" class="contact-v20-image" />
          </div>
        </div>
      </div>
    </div>
  </section>

  @include('partials.public-footer')

  <script>
    function togglePricing(plan) {
      const btnMonthly = document.getElementById('btn-monthly');
      const btnYearly = document.getElementById('btn-yearly');
      if (plan === 'monthly') {
        btnMonthly.className = 'pricing-toggle-active rounded-full px-5 py-2 text-sm font-medium';
        btnYearly.className = 'pricing-toggle-inactive rounded-full px-5 py-2 text-sm font-medium';
      } else {
        btnYearly.className = 'pricing-toggle-active rounded-full px-5 py-2 text-sm font-medium';
        btnMonthly.className = 'pricing-toggle-inactive rounded-full px-5 py-2 text-sm font-medium';
      }
    }

    (function setupDemoMenu() {
      const wrap = document.getElementById('demo-menu-wrap');
      const trigger = document.getElementById('demo-trigger');
      const panel = document.getElementById('demo-menu-panel');
      if (!wrap || !trigger || !panel) return;

      const hoverEnabled = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
      let closeTimer = null;

      const cancelClose = () => {
        if (closeTimer) {
          clearTimeout(closeTimer);
          closeTimer = null;
        }
      };

      const openMenu = () => {
        cancelClose();
        wrap.classList.add('is-open');
        trigger.setAttribute('aria-expanded', 'true');
      };

      const closeMenu = () => {
        cancelClose();
        wrap.classList.remove('is-open');
        trigger.setAttribute('aria-expanded', 'false');
      };

      const scheduleClose = () => {
        cancelClose();
        closeTimer = setTimeout(() => {
          closeMenu();
        }, 180);
      };

      const toggleMenu = () => {
        if (wrap.classList.contains('is-open')) {
          closeMenu();
        } else {
          openMenu();
        }
      };

      trigger.addEventListener('click', function (event) {
        if (hoverEnabled) return;
        event.preventDefault();
        toggleMenu();
      });

      trigger.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' || event.key === ' ') {
          event.preventDefault();
          toggleMenu();
        }
      });

      if (hoverEnabled) {
        wrap.addEventListener('mouseenter', openMenu);
        wrap.addEventListener('mouseleave', scheduleClose);
        panel.addEventListener('mouseenter', cancelClose);
        panel.addEventListener('mouseleave', scheduleClose);
      }

      wrap.addEventListener('focusin', openMenu);
      wrap.addEventListener('focusout', function () {
        setTimeout(() => {
          if (!wrap.contains(document.activeElement)) {
            scheduleClose();
          }
        }, 0);
      });

      document.addEventListener('click', function (event) {
        if (!wrap.contains(event.target)) {
          closeMenu();
        }
      });

      document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
          closeMenu();
          trigger.focus();
        }
      });
    })();
  </script>
</body>
</html>
