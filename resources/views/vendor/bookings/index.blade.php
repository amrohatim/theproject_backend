@extends('layouts.dashboard')

@section('title', 'Bookings Management')
@section('page-title', 'Bookings Management')

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('messages.bookings_management') }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('messages.manage_service_bookings') }}</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('vendor.bookings.calendar') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 mr-2">
                <i class="fas fa-calendar-alt mr-2"></i> {{ __('messages.calendar_view') }}
            </a>
            <a href="{{ route('vendor.bookings.export') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-file-export mr-2"></i> {{ __('messages.export_bookings') }}
            </a>
        </div>
    </div>

    <!-- Search and filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 p-4 border border-gray-200 dark:border-gray-700">
        <form action="{{ route('vendor.bookings.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.search') }}</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" id="search" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="{{ __('messages.search_booking_placeholder') }}">
                    </div>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.status') }}</label>
                <select name="status" id="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">{{ __('messages.all_status') }}</option>
                    <option value="pending">{{ __('messages.pending') }}</option>
                    <option value="confirmed">{{ __('messages.confirmed') }}</option>
                    <option value="completed">{{ __('messages.completed') }}</option>
                    <option value="cancelled">{{ __('messages.cancelled') }}</option>
                    <option value="no_show">{{ __('messages.no_show') }}</option>
                </select>
                </div>

                <div>
                    <label for="branch" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.branch') }}</label>
                <select name="branch" id="branch" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">{{ __('messages.all_branches') }}</option>
                    @foreach($branches ?? [] as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
                </div>

                <div>
                    <label for="date_range" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.date_range') }}</label>
                <select name="date_range" id="date_range" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">{{ __('messages.all_time') }}</option>
                    <option value="today">{{ __('messages.today') }}</option>
                    <option value="tomorrow">{{ __('messages.tomorrow') }}</option>
                    <option value="this_week">{{ __('messages.this_week') }}</option>
                    <option value="next_week">{{ __('messages.next_week') }}</option>
                    <option value="this_month">{{ __('messages.this_month') }}</option>
                    <option value="past">{{ __('messages.past_bookings') }}</option>
                </select>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-filter mr-2"></i> {{ __('messages.filter') }}
            </button>
            </div>
        </form>
    </div>

    <!-- Bookings Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100 dark:bg-indigo-900 mr-4">
                    <i class="fas fa-calendar-check text-indigo-500 dark:text-indigo-400"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.total_bookings') }}</p>
                    <p class="text-2xl font-semibold text-gray-800 dark:text-white">{{ $stats->total_bookings ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900 mr-4">
                    <i class="fas fa-check-circle text-green-500 dark:text-green-400"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.completed') }}</p>
                    <p class="text-2xl font-semibold text-gray-800 dark:text-white">{{ $stats->completed_bookings ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900 mr-4">
                    <i class="fas fa-clock text-yellow-500 dark:text-yellow-400"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.upcoming') }}</p>
                    <p class="text-2xl font-semibold text-gray-800 dark:text-white">{{ $stats->upcoming_bookings ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 mr-4">
                    <i class="fas fa-dollar-sign text-blue-500 dark:text-blue-400"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('messages.total_revenue') }}</p>
                    <p class="text-2xl font-semibold text-gray-800 dark:text-white">${{ number_format($stats->total_revenue ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bookings list -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.booking_id') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.customer') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.service') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.branch') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.date_time') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.status') }}</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($bookings ?? [] as $booking)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">#{{ $booking->booking_number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">{{ $booking->user->name ?? __('messages.guest') }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $booking->user->email ?? __('messages.no_email') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">{{ $booking->service->name ?? __('messages.unknown_service') }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">${{ number_format($booking->service->price ?? 0, 2) }} • {{ $booking->service->duration ?? 0 }} {{ __('messages.min') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $booking->branch->name ?? __('messages.no_branch') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $booking->booking_date ? date('M d, Y', strtotime($booking->booking_date)) : __('messages.no_date') }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $booking->booking_time ? date('h:i A', strtotime($booking->booking_time)) : '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($booking->status == 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($booking->status == 'confirmed') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @elseif($booking->status == 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @elseif($booking->status == 'no_show') bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @endif">
                                {{ ucfirst($booking->status ?? 'pending') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('vendor.bookings.show', $booking->id) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-3">
                                <i class="fas fa-eye"></i> {{ __('messages.view') }}
                            </a>
                            <a href="{{ route('vendor.bookings.edit', $booking->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 mr-3">
                                <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                            </a>
                            <a href="{{ route('vendor.bookings.invoice', $booking->id) }}" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300">
                                <i class="fas fa-file-invoice"></i> {{ __('messages.invoice') }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center py-4">
                                <i class="fas fa-calendar-alt text-gray-300 dark:text-gray-600 text-5xl mb-4"></i>
                                <p>{{ __('messages.no_bookings_found') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        @if(method_exists($bookings, 'links'))
            {{ $bookings->links() }}
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/vendor-autocomplete.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    if (searchInput) {
        new VendorAutoComplete(searchInput, {
            apiUrl: '{{ route('vendor.bookings.search-suggestions') }}',
            placeholder: 'Search bookings, customers, services...'
        });
    }
});
</script>
@endpush
