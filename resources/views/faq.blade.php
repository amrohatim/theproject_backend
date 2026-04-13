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
  @include('partials.public-shell-styles')
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

  <section class="upper-shell relative isolate overflow-hidden">
    @include('partials.public-navbar', ['publicNavActive' => 'faq'])
  </section>

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
  @include('partials.public-footer')
</body>
</html>
