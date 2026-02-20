@php
    $currentLocale = app()->getLocale();
    $supportedLocales = config('app.supported_locales', [
        'en' => ['name' => 'English', 'native' => 'English', 'flag' => '🇺🇸', 'rtl' => false],
        'ar' => ['name' => 'Arabic', 'native' => 'العربية', 'flag' => '🇸🇦', 'rtl' => true]
    ]);
    $currentLanguage = $supportedLocales[$currentLocale] ?? ['native' => strtoupper($currentLocale), 'flag' => '🌐', 'rtl' => false];
    $switcherId = 'language-switcher-' . uniqid();
@endphp

<div class="language-switcher relative" id="{{ $switcherId }}">
    <button
        type="button"
        class="language-toggle inline-flex items-center gap-2 rounded-full border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 shadow-sm transition-colors hover:bg-gray-50"
        aria-expanded="false"
        aria-haspopup="true"
    >
        <span class="flag-icon">{{ $currentLanguage['flag'] }}</span>
        <span class="hidden sm:inline">{{ $currentLanguage['native'] }}</span>
        <span class="sm:hidden">{{ strtoupper($currentLocale) }}</span>
        <svg class="h-3.5 w-3.5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
        </svg>
    </button>

    <ul class="language-menu absolute {{ ($currentLanguage['rtl'] ?? false) ? 'left-0' : 'right-0' }} z-50 mt-2 hidden min-w-[150px] rounded-xl border border-gray-200 bg-white p-1 shadow-lg">
        @foreach($supportedLocales as $locale => $details)
            <li>
                <a
                    class="language-option {{ $locale === $currentLocale ? 'active' : '' }} flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-50"
                    href="{{ route('language.switch', $locale) }}"
                >
                    <span class="flag-icon">{{ $details['flag'] }}</span>
                    <span>{{ $details['native'] }}</span>
                    @if($locale === $currentLocale)
                        <span class="ms-auto text-pink-600">✓</span>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const root = document.getElementById('{{ $switcherId }}');
    if (!root) return;

    const button = root.querySelector('.language-toggle');
    const menu = root.querySelector('.language-menu');
    if (!button || !menu) return;

    button.addEventListener('click', function () {
        const isOpen = !menu.classList.contains('hidden');
        menu.classList.toggle('hidden', isOpen);
        button.setAttribute('aria-expanded', (!isOpen).toString());
    });

    document.addEventListener('click', function (event) {
        if (!root.contains(event.target)) {
            menu.classList.add('hidden');
            button.setAttribute('aria-expanded', 'false');
        }
    });
});
</script>

<style>
.language-switcher .flag-icon {
    line-height: 1;
}

.language-switcher .language-option.active {
    background-color: #fdf0ee;
    color: #111827;
}

@media (max-width: 768px) {
    .language-switcher .language-toggle {
        padding: 0.35rem 0.65rem;
    }
}
</style>
