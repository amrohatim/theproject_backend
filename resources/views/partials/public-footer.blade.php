<footer class="border-t border-gray-200 bg-white">
  <div class="mx-auto max-w-7xl px-6 py-14">
    <div class="grid gap-10 md:grid-cols-5">
      <div class="md:col-span-2">
        <a href="/" class="font-serif text-2xl text-brand-dark">{{ config('app.name') }}</a>
        <p class="mt-3 text-sm text-gray-500">{{ __('messages.gogo_footer_description') }}</p>
        <div class="mt-4 flex">
          <img src="{{ asset('assets/landLogo.png') }}" alt="{{ config('app.name') }}">
        </div>
      </div>
      <div>
        <h4 class="text-sm font-semibold text-brand-dark">{{ __('messages.support') }}</h4>
        <ul class="mt-4 flex flex-col gap-3">
          <li><a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">{{ __('messages.gogo_help_centre') }}</a></li>
          <li><a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">{{ __('messages.gogo_account_information') }}</a></li>
          <li><a href="{{ route('about.us') }}" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">{{ __('messages.about') }}</a></li>
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
        <h4 class="text-sm font-semibold text-brand-dark">{{ __('messages.subscription') }}</h4>
        <ul class="mt-4 flex flex-col gap-3">
          <li><a href="/register/vendor" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">{{ __('messages.company_reg') }}</a></li>
          <li><a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">{{ __('messages.merchant_reg') }}</a></li>
          <li><a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">{{ __('messages.provider_reg') }}</a></li>
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
