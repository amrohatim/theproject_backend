@extends('layouts.dashboard')

@section('title', __('provider.analytics'))
@section('page-title', __('provider.analytics'))

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
        border-radius: 0.375rem;
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
</style>
@endsection

@section('content')
<div class="container mx-auto space-y-6">
    <div class="shad-card p-4">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('provider.analytics') }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('provider.store_activity') }}</p>
            </div>

            <details class="relative">
                <summary class="cursor-pointer list-none inline-flex items-center gap-2 rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                    <i class="fas fa-sliders-h"></i>
                    {{ __('messages.filter') }}
                </summary>
                <div class="absolute right-0 mt-2 w-[320px] rounded-lg border border-gray-200 bg-white p-4 shadow-lg dark:border-gray-700 dark:bg-gray-800 z-20">
                    <form method="GET" action="{{ route('provider.analytics.index') }}" class="space-y-3">
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">{{ __('provider.date') }}</label>
                            <select name="date_range" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                                <option value="7d" {{ ($filters['date_range'] ?? '30d') === '7d' ? 'selected' : '' }}>Last 7 days</option>
                                <option value="30d" {{ ($filters['date_range'] ?? '30d') === '30d' ? 'selected' : '' }}>Last 30 days</option>
                                <option value="90d" {{ ($filters['date_range'] ?? '30d') === '90d' ? 'selected' : '' }}>Last 90 days</option>
                                <option value="365d" {{ ($filters['date_range'] ?? '30d') === '365d' ? 'selected' : '' }}>Last 12 months</option>
                                <option value="all" {{ ($filters['date_range'] ?? '30d') === 'all' ? 'selected' : '' }}>All time</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">{{ __('provider.status') }}</label>
                            <select name="status" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                                <option value="">All statuses</option>
                                <option value="approved" {{ ($filters['status'] ?? '') === 'approved' ? 'selected' : '' }}>approved</option>
                                <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>pending</option>
                                <option value="rejected" {{ ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' }}>rejected</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">{{ __('provider.category') }}</label>
                            <select name="category_id" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                                <option value="">All categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ (int) ($filters['category_id'] ?? 0) === (int) $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center gap-2 pt-1">
                            <button type="submit" class="rounded-md bg-[var(--primary)] px-3 py-2 text-sm font-semibold text-white">
                                {{ __('messages.apply') }}
                            </button>
                            <a href="{{ route('provider.analytics.index') }}" class="rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 dark:border-gray-600 dark:text-gray-300">
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
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('provider.profile_views') }}</p>
                <i class="fas fa-eye text-cyan-500"></i>
            </div>
            <p class="mt-3 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($kpis['profileViews'] ?? 0) }}</p>
        </div>
        <div class="shad-card p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('provider.total_products') }}</p>
                <i class="fas fa-box text-indigo-500"></i>
            </div>
            <p class="mt-3 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($kpis['totalProducts'] ?? 0) }}</p>
        </div>
        <div class="shad-card p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('provider.avg_rating') }}</p>
                <i class="fas fa-star text-yellow-500"></i>
            </div>
            <p class="mt-3 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($kpis['avgRating'] ?? 0, 1) }}</p>
        </div>
        <div class="shad-card p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('provider.active_products') }}</p>
                <i class="fas fa-check-circle text-green-500"></i>
            </div>
            <p class="mt-3 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($kpis['activeProducts'] ?? 0) }}</p>
        </div>
    </div>

    <div class="shad-card p-4">
        <div class="inline-flex rounded-lg bg-gray-100 p-1 dark:bg-gray-900">
            <button type="button" class="tab-trigger active" data-tab-trigger="engagement">{{ __('provider.analytics_engagement') }}</button>
            <button type="button" class="tab-trigger" data-tab-trigger="inventory">{{ __('provider.analytics_inventory') }}</button>
            <button type="button" class="tab-trigger" data-tab-trigger="ratings">{{ __('provider.analytics_ratings') }}</button>
            <button type="button" class="tab-trigger" data-tab-trigger="notifications">{{ __('provider.analytics_notifications') }}</button>
        </div>
    </div>

    <div id="tab-engagement" class="tab-panel active space-y-4">
        <div class="shad-card p-5">
            <div class="mb-4">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('provider.analytics_views_trend') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('provider.analytics_views_trend_desc') }}</p>
            </div>
            <div class="h-72">
                <canvas id="viewsTrendChart"></canvas>
            </div>
            <div id="viewsTrendEmpty" class="hidden mt-4 rounded-md border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500 dark:border-gray-600 dark:text-gray-400">
                {{ __('provider.analytics_no_data') }}
            </div>
        </div>
    </div>

    <div id="tab-inventory" class="tab-panel space-y-4">
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
            <div class="shad-card p-5">
                <h3 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">{{ __('provider.analytics_products_by_status') }}</h3>
                <div class="h-72">
                    <canvas id="productsStatusChart"></canvas>
                </div>
                <div id="productsStatusEmpty" class="hidden mt-4 rounded-md border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500 dark:border-gray-600 dark:text-gray-400">
                    {{ __('provider.analytics_no_data') }}
                </div>
            </div>
            <div class="shad-card p-5">
                <h3 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">{{ __('provider.analytics_products_by_category') }}</h3>
                <div class="h-72">
                    <canvas id="productsCategoryChart"></canvas>
                </div>
                <div id="productsCategoryEmpty" class="hidden mt-4 rounded-md border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500 dark:border-gray-600 dark:text-gray-400">
                    {{ __('provider.analytics_no_data') }}
                </div>
            </div>
        </div>

        <div class="shad-card">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('provider.analytics_top_products') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('provider.analytics_top_products_desc') }}</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">{{ __('provider.product_name') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">{{ __('provider.category') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">{{ __('provider.status') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">{{ __('provider.stock') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">{{ __('provider.price') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">{{ __('provider.avg_rating') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse($productRows as $row)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $row->product_name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $row->category->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $row->status }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $row->stock }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">${{ number_format($row->price, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ number_format((float) $row->rating, 1) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    <div class="animate-pulse">{{ __('provider.analytics_no_products') }}</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="tab-ratings" class="tab-panel space-y-4">
        <div class="shad-card p-5">
            <h3 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">{{ __('provider.analytics_rating_distribution') }}</h3>
            <div class="h-72">
                <canvas id="ratingDistributionChart"></canvas>
            </div>
            <div id="ratingDistributionEmpty" class="hidden mt-4 rounded-md border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500 dark:border-gray-600 dark:text-gray-400">
                {{ __('provider.analytics_no_data') }}
            </div>
        </div>
    </div>

    <div id="tab-notifications" class="tab-panel space-y-4">
        <div class="shad-card p-5">
            <h3 class="mb-4 text-base font-semibold text-gray-900 dark:text-white">{{ __('provider.analytics_notifications_by_type') }}</h3>
            <div class="h-72">
                <canvas id="notificationsTypeChart"></canvas>
            </div>
            <div id="notificationsTypeEmpty" class="hidden mt-4 rounded-md border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500 dark:border-gray-600 dark:text-gray-400">
                {{ __('provider.analytics_no_data') }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const analyticsPayload = {
        viewsTrend: @json($viewsTrend),
        productsByStatus: @json($productsByStatus),
        ratingDistribution: @json($ratingDistribution),
        productsByCategory: @json($productsByCategory),
        notificationsByType: @json($notificationsByType),
    };

    function isSeriesEmpty(values) {
        return !Array.isArray(values) || values.length === 0 || values.every(v => Number(v) === 0);
    }

    function renderOrEmpty(canvasId, emptyId, config) {
        const canvas = document.getElementById(canvasId);
        const empty = document.getElementById(emptyId);
        if (!canvas) return;

        const values = (config.data?.datasets?.[0]?.data || []).map(Number);
        if (isSeriesEmpty(values)) {
            canvas.style.display = 'none';
            if (empty) empty.classList.remove('hidden');
            return;
        }

        canvas.style.display = 'block';
        if (empty) empty.classList.add('hidden');
        new Chart(canvas, config);
    }

    renderOrEmpty('viewsTrendChart', 'viewsTrendEmpty', {
        type: 'line',
        data: {
            labels: analyticsPayload.viewsTrend.labels || [],
            datasets: [{
                label: 'Views',
                data: analyticsPayload.viewsTrend.values || [],
                borderColor: '#0ea5e9',
                backgroundColor: 'rgba(14,165,233,0.15)',
                tension: 0.35,
                fill: true,
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    renderOrEmpty('productsStatusChart', 'productsStatusEmpty', {
        type: 'bar',
        data: {
            labels: analyticsPayload.productsByStatus.labels || [],
            datasets: [{
                label: 'Products',
                data: analyticsPayload.productsByStatus.values || [],
                backgroundColor: ['#22c55e', '#f59e0b', '#ef4444', '#6366f1'],
                borderRadius: 6,
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    renderOrEmpty('productsCategoryChart', 'productsCategoryEmpty', {
        type: 'doughnut',
        data: {
            labels: analyticsPayload.productsByCategory.labels || [],
            datasets: [{
                data: analyticsPayload.productsByCategory.values || [],
                backgroundColor: ['#3b82f6','#8b5cf6','#f59e0b','#10b981','#ef4444','#06b6d4','#84cc16','#f97316'],
            }]
        },
        options: { maintainAspectRatio: false }
    });

    renderOrEmpty('ratingDistributionChart', 'ratingDistributionEmpty', {
        type: 'bar',
        data: {
            labels: analyticsPayload.ratingDistribution.labels || [],
            datasets: [{
                label: 'Ratings',
                data: analyticsPayload.ratingDistribution.values || [],
                backgroundColor: '#f59e0b',
                borderRadius: 6,
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, precision: 0 } }
        }
    });

    renderOrEmpty('notificationsTypeChart', 'notificationsTypeEmpty', {
        type: 'pie',
        data: {
            labels: analyticsPayload.notificationsByType.labels || [],
            datasets: [{
                data: analyticsPayload.notificationsByType.values || [],
                backgroundColor: ['#0ea5e9','#22c55e','#f59e0b','#ef4444','#a855f7'],
            }]
        },
        options: { maintainAspectRatio: false }
    });

    document.querySelectorAll('[data-tab-trigger]').forEach(btn => {
        btn.addEventListener('click', () => {
            const tab = btn.dataset.tabTrigger;
            document.querySelectorAll('[data-tab-trigger]').forEach(el => el.classList.remove('active'));
            btn.classList.add('active');

            document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.remove('active'));
            const target = document.getElementById(`tab-${tab}`);
            if (target) target.classList.add('active');
        });
    });
</script>
@endsection
