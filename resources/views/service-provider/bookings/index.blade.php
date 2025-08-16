@extends('layouts.service-provider')

@section('title', __('service_provider.bookings'))
@section('page-title', __('service_provider.bookings'))

@section('content')
<div class="container mx-auto">
    <!-- Filters -->
    <div class="mb-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <div class="md:col-span-2">
                <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">{{ __('service_provider.search') }}</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('service_provider.customer_or_service') }}"
                    class="w-full rounded-md  p-1 border border-[#53D2DC] dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-0 focus:border-transparent" />
            </div>
            <div>
                <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">{{ __('service_provider.status') }}</label>
                <select name="status" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-0 focus:border-[#53D2DC]">
                    <option value="">{{ __('service_provider.all') }}</option>
                    @foreach(['pending','confirmed','in_progress','completed','cancelled'] as $st)
                        <option value="{{ $st }}" @selected(request('status')==$st)>{{ __('service_provider.booking_status_' . $st) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">{{ __('service_provider.from') }}</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-0 focus:border-[#53D2DC]" />
            </div>
            <div>
                <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">{{ __('service_provider.to') }}</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-0 focus:border-[#53D2DC]" />
            </div>
            <div class="flex items-end">
                <button class="inline-flex items-center px-4 py-2 bg-[#53D2DC] text-white rounded-md hover:opacity-90 active:opacity-80">
                    <i class="fas fa-filter mr-2"></i> {{ __('service_provider.filter') }}
                </button>
            </div>
        </form>
    </div>

    <!-- List -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('service_provider.bookings') }}</h3>
        </div>
        @if(($bookings ?? collect())->count())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('service_provider.customer') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('service_provider.service') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('service_provider.date') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('service_provider.status') }}</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($bookings as $booking)
                            <tr>
                                <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $booking->user->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $booking->service->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ optional($booking->booking_date)->format('Y-m-d') }} {{ $booking->booking_time }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @class([
                                            'bg-yellow-100 text-yellow-800' => $booking->status==='pending',
                                            'bg-blue-100 text-blue-800' => $booking->status==='confirmed',
                                            'bg-green-100 text-green-800' => $booking->status==='completed',
                                            'bg-red-100 text-red-800' => $booking->status==='cancelled',
                                            'bg-gray-100 text-gray-800' => !in_array($booking->status,["pending","confirmed","completed","cancelled"])])">
                                        {{ __('service_provider.booking_status_' . $booking->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('service-provider.bookings.show', $booking) }}" class="text-[#53D2DC] hover:underline text-sm">{{ __('service_provider.view') }}</a>
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
