<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ __('messages.services') }} | {{ config('app.name') }}</title>
  <link rel="stylesheet" href="{{ asset('css/global.css') }}" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display&family=Manrope:wght@700&family=Playfair+Display:wght@700&family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
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
              pink: 'var(--primary)',
              'pink-light': 'var(--primary-light)',
              'pink-bg': 'var(--towhite)',
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
    .services-page {
      font-family: "Poppins", sans-serif;
      background: #f9fcfd;
      color: #141423;
    }
    .services-shell {
      width: min(1360px, calc(100% - 2rem));
      margin-inline: auto;
    }
    .services-hero-wrap {
      padding-top: 1.25rem;
      background: #fff;
    }
    .services-hero {
      display: grid;
      grid-template-columns: 300px 1fr;
      gap: 1rem;
      align-items: stretch;
      min-height: 536px;
    }
    .services-hero-visual {
      position: relative;
      border: 5px solid #f7f3fa;
      border-top: 0;
      border-radius: 0 0 250px 250px;
      overflow: visible;
      min-height: 536px;
      display: flex;
      align-items: flex-end;
      justify-content: center;
      background: #fff;
      z-index: 10;
    }
    .services-hero-visual img {
      width: 295px;
      height: 295px;
      object-fit: cover;
      object-position: center;
      border-radius: 999px;
      margin-bottom: 0;
    }
    .services-hero-badge {
      position: absolute;
      top: 75px;
      inset-inline-end: -74px;
      width: 200px;
      height: 200px;
      border-radius: 999px;
      background: #9058b0;
      border: 1px solid #f0f0ff;
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      font-size: 30px;
      font-weight: 700;
      line-height: 1.2;
      z-index: 40;
      padding: 0 8px;
    }
    .services-hero-content {
      position: relative;
      min-height: 391px;
      margin-top: 0;
      border-radius: 0 0 8px 0;
      background: rgba(216, 195, 230, 0.2);
      padding: 72px 70px 48px;
      z-index: 1;
    }
    .services-overline {
      margin: 0;
      color: #834ca5;
      font-size: 16px;
      line-height: 32px;
      font-weight: 400;
    }
    .services-title {
      margin: 8px 0 0;
      color: #8851a9;
      font-size: clamp(2rem, 2.7vw, 2.6rem);
      line-height: 1.3;
      font-weight: 500;
      max-width: 780px;
    }
    .services-hero-copy {
      margin-top: 16px;
      color: #a169be;
      font-size: 18px;
      line-height: 1.56;
      font-weight: 400;
      max-width: 820px;
    }
    .services-intro {
      text-align: center;
      color: #4b496a;
      font-size: 18px;
      line-height: 1.67;
      max-width: 670px;
      margin: 110px auto 56px;
    }
    .services-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 28px;
      padding-bottom: 80px;
    }
    .service-card {
      background: #fff;
      border: 1px solid #d4d2e3;
      border-radius: 24px;
      overflow: hidden;
      min-height: 658px;
      display: flex;
      flex-direction: column;
    }
    .service-card-image {
      width: 100%;
      aspect-ratio: 1 / 1;
      object-fit: cover;
      object-position: center;
      display: block;
    }
    .service-card-body {
      padding: 24px 30px 32px;
      color: #4b496a;
      font-size: 18px;
      line-height: 1.67;
      font-weight: 400;
    }
    @media (max-width: 1200px) {
      .services-hero {
        grid-template-columns: 260px 1fr;
      }
      .services-hero-visual {
        min-height: 480px;
      }
      .services-hero-badge {
        width: 168px;
        height: 168px;
        inset-inline-end: -56px;
        top: 72px;
        font-size: 25px;
      }
      .services-hero-content {
        padding: 56px 36px 36px;
      }
    }
    @media (max-width: 980px) {
      .services-hero {
        grid-template-columns: 1fr;
        gap: 0;
      }
      .services-hero-visual {
        min-height: 360px;
        border: 0;
      }
      .services-hero-badge {
        inset-inline-end: auto;
        inset-inline-start: 50%;
        transform: translateX(-50%);
        top: 20px;
      }
      .services-hero-content {
        margin-top: 0;
        min-height: auto;
        border-radius: 8px;
        padding: 42px 24px 28px;
      }
      .services-intro {
        margin-top: 54px;
      }
      .services-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }
    @media (max-width: 700px) {
      .services-shell {
        width: calc(100% - 1.25rem);
      }
      .services-title {
        font-size: 1.65rem;
      }
      .services-hero-copy,
      .services-intro,
      .service-card-body {
        font-size: 16px;
      }
      .services-intro {
        margin: 40px auto 28px;
      }
      .services-grid {
        grid-template-columns: 1fr;
        gap: 18px;
        padding-bottom: 48px;
      }
      .service-card {
        min-height: 0;
      }
      .service-card-body {
        padding: 20px 22px 24px;
      }
    }
  </style>
  @include('partials.public-shell-styles')
</head>
<body class="services-page antialiased">
  @php
    $serviceImg1 = asset('assets/' . rawurlencode('Image Placeholder1.png'));
    $serviceImg2 = asset('assets/' . rawurlencode('Image Placeholder2.png'));
    $serviceImg3 = asset('assets/' . rawurlencode('Image Placeholder3.png'));
    $serviceImg4 = asset('assets/' . rawurlencode('Image Placeholder4.png'));
  @endphp

  <section class="upper-shell relative isolate overflow-hidden">
    @include('partials.public-navbar', ['publicNavActive' => 'services'])
  </section>

  <main>
    <section class="services-hero-wrap">
      <div class="services-shell services-hero">
        <div class="services-hero-visual">
          <img src="{{ $serviceImg4 }}" alt="{{ __('messages.services_page_image_1_alt') }}" />
          <div class="services-hero-badge">{{ __('messages.services') }}</div>
        </div>
        <div class="services-hero-content">
          <p class="services-overline">{{ __('messages.services_page_features_label') }}</p>
          <h1 class="services-title">{{ __('messages.services_page_hero_title') }}</h1>
          <p class="services-hero-copy">{{ __('messages.services_page_hero_description') }}</p>
        </div>
      </div>
    </section>

    <section class="services-shell">
      <p class="services-intro">{{ __('messages.services_page_intro_paragraph') }}</p>

      <div class="services-grid">
        <article class="service-card">
          <img src="{{ $serviceImg2 }}" alt="{{ __('messages.services_page_image_2_alt') }}" class="service-card-image" />
          <div class="service-card-body">{{ __('messages.services_page_card_1_description') }}</div>
        </article>

        <article class="service-card">
          <img src="{{ $serviceImg3 }}" alt="{{ __('messages.services_page_image_3_alt') }}" class="service-card-image" />
          <div class="service-card-body">{{ __('messages.services_page_card_2_description') }}</div>
        </article>

        <article class="service-card">
          <img src="{{ $serviceImg1 }}" alt="{{ __('messages.services_page_image_4_alt') }}" class="service-card-image" />
          <div class="service-card-body">{{ __('messages.services_page_card_3_description') }}</div>
        </article>
      </div>
    </section>
  </main>
  @include('partials.public-footer')
</body>
</html>
