<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ __('messages.about_us') }} | {{ config('app.name') }}</title>
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
    .about-us-page {
      background: #fff;
      color: #f9fcfd;
      font-family: "Poppins", sans-serif;
    }
    .about-shell {
      width: min(1400px, calc(100% - 2rem));
      margin-inline: auto;
    }
    .about-mission {
      display: grid;
      grid-template-columns: 300px 1fr;
      gap: 1rem;
      align-items: stretch;
      margin-top: 1.5rem;
    }
    .about-mission-left {
      border: 5px solid #f7f3fa;
      border-top: 0;
      border-radius: 0 0 220px 220px;
      padding: 0 0 1rem;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-end;
      position: relative;
      overflow: visible;
      min-height: 380px;
    }
    .about-badge {
      position: absolute;
      top: -2rem;
      inset-inline-end: -3.2rem;
      width: 180px;
      height: 180px;
      border-radius: 999px;
      background: #9058b0;
      border: 1px solid #f0f0ff;
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      font-size: 2rem;
      font-weight: 700;
      line-height: 1.2;
      z-index: 2;
    }
    .about-mission-avatar {
      width: 286px;
      height: 286px;
      border-radius: 999px;
      border: 5px solid #d8c3e6;
      object-fit: cover;
      object-position: center;
      margin-bottom: -2px;
    }
    .about-mission-right {
      background: rgba(216, 195, 230, 0.2);
      border-radius: 0 0 7px 0;
      padding: 4.5rem 4rem 2.5rem;
      min-height: 390px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    .about-overline {
      color: #834ca5;
      font-size: .95rem;
      line-height: 1.3;
      margin: 0 0 .7rem;
      font-weight: 400;
    }
    .about-mission-title {
      margin: 0;
      color: #8851a9;
      font-size: clamp(1.6rem, 2.6vw, 2.2rem);
      line-height: 1.35;
      font-weight: 500;
    }
    .about-mission-copy {
      margin-top: 1rem;
      max-width: 760px;
      color: #a169be;
      font-size: 1rem;
      line-height: 1.75;
      font-weight: 400;
    }
    .about-socials {
      display: flex;
      gap: .7rem;
      margin-top: 1rem;
    }
    .about-socials span {
      width: 28px;
      height: 28px;
      border-radius: 999px;
      border: 1px solid #8d55ad;
      color: #8d55ad;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 13px;
      font-family: "Inter", sans-serif;
      font-weight: 600;
    }
    .about-highlight {
      display: grid;
      grid-template-columns: 1fr 510px;
      align-items: center;
      gap: 2rem;
      padding: 3rem 1rem;
    }
    .about-highlight-title {
      margin: 0 0 1rem;
      color: #a16abe;
      font-size: clamp(1.75rem, 2.7vw, 2.9rem);
      line-height: 1.3;
      font-weight: 700;
    }
    .about-highlight-copy {
      margin: 0;
      color: #1f1f1f;
      font-size: 1rem;
      line-height: 1.75;
      font-weight: 300;
      max-width: 850px;
    }
    .about-highlight-visual {
      width: 510px;
      height: 510px;
      position: relative;
      margin-inline: auto;
      border-radius: 999px;
    }
    .about-highlight-visual::before {
      content: "";
      position: absolute;
      inset-inline-end: 0;
      top: 58px;
      width: 388px;
      height: 388px;
      border-radius: 999px;
      background: #d8c3e6;
      z-index: 0;
    }
    .about-highlight-visual img {
      position: absolute;
      left: 0;
      top: 96px;
      width: 476px;
      height: 476px;
      border-radius: 999px;
      border: 5px solid #f7f3fa;
      object-fit: cover;
      object-position: center;
      z-index: 1;
    }
    .about-team {
      background: #f9fcfd;
      color: #fff;
      padding: 2.75rem 1rem;
    }
    .about-team-grid {
      display: grid;
      grid-template-columns: 620px 1fr;
      gap: 2rem;
      align-items: center;
    }
    .about-team-left {
      display: grid;
      grid-template-columns: 285px 315px;
      gap: 1rem;
      min-height: 560px;
      align-items: center;
    }
    .about-team-stack {
      display: grid;
      gap: 1.4rem;
      justify-content: center;
    }
    .team-circle-sm,
    .team-circle-lg {
      position: relative;
      border-radius: 999px;
      border: 5px solid #d8c3e6;
      overflow: hidden;
      background: #e8d9f1;
    }
    .team-circle-sm {
      width: 250px;
      height: 250px;
    }
    .team-circle-lg {
      width: 275px;
      height: 275px;
      margin-inline: auto;
      box-shadow: 0 4px 4px rgba(0, 0, 0, .25);
    }
    .team-circle-sm img,
    .team-circle-lg img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      object-position: center;
    }
    .team-name-tag {
      position: absolute;
      top: 104px;
      inset-inline-start: 0;
      background: rgba(216, 195, 230, .85);
      color: #9058b0;
      border-radius: 5px;
      font-family: "Inter", sans-serif;
      font-size: 16px;
      font-weight: 300;
      line-height: 1.2;
      padding: 5px 10px;
    }
    .team-name-tag.is-wide {
      padding-inline: 12px;
    }
    .about-team-content h2 {
      margin: 0 0 1.2rem;
      color: #a169be;
      font-size: 2rem;
      line-height: 1.3;
      font-weight: 700;
    }
    .team-bio + .team-bio {
      margin-top: 1.25rem;
    }
    .team-bio h3 {
      margin: 0 0 .6rem;
      color: #a169be;
      font-size: 1.25rem;
      line-height: 1.3;
      font-weight: 600;
    }
    .team-bio p {
      margin: 0;
      color: #362a3dff;
      font-size: 1rem;
      line-height: 1.7;
      font-weight: 400;
      max-width: 760px;
    }
    @media (max-width: 1180px) {
      .about-mission {
        grid-template-columns: 240px 1fr;
      }
      .about-mission-left {
        min-height: 320px;
      }
      .about-badge {
        width: 140px;
        height: 140px;
        inset-inline-end: -2.5rem;
        font-size: 1.55rem;
      }
      .about-mission-avatar {
        width: 220px;
        height: 220px;
      }
      .about-mission-right {
        padding: 2.8rem 2rem;
      }
      .about-highlight {
        grid-template-columns: 1fr 420px;
      }
      .about-highlight-visual {
        width: 420px;
        height: 420px;
      }
      .about-highlight-visual::before {
        width: 300px;
        height: 300px;
        top: 50px;
      }
      .about-highlight-visual img {
        width: 390px;
        height: 390px;
        top: 85px;
      }
      .about-team-grid {
        grid-template-columns: 1fr;
      }
      .about-team-left {
        grid-template-columns: 1fr 1fr;
        min-height: auto;
      }
    }
    @media (max-width: 900px) {
      .about-mission {
        grid-template-columns: 1fr;
      }
      .about-mission-left {
        min-height: auto;
        border: 0;
        padding: 1rem;
      }
      .about-badge {
        inset-inline-end: 0;
        top: .5rem;
      }
      .about-highlight {
        grid-template-columns: 1fr;
        padding-top: 1rem;
      }
      .about-highlight-visual {
        width: 320px;
        height: 320px;
      }
      .about-highlight-visual::before {
        width: 230px;
        height: 230px;
        top: 44px;
      }
      .about-highlight-visual img {
        width: 300px;
        height: 300px;
        top: 72px;
      }
      .about-team-left {
        grid-template-columns: 1fr;
      }
      .about-team-stack {
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
      }
      .team-circle-sm,
      .team-circle-lg {
        width: 220px;
        height: 220px;
      }
      .team-name-tag {
        top: 86px;
      }
    }
    @media (max-width: 560px) {
      .about-shell {
        width: calc(100% - 1.25rem);
      }
      .about-mission-right {
        padding: 1.6rem 1rem;
      }
      .about-socials {
        flex-wrap: wrap;
      }
      .about-team {
        padding-inline: 0;
      }
      .about-team-content h2 {
        font-size: 1.7rem;
      }
      .team-bio h3 {
        font-size: 1.12rem;
      }
      .team-bio p {
        font-size: .94rem;
      }
      .about-highlight-copy {
        font-size: .95rem;
      }
    }
  </style>
  @include('partials.public-shell-styles')
</head>
<body class="about-us-page antialiased">
  <section class="upper-shell relative isolate overflow-hidden">
    @include('partials.public-navbar', ['publicNavActive' => 'about'])
  </section>

  <main class="py-4 sm:py-6">
    <section class="about-shell about-mission">
      <div class="about-mission-left">
        <div class="about-badge">{{ __('messages.about_us') }}</div>
        <img src="{{ asset('assets/user2.png') }}" alt="{{ __('messages.about_page_user4_alt') }}" class="about-mission-avatar" />
      </div>
      <div class="about-mission-right">
        <p class="about-overline">{{ __('messages.about_page_mission_label') }}</p>
        <h1 class="about-mission-title">{{ __('messages.about_page_mission_title') }}</h1>
        <p class="about-mission-copy">{{ __('messages.about_page_mission_paragraph') }}</p>
        <div class="about-socials" aria-label="{{ __('messages.about_page_social_label') }}">
          <span aria-hidden="true">t</span>
          <span aria-hidden="true">f</span>
          <span aria-hidden="true">ig</span>
          <span aria-hidden="true">yt</span>
        </div>
      </div>
    </section>

    <section class="about-shell about-highlight">
      <div>
        <h2 class="about-highlight-title">{{ __('messages.about_page_highlight_heading') }}</h2>
        <p class="about-highlight-copy">{{ __('messages.about_page_highlight_paragraph') }}</p>
      </div>
      <div class="about-highlight-visual">
        <img src="{{ asset('assets/user1.png') }}" alt="{{ __('messages.about_page_user3_alt') }}" />
      </div>
    </section>

    <section class="about-team">
      <div class="about-shell about-team-grid">
        <div class="about-team-left">
          <div class="about-team-stack">
            <div class="team-circle-sm">
              <img src="{{ asset('assets/user2.png') }}" alt="{{ __('messages.about_page_user2_alt') }}">
              <span class="team-name-tag">{{ __('messages.about_page_member_2_name') }}</span>
            </div>
            <div class="team-circle-sm">
              <img src="{{ asset('assets/user1.png') }}" alt="{{ __('messages.about_page_user1_alt') }}">
              <span class="team-name-tag">{{ __('messages.about_page_member_1_name') }}</span>
            </div>
          </div>
          <div class="team-circle-lg">
            <img src="{{ asset('assets/user3.png') }}" alt="{{ __('messages.about_page_user3_alt') }}">
            <span class="team-name-tag is-wide">{{ __('messages.about_page_member_3_name') }}</span>
          </div>
        </div>
        <div class="about-team-content">
          <h2>{{ __('messages.about_page_team_heading') }}</h2>

          <article class="team-bio">
            <h3>{{ __('messages.about_page_member_1_name') }}</h3>
            <p>{{ __('messages.about_page_member_1_bio') }}</p>
          </article>

          <article class="team-bio">
            <h3>{{ __('messages.about_page_member_2_name') }}</h3>
            <p>{{ __('messages.about_page_member_2_bio') }}</p>
          </article>

          <article class="team-bio">
            <h3>{{ __('messages.about_page_member_3_name') }}</h3>
            <p>{{ __('messages.about_page_member_3_bio') }}</p>
          </article>
        </div>
      </div>
    </section>
  </main>

  @include('partials.public-footer')
</body>
</html>
