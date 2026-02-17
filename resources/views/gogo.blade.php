
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dala3chic - Increase Your Productivity</title>
  <meta name="description" content="Let's make your work more organized and easily using the Dala Dashboard with many of the latest features in managing work every day." />
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
  </style>
</head>
<body class="bg-white antialiased">

  <!-- NAVBAR -->
  <header class="w-full bg-white">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
      <a href="/" class="font-serif text-2xl text-brand-dark">Dala3chic</a>
      <nav class="hidden items-center gap-8 md:flex">
        <a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">Home</a>
        <a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">Product</a>
        <a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">FAQ</a>
        <a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">Blog</a>
        <a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">About Us</a>
      </nav>
      <div class="hidden items-center gap-4 md:flex">
        <a href="#" class="text-sm text-gray-500 hover:text-brand-dark">Login</a>
        <a href="#" class="rounded-full bg-brand-pink px-5 py-2 text-sm text-white transition-opacity hover:opacity-90">Sign Up</a>
      </div>
      <button class="md:hidden" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
      </button>
    </div>
    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden border-t border-gray-100 px-6 pb-4 md:hidden">
      <nav class="flex flex-col gap-3 py-3">
        <a href="#" class="text-sm text-gray-500 hover:text-brand-dark">Home</a>
        <a href="#" class="text-sm text-gray-500 hover:text-brand-dark">Product</a>
        <a href="#" class="text-sm text-gray-500 hover:text-brand-dark">FAQ</a>
        <a href="#" class="text-sm text-gray-500 hover:text-brand-dark">Blog</a>
        <a href="#" class="text-sm text-gray-500 hover:text-brand-dark">About Us</a>
      </nav>
      <div class="flex items-center gap-4 pt-2">
        <a href="#" class="text-sm text-gray-500 hover:text-brand-dark">Login</a>
        <a href="#" class="rounded-full bg-brand-pink px-5 py-2 text-sm text-white">Sign Up</a>
      </div>
    </div>
  </header>

  <!-- HERO -->
  <section class="relative overflow-hidden bg-white pb-16 pt-8">
    <div class="mx-auto max-w-7xl px-6">
      <div class="grid items-center gap-12 lg:grid-cols-2">
        <div class="relative z-10">
          <h1 class="font-serif text-5xl leading-tight text-brand-dark md:text-6xl lg:text-[64px]">
            We're here to<br />Increase your<br />Productivity
          </h1>
          <p class="mt-6 max-w-md text-base leading-relaxed text-gray-500">
            Let's make your work more organize and easily using the Dala Dashboard with many of the latest features in managing work every day.
          </p>
          <div class="mt-8 flex items-center gap-6">
            <button class="rounded-full bg-brand-pink px-8 py-3 text-sm font-medium text-white transition-opacity hover:opacity-90">Start Now</button>
            <button class="flex items-center gap-2 text-sm font-medium text-brand-dark">
              <span class="flex h-10 w-10 items-center justify-center rounded-full border border-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="6 3 20 12 6 21 6 3"/></svg>
              </span>
              View Demo
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
                <img src="/images/hero-woman.jpg" alt="Professional businesswoman" width="450" height="550" class="h-auto w-full object-cover" />
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
        <p class="text-lg font-medium text-brand-dark">More than 25,000 Store and Merchant</p>
        <div class="mt-6 flex flex-wrap items-center justify-center gap-6 md:gap-10">
          <span class="text-sm text-gray-500">Dubai</span>
          <span class="text-sm text-gray-500">Ajman</span>
          <span class="text-sm text-gray-500">Sharjah</span>
          <span class="text-sm text-gray-500">Abu Dhabi</span>
          <span class="text-sm text-gray-500">Um Al Queen</span>
          <span class="text-sm text-gray-500">Fujiran</span>
          <span class="text-sm text-gray-500">Ras Al Khima</span>
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
            How we support our partners<br />all over the Emirates
          </h2>
          <p class="mt-6 text-sm leading-relaxed text-gray-500">
            Offering an integrated SaaS platform that empowers female merchants and service providers to manage their businesses, services, and orders from one smart dashboard and stand by your side, receive your portion and take care of your customers.
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
              <p class="text-xs text-gray-500">trustpilots</p>
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
              <p class="text-xs text-gray-500">Clutchanlytica</p>
            </div>
          </div>
        </div>
        <div class="flex flex-col gap-8">
          <div class="flex gap-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand-pink-bg">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
            </div>
            <div>
              <h3 class="text-base font-semibold text-brand-dark">Business Operations Hub</h3>
              <p class="mt-1 text-sm text-gray-500">Everything you need to run your store or service - inventory, services, schedules, and branches all in one place.</p>
            </div>
          </div>
          <div class="flex gap-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand-pink-bg">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div>
              <h3 class="text-base font-semibold text-brand-dark">Commerce &amp; Revenue Control</h3>
              <p class="mt-1 text-sm text-gray-500">Full visibility into orders, bookings, commissions, earnings, and history with secure and transparent financial tracking.</p>
            </div>
          </div>
          <div class="flex gap-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand-pink-bg">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div>
              <h3 class="text-base font-semibold text-brand-dark">Engagement</h3>
              <p class="mt-1 text-sm text-gray-500">Navigate your dashboard with ease and engage with your clients.</p>
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
        <h2 class="font-serif text-3xl leading-tight text-brand-dark md:text-4xl">Our Features<br />you can get</h2>
        <div class="max-w-md">
          <p class="text-sm leading-relaxed text-gray-500">We offer a variety of interesting features that you can help increase your productivity at work and manage your project easily.</p>
          <button class="mt-4 rounded-full bg-brand-pink px-6 py-2.5 text-sm font-medium text-white transition-opacity hover:opacity-90">Get Started</button>
        </div>
      </div>
      <div class="mt-14 grid gap-6 md:grid-cols-3">
        <div class="relative overflow-hidden rounded-2xl bg-[#FAA8BF]">
          <img src="{{asset('assets/FD.jpg')}}" alt="Free delivery feature" width="400" height="250" class="h-48 w-full object-cover" />
        </div>
        <div class="overflow-hidden rounded-2xl">
          <div class="relative">
            <div class="absolute right-4 top-4 z-10 flex gap-2">
              <div class="rounded-lg bg-white/90 px-3 py-1.5 text-xs font-semibold text-brand-dark backdrop-blur-sm">86%</div>
              <div class="rounded-lg bg-white/90 px-3 py-1.5 text-xs font-semibold text-brand-dark backdrop-blur-sm">44%</div>
            </div>
            <img src="{{asset('assets/RC.jpg')}}" alt="Resolution center feature" width="400" height="300" class="h-64 w-full rounded-2xl object-cover" />
          </div>
        </div>
        <div class="overflow-hidden rounded-2xl">
          <img src="{{asset('assets/DA.jpg')}}" alt="Daily analytics feature" width="400" height="300" class="h-64 w-full rounded-2xl object-cover" />
        </div>
      </div>
      <div class="mt-8 grid gap-6 md:grid-cols-3">
        <div>
          <h3 class="text-lg font-semibold text-brand-dark">Free Delivery</h3>
          <p class="mt-2 text-sm text-gray-500">Here you can handle projects together with team virtually.</p>
        </div>
        <div>
          <h3 class="text-lg font-semibold text-brand-dark">Resolution Center</h3>
          <p class="mt-2 text-sm text-gray-500">No need to worry about storage because we provide storage up to 2 TB.</p>
        </div>
        <div>
          <h3 class="text-lg font-semibold text-brand-dark">Daily Analytics</h3>
          <p class="mt-2 text-sm text-gray-500">We always provide useful information to make it easier for you every day.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- BENEFITS -->
  <section class="bg-brand-pink-bg py-20">
    <div class="mx-auto max-w-7xl px-6">
      <div class="grid items-center gap-12 lg:grid-cols-2">
        <div>
          <h2 class="font-serif text-3xl leading-tight text-brand-dark md:text-4xl">What Benefit Will<br />You Get</h2>
          <div class="mt-8 flex flex-col gap-5">
            <div class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              <span class="text-sm text-brand-dark">All-in-One Business Dashboard</span>
            </div>
            <div class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              <span class="text-sm text-brand-dark">Smart Order &amp; Booking Management</span>
            </div>
            <div class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              <span class="text-sm text-brand-dark">Business Insights &amp; Reports</span>
            </div>
            <div class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              <span class="text-sm text-brand-dark">Secure Payments &amp; Payout Tracking</span>
            </div>
            <div class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              <span class="text-sm text-brand-dark">Online Transaction</span>
            </div>
          </div>
        </div>
        <div class="relative flex justify-center">
          <div class="absolute left-1/2 top-1/2 h-80 w-80 -translate-x-1/2 -translate-y-1/2 rounded-full bg-brand-pink-light opacity-50 blur-3xl"></div>
          <div class="relative z-10 w-full max-w-sm">
            <div class="absolute -left-4 top-8 z-20 rounded-xl bg-white p-3 shadow-lg">
              <div class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-full bg-pink-100"></div>
                <div>
                  <p class="text-xs font-semibold text-brand-dark">Amanda Young</p>
                  <p class="text-[10px] text-gray-500">Transfer</p>
                </div>
              </div>
            </div>
            <div class="absolute -right-2 top-20 z-20 rounded-xl bg-white px-4 py-2 shadow-lg">
              <span class="text-sm font-semibold text-brand-dark">245.00 AED</span>
            </div>
            <img src="{{ asset('assets/mobileHome.jpg') }}" alt="App mockup showing money transfer" width="350" height="500" class="mx-auto h-auto w-64 rounded-3xl object-cover shadow-2xl" />
            <div class="absolute -right-6 bottom-16 z-20 rounded-xl bg-white px-4 py-2 shadow-lg">
              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span class="text-xs font-medium text-brand-dark">Money Transfer Successful</span>
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
        <h2 class="font-serif text-3xl leading-tight text-brand-dark md:text-4xl">Choose Plan<br />That's Right For You</h2>
        <p class="mt-4 text-sm text-gray-500">Choose plan that works best for you, feel free to contact us</p>
        <div class="mt-6 inline-flex items-center rounded-full bg-gray-100 p-1">
          <button id="btn-monthly" class="rounded-full px-5 py-2 text-sm font-medium text-gray-500" onclick="togglePricing('monthly')">Bill Monthly</button>
          <button id="btn-yearly" class="rounded-full bg-brand-pink px-5 py-2 text-sm font-medium text-white shadow-sm" onclick="togglePricing('yearly')">Bill Yearly</button>
        </div>
      </div>
      <div class="mt-12 grid gap-6 md:grid-cols-3">
        <!-- Merchant -->
        <div class="rounded-2xl border border-gray-200 bg-white p-8">
          <h3 class="text-xl font-semibold text-brand-dark">Merchant</h3>
          <p class="mt-2 text-sm text-gray-500">Here is go, and test your superpowers</p>
          <div class="mt-6"><span class="text-4xl font-bold text-brand-dark">99</span></div>
          <ul class="mt-8 flex flex-col gap-4">
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">2 Users</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">2 Files</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">Public Share &amp; Comments</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">Chat Support</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">New Income apps</span>
            </li>
          </ul>
          <button class="mt-8 w-full rounded-full border border-gray-200 py-3 text-sm font-medium text-brand-dark transition-colors hover:bg-gray-50">Signup for Free</button>
        </div>
        <!-- Vendor (highlighted) -->
        <div class="relative overflow-hidden rounded-2xl bg-brand-pink p-8 text-white">
          <div class="absolute right-4 top-4 rounded-full bg-white/20 px-3 py-1 text-xs font-medium text-white">Save 63 a year</div>
          <h3 class="text-xl font-semibold">Vendor</h3>
          <p class="mt-2 text-sm text-white/80">Enjoy the full power of infinite possibilities</p>
          <div class="mt-6"><span class="text-4xl font-bold">99 AED</span></div>
          <ul class="mt-8 flex flex-col gap-4">
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm">4 Users</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm">All apps</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm">Unlimited editable exports</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm">Folders and collaboration</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm">All Incoming apps</span>
            </li>
          </ul>
          <button class="mt-8 w-full rounded-full bg-white py-3 text-sm font-medium text-brand-pink transition-opacity hover:opacity-90">Go to pro</button>
        </div>
        <!-- Provider -->
        <div class="rounded-2xl border border-gray-200 bg-white p-8">
          <h3 class="text-xl font-semibold text-brand-dark">Provider</h3>
          <p class="mt-2 text-sm text-gray-500">Unlock new superpowers and join the Design League</p>
          <div class="mt-6"><span class="text-4xl font-bold text-brand-dark">99</span></div>
          <ul class="mt-8 flex flex-col gap-4">
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">All the features of pro plan</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">Account success Manager</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">Single Sign-On (SSO)</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">Co-conception program</span>
            </li>
            <li class="flex items-center gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d9657a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              <span class="text-sm text-brand-dark">Collaboration Soon</span>
            </li>
          </ul>
          <button class="mt-8 w-full rounded-full border border-gray-200 py-3 text-sm font-medium text-brand-dark transition-colors hover:bg-gray-50">Gain Business</button>
        </div>
      </div>
    </div>
  </section>

  <!-- TESTIMONIAL + CONTACT -->
  <section class="bg-brand-dark py-20 text-gray-100">
    <div class="mx-auto max-w-7xl px-6">
      <div class="grid gap-12 lg:grid-cols-2">
        <div>
          <h2 class="font-serif text-3xl leading-tight md:text-4xl">People are Saying<br />About DoWhith</h2>
          <p class="mt-4 text-sm leading-relaxed text-gray-400">Everything you need to accept to payment and grow your money or manage anywhere on planet.</p>
          <div class="mt-10">
            <span class="font-serif text-5xl text-brand-pink">&ldquo;</span>
            <p class="mt-2 text-sm leading-relaxed text-gray-300">I am very helped by this E-wallet application, my days are very easy to use this application and it's very helpful in my life, even I can pay a short time.</p>
            <div class="mt-6">
              <p class="text-sm font-medium text-gray-100">. Aria Zinianko</p>
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
          <h3 class="text-center text-xl font-semibold text-white">Get Started</h3>
          <form class="mt-6 flex flex-col gap-4" onsubmit="event.preventDefault()">
            <div>
              <label for="email" class="text-xs text-gray-400">Email</label>
              <input id="email" type="email" class="mt-1 w-full rounded-lg border border-brand-dark-border bg-brand-dark px-4 py-3 text-sm text-gray-200 placeholder:text-gray-500 focus:border-brand-pink focus:outline-none" placeholder="Enter your email" />
            </div>
            <div>
              <label for="message" class="text-xs text-gray-400">Message</label>
              <textarea id="message" rows="3" class="mt-1 w-full resize-none rounded-lg border border-brand-dark-border bg-brand-dark px-4 py-3 text-sm text-gray-200 placeholder:text-gray-500 focus:border-brand-pink focus:outline-none" placeholder="What are you say ?"></textarea>
            </div>
            <button type="submit" class="rounded-lg bg-brand-pink py-3 text-sm font-medium text-white transition-opacity hover:opacity-90">Contact now</button>
            <p class="text-center text-xs text-gray-500">Start Free Trial</p>
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
          <a href="/" class="font-serif text-2xl text-brand-dark">Dala3chic</a>
          <p class="mt-3 text-sm text-gray-500">Get started now! try our product</p>
          <div class="mt-4 flex">
            <input type="email" placeholder="Enter your email here" class="w-full max-w-[240px] rounded-l-full border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-brand-dark placeholder:text-gray-400 focus:border-brand-pink focus:outline-none" />
            <button class="-ml-2 flex h-10 w-10 items-center justify-center rounded-full bg-brand-pink text-white">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7" /></svg>
            </button>
          </div>
        </div>
        <div>
          <h4 class="text-sm font-semibold text-brand-dark">Support</h4>
          <ul class="mt-4 flex flex-col gap-3">
            <li><a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">Help centre</a></li>
            <li><a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">Account information</a></li>
            <li><a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">About</a></li>
            <li><a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">Contact us</a></li>
          </ul>
        </div>
        <div>
          <h4 class="text-sm font-semibold text-brand-dark">Help and Solution</h4>
          <ul class="mt-4 flex flex-col gap-3">
            <li><a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">Talk to support</a></li>
            <li><a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">Support docs</a></li>
            <li><a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">System status</a></li>
            <li><a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">Covid response</a></li>
          </ul>
        </div>
        <div>
          <h4 class="text-sm font-semibold text-brand-dark">Product</h4>
          <ul class="mt-4 flex flex-col gap-3">
            <li><a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">Update</a></li>
            <li><a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">Security</a></li>
            <li><a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">Beta test</a></li>
            <li><a href="#" class="text-sm text-gray-500 transition-colors hover:text-brand-dark">Pricing product</a></li>
          </ul>
        </div>
      </div>
    </div>
    <div class="border-t border-gray-200">
      <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-6 py-5 md:flex-row">
        <p class="text-xs text-gray-500">&copy; 2022 Biccas Inc. Copyright and rights reserved</p>
        <div class="flex gap-6">
          <a href="#" class="text-xs text-gray-500 transition-colors hover:text-brand-dark">Terms and Conditions</a>
          <a href="#" class="text-xs text-gray-500 transition-colors hover:text-brand-dark">Privacy Policy</a>
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
