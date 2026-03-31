<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Branch;
use App\Models\BranchRating;
use App\Models\Category;
use App\Models\Company;
use App\Models\Deal;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductsManager;
use App\Models\Service;
use App\Models\ServiceProvider;
use App\Models\VendorNotification;
use App\Models\VendorSubscription;
use App\Models\Wishlist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $company = Company::where('user_id', $user->id)->first();

        $dateRange = (string) $request->input('date_range', '90d');
        $selectedBranch = (int) $request->input('branch_id', 0) ?: null;
        $selectedStatus = (string) $request->input('status', '');
        $branchStatuses = ['active', 'inactive'];
        $catalogStatuses = ['pending', 'approved', 'rejected'];
        $bookingStatuses = ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'];
        $orderItemStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

        $startDate = match ($dateRange) {
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            '365d' => now()->subDays(365),
            default => null,
        };

        $kpis = [
            'totalBranches' => 0,
            'activeBranches' => 0,
            'profileViews' => 0,
            'totalProducts' => 0,
            'activeProducts' => 0,
            'totalServices' => 0,
            'activeServices' => 0,
            'totalOrderItems' => 0,
            'totalBookings' => 0,
            'avgRating' => 0.0,
            'totalRatings' => 0,
            'wishlistCount' => 0,
            'activeDeals' => 0,
            'unreadNotifications' => 0,
        ];

        $timeseries = [
            'labels' => [],
            'bookings' => [],
            'orderItems' => [],
            'productsCreated' => [],
            'servicesCreated' => [],
            'notifications' => [],
        ];

        $distributions = [
            'branchesByStatus' => ['labels' => [], 'values' => []],
            'productsByStatus' => ['labels' => [], 'values' => []],
            'servicesByStatus' => ['labels' => [], 'values' => []],
            'bookingsByStatus' => ['labels' => [], 'values' => []],
            'orderItemsByStatus' => ['labels' => [], 'values' => []],
            'productsByCategory' => ['labels' => [], 'values' => []],
            'servicesByCategory' => ['labels' => [], 'values' => []],
            'ratingsBreakdown' => ['labels' => ['1', '2', '3', '4', '5'], 'values' => [0, 0, 0, 0, 0]],
            'notificationsByType' => ['labels' => [], 'values' => []],
            'stockBuckets' => ['labels' => ['0', '1-5', '6-20', '21+'], 'values' => [0, 0, 0, 0]],
            'branchesByEmirate' => ['labels' => [], 'values' => []],
        ];

        $funnels = [
            'bookingFlow' => ['pending' => 0, 'confirmed' => 0, 'completed' => 0, 'cancelled_or_no_show' => 0],
            'orderItemFlow' => ['pending' => 0, 'processing' => 0, 'shipped' => 0, 'delivered' => 0, 'cancelled' => 0],
        ];

        $rankings = [
            'topBranchesByViews' => [],
            'topBranchesByOrders' => [],
            'topProductsByViews' => [],
            'topProductsByOrders' => [],
            'topServicesByViews' => [],
            'topServicesByOrders' => [],
            'lowStockProducts' => [],
        ];

        $tables = [
            'topProducts' => [],
            'topServices' => [],
            'branchHealth' => [],
            'subscriptionStatus' => [],
            'teamSummary' => [],
            'dealsSummary' => [],
        ];

        $quality = [
            'nullRates' => [],
            'zeroVsNonZero' => [],
            'outliers' => [],
        ];

        $geo = [
            'points' => [],
            'byEmirate' => [],
            'coverageStats' => [
                'totalBranches' => 0,
                'withCoordinates' => 0,
                'withoutCoordinates' => 0,
                'coveragePct' => 0,
                'centroid' => ['lat' => null, 'lng' => null],
                'bounds' => ['minLat' => null, 'maxLat' => null, 'minLng' => null, 'maxLng' => null],
                'nearestPairKm' => null,
                'farthestPairKm' => null,
            ],
        ];

        $branches = collect();

        if ($company) {
            $allBranchesQuery = Branch::where('company_id', $company->id);
            $allBranchIds = $allBranchesQuery->pluck('id')->values()->all();

            // KPI cards should represent real all-time totals for the company.
            $kpis['totalBranches'] = count($allBranchIds);
            $kpis['activeBranches'] = Branch::where('company_id', $company->id)
                ->where('status', 'active')
                ->count();
            $kpis['profileViews'] = (int) Branch::where('company_id', $company->id)->sum('view_count');

            $branchRatingStats = Branch::where('company_id', $company->id)
                ->selectRaw('COALESCE(SUM(average_rating * total_ratings), 0) as weighted_rating_sum')
                ->selectRaw('COALESCE(SUM(total_ratings), 0) as total_ratings_count')
                ->first();
            $totalRatingsCount = (int) ($branchRatingStats?->total_ratings_count ?? 0);
            $kpis['avgRating'] = $totalRatingsCount > 0
                ? round(((float) ($branchRatingStats?->weighted_rating_sum ?? 0)) / $totalRatingsCount, 2)
                : 0.0;
            $kpis['totalRatings'] = $totalRatingsCount;

            $kpis['totalProducts'] = empty($allBranchIds)
                ? 0
                : Product::whereIn('branch_id', $allBranchIds)->count();
            $kpis['activeProducts'] = empty($allBranchIds)
                ? 0
                : Product::whereIn('branch_id', $allBranchIds)->where('is_available', true)->count();
            $kpis['totalServices'] = empty($allBranchIds)
                ? 0
                : Service::whereIn('branch_id', $allBranchIds)->count();
            $kpis['activeServices'] = empty($allBranchIds)
                ? 0
                : Service::whereIn('branch_id', $allBranchIds)->where('is_available', true)->count();
            $kpis['totalOrderItems'] = OrderItem::where('vendor_id', $company->id)->count();
            $kpis['totalBookings'] = empty($allBranchIds)
                ? 0
                : Booking::whereIn('branch_id', $allBranchIds)->count();
            $kpis['activeDeals'] = empty($allBranchIds)
                ? 0
                : Deal::whereIn('branch_id', $allBranchIds)->where('status', 'active')->count();
            $kpis['wishlistCount'] = empty($allBranchIds)
                ? 0
                : Wishlist::whereIn('product_id', Product::whereIn('branch_id', $allBranchIds)->select('id'))->count();
            $kpis['unreadNotifications'] = VendorNotification::query()
                ->forVendorCompanyRecipient($company->id)
                ->where('is_opened', false)
                ->count();

            $branchesQuery = Branch::where('company_id', $company->id);
            if (in_array($selectedStatus, $branchStatuses, true)) {
                $branchesQuery->where('status', $selectedStatus);
            }

            $branches = $branchesQuery->get([
                'id', 'company_id', 'name', 'status', 'emirate', 'address', 'lat', 'lng',
                'view_count', 'order_count', 'average_rating', 'total_ratings', 'created_at',
            ]);

            $branchIds = $branches->pluck('id')->values()->all();

            $productsQuery = Product::whereIn('branch_id', $branchIds);
            if (in_array($selectedStatus, $catalogStatuses, true)) {
                $productsQuery->where('status', $selectedStatus);
            }

            $servicesQuery = Service::whereIn('branch_id', $branchIds);
            if (in_array($selectedStatus, $catalogStatuses, true)) {
                $servicesQuery->where('status', $selectedStatus);
            }

            $bookingsQuery = Booking::whereIn('branch_id', $branchIds)
                ->when($selectedBranch, fn ($q) => $q->where('branch_id', $selectedBranch));
            if (in_array($selectedStatus, $bookingStatuses, true)) {
                $bookingsQuery->where('status', $selectedStatus);
            }

            $orderItemsQuery = OrderItem::where('vendor_id', $company->id)
                ->when($selectedBranch, fn ($q) => $q->where('branch_id', $selectedBranch));
            if (in_array($selectedStatus, $orderItemStatuses, true)) {
                $orderItemsQuery->where('status', $selectedStatus);
            }
            $notificationsQuery = VendorNotification::query()
                ->forVendorCompanyRecipient($company->id);

            if ($startDate) {
                $bookingsQuery->where('created_at', '>=', $startDate);
                $orderItemsQuery->where('created_at', '>=', $startDate);
                $notificationsQuery->where('created_at', '>=', $startDate);
            }

            $products = (clone $productsQuery)->get([
                'id', 'branch_id', 'category_id', 'name', 'status', 'stock', 'price', 'rating',
                'view_count', 'order_count', 'is_available', 'created_at',
            ]);
            $services = (clone $servicesQuery)->get([
                'id', 'branch_id', 'category_id', 'name', 'status', 'price', 'duration', 'rating',
                'view_count', 'order_count', 'home_service', 'is_available', 'created_at',
            ]);
            $bookings = (clone $bookingsQuery)->get([
                'id', 'branch_id', 'service_id', 'status', 'payment_status', 'is_home_service',
                'service_location', 'price', 'duration', 'booking_date', 'created_at',
            ]);
            $orderItems = (clone $orderItemsQuery)->get([
                'id', 'order_id', 'branch_id', 'product_id', 'status', 'quantity', 'price', 'total',
                'discount_amount', 'created_at',
            ]);
            $notifications = (clone $notificationsQuery)->get([
                'id', 'notification_type', 'is_opened', 'created_at',
            ]);

            $timeseries = $this->buildTimeseries(
                $startDate,
                $bookings,
                $orderItems,
                $products,
                $services,
                $notifications
            );

            $distributions['branchesByStatus'] = $this->toLabelValue($branches->groupBy('status')->map->count());
            $distributions['productsByStatus'] = $this->toLabelValue($products->groupBy('status')->map->count());
            $distributions['servicesByStatus'] = $this->toLabelValue($services->groupBy('status')->map->count());
            $distributions['bookingsByStatus'] = $this->toLabelValue($bookings->groupBy('status')->map->count());
            $distributions['orderItemsByStatus'] = $this->toLabelValue($orderItems->groupBy('status')->map->count());
            $distributions['notificationsByType'] = $this->toLabelValue($notifications->groupBy('notification_type')->map->count());

            $distributions['stockBuckets']['values'] = [
                $products->where('stock', 0)->count(),
                $products->filter(fn ($p) => $p->stock >= 1 && $p->stock <= 5)->count(),
                $products->filter(fn ($p) => $p->stock >= 6 && $p->stock <= 20)->count(),
                $products->filter(fn ($p) => $p->stock >= 21)->count(),
            ];

            $productCategories = Category::whereIn('id', $products->pluck('category_id')->filter()->unique()->values()->all())
                ->pluck('name', 'id');
            $serviceCategories = Category::whereIn('id', $services->pluck('category_id')->filter()->unique()->values()->all())
                ->pluck('name', 'id');

            $distributions['productsByCategory'] = $this->toLabelValue(
                $products->groupBy('category_id')->map(fn ($group, $categoryId) => [
                    'label' => $productCategories[$categoryId] ?? 'Uncategorized',
                    'value' => $group->count(),
                ])->sortByDesc('value')->take(10)->values()->mapWithKeys(fn ($item) => [$item['label'] => $item['value']])
            );

            $distributions['servicesByCategory'] = $this->toLabelValue(
                $services->groupBy('category_id')->map(fn ($group, $categoryId) => [
                    'label' => $serviceCategories[$categoryId] ?? 'Uncategorized',
                    'value' => $group->count(),
                ])->sortByDesc('value')->take(10)->values()->mapWithKeys(fn ($item) => [$item['label'] => $item['value']])
            );

            $ratingRows = BranchRating::whereIn('branch_id', $branchIds)
                ->selectRaw('rating, COUNT(*) as total')
                ->groupBy('rating')
                ->pluck('total', 'rating');
            $distributions['ratingsBreakdown']['values'] = collect([1, 2, 3, 4, 5])
                ->map(fn ($r) => (int) ($ratingRows[$r] ?? 0))
                ->values()
                ->all();

            $distributions['branchesByEmirate'] = $this->toLabelValue(
                $branches->groupBy(fn ($b) => $b->emirate ?: 'Unknown')->map->count()
            );

            $funnels['bookingFlow'] = [
                'pending' => $bookings->where('status', 'pending')->count(),
                'confirmed' => $bookings->where('status', 'confirmed')->count(),
                'completed' => $bookings->where('status', 'completed')->count(),
                'cancelled_or_no_show' => $bookings->filter(fn ($b) => in_array($b->status, ['cancelled', 'no_show'], true))->count(),
            ];

            $funnels['orderItemFlow'] = [
                'pending' => $orderItems->where('status', 'pending')->count(),
                'processing' => $orderItems->where('status', 'processing')->count(),
                'shipped' => $orderItems->where('status', 'shipped')->count(),
                'delivered' => $orderItems->where('status', 'delivered')->count(),
                'cancelled' => $orderItems->where('status', 'cancelled')->count(),
            ];

            $rankings['topBranchesByViews'] = $branches->sortByDesc('view_count')->take(10)->values()->map(fn ($b) => [
                'name' => $b->name,
                'emirate' => $b->emirate,
                'value' => (int) $b->view_count,
            ])->all();

            $rankings['topBranchesByOrders'] = $branches->sortByDesc('order_count')->take(10)->values()->map(fn ($b) => [
                'name' => $b->name,
                'emirate' => $b->emirate,
                'value' => (int) $b->order_count,
            ])->all();

            $rankings['topProductsByViews'] = $products->sortByDesc('view_count')->take(10)->values()->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'value' => (int) $p->view_count,
            ])->all();

            $rankings['topProductsByOrders'] = $products->sortByDesc('order_count')->take(10)->values()->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'value' => (int) $p->order_count,
            ])->all();

            $rankings['topServicesByViews'] = $services->sortByDesc('view_count')->take(10)->values()->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'value' => (int) $s->view_count,
            ])->all();

            $rankings['topServicesByOrders'] = $services->sortByDesc('order_count')->take(10)->values()->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'value' => (int) $s->order_count,
            ])->all();

            $rankings['lowStockProducts'] = $products->sortBy('stock')->take(10)->values()->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'stock' => (int) $p->stock,
                'status' => $p->status,
            ])->all();

            $tables['topProducts'] = $products->sortByDesc('order_count')->take(10)->values()->map(function ($p) use ($productCategories) {
                $price = is_null($p->price) ? 0 : (float) $p->price;
                return [
                    'name' => $p->name,
                    'category' => $productCategories[$p->category_id] ?? 'Uncategorized',
                    'status' => (string) $p->status,
                    'stock' => (int) $p->stock,
                    'orders' => (int) $p->order_count,
                    'views' => (int) $p->view_count,
                    'price' => round($price, 2),
                ];
            })->all();

            $tables['topServices'] = $services->sortByDesc('order_count')->take(10)->values()->map(function ($s) use ($serviceCategories) {
                $price = is_null($s->price) ? 0 : (float) $s->price;
                return [
                    'name' => $s->name,
                    'category' => $serviceCategories[$s->category_id] ?? 'Uncategorized',
                    'status' => (string) $s->status,
                    'duration' => (int) $s->duration,
                    'orders' => (int) $s->order_count,
                    'views' => (int) $s->view_count,
                    'price' => round($price, 2),
                ];
            })->all();

            $tables['branchHealth'] = $branches->sortByDesc('view_count')->values()->map(fn ($b) => [
                'name' => $b->name,
                'status' => $b->status,
                'emirate' => $b->emirate ?: '-',
                'views' => (int) $b->view_count,
                'orders' => (int) $b->order_count,
                'average_rating' => (float) $b->average_rating,
                'total_ratings' => (int) $b->total_ratings,
            ])->all();

            $subscription = VendorSubscription::where('company_id', $company->id)->latest()->first();
            $tables['subscriptionStatus'] = [
                'status' => $subscription?->status ?? 'none',
                'start_at' => optional($subscription?->start_at)->toDateString(),
                'end_at' => optional($subscription?->end_at)->toDateString(),
                'days_remaining' => $subscription?->days_remaining ?? 0,
            ];

            $tables['teamSummary'] = [
                'product_managers' => ProductsManager::where('company_id', $company->id)->count(),
                'service_providers' => ServiceProvider::where('company_id', $company->id)->count(),
            ];

            $tables['dealsSummary'] = [
                'active' => Deal::whereIn('branch_id', $branchIds)->where('status', 'active')->count(),
                'inactive' => Deal::whereIn('branch_id', $branchIds)->where('status', 'inactive')->count(),
                'total' => Deal::whereIn('branch_id', $branchIds)->count(),
            ];

            $quality['nullRates'] = [
                [
                    'entity' => 'branches',
                    'field' => 'emirate',
                    'null_count' => $branches->filter(fn ($b) => empty($b->emirate))->count(),
                    'total' => $branches->count(),
                ],
                [
                    'entity' => 'branches',
                    'field' => 'address',
                    'null_count' => $branches->filter(fn ($b) => empty($b->address))->count(),
                    'total' => $branches->count(),
                ],
                [
                    'entity' => 'products',
                    'field' => 'category_id',
                    'null_count' => $products->filter(fn ($p) => empty($p->category_id))->count(),
                    'total' => $products->count(),
                ],
                [
                    'entity' => 'services',
                    'field' => 'category_id',
                    'null_count' => $services->filter(fn ($s) => empty($s->category_id))->count(),
                    'total' => $services->count(),
                ],
            ];

            $quality['zeroVsNonZero'] = [
                [
                    'metric' => 'branch_view_count',
                    'zero' => $branches->where('view_count', 0)->count(),
                    'non_zero' => $branches->where('view_count', '>', 0)->count(),
                ],
                [
                    'metric' => 'product_order_count',
                    'zero' => $products->where('order_count', 0)->count(),
                    'non_zero' => $products->where('order_count', '>', 0)->count(),
                ],
                [
                    'metric' => 'service_order_count',
                    'zero' => $services->where('order_count', 0)->count(),
                    'non_zero' => $services->where('order_count', '>', 0)->count(),
                ],
            ];

            $quality['outliers'] = [
                'highestBranchViews' => $branches->sortByDesc('view_count')->take(3)->values()->map(fn ($b) => [
                    'name' => $b->name,
                    'value' => (int) $b->view_count,
                ])->all(),
                'lowestBranchViews' => $branches->sortBy('view_count')->take(3)->values()->map(fn ($b) => [
                    'name' => $b->name,
                    'value' => (int) $b->view_count,
                ])->all(),
            ];

            $geo = $this->buildGeoPayload($branches);
        }

        return view('vendor.analytics.index', [
            'company' => $company,
            'branches' => $branches,
            'kpis' => $kpis,
            'timeseries' => $timeseries,
            'distributions' => $distributions,
            'funnels' => $funnels,
            'rankings' => $rankings,
            'tables' => $tables,
            'quality' => $quality,
            'geo' => $geo,
            'filters' => [
                'date_range' => $dateRange,
                'branch_id' => $selectedBranch,
                'status' => $selectedStatus,
            ],
        ]);
    }

    private function toLabelValue(Collection $map): array
    {
        $pairs = $map
            ->map(fn ($value, $label) => ['label' => (string) ($label ?: 'unknown'), 'value' => (int) $value])
            ->sortByDesc('value')
            ->values();

        return [
            'labels' => $pairs->pluck('label')->all(),
            'values' => $pairs->pluck('value')->all(),
        ];
    }

    private function buildTimeseries(
        ?Carbon $startDate,
        Collection $bookings,
        Collection $orderItems,
        Collection $products,
        Collection $services,
        Collection $notifications
    ): array {
        $days = [];
        $from = $startDate ? $startDate->copy()->startOfDay() : null;
        if (is_null($from)) {
            $allDates = collect([
                ...$bookings->pluck('created_at')->filter()->all(),
                ...$orderItems->pluck('created_at')->filter()->all(),
                ...$products->pluck('created_at')->filter()->all(),
                ...$services->pluck('created_at')->filter()->all(),
                ...$notifications->pluck('created_at')->filter()->all(),
            ]);

            $earliest = $allDates->sort()->first();
            $from = $earliest ? Carbon::parse($earliest)->startOfDay() : now()->subDays(29)->startOfDay();
        }
        $to = now()->startOfDay();

        for ($cursor = $from->copy(); $cursor->lte($to); $cursor->addDay()) {
            $days[$cursor->format('Y-m-d')] = [
                'label' => $cursor->format('M d'),
                'bookings' => 0,
                'orderItems' => 0,
                'productsCreated' => 0,
                'servicesCreated' => 0,
                'notifications' => 0,
            ];
        }

        $countByDay = function (Collection $rows) {
            return $rows->groupBy(fn ($row) => optional($row->created_at)->format('Y-m-d'))->map->count();
        };

        foreach ($countByDay($bookings) as $day => $count) {
            if (isset($days[$day])) {
                $days[$day]['bookings'] = (int) $count;
            }
        }
        foreach ($countByDay($orderItems) as $day => $count) {
            if (isset($days[$day])) {
                $days[$day]['orderItems'] = (int) $count;
            }
        }
        foreach ($countByDay($products) as $day => $count) {
            if (isset($days[$day])) {
                $days[$day]['productsCreated'] = (int) $count;
            }
        }
        foreach ($countByDay($services) as $day => $count) {
            if (isset($days[$day])) {
                $days[$day]['servicesCreated'] = (int) $count;
            }
        }
        foreach ($countByDay($notifications) as $day => $count) {
            if (isset($days[$day])) {
                $days[$day]['notifications'] = (int) $count;
            }
        }

        return [
            'labels' => array_values(array_column($days, 'label')),
            'bookings' => array_values(array_column($days, 'bookings')),
            'orderItems' => array_values(array_column($days, 'orderItems')),
            'productsCreated' => array_values(array_column($days, 'productsCreated')),
            'servicesCreated' => array_values(array_column($days, 'servicesCreated')),
            'notifications' => array_values(array_column($days, 'notifications')),
        ];
    }

    private function buildGeoPayload(Collection $branches): array
    {
        $points = $branches
            ->filter(fn ($b) => !is_null($b->lat) && !is_null($b->lng))
            ->map(fn ($b) => [
                'id' => (int) $b->id,
                'name' => (string) $b->name,
                'lat' => (float) $b->lat,
                'lng' => (float) $b->lng,
                'emirate' => $b->emirate,
                'status' => $b->status,
            ])
            ->values();

        $withCoordinates = $points->count();
        $totalBranches = $branches->count();
        $withoutCoordinates = max($totalBranches - $withCoordinates, 0);

        $coverageStats = [
            'totalBranches' => $totalBranches,
            'withCoordinates' => $withCoordinates,
            'withoutCoordinates' => $withoutCoordinates,
            'coveragePct' => $totalBranches > 0 ? round(($withCoordinates / $totalBranches) * 100, 2) : 0,
            'centroid' => ['lat' => null, 'lng' => null],
            'bounds' => ['minLat' => null, 'maxLat' => null, 'minLng' => null, 'maxLng' => null],
            'nearestPairKm' => null,
            'farthestPairKm' => null,
        ];

        if ($withCoordinates > 0) {
            $coverageStats['centroid'] = [
                'lat' => round((float) $points->avg('lat'), 6),
                'lng' => round((float) $points->avg('lng'), 6),
            ];
            $coverageStats['bounds'] = [
                'minLat' => (float) $points->min('lat'),
                'maxLat' => (float) $points->max('lat'),
                'minLng' => (float) $points->min('lng'),
                'maxLng' => (float) $points->max('lng'),
            ];
        }

        if ($withCoordinates >= 2) {
            $nearest = null;
            $farthest = 0.0;
            $count = $points->count();
            $items = $points->all();

            for ($i = 0; $i < $count; $i++) {
                for ($j = $i + 1; $j < $count; $j++) {
                    $distance = $this->haversineKm(
                        (float) $items[$i]['lat'],
                        (float) $items[$i]['lng'],
                        (float) $items[$j]['lat'],
                        (float) $items[$j]['lng']
                    );

                    if (is_null($nearest) || $distance < $nearest) {
                        $nearest = $distance;
                    }
                    if ($distance > $farthest) {
                        $farthest = $distance;
                    }
                }
            }

            $coverageStats['nearestPairKm'] = is_null($nearest) ? null : round($nearest, 2);
            $coverageStats['farthestPairKm'] = round($farthest, 2);
        }

        $byEmirate = $branches
            ->groupBy(fn ($branch) => $branch->emirate ?: 'Unknown')
            ->map(function (Collection $group) {
                $withCoords = $group->filter(fn ($row) => !is_null($row->lat) && !is_null($row->lng))->count();

                return [
                    'emirate' => $group->first()->emirate ?: 'Unknown',
                    'count' => $group->count(),
                    'withCoordinates' => $withCoords,
                    'withoutCoordinates' => max($group->count() - $withCoords, 0),
                ];
            })
            ->sortByDesc('count')
            ->values()
            ->all();

        return [
            'points' => $points->all(),
            'byEmirate' => $byEmirate,
            'coverageStats' => $coverageStats,
        ];
    }

    private function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadiusKm = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusKm * $c;
    }
}
