@extends('layouts.dashboard')

@section('title', __('messages.analytics'))
@section('page-title', __('messages.analytics'))

@section('styles')
<style>
    .shad-card {
        border-radius: 0.75rem;
        border: 1px solid rgb(229 231 235);
        background-color: #fff;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }
    .dark .shad-card {
        border-color: rgb(55 65 81);
        background-color: rgb(31 41 55);
    }
    .tab-trigger {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 500;
        border-radius: 0.5rem;
        transition: color 0.2s ease, background-color 0.2s ease;
    }
    .tab-trigger.active {
        background-color: var(--primary);
        color: #fff;
    }
    .tab-trigger:not(.active) {
        color: rgb(75 85 99);
    }
    .tab-trigger:not(.active):hover {
        background-color: rgb(243 244 246);
    }
    .dark .tab-trigger:not(.active) {
        color: rgb(209 213 219);
    }
    .dark .tab-trigger:not(.active):hover {
        background-color: rgb(55 65 81);
    }
    .tab-panel { display: none; }
    .tab-panel.active { display: block; }
    .empty-state {
        border: 1px dashed rgb(209 213 219);
        border-radius: 0.5rem;
        padding: 1rem;
        text-align: center;
        color: rgb(107 114 128);
        font-size: 0.875rem;
    }
    .dark .empty-state {
        border-color: rgb(75 85 99);
        color: rgb(156 163 175);
    }
</style>
@endsection

@section('content')
<div class="container mx-auto space-y-6">
    @php
        $isArabic = app()->getLocale() === 'ar';
        $statusLabels = [
            'active' => __('messages.active'),
            'inactive' => __('messages.inactive'),
            'approved' => __('messages.approved'),
            'pending' => __('messages.pending'),
            'cancelled' => __('messages.cancelled'),
            'confirmed' => $isArabic ? 'مؤكد' : 'Confirmed',
            'completed' => __('messages.completed'),
            'rejected' => __('messages.rejected'),
            'processing' => $isArabic ? 'قيد المعالجة' : 'Processing',
            'shipped' => $isArabic ? 'تم الشحن' : 'Shipped',
            'delivered' => $isArabic ? 'تم التسليم' : 'Delivered',
            'no_show' => $isArabic ? 'عدم حضور' : 'No Show',
            'none' => $isArabic ? 'لا يوجد' : 'None',
        ];
        $allStatusesLabel = $isArabic ? 'كل الحالات' : 'All statuses';

        $qualityEntityLabels = [
            'branches' => $isArabic ? 'الفروع' : 'Branches',
            'products' => $isArabic ? 'المنتجات' : 'Products',
            'services' => $isArabic ? 'الخدمات' : 'Services',
        ];
        $qualityFieldLabels = [
            'emirate' => $isArabic ? 'الإمارة' : 'Emirate',
            'address' => $isArabic ? 'العنوان' : 'Address',
            'category_id' => $isArabic ? 'معرف التصنيف' : 'Category ID',
        ];
        $qualityMetricLabels = [
            'branch_view_count' => $isArabic ? 'مشاهدات الفروع' : 'Branch Views',
            'product_order_count' => $isArabic ? 'طلبات المنتجات' : 'Product Orders',
            'service_order_count' => $isArabic ? 'طلبات الخدمات' : 'Service Orders',
        ];
        $funnelStageLabels = [
            'pending' => $statusLabels['pending'],
            'confirmed' => $statusLabels['confirmed'],
            'completed' => $statusLabels['completed'],
            'cancelled_or_no_show' => $isArabic ? 'ملغي أو عدم حضور' : 'Cancelled or No Show',
            'processing' => $statusLabels['processing'],
            'shipped' => $statusLabels['shipped'],
            'delivered' => $statusLabels['delivered'],
            'cancelled' => $statusLabels['cancelled'],
        ];
        $normalizeFieldLabel = function (string $value) use ($qualityFieldLabels) {
            return $qualityFieldLabels[$value] ?? str_replace(' Id', ' ID', ucwords(str_replace('_', ' ', $value)));
        };
        $normalizeMetricLabel = function (string $value) use ($qualityMetricLabels) {
            return $qualityMetricLabels[$value] ?? ucwords(str_replace('_', ' ', $value));
        };
        $normalizeEntityLabel = function (string $value) use ($qualityEntityLabels) {
            return $qualityEntityLabels[$value] ?? ucwords(str_replace('_', ' ', $value));
        };
        $normalizeStatusLabel = function (?string $value) use ($statusLabels) {
            if (is_null($value) || $value === '') {
                return '-';
            }

            return $statusLabels[$value] ?? ucwords(str_replace('_', ' ', $value));
        };
    @endphp

    <div class="shad-card p-4">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('messages.vendor_analytics_title') }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.vendor_analytics_desc') }}</p>
            </div>

            <details class="relative inline-block">
                <summary class="cursor-pointer list-none inline-flex items-center gap-2 rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                    <i class="fas fa-sliders-h"></i>
                    {{ __('messages.filter') }}
                </summary>
                <div class="absolute mt-2 w-[320px] rounded-lg border border-gray-200 bg-white p-4 shadow-lg dark:border-gray-700 dark:bg-gray-800 z-20" style="inset-inline-end: 0;">
                    <form method="GET" action="{{ route('vendor.analytics.index') }}" class="space-y-3">
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">{{ __('messages.date_range') }}</label>
                            <select name="date_range" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                                <option value="7d" {{ ($filters['date_range'] ?? 'all') === '7d' ? 'selected' : '' }}>Last 7 days</option>
                                <option value="30d" {{ ($filters['date_range'] ?? 'all') === '30d' ? 'selected' : '' }}>Last 30 days</option>
                                <option value="90d" {{ ($filters['date_range'] ?? 'all') === '90d' ? 'selected' : '' }}>Last 90 days</option>
                                <option value="365d" {{ ($filters['date_range'] ?? 'all') === '365d' ? 'selected' : '' }}>Last 12 months</option>
                                <option value="all" {{ ($filters['date_range'] ?? 'all') === 'all' ? 'selected' : '' }}>All time</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">{{ __('messages.branch') }}</label>
                            <select name="branch_id" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                                <option value="">All branches</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ (int) ($filters['branch_id'] ?? 0) === (int) $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">{{ __('messages.status') }}</label>
                            <select name="status" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                                <option value="">{{ $allStatusesLabel }}</option>
                                <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>{{ $statusLabels['active'] }}</option>
                                <option value="approved" {{ ($filters['status'] ?? '') === 'approved' ? 'selected' : '' }}>{{ $statusLabels['approved'] }}</option>
                                <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>{{ $statusLabels['pending'] }}</option>
                                <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>{{ $statusLabels['inactive'] }}</option>
                                <option value="cancelled" {{ ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>{{ $statusLabels['cancelled'] }}</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-2 pt-1">
                            <button type="submit" class="rounded-md bg-[var(--primary)] px-3 py-2 text-sm font-semibold text-white">
                                {{ __('messages.apply') }}
                            </button>
                            <a href="{{ route('vendor.analytics.index') }}" class="rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 dark:border-gray-600 dark:text-gray-300">
                                {{ __('messages.reset') }}
                            </a>
                        </div>
                    </form>
                </div>
            </details>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="shad-card p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.views') }}</p>
                <i class="fas fa-eye text-cyan-500"></i>
            </div>
            <p class="mt-3 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($kpis['profileViews'] ?? 0) }}</p>
        </div>
        <div class="shad-card p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.total_products') }}</p>
                <i class="fas fa-box text-indigo-500"></i>
            </div>
            <p class="mt-3 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($kpis['totalProducts'] ?? 0) }}</p>
        </div>
        <div class="shad-card p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.total_services') }}</p>
                <i class="fas fa-concierge-bell text-purple-500"></i>
            </div>
            <p class="mt-3 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($kpis['totalServices'] ?? 0) }}</p>
        </div>
        <div class="shad-card p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.average_rating') }}</p>
                <i class="fas fa-star text-yellow-500"></i>
            </div>
            <p class="mt-3 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($kpis['avgRating'] ?? 0, 2) }}</p>
        </div>
        <div class="shad-card p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.total_branches') }}</p>
                <i class="fas fa-store text-blue-500"></i>
            </div>
            <p class="mt-3 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($kpis['totalBranches'] ?? 0) }}</p>
        </div>
        <div class="shad-card p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.total_order_items') }}</p>
                <i class="fas fa-shopping-cart text-orange-500"></i>
            </div>
            <p class="mt-3 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($kpis['totalOrderItems'] ?? 0) }}</p>
        </div>
        <div class="shad-card p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.total_bookings') }}</p>
                <i class="fas fa-calendar-check text-green-500"></i>
            </div>
            <p class="mt-3 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($kpis['totalBookings'] ?? 0) }}</p>
        </div>
        <div class="shad-card p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.unread_notifications') }}</p>
                <i class="fas fa-bell text-red-500"></i>
            </div>
            <p class="mt-3 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($kpis['unreadNotifications'] ?? 0) }}</p>
        </div>
    </div>

    <div class="shad-card p-4">
        <div class="inline-flex flex-wrap gap-2 rounded-lg bg-gray-100 p-1 dark:bg-gray-900">
            <button type="button" class="tab-trigger active" data-tab-trigger="overview">{{ __('messages.analytics_overview') }}</button>
            <button type="button" class="tab-trigger" data-tab-trigger="catalog">{{ __('messages.analytics_catalog') }}</button>
            <button type="button" class="tab-trigger" data-tab-trigger="commerce">{{ __('messages.analytics_commerce') }}</button>
            <button type="button" class="tab-trigger" data-tab-trigger="bookings">{{ __('messages.analytics_bookings') }}</button>
            <button type="button" class="tab-trigger" data-tab-trigger="ratings">{{ __('messages.analytics_ratings') }}</button>
            <button type="button" class="tab-trigger" data-tab-trigger="geo">{{ __('messages.analytics_geo') }}</button>
            <button type="button" class="tab-trigger" data-tab-trigger="ops">{{ __('messages.analytics_ops') }}</button>
            <button type="button" class="tab-trigger" data-tab-trigger="quality">{{ __('messages.analytics_quality') }}</button>
        </div>
    </div>

    <div id="tab-overview" class="tab-panel active space-y-4">
        <div class="shad-card p-5">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('messages.analytics_activity_timeline') }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.analytics_activity_timeline_desc') }}</p>
            <div class="h-80 mt-4"><canvas id="timelineChart"></canvas></div>
            <div id="timelineChartEmpty" class="empty-state hidden mt-4">{{ __('messages.no_data') }}</div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
            <div class="shad-card p-5">
                <h3 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">{{ __('messages.analytics_branch_status') }}</h3>
                <div class="h-72"><canvas id="branchStatusChart"></canvas></div>
                <div id="branchStatusChartEmpty" class="empty-state hidden mt-4">{{ __('messages.no_data') }}</div>
            </div>
            <div class="shad-card p-5">
                <h3 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">{{ __('messages.analytics_notifications_type') }}</h3>
                <div class="h-72"><canvas id="notificationsTypeChart"></canvas></div>
                <div id="notificationsTypeChartEmpty" class="empty-state hidden mt-4">{{ __('messages.no_data') }}</div>
            </div>
        </div>
    </div>

    <div id="tab-catalog" class="tab-panel space-y-4">
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
            <div class="shad-card p-5">
                <h3 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">{{ __('messages.analytics_products_status') }}</h3>
                <div class="h-72"><canvas id="productsStatusChart"></canvas></div>
                <div id="productsStatusChartEmpty" class="empty-state hidden mt-4">{{ __('messages.no_data') }}</div>
            </div>
            <div class="shad-card p-5">
                <h3 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">{{ __('messages.analytics_services_status') }}</h3>
                <div class="h-72"><canvas id="servicesStatusChart"></canvas></div>
                <div id="servicesStatusChartEmpty" class="empty-state hidden mt-4">{{ __('messages.no_data') }}</div>
            </div>
            <div class="shad-card p-5">
                <h3 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">{{ __('messages.analytics_stock_buckets') }}</h3>
                <div class="h-72"><canvas id="stockBucketsChart"></canvas></div>
                <div id="stockBucketsChartEmpty" class="empty-state hidden mt-4">{{ __('messages.no_data') }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
            <div class="shad-card p-5">
                <h3 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">{{ __('messages.analytics_products_category') }}</h3>
                <div class="h-80"><canvas id="productsCategoryChart"></canvas></div>
                <div id="productsCategoryChartEmpty" class="empty-state hidden mt-4">{{ __('messages.no_data') }}</div>
            </div>
            <div class="shad-card p-5">
                <h3 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">{{ __('messages.analytics_services_category') }}</h3>
                <div class="h-80"><canvas id="servicesCategoryChart"></canvas></div>
                <div id="servicesCategoryChartEmpty" class="empty-state hidden mt-4">{{ __('messages.no_data') }}</div>
            </div>
        </div>

        <div class="shad-card overflow-x-auto">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('messages.analytics_top_services_table') }}</h3>
            </div>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">{{ __('messages.service') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">{{ __('messages.category') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">{{ __('messages.status') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">{{ __('messages.duration') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">{{ __('messages.orders') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">{{ __('messages.views') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                    @forelse(($tables['topServices'] ?? []) as $row)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $row['name'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $row['category'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $normalizeStatusLabel($row['status']) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $row['duration'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $row['orders'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $row['views'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('messages.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="shad-card overflow-x-auto">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('messages.analytics_top_products_table') }}</h3>
            </div>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">{{ __('messages.product') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">{{ __('messages.category') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">{{ __('messages.status') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">{{ __('messages.stock') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">{{ __('messages.orders') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">{{ __('messages.views') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                    @forelse(($tables['topProducts'] ?? []) as $row)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $row['name'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $row['category'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $normalizeStatusLabel($row['status']) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $row['stock'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $row['orders'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $row['views'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('messages.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="tab-commerce" class="tab-panel space-y-4">
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
            <div class="shad-card p-5">
                <h3 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">{{ __('messages.analytics_order_items_status') }}</h3>
                <div class="h-72"><canvas id="orderItemsStatusChart"></canvas></div>
                <div id="orderItemsStatusChartEmpty" class="empty-state hidden mt-4">{{ __('messages.no_data') }}</div>
            </div>
            <div class="shad-card p-5">
                <h3 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">{{ __('messages.analytics_order_funnel') }}</h3>
                <div class="space-y-3">
                    @foreach(($funnels['orderItemFlow'] ?? []) as $stage => $value)
                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span class="text-gray-700 dark:text-gray-300">{{ $funnelStageLabels[$stage] ?? ucwords(str_replace('_', ' ', $stage)) }}</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($value) }}</span>
                            </div>
                            <div class="h-2 w-full rounded bg-gray-200 dark:bg-gray-700">
                                @php
                                    $maxOrderFlow = max(1, max($funnels['orderItemFlow'] ?? [1]));
                                    $pct = ($value / $maxOrderFlow) * 100;
                                @endphp
                                <div class="h-2 rounded bg-[var(--primary)]" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div id="tab-bookings" class="tab-panel space-y-4">
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
            <div class="shad-card p-5">
                <h3 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">{{ __('messages.analytics_bookings_status') }}</h3>
                <div class="h-72"><canvas id="bookingsStatusChart"></canvas></div>
                <div id="bookingsStatusChartEmpty" class="empty-state hidden mt-4">{{ __('messages.no_data') }}</div>
            </div>
            <div class="shad-card p-5">
                <h3 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">{{ __('messages.analytics_booking_funnel') }}</h3>
                <div class="space-y-3">
                    @foreach(($funnels['bookingFlow'] ?? []) as $stage => $value)
                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span class="text-gray-700 dark:text-gray-300">{{ $funnelStageLabels[$stage] ?? ucwords(str_replace('_', ' ', $stage)) }}</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($value) }}</span>
                            </div>
                            <div class="h-2 w-full rounded bg-gray-200 dark:bg-gray-700">
                                @php
                                    $maxBookingFlow = max(1, max($funnels['bookingFlow'] ?? [1]));
                                    $pct = ($value / $maxBookingFlow) * 100;
                                @endphp
                                <div class="h-2 rounded bg-[var(--primary)]" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div id="tab-ratings" class="tab-panel space-y-4">
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
            <div class="shad-card p-5">
                <h3 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">{{ __('messages.analytics_ratings_distribution') }}</h3>
                <div class="h-72"><canvas id="ratingsChart"></canvas></div>
                <div id="ratingsChartEmpty" class="empty-state hidden mt-4">{{ __('messages.no_data') }}</div>
            </div>
            <div class="shad-card p-5">
                <h3 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">{{ __('messages.analytics_top_branches_views') }}</h3>
                <div class="space-y-2">
                    @forelse(($rankings['topBranchesByViews'] ?? []) as $row)
                        <div class="flex items-center justify-between rounded-md border border-gray-200 px-3 py-2 dark:border-gray-700">
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $row['name'] }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $row['emirate'] ?: '-' }}</div>
                            </div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($row['value']) }}</div>
                        </div>
                    @empty
                        <div class="empty-state">{{ __('messages.no_data') }}</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div id="tab-geo" class="tab-panel space-y-4">
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
            <div class="shad-card p-5">
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.analytics_geo_with_coords') }}</div>
                <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($geo['coverageStats']['withCoordinates'] ?? 0) }}</div>
            </div>
            <div class="shad-card p-5">
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.analytics_geo_without_coords') }}</div>
                <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($geo['coverageStats']['withoutCoordinates'] ?? 0) }}</div>
            </div>
            <div class="shad-card p-5">
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.analytics_geo_coverage') }}</div>
                <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($geo['coverageStats']['coveragePct'] ?? 0, 2) }}%</div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
            <div class="shad-card p-5">
                <h3 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">{{ __('messages.analytics_geo_emirate_density') }}</h3>
                <div class="h-80"><canvas id="emirateDensityChart"></canvas></div>
                <div id="emirateDensityChartEmpty" class="empty-state hidden mt-4">{{ __('messages.no_data') }}</div>
            </div>
            <div class="shad-card p-5">
                <h3 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">{{ __('messages.analytics_geo_summary') }}</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('messages.analytics_geo_centroid') }}</dt>
                        <dd class="font-semibold text-gray-900 dark:text-white">
                            {{ $geo['coverageStats']['centroid']['lat'] ?? '-' }}, {{ $geo['coverageStats']['centroid']['lng'] ?? '-' }}
                        </dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('messages.analytics_geo_nearest_pair_km') }}</dt>
                        <dd class="font-semibold text-gray-900 dark:text-white">{{ $geo['coverageStats']['nearestPairKm'] ?? '-' }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('messages.analytics_geo_farthest_pair_km') }}</dt>
                        <dd class="font-semibold text-gray-900 dark:text-white">{{ $geo['coverageStats']['farthestPairKm'] ?? '-' }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('messages.analytics_geo_bounds') }}</dt>
                        <dd class="font-semibold text-gray-900 dark:text-white text-right">
                            {{ $geo['coverageStats']['bounds']['minLat'] ?? '-' }} / {{ $geo['coverageStats']['bounds']['maxLat'] ?? '-' }}<br>
                            {{ $geo['coverageStats']['bounds']['minLng'] ?? '-' }} / {{ $geo['coverageStats']['bounds']['maxLng'] ?? '-' }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="shad-card overflow-x-auto">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('messages.analytics_geo_points_table') }}</h3>
            </div>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">{{ __('messages.branch') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">{{ __('messages.location') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Lat</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Lng</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">{{ __('messages.status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                    @forelse(($geo['points'] ?? []) as $row)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $row['id'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $row['name'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $row['emirate'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $row['lat'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $row['lng'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $normalizeStatusLabel($row['status']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('messages.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="tab-ops" class="tab-panel space-y-4">
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
            <div class="shad-card p-5">
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.analytics_active_deals') }}</div>
                <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($kpis['activeDeals'] ?? 0) }}</div>
            </div>
            <div class="shad-card p-5">
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.analytics_wishlist_count') }}</div>
                <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($kpis['wishlistCount'] ?? 0) }}</div>
            </div>
            <div class="shad-card p-5">
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.subscription') }}</div>
                <div class="mt-2 text-xl font-bold text-gray-900 dark:text-white">{{ $normalizeStatusLabel($tables['subscriptionStatus']['status'] ?? 'none') }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ __('messages.analytics_days_remaining') }}: {{ $tables['subscriptionStatus']['days_remaining'] ?? 0 }}</div>
            </div>
        </div>

        <div class="shad-card p-5">
            <h3 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">{{ __('messages.analytics_team_summary') }}</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                <div class="rounded-md border border-gray-200 p-3 dark:border-gray-700">
                    <div class="text-gray-500 dark:text-gray-400">{{ __('messages.analytics_product_managers') }}</div>
                    <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $tables['teamSummary']['product_managers'] ?? 0 }}</div>
                </div>
                <div class="rounded-md border border-gray-200 p-3 dark:border-gray-700">
                    <div class="text-gray-500 dark:text-gray-400">{{ __('messages.analytics_service_providers') }}</div>
                    <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $tables['teamSummary']['service_providers'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    <div id="tab-quality" class="tab-panel space-y-4">
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
            <div class="shad-card p-5 overflow-x-auto">
                <h3 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">{{ __('messages.analytics_null_rates') }}</h3>
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Entity</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Field</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Null</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse(($quality['nullRates'] ?? []) as $row)
                            <tr>
                                <td class="px-3 py-2 text-sm text-gray-900 dark:text-white">{{ $normalizeEntityLabel($row['entity']) }}</td>
                                <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $normalizeFieldLabel($row['field']) }}
                                </td>
                                <td class="px-3 py-2 text-sm text-gray-900 dark:text-white">{{ $row['null_count'] }}</td>
                                <td class="px-3 py-2 text-sm text-gray-900 dark:text-white">{{ $row['total'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-3 py-6 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('messages.no_data') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="shad-card p-5 overflow-x-auto">
                <h3 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">{{ __('messages.analytics_zero_non_zero') }}</h3>
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Metric</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Zero</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Non-zero</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse(($quality['zeroVsNonZero'] ?? []) as $row)
                            <tr>
                                <td class="px-3 py-2 text-sm text-gray-900 dark:text-white">
                                    {{ $normalizeMetricLabel($row['metric']) }}
                                </td>
                                <td class="px-3 py-2 text-sm text-gray-900 dark:text-white">{{ $row['zero'] }}</td>
                                <td class="px-3 py-2 text-sm text-gray-900 dark:text-white">{{ $row['non_zero'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-3 py-6 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('messages.no_data') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const payload = {
        timeseries: @json($timeseries),
        distributions: @json($distributions),
    };
    const statusLabelMap = {
        active: @json($statusLabels['active']),
        inactive: @json($statusLabels['inactive']),
        approved: @json($statusLabels['approved']),
        pending: @json($statusLabels['pending']),
        cancelled: @json($statusLabels['cancelled']),
        confirmed: @json($statusLabels['confirmed']),
        completed: @json($statusLabels['completed']),
        rejected: @json($statusLabels['rejected']),
        processing: @json($statusLabels['processing']),
        shipped: @json($statusLabels['shipped']),
        delivered: @json($statusLabels['delivered']),
        no_show: @json($statusLabels['no_show']),
    };
    const mapStatusLabels = (labels) => (labels || []).map((label) => statusLabelMap[label] || label);

    function hasAnyData(values) {
        if (!Array.isArray(values)) return false;
        return values.some(v => Number(v) > 0);
    }

    function renderChart(id, emptyId, config, dataValues) {
        const canvas = document.getElementById(id);
        const emptyEl = document.getElementById(emptyId);

        if (!canvas) return;

        if (!hasAnyData(dataValues)) {
            canvas.style.display = 'none';
            if (emptyEl) emptyEl.classList.remove('hidden');
            return;
        }

        if (emptyEl) emptyEl.classList.add('hidden');
        canvas.style.display = 'block';
        new Chart(canvas, config);
    }

    renderChart('timelineChart', 'timelineChartEmpty', {
        type: 'line',
        data: {
            labels: payload.timeseries.labels || [],
            datasets: [
                { label: '{{ __('messages.bookings') }}', data: payload.timeseries.bookings || [], borderColor: '#16a34a', backgroundColor: 'rgba(22,163,74,.12)', tension: 0.3, fill: true },
                { label: '{{ __('messages.order_items') }}', data: payload.timeseries.orderItems || [], borderColor: '#f97316', backgroundColor: 'rgba(249,115,22,.12)', tension: 0.3, fill: true },
                { label: '{{ __('messages.products') }}', data: payload.timeseries.productsCreated || [], borderColor: '#6897ffff', backgroundColor: 'rgba(79,70,229,.12)', tension: 0.3, fill: true },
                { label: '{{ __('messages.services') }}', data: payload.timeseries.servicesCreated || [], borderColor: '#7c3aed', backgroundColor: 'rgba(124,58,237,.12)', tension: 0.3, fill: true },
                { label: '{{ __('messages.notifications') }}', data: payload.timeseries.notifications || [], borderColor: '#dc2626', backgroundColor: 'rgba(220,38,38,.12)', tension: 0.3, fill: true },
            ],
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } },
    }, [
        ...(payload.timeseries.bookings || []),
        ...(payload.timeseries.orderItems || []),
        ...(payload.timeseries.productsCreated || []),
        ...(payload.timeseries.servicesCreated || []),
        ...(payload.timeseries.notifications || []),
    ]);

    function renderDoughnut(id, emptyId, labels, values, colors) {
        renderChart(id, emptyId, {
            type: 'doughnut',
            data: { labels, datasets: [{ data: values, backgroundColor: colors }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } },
        }, values);
    }

    function renderBar(id, emptyId, labels, values, color = '#2563eb') {
        renderChart(id, emptyId, {
            type: 'bar',
            data: { labels, datasets: [{ data: values, backgroundColor: color, borderRadius: 6 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } },
        }, values);
    }

    renderDoughnut(
        'branchStatusChart',
        'branchStatusChartEmpty',
        mapStatusLabels(payload.distributions.branchesByStatus.labels || []),
        payload.distributions.branchesByStatus.values || [],
        ['#2563eb', '#16a34a', '#f59e0b', '#dc2626', '#6b7280']
    );

    renderDoughnut(
        'notificationsTypeChart',
        'notificationsTypeChartEmpty',
        payload.distributions.notificationsByType.labels || [],
        payload.distributions.notificationsByType.values || [],
        ['#0ea5e9', '#8b5cf6', '#14b8a6', '#f97316', '#ef4444']
    );

    renderDoughnut(
        'productsStatusChart',
        'productsStatusChartEmpty',
        mapStatusLabels(payload.distributions.productsByStatus.labels || []),
        payload.distributions.productsByStatus.values || [],
        ['#6366f1', '#10b981', '#f59e0b', '#ef4444']
    );

    renderDoughnut(
        'servicesStatusChart',
        'servicesStatusChartEmpty',
        mapStatusLabels(payload.distributions.servicesByStatus.labels || []),
        payload.distributions.servicesByStatus.values || [],
        ['#8b5cf6', '#10b981', '#f59e0b', '#ef4444']
    );

    renderBar(
        'stockBucketsChart',
        'stockBucketsChartEmpty',
        payload.distributions.stockBuckets.labels || [],
        payload.distributions.stockBuckets.values || [],
        '#0f766e'
    );

    renderBar(
        'productsCategoryChart',
        'productsCategoryChartEmpty',
        payload.distributions.productsByCategory.labels || [],
        payload.distributions.productsByCategory.values || [],
        '#4338ca'
    );

    renderBar(
        'servicesCategoryChart',
        'servicesCategoryChartEmpty',
        payload.distributions.servicesByCategory.labels || [],
        payload.distributions.servicesByCategory.values || [],
        '#7c3aed'
    );

    renderDoughnut(
        'orderItemsStatusChart',
        'orderItemsStatusChartEmpty',
        mapStatusLabels(payload.distributions.orderItemsByStatus.labels || []),
        payload.distributions.orderItemsByStatus.values || [],
        ['#f59e0b', '#3b82f6', '#0ea5e9', '#10b981', '#ef4444']
    );

    renderDoughnut(
        'bookingsStatusChart',
        'bookingsStatusChartEmpty',
        mapStatusLabels(payload.distributions.bookingsByStatus.labels || []),
        payload.distributions.bookingsByStatus.values || [],
        ['#f59e0b', '#3b82f6', '#10b981', '#ef4444', '#6b7280']
    );

    renderBar(
        'ratingsChart',
        'ratingsChartEmpty',
        payload.distributions.ratingsBreakdown.labels || [],
        payload.distributions.ratingsBreakdown.values || [],
        '#ca8a04'
    );

    renderBar(
        'emirateDensityChart',
        'emirateDensityChartEmpty',
        payload.distributions.branchesByEmirate.labels || [],
        payload.distributions.branchesByEmirate.values || [],
        '#0284c7'
    );

    document.querySelectorAll('[data-tab-trigger]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const key = btn.getAttribute('data-tab-trigger');

            document.querySelectorAll('[data-tab-trigger]').forEach((trigger) => {
                trigger.classList.remove('active');
            });
            btn.classList.add('active');

            document.querySelectorAll('.tab-panel').forEach((panel) => panel.classList.remove('active'));
            const panel = document.getElementById(`tab-${key}`);
            if (panel) panel.classList.add('active');
        });
    });
</script>
@endsection
