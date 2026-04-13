@php
  $isAuthenticated = auth()->check();
  $getStartedUrl = $isAuthenticated ? url('/dashboard') : route('register');
  $publicNavActive = $publicNavActive ?? '';
@endphp

<header class="w-full">
  <div class="relative z-20 mx-auto flex w-full max-w-[1260px] items-center justify-between px-5 pb-3 pt-6 sm:px-6 lg:px-8">
    <div class="flex items-center gap-3">
      <img src="{{ asset('assets/landLogo.png') }}" width="40" height="40" alt="Logo">
      <div class="flex flex-col">
        <p class="font-playfair text-md text-white sm:text-xl">GLOW LABS</p>
        <p class="font-Montserrat text-sm text-white sm:text-sm">{{ __('messages.modern_login_brand_subtitle') }}</p>
      </div>
    </div>

    <nav class="hidden items-center gap-9 lg:flex">
      <a href="{{ route('home') }}" class="upper-nav-link{{ $publicNavActive === 'home' ? ' is-active' : '' }}">{{ __('messages.home') }}</a>
      <a href="{{ route('faq') }}" class="upper-nav-link{{ $publicNavActive === 'faq' ? ' is-active' : '' }}">{{ __('messages.faq') }}</a>
      <a href="{{ route('services.public') }}" class="upper-nav-link{{ $publicNavActive === 'services' ? ' is-active' : '' }}">{{ __('messages.services') }}</a>
      <a href="{{ route('about.us') }}" class="upper-nav-link{{ $publicNavActive === 'about' ? ' is-active' : '' }}">{{ __('messages.about_us') }}</a>
    </nav>

    <div class="flex items-center gap-3">
      <x-language-switcher />
      <div class="upper-auth-segment hidden items-center lg:flex">
        <a href="{{ $isAuthenticated ? $getStartedUrl : route('login') }}" class="upper-login-btn px-8 py-4 text-sm text-center font-semibold tracking-wide">{{ __('messages.login') }}</a>
        <a href="{{ route('register') }}" class="upper-signup-btn border-l border-[#ffa006]/80 px-8 py-4 text-sm text-center font-semibold tracking-wide">{{ __('messages.signup') }}</a>
      </div>
      <button class="rounded-md p-2 text-white lg:hidden" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
      </button>
    </div>
  </div>

  <div id="mobile-menu" class="relative z-20 mx-5 hidden rounded-xl border border-white/20 bg-black/20 px-5 pb-4 md:mx-6 lg:hidden">
    <nav class="flex flex-col gap-3 py-4">
      <a href="{{ route('home') }}" class="text-sm text-white/90 hover:text-white">{{ __('messages.home') }}</a>
      <a href="{{ route('faq') }}" class="text-sm text-white/90 hover:text-white">{{ __('messages.faq') }}</a>
      <a href="{{ route('services.public') }}" class="text-sm text-white/90 hover:text-white">{{ __('messages.services') }}</a>
      <a href="{{ route('about.us') }}" class="text-sm text-white/90 hover:text-white">{{ __('messages.about_us') }}</a>
    </nav>
    <div class="upper-auth-segment flex w-full items-center">
      <a href="{{ $isAuthenticated ? $getStartedUrl : route('login') }}" class="upper-login-btn flex-1 px-4 py-3 text-center text-sm font-semibold tracking-wide">{{ __('messages.login') }}</a>
      <a href="{{ route('register') }}" class="upper-signup-btn flex-1 border-l border-[#ffa006]/80 px-4 py-3 text-center text-sm font-semibold tracking-wide">{{ __('messages.signup') }}</a>
    </div>
  </div>
</header>
