
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ __('messages.gogo_title') }}</title>
  <meta name="description" content="{{ __('messages.gogo_meta_description') }}" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet" />
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
    .login-cta {
      position: relative;
      display: inline-block;
      transition: color .25s ease, transform .25s ease;
    }
    .login-cta::after {
      content: '';
      position: absolute;
      left: 0;
      bottom: -3px;
      width: 100%;
      height: 2px;
      background: #d9657a;
      transform: scaleX(0);
      transform-origin: left;
      transition: transform .28s ease;
    }
    .login-cta:hover {
      color: #1e2536;
      transform: translateY(-2px);
    }
    .login-cta:hover::after {
      transform: scaleX(1);
    }
  </style>
</head>
<body class="bg-white antialiased">

  <!-- NAVBAR -->
  <header class="w-full bg-white">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
      <a href="/" class="font-serif text-2xl text-brand-dark">{{ config('app.name') }}</a>
      <nav class="hidden items-center gap-8 md:flex">
        <a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">{{ __('messages.home') }}</a>
        <a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">{{ __('messages.product') }}</a>
        <a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">{{ __('messages.faq') }}</a>
        <a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">{{ __('messages.blog') }}</a>
        <a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">{{ __('messages.about_us') }}</a>
      </nav>
      <div class="flex items-center gap-3">
        <x-language-switcher />
        <div class="hidden items-center gap-4 md:flex">
          <a href="/login" class="login-cta text-sm text-gray-500">{{ __('messages.login') }}</a>
          <a href="/register" class="rounded-full bg-brand-pink px-5 py-2 text-sm text-white transition-opacity hover:opacity-90">{{ __('messages.signup') }}</a>
        </div>
        <button class="md:hidden" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
        </button>
      </div>
    </div>
    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden border-t border-gray-100 px-6 pb-4 md:hidden">
      <nav class="flex flex-col gap-3 py-3">
        <a href="#" class="text-sm text-gray-500 hover:text-brand-dark">{{ __('messages.home') }}</a>
        <a href="#" class="text-sm text-gray-500 hover:text-brand-dark">{{ __('messages.product') }}</a>
        <a href="#" class="text-sm text-gray-500 hover:text-brand-dark">{{ __('messages.faq') }}</a>
        <a href="#" class="text-sm text-gray-500 hover:text-brand-dark">{{ __('messages.blog') }}</a>
        <a href="#" class="text-sm text-gray-500 hover:text-brand-dark">{{ __('messages.about_us') }}</a>
      </nav>
      <div class="flex items-center gap-4 pt-2">
        <a href="/login" class="login-cta text-sm text-gray-500">{{ __('messages.login') }}</a>
        <a href="#" class="rounded-full bg-brand-pink px-5 py-2 text-sm text-white">{{ __('messages.signup') }}</a>
      </div>
    </div>
  </header>

  <!-- HERO -->
  <section class="relative overflow-hidden bg-white pb-16 pt-8">
    <div class="mx-auto max-w-7xl px-6">
      <div class="grid items-center gap-12 lg:grid-cols-2">
        <div class="relative z-10">
          <h1 class="font-serif text-5xl leading-tight text-brand-dark md:text-6xl lg:text-[64px]">
            {!! __('messages.gogo_hero_title') !!}
          </h1>
          <p class="mt-6 max-w-md text-base leading-relaxed text-gray-500">
            {{ __('messages.gogo_hero_description') }}
          </p>
          <div class="mt-8 flex items-center gap-6">
            <button class="rounded-full bg-brand-pink px-8 py-3 text-sm font-medium text-white transition-opacity hover:opacity-90">{{ __('messages.gogo_start_now') }}</button>
            <button class="flex items-center gap-2 text-sm font-medium text-brand-dark">
              <span class="flex h-10 w-10 items-center justify-center rounded-full border border-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="6 3 20 12 6 21 6 3"/></svg>
              </span>
              {{ __('messages.gogo_view_demo') }}
            </button>
          </div>
        </div>
        <div class="relative">
          <div class="absolute -right-10 -top-10 h-[500px] w-[500px] rounded-full bg-brand-pink-bg opacity-60 blur-3xl"></div>
          <div class="relative z-10">
            <div class="relative mx-auto w-full max-w-md">
              <div class="absolute -left-4 top-4 z-20 rounded-xl bg-white p-3 shadow-lg">
                <div class="flex items-center gap-2">
                  <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 text-xs font-bold text-brand-dark">45</div>
                  <div class="h-1.5 w-12 rounded-full bg-gray-200"></div>
                </div>
              </div>
              <div class="overflow-hidden rounded-2xl">
                <img src="assets/PBW.jpg" alt="{{ __('messages.gogo_alt_professional_businesswoman') }}" width="450" height="550" class="h-auto w-full object-cover" />
              </div>
              <div class="absolute -right-2 bottom-24 z-20 rounded-xl bg-white px-4 py-2 shadow-lg">
                <span class="text-sm font-semibold text-brand-dark">245.00 AED</span>
              </div>
              <div class="absolute -right-8 top-8 z-20 h-16 w-24 rounded-xl bg-[#2D1B69] shadow-lg"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="mt-20 text-center">
        <p class="text-lg font-medium text-brand-dark">{{ __('messages.gogo_more_than_stores') }}</p>
        <div class="mt-6 flex flex-wrap items-center justify-center gap-6 md:gap-10">
          <span class="text-sm text-gray-500">{{ __('messages.gogo_city_dubai') }}</span>
          <span class="text-sm text-gray-500">{{ __('messages.gogo_city_ajman') }}</span>
          <span class="text-sm text-gray-500">{{ __('messages.gogo_city_sharjah') }}</span>
          <span class="text-sm text-gray-500">{{ __('messages.gogo_city_abu_dhabi') }}</span>
          <span class="text-sm text-gray-500">{{ __('messages.gogo_city_um_al_quwain') }}</span>
          <span class="text-sm text-gray-500">{{ __('messages.gogo_city_fujairah') }}</span>
          <span class="text-sm text-gray-500">{{ __('messages.gogo_city_ras_al_khaimah') }}</span>
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
  <section class="bg-brand-pink-bg py-20">
    <div class="mx-auto max-w-7xl px-6">
      <div class="grid items-center gap-12 lg:grid-cols-2">
        <div>
          <h2 class="font-serif text-3xl leading-tight text-brand-dark md:text-4xl">{!! __('messages.gogo_benefits_title') !!}</h2>
          <div class="mt-8 flex flex-col gap-5">
            <div class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              <span class="text-sm text-brand-dark">{{ __('messages.gogo_all_in_one_business_dashboard') }}</span>
            </div>
            <div class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              <span class="text-sm text-brand-dark">{{ __('messages.gogo_smart_order_booking_management') }}</span>
            </div>
            <div class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              <span class="text-sm text-brand-dark">{{ __('messages.gogo_business_insights_reports') }}</span>
            </div>
            <div class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              <span class="text-sm text-brand-dark">{{ __('messages.gogo_secure_payments_payout_tracking') }}</span>
            </div>
            <div class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              <span class="text-sm text-brand-dark">{{ __('messages.gogo_online_transaction') }}</span>
            </div>
          </div>
        </div>
        <div class="relative flex justify-center">
          <div class="absolute left-1/2 top-1/2 h-80 w-80 -translate-x-1/2 -translate-y-1/2 rounded-full bg-brand-pink-light opacity-50 blur-3xl"></div>
          <div class="relative z-10 w-full max-w-sm">
            <div class="absolute -left-4 top-8 z-20 rounded-xl bg-white p-3 shadow-lg">
              <div class="flex items-center gap-2">
                <img src="{{ asset('assets/prof.png') }}" alt="Profile" class="h-12 w-12 rounded-full object-cover" />
                <div>
                  <p class="text-xs font-semibold text-brand-dark">{{ __('messages.gogo_amanda_young') }}</p>
                  <p class="text-[10px] text-gray-500">{{ __('messages.gogo_transfer') }}</p>
                </div>
              </div>
            </div>
            <div class="absolute -right-2 top-20 z-20 rounded-xl bg-white px-4 py-2 shadow-lg">
              <span class="text-sm font-semibold text-brand-dark">245.00 AED</span>
            </div>
            <img src="{{ asset('assets/mobileHome.jpg') }}" alt="{{ __('messages.gogo_alt_money_transfer_mockup') }}" width="350" height="500" class="mx-auto h-auto w-64 rounded-3xl object-cover shadow-2xl" />
            <div class="absolute -right-6 bottom-16 z-20 rounded-xl bg-white px-4 py-2 shadow-lg">
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

  <!-- PRICING -->
  <section class="bg-white py-20">
    <div class="mx-auto max-w-7xl px-6">
      <div class="text-center">
        <h2 class="font-serif text-3xl leading-tight text-brand-dark md:text-4xl">{!! __('messages.gogo_choose_plan_title') !!}</h2>
        <p class="mt-4 text-sm text-gray-500">{{ __('messages.gogo_choose_plan_description') }}</p>
        <div class="mt-6 inline-flex items-center rounded-full bg-gray-100 p-1">
          <button id="btn-monthly" class="rounded-full px-5 py-2 text-sm font-medium text-gray-500" onclick="togglePricing('monthly')">{{ __('messages.gogo_bill_monthly') }}</button>
          <button id="btn-yearly" class="rounded-full bg-brand-pink px-5 py-2 text-sm font-medium text-white shadow-sm" onclick="togglePricing('yearly')">{{ __('messages.gogo_bill_yearly') }}</button>
        </div>
      </div>
      <div class="mt-12 grid gap-6 md:grid-cols-3">
        <!-- Merchant -->
        <div class="rounded-2xl border border-gray-200 bg-white p-8">
          <h3 class="text-xl font-semibold text-brand-dark">{{ __('messages.merchant') }}</h3>
          <p class="mt-2 text-sm text-gray-500">{{ __('messages.gogo_merchant_plan_description') }}</p>
          <div class="mt-6"><span class="text-4xl font-bold text-brand-dark">99</span></div>
          <ul class="mt-8 flex flex-col gap-4">
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">{{ __('messages.gogo_2_users') }}</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">{{ __('messages.gogo_2_files') }}</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">{{ __('messages.gogo_public_share_comments') }}</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">{{ __('messages.gogo_chat_support') }}</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">{{ __('messages.gogo_new_income_apps') }}</span>
            </li>
          </ul>
          <button class="mt-8 w-full rounded-full border border-gray-200 py-3 text-sm font-medium text-brand-dark transition-colors hover:bg-gray-50">{{ __('messages.gogo_signup_for_free') }}</button>
        </div>
        <!-- Vendor (highlighted) -->
        <div class="relative overflow-hidden rounded-2xl bg-[#eb788dff] p-8 text-white">
          <div class="absolute right-4 top-4 rounded-full bg-white/20 px-3 py-1 text-xs font-medium text-white">{{ __('messages.gogo_save_63_a_year') }}</div>
          <h3 class="text-xl font-semibold">{{ __('messages.vendor') }}</h3>
          <p class="mt-2 text-sm text-white/80">{{ __('messages.gogo_vendor_plan_description') }}</p>
          <div class="mt-6"><span class="text-4xl font-bold">99 AED</span></div>
          <ul class="mt-8 flex flex-col gap-4">
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm">{{ __('messages.gogo_4_users') }}</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm">{{ __('messages.gogo_all_apps') }}</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm">{{ __('messages.gogo_unlimited_editable_exports') }}</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm">{{ __('messages.gogo_folders_collaboration') }}</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm">{{ __('messages.gogo_all_incoming_apps') }}</span>
            </li>
          </ul>
          <button class="mt-8 w-full rounded-full bg-white py-3 text-sm font-medium text-brand-pink transition-opacity hover:opacity-90">{{ __('messages.gogo_go_to_pro') }}</button>
        </div>
        <!-- Provider -->
        <div class="rounded-2xl border border-gray-200 bg-white p-8">
          <h3 class="text-xl font-semibold text-brand-dark">{{ __('messages.provider') }}</h3>
          <p class="mt-2 text-sm text-gray-500">{{ __('messages.gogo_provider_plan_description') }}</p>
          <div class="mt-6"><span class="text-4xl font-bold text-brand-dark">99</span></div>
          <ul class="mt-8 flex flex-col gap-4">
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">{{ __('messages.gogo_all_features_pro_plan') }}</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">{{ __('messages.gogo_account_success_manager') }}</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">{{ __('messages.gogo_single_sign_on_sso') }}</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">{{ __('messages.gogo_co_conception_program') }}</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">{{ __('messages.gogo_collaboration_soon') }}</span>
            </li>
          </ul>
          <button class="mt-8 w-full rounded-full border border-gray-200 py-3 text-sm font-medium text-brand-dark transition-colors hover:bg-gray-50">{{ __('messages.gogo_gain_business') }}</button>
        </div>
      </div>
    </div>
  </section>

  <!-- TESTIMONIAL + CONTACT -->
  <section class="bg-brand-dark py-20 text-gray-100">
    <div class="mx-auto max-w-7xl px-6">
      <div class="grid gap-12 lg:grid-cols-2">
        <div>
          <h2 class="font-serif text-3xl leading-tight md:text-4xl">{!! __('messages.gogo_testimonial_title') !!}</h2>
          <p class="mt-4 text-sm leading-relaxed text-gray-400">{{ __('messages.gogo_testimonial_description') }}</p>
          <div class="mt-10">
            <span class="font-serif text-5xl text-brand-pink">&ldquo;</span>
            <p class="mt-2 text-sm leading-relaxed text-gray-300">{{ __('messages.gogo_testimonial_quote') }}</p>
            <div class="mt-6">
              <p class="text-sm font-medium text-gray-100">{{ __('messages.gogo_testimonial_author') }}</p>
            </div>
            <div class="mt-4 flex h-12 w-12 items-center justify-center rounded-full border border-gray-600">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="white" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="6 3 20 12 6 21 6 3"/></svg>
            </div>
          </div>
        </div>
        <div class="rounded-2xl bg-brand-dark-card p-8">
          <div class="mb-6 flex justify-center gap-3">
            <div class="rounded-xl bg-brand-pink/20 px-4 py-2"><div class="h-2 w-16 rounded-full bg-brand-pink/40"></div></div>
            <div class="rounded-xl bg-brand-dark-border px-4 py-2"><div class="h-2 w-12 rounded-full bg-gray-500"></div></div>
          </div>
          <h3 class="text-center text-xl font-semibold text-white">{{ __('messages.gogo_get_started') }}</h3>
          <form class="mt-6 flex flex-col gap-4" onsubmit="event.preventDefault()">
            <div>
              <label for="email" class="text-xs text-gray-400">{{ __('messages.email') }}</label>
              <input id="email" type="email" class="mt-1 w-full rounded-lg border border-brand-dark-border bg-brand-dark px-4 py-3 text-sm text-gray-200 placeholder:text-gray-500 focus:border-brand-pink focus:outline-none" placeholder="{{ __('messages.enter_email') }}" />
            </div>
            <div>
              <label for="message" class="text-xs text-gray-400">{{ __('messages.message') }}</label>
              <textarea id="message" rows="3" class="mt-1 w-full resize-none rounded-lg border border-brand-dark-border bg-brand-dark px-4 py-3 text-sm text-gray-200 placeholder:text-gray-500 focus:border-brand-pink focus:outline-none" placeholder="{{ __('messages.gogo_what_are_you_saying') }}"></textarea>
            </div>
            <button type="submit" class="rounded-lg bg-brand-pink py-3 text-sm font-medium text-white transition-opacity hover:opacity-90">{{ __('messages.gogo_contact_now') }}</button>
            <p class="text-center text-xs text-gray-500">{{ __('messages.gogo_start_free_trial') }}</p>
          </form>
        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="border-t border-gray-200 bg-white">
    <div class="mx-auto max-w-7xl px-6 py-14">
      <div class="grid gap-10 md:grid-cols-5">
        <div class="md:col-span-2">
          <a href="/" class="font-serif text-2xl text-brand-dark">{{ config('app.name') }}</a>
          <p class="mt-3 text-sm text-gray-500">{{ __('messages.gogo_footer_description') }}</p>
          <div class="mt-4 flex">
            <input type="email" placeholder="{{ __('messages.gogo_enter_email_here') }}" class="w-full max-w-[240px] rounded-l-full border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-brand-dark placeholder:text-gray-400 focus:border-brand-pink focus:outline-none" />
            <button class="-ml-2 flex h-10 w-10 items-center justify-center rounded-full bg-brand-pink text-white">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7" /></svg>
            </button>
          </div>
        </div>
        <div>
          <h4 class="text-sm font-semibold text-brand-dark">{{ __('messages.support') }}</h4>
          <ul class="mt-4 flex flex-col gap-3">
            <li><a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">{{ __('messages.gogo_help_centre') }}</a></li>
            <li><a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">{{ __('messages.gogo_account_information') }}</a></li>
            <li><a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">{{ __('messages.about') }}</a></li>
            <li><a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">{{ __('messages.gogo_contact_us') }}</a></li>
          </ul>
        </div>
        <div>
          <h4 class="text-sm font-semibold text-brand-dark">{{ __('messages.gogo_help_and_solution') }}</h4>
          <ul class="mt-4 flex flex-col gap-3">
            <li><a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">{{ __('messages.gogo_talk_to_support') }}</a></li>
            <li><a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">{{ __('messages.gogo_support_docs') }}</a></li>
            <li><a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">{{ __('messages.gogo_system_status') }}</a></li>
            <li><a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">{{ __('messages.gogo_covid_response') }}</a></li>
          </ul>
        </div>
        <div>
          <h4 class="text-sm font-semibold text-brand-dark">{{ __('messages.product') }}</h4>
          <ul class="mt-4 flex flex-col gap-3">
            <li><a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">{{ __('messages.update') }}</a></li>
            <li><a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">{{ __('messages.security') }}</a></li>
            <li><a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">{{ __('messages.gogo_beta_test') }}</a></li>
            <li><a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">{{ __('messages.gogo_pricing_product') }}</a></li>
          </ul>
        </div>
      </div>
    </div>
    <div class="border-t border-gray-200">
      <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-6 py-5 md:flex-row">
        <p class="text-xs text-gray-500">{{ __('messages.gogo_copyright') }}</p>
        <div class="flex gap-6">
          <a href="#" class="text-xs text-gray-500 transition-colors hover:text-brand-dark">{{ __('messages.terms') }}</a>
          <a href="#" class="text-xs text-gray-500 transition-colors hover:text-brand-dark">{{ __('messages.privacy') }}</a>
        </div>
      </div>
    </div>
  </footer>

  <script>
    function togglePricing(plan) {
      const btnMonthly = document.getElementById('btn-monthly');
      const btnYearly = document.getElementById('btn-yearly');
      if (plan === 'monthly') {
        btnMonthly.className = 'rounded-full bg-brand-pink px-5 py-2 text-sm font-medium text-white shadow-sm';
        btnYearly.className = 'rounded-full px-5 py-2 text-sm font-medium text-gray-500';
      } else {
        btnYearly.className = 'rounded-full bg-brand-pink px-5 py-2 text-sm font-medium text-white shadow-sm';
        btnMonthly.className = 'rounded-full px-5 py-2 text-sm font-medium text-gray-500';
      }
    }
  </script>
</body>
</html>
