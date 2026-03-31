<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ProviderProduct;
use App\Models\ProviderRating;
use App\Models\VendorNotification;
use App\Models\ViewTracking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    /**
     * Display the provider analytics page.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $provider = $user?->providerRecord;

        $dateRange = (string) $request->input('date_range', '30d');
        $selectedStatus = (string) $request->input('status', '');
        $selectedCategory = (int) $request->input('category_id', 0) ?: null;

        $startDate = match ($dateRange) {
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            '365d' => now()->subDays(365),
            default => null, // all
        };

        $kpis = [
            'profileViews' => 0,
            'totalProducts' => 0,
            'avgRating' => 0,
            'activeProducts' => 0,
            'unreadNotifications' => 0,
        ];

        $viewsTrend = [
            'labels' => [],
            'values' => [],
        ];
        $productsByStatus = [
            'labels' => [],
            'values' => [],
        ];
        $ratingDistribution = [
            'labels' => ['1', '2', '3', '4', '5'],
            'values' => [0, 0, 0, 0, 0],
        ];
        $productsByCategory = [
            'labels' => [],
            'values' => [],
        ];
        $notificationsByType = [
            'labels' => [],
            'values' => [],
        ];
        $productRows = collect();
        $categories = collect();

        if ($provider) {
            $baseProductQuery = ProviderProduct::query()->where('provider_id', $provider->id);

            if (!empty($selectedStatus)) {
                $baseProductQuery->where('status', $selectedStatus);
            }

            if (!empty($selectedCategory)) {
                $baseProductQuery->where('category_id', $selectedCategory);
            }

            $kpis['totalProducts'] = ProviderProduct::where('provider_id', $provider->id)->count();
            $kpis['activeProducts'] = ProviderProduct::where('provider_id', $provider->id)
                ->where('is_active', true)
                ->count();
            $kpis['avgRating'] = (float) (ProviderRating::where('provider_id', $provider->id)->avg('rating') ?? 0);

            $viewsQuery = ViewTracking::where('entity_type', 'provider')
                ->where('entity_id', $provider->id);

            if ($startDate) {
                $viewsQuery->where('viewed_at', '>=', $startDate);
            }

            $kpis['profileViews'] = (clone $viewsQuery)->count();

            $kpis['unreadNotifications'] = VendorNotification::where('recipient_type', VendorNotification::RECIPIENT_PROVIDER)
                ->where('recipient_id', $provider->id)
                ->where('is_opened', false)
                ->count();

            $viewsByDay = (clone $viewsQuery)
                ->selectRaw('DATE(viewed_at) as day, COUNT(*) as total')
                ->groupBy('day')
                ->orderBy('day')
                ->get();

            $viewsTrend['labels'] = $viewsByDay->pluck('day')->map(fn ($d) => Carbon::parse($d)->format('M d'))->values()->all();
            $viewsTrend['values'] = $viewsByDay->pluck('total')->map(fn ($v) => (int) $v)->values()->all();

            $statusRows = (clone $baseProductQuery)
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->orderBy('status')
                ->get();
            $productsByStatus['labels'] = $statusRows->pluck('status')->values()->all();
            $productsByStatus['values'] = $statusRows->pluck('total')->map(fn ($v) => (int) $v)->values()->all();

            $ratings = ProviderRating::where('provider_id', $provider->id)
                ->selectRaw('rating, COUNT(*) as total')
                ->groupBy('rating')
                ->pluck('total', 'rating');
            $ratingDistribution['values'] = collect([1, 2, 3, 4, 5])
                ->map(fn ($r) => (int) ($ratings[$r] ?? 0))
                ->values()
                ->all();

            $categoryRows = (clone $baseProductQuery)
                ->leftJoin('categories', 'provider_products.category_id', '=', 'categories.id')
                ->selectRaw("COALESCE(categories.name, 'Uncategorized') as category_name, COUNT(*) as total")
                ->groupBy('category_name')
                ->orderByDesc('total')
                ->limit(8)
                ->get();
            $productsByCategory['labels'] = $categoryRows->pluck('category_name')->values()->all();
            $productsByCategory['values'] = $categoryRows->pluck('total')->map(fn ($v) => (int) $v)->values()->all();

            $notificationRows = VendorNotification::where('recipient_type', VendorNotification::RECIPIENT_PROVIDER)
                ->where('recipient_id', $provider->id)
                ->when($startDate, fn ($q) => $q->where('created_at', '>=', $startDate))
                ->selectRaw('notification_type, COUNT(*) as total')
                ->groupBy('notification_type')
                ->orderBy('notification_type')
                ->get();
            $notificationsByType['labels'] = $notificationRows->pluck('notification_type')->values()->all();
            $notificationsByType['values'] = $notificationRows->pluck('total')->map(fn ($v) => (int) $v)->values()->all();

            $productRows = (clone $baseProductQuery)
                ->with('category:id,name')
                ->orderByDesc('stock')
                ->limit(10)
                ->get([
                    'id',
                    'product_name',
                    'status',
                    'stock',
                    'price',
                    'rating',
                    'total_ratings',
                    'category_id',
                    'created_at',
                ]);

            $categories = Category::whereIn(
                'id',
                ProviderProduct::where('provider_id', $provider->id)
                    ->whereNotNull('category_id')
                    ->select('category_id')
            )
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        return view('provider.analytics.index', [
            'provider' => $provider,
            'kpis' => $kpis,
            'viewsTrend' => $viewsTrend,
            'productsByStatus' => $productsByStatus,
            'ratingDistribution' => $ratingDistribution,
            'productsByCategory' => $productsByCategory,
            'notificationsByType' => $notificationsByType,
            'productRows' => $productRows,
            'categories' => $categories,
            'filters' => [
                'date_range' => $dateRange,
                'status' => $selectedStatus,
                'category_id' => $selectedCategory,
            ],
        ]);
    }
}
