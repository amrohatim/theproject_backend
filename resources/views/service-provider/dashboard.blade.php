@extends('layouts.service-provider')

@section('title', __('service_provider.dashboard'))
@section('page-title', __('service_provider.dashboard'))

@section('content')
<div class="container mx-auto">
    <!-- Welcome Section -->
    <div class="mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('service_provider.welcome_user', ['name' => $user->name]) }}</h2>
                    <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('service_provider.dashboard_overview') }}</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        {{ __('service_provider.service_provider') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total Services -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="rounded-md bg-blue-100 dark:bg-blue-900 p-3">
                        <i class="fas fa-cog text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('service_provider.total_services') }}</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_services'] }}</div>
                </div>
            </div>
        </div>

        <!-- Total Branches -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="rounded-md bg-green-100 dark:bg-green-900 p-3">
                        <i class="fas fa-store text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('service_provider.accessible_branches') }}</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_branches'] }}</div>
                </div>
            </div>
        </div>

        <!-- Total Bookings -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="rounded-md bg-purple-100 dark:bg-purple-900 p-3">
                        <i class="fas fa-calendar-check text-purple-600 dark:text-purple-400 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('service_provider.total_bookings') }}</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_bookings'] }}</div>
                </div>
            </div>
        </div>

        <!-- Pending Bookings -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="rounded-md bg-yellow-100 dark:bg-yellow-900 p-3">
                        <i class="fas fa-clock text-yellow-600 dark:text-yellow-400 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('service_provider.pending_bookings') }}</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['pending_bookings'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Services -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('service_provider.your_services') }}</h3>
                    <a href="{{ route('service-provider.services.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                        {{ __('service_provider.view_all') }}
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if($services->count() > 0)
                    <div class="space-y-4">
                        @foreach($services->take(5) as $service)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        @if($service->image)
                                            <img src="{{ asset($service->image) }}" alt="{{ $service->name }}" class="h-10 w-10 rounded-lg object-cover">
                                        @else
                                            <div class="h-10 w-10 rounded-lg bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                                <i class="fas fa-cog text-blue-600 dark:text-blue-400"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $service->name }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $service->branch->name ?? __('service_provider.no_branch') }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($service->price, 2) }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $service->duration }} min</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <div class="mx-auto h-12 w-12 text-gray-400">
                            <i class="fas fa-cog text-4xl"></i>
                        </div>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('service_provider.no_services_assigned') }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('service_provider.contact_admin_services') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('service_provider.recent_bookings') }}</h3>
                    <a href="{{ route('service-provider.bookings.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                        {{ __('service_provider.view_all') }}
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if($recentBookings->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentBookings as $booking)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                                            <i class="fas fa-user text-purple-600 dark:text-purple-400"></i>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $booking->user->name ?? __('service_provider.unknown_customer') }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $booking->service->name ?? __('service_provider.unknown_service') }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($booking->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @elseif($booking->status === 'confirmed') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                        @elseif($booking->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @endif">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $booking->booking_date->format('M d, Y') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <div class="mx-auto h-12 w-12 text-gray-400">
                            <i class="fas fa-calendar-check text-4xl"></i>
                        </div>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('service_provider.no_recent_bookings') }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('service_provider.bookings_appear_here') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Active Deals -->
    @if($activeDeals->count() > 0)
        <div class="mt-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('service_provider.active_deals') }}</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($activeDeals as $deal)
                            <div class="p-4 bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900 dark:to-green-800 rounded-lg border border-green-200 dark:border-green-700">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-medium text-green-900 dark:text-green-100">{{ $deal->title }}</h4>
                                        <p class="text-sm text-green-700 dark:text-green-300">{{ $deal->service->name ?? __('service_provider.service') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-lg font-bold text-green-900 dark:text-green-100">{{ $deal->discount_percentage }}% OFF</span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <p class="text-xs text-green-600 dark:text-green-400">
                                        {{ __('service_provider.valid_until', ['date' => $deal->end_date->format('M d, Y')]) }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh statistics every 5 minutes
    setInterval(function() {
        fetch('{{ route("service-provider.dashboard.stats") }}')
            .then(response => response.json())
            .then(data => {
                // Update statistics if needed
                console.log('Statistics updated:', data);
            })
            .catch(error => console.error('Error updating statistics:', error));
    }, 300000); // 5 minutes
});
</script>
@endsection
