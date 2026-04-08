<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ __('messages.faq_page_meta_title') }}</title>
  <meta name="description" content="{{ __('messages.faq_page_meta_description') }}" />
  <link rel="stylesheet" href="{{ asset('css/global.css') }}" />
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
      background: var(--primary);
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
    .faq-panel {
      border: 1px solid #e5e7eb;
      border-radius: 16px;
      background: #fff;
      transition: border-color .2s ease, box-shadow .2s ease;
    }
    .faq-panel[open] {
      border-color: var(--primary);
      box-shadow: 0 10px 30px rgba(30, 37, 54, 0.06);
    }
    .faq-summary {
      list-style: none;
      cursor: pointer;
    }
    .faq-summary::-webkit-details-marker {
      display: none;
    }
    .faq-icon {
      transition: transform .2s ease;
    }
    .faq-panel[open] .faq-icon {
      transform: rotate(45deg);
    }
  </style>
</head>
<body class="bg-white antialiased">
  @php
    $faqIndexes = range(1, 15);

    $renderFaqAnswer = function (string $text): string {
        $escaped = e($text);
        $withLinks = preg_replace_callback(
            '/(https?:\/\/[^\s<]+)/i',
            function (array $matches): string {
                $url = $matches[1];
                return '<a href="' . $url . '" target="_blank" rel="noopener noreferrer" class="font-medium text-brand-pink underline decoration-brand-pink/40 underline-offset-4 hover:decoration-brand-pink">' . $url . '</a>';
            },
            $escaped
        );

        return nl2br($withLinks);
    };
  @endphp

  <header class="w-full bg-white">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
      <div class="flex flex-row items-center justifiy-center gap-2">
        <img src="{{ asset('assets/logo.png') }}" width="40" height="40">
      <p class="font-serif text-2xl text-brand-dark">{{ config('app.name') }}</p>
      </div>
      <nav class="hidden items-center gap-8 md:flex">
        <a href="{{ route('home') }}" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">{{ __('messages.home') }}</a>
        <a href="{{ route('faq') }}" class="text-sm font-medium text-brand-dark">{{ __('messages.faq') }}</a>
        <a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">{{ __('messages.services') }}</a>
        <a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">{{ __('messages.about_us') }}</a>
      </nav>
      <div class="flex items-center gap-3">
        <x-language-switcher />
        <div class="hidden items-center gap-4 md:flex">
          <a href="{{ $isAuthenticated ? $getStartedUrl : route('login') }}" class="login-cta text-sm text-gray-500">{{ __('messages.login') }}</a>
          <a href="{{ route('register') }}" class="rounded-full bg-brand-pink px-5 py-2 text-sm text-white transition-opacity hover:opacity-90">{{ __('messages.signup') }}</a>
        </div>
        <button class="md:hidden" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
        </button>
      </div>
    </div>

    <div id="mobile-menu" class="hidden border-t border-gray-100 px-6 pb-4 md:hidden">
      <nav class="flex flex-col gap-3 py-3">
        <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-brand-dark">{{ __('messages.home') }}</a>
        <a href="{{ route('faq') }}" class="text-sm font-medium text-brand-dark">{{ __('messages.faq') }}</a>
        <a href="#" class="text-sm text-gray-500 hover:text-brand-dark">{{ __('messages.services') }}</a>
        <a href="#" class="text-sm text-gray-500 hover:text-brand-dark">{{ __('messages.about_us') }}</a>
      </nav>
      <div class="flex items-center gap-4 pt-2">
        <a href="{{ $isAuthenticated ? $getStartedUrl : route('login') }}" class="login-cta text-sm text-gray-500">{{ __('messages.login') }}</a>
        <a href="{{ route('register') }}" class="rounded-full bg-brand-pink px-5 py-2 text-sm text-white">{{ __('messages.signup') }}</a>
      </div>
    </div>
  </header>

  <main class="relative overflow-hidden py-12 md:py-20">

    <div class="relative mx-auto max-w-4xl px-6">
      <div class="text-center">
        <h1 class="font-serif text-4xl leading-tight text-brand-dark md:text-5xl">{{ __('messages.faq_page_title') }}</h1>
      </div>

      <section class="mt-10 space-y-4 md:mt-14" aria-label="FAQ Accordion">
        @foreach ($faqIndexes as $index)
          <details class="faq-panel group px-5 py-4 md:px-7 md:py-5" @if($index === 1) open @endif>
            <summary class="faq-summary flex items-start justify-between gap-4">
              <h2 class="text-base font-semibold leading-7 text-brand-dark md:text-lg">{{ __('messages.faq_glowlabs_q' . $index) }}</h2>
              <span class="faq-icon mt-1 inline-flex h-6 w-6 items-center justify-center rounded-full border border-gray-200 text-brand-dark">+</span>
            </summary>
            <div class="pt-4 text-sm leading-7 text-gray-600 md:text-base">{!! $renderFaqAnswer(__('messages.faq_glowlabs_a' . $index)) !!}</div>
          </details>
        @endforeach
      </section>
    </div>
  </main>
</body>
</html>
