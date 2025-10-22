@extends('layouts.service-provider')

@section('title', __('service_provider.bookings'))
@section('page-title', __('service_provider.bookings'))

@section('content')
@php
    $filterFlags = [
        'search' => request()->filled('search'),
        'status' => request()->filled('status'),
        'date_from' => request()->filled('date_from'),
        'date_to' => request()->filled('date_to'),
    ];
    $hasFilters = in_array(true, $filterFlags, true);
    $activeFilterCount = array_sum(array_map(fn ($flag) => $flag ? 1 : 0, $filterFlags));
    $totalBookings = isset($bookings)
        ? (method_exists($bookings, 'total') ? $bookings->total() : $bookings->count())
        : 0;
@endphp
<div class="container mx-auto">
    <!-- Filters -->
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700/60">
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('service_provider.bookings') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('service_provider.bookings_appear_here') }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="flex items-center px-3 py-1 rounded-full bg-[#53D2DC]/10 text-[#31818A] text-xs font-semibold">
                        <span class="inline-flex items-center mr-1.5 h-2.5 w-2.5 rounded-full bg-[#53D2DC]"></span>
                        {{ __('service_provider.total_bookings') ?? 'Total' }}: {{ number_format($totalBookings) }}
                    </div>
                    @if($hasFilters)
                        <a href="{{ route('service-provider.bookings.index') }}" class="inline-flex items-center px-3 py-1 text-xs font-semibold text-gray-600 dark:text-gray-300 hover:text-[#53D2DC] transition-colors">
                            <i class="fas fa-times-circle mr-1"></i> {{ __('service_provider.clear_all_filters') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
        <form method="GET" class="px-6 py-5 grid grid-cols-1 gap-4 md:grid-cols-5">
            <div class="md:col-span-2">
                <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">{{ __('service_provider.search') }}</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center text-[#53D2DC]">
                        <i class="fas fa-search text-sm"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('service_provider.customer_or_service') }}"
                        class="w-full rounded-lg pl-9 pr-3 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white bg-gray-50 focus:bg-white dark:focus:bg-gray-700 focus:ring-2 focus:ring-[#53D2DC]/40 focus:border-[#53D2DC]" />
                </div>
            </div>
            <div>
                <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">{{ __('service_provider.status') }}</label>
                <select name="status" class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white bg-gray-50 focus:bg-white dark:focus:bg-gray-700 focus:ring-2 focus:ring-[#53D2DC]/40 focus:border-[#53D2DC]">
                    <option value="">{{ __('service_provider.all') }}</option>
                    @foreach(['pending','confirmed','in_progress','completed','cancelled'] as $st)
                        <option value="{{ $st }}" @selected(request('status')==$st)>{{ __('service_provider.booking_status_' . $st) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">{{ __('service_provider.from') }}</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white bg-gray-50 focus:bg-white dark:focus:bg-gray-700 focus:ring-2 focus:ring-[#53D2DC]/40 focus:border-[#53D2DC]" />
            </div>
            <div>
                <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">{{ __('service_provider.to') }}</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white bg-gray-50 focus:bg-white dark:focus:bg-gray-700 focus:ring-2 focus:ring-[#53D2DC]/40 focus:border-[#53D2DC]" />
            </div>
            <div class="flex items-end">
                <button class="inline-flex items-center px-4 py-2 bg-[#53D2DC] text-white rounded-lg font-semibold shadow-sm hover:opacity-90 active:opacity-80 focus:outline-none focus:ring-4 focus:ring-[#53D2DC]/30 transition">
                    <i class="fas fa-filter mr-2 text-sm"></i> {{ __('service_provider.filter') }}
                </button>
            </div>
        </form>
    </div>

    <!-- List -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700/80 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700/60 flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center space-x-2">
                <i class="fas fa-calendar-check text-[#53D2DC]"></i>
                <span>{{ __('service_provider.bookings') }}</span>
            </h3>
            <div class="flex items-center gap-3">
                @if($hasFilters)
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#53D2DC]/15 text-[#31818A] text-xs font-semibold">
                        <i class="fas fa-filter mr-1"></i> {{ __('service_provider.filters_active', ['count' => $activeFilterCount]) }}
                    </span>
                @endif
                <a href="{{ route('service-provider.bookings.calendar') }}" class="inline-flex items-center px-3 py-1.5 border border-[#53D2DC] text-[#53D2DC] rounded-lg text-xs font-semibold uppercase tracking-wide hover:bg-[#53D2DC] hover:text-white transition-colors duration-150">
                    <i class="fas fa-calendar-alt mr-2 text-xs"></i> {{ __('messages.calendar_view') }}
                </a>
            </div>
        </div>
        @if(($bookings ?? collect())->count())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300">{{ __('service_provider.customer') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300">{{ __('service_provider.service') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300">{{ __('service_provider.date') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300">{{ __('service_provider.status') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-300">{{ __('messages.actions') ?? 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($bookings as $booking)
                            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/40 transition-colors">
                                <td class="px-6 py-4 text-gray-900 dark:text-white font-medium">
                                    <div>{{ $booking->user->name ?? __('service_provider.unknown_customer') }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $booking->user->email ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                                    <div class="flex items-center space-x-3">
                                        @if($booking->service && $booking->service->image)
                                            <img src="{{ $booking->service->image }}" alt="{{ $booking->service->name }}" class="h-10 w-10 rounded-lg object-cover border border-gray-200 dark:border-gray-700">
                                        @endif
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $booking->service->name ?? __('service_provider.unknown_service') }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $booking->branch->name ?? __('service_provider.no_branch') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ optional($booking->booking_date)->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $booking->booking_time ? date('h:i A', strtotime($booking->booking_time)) : '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusStyles = [
                                            'pending' => 'bg-amber-50 text-amber-700 border border-amber-200',
                                            'confirmed' => 'bg-blue-50 text-blue-700 border border-blue-200',
                                            'in_progress' => 'bg-purple-50 text-purple-700 border border-purple-200',
                                            'completed' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                            'cancelled' => 'bg-rose-50 text-rose-700 border border-rose-200',
                                        ];

                                        $statusDot = [
                                            'pending' => 'bg-amber-500',
                                            'confirmed' => 'bg-blue-500',
                                            'in_progress' => 'bg-purple-500',
                                            'completed' => 'bg-emerald-500',
                                            'cancelled' => 'bg-rose-500',
                                        ];

                                        $status = $booking->status ?? 'pending';
                                @endphp
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wide {{ $statusStyles[$status] ?? 'bg-gray-100 text-gray-700 border border-gray-200' }}">
                                    <span class="inline-flex h-2.5 w-2.5 rounded-full mr-2 {{ $statusDot[$status] ?? 'bg-gray-400' }}"></span>
                                    {{ __('service_provider.booking_status_' . $status) ?? ucfirst($status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex flex-wrap gap-2 justify-end">
                                        <a href="{{ route('service-provider.bookings.show', $booking) }}" class="inline-flex items-center px-3 py-1.5 border border-[#53D2DC] text-[#53D2DC] rounded-lg text-xs font-semibold uppercase tracking-wide hover:bg-[#53D2DC] hover:text-white transition-colors duration-150">
                                            <i class="fas fa-eye mr-2 text-xs"></i> {{ __('messages.view_details') }}
                                        </a>
                                        <a href="{{ route('service-provider.bookings.edit', $booking) }}" class="inline-flex items-center px-3 py-1.5 border border-[#53D2DC] text-[#53D2DC] rounded-lg text-xs font-semibold uppercase tracking-wide hover:bg-[#53D2DC] hover:text-white transition-colors duration-150">
                                            <i class="fas fa-edit mr-2 text-xs"></i> {{ __('messages.edit_booking') }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">{{ $bookings->links() }}</div>
        @else
            <div class="p-8 text-center">
                <div class="mx-auto h-14 w-14 rounded-full bg-[#53D2DC]/15 flex items-center justify-center">
                    <i class="fas fa-calendar-check text-[#53D2DC]"></i>
                </div>
                <h4 class="mt-3 text-gray-900 dark:text-white font-medium">{{ __('service_provider.no_bookings_found') }}</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('service_provider.bookings_appear_here') }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
