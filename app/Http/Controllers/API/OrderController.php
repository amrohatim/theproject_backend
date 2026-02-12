<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Company;
use App\Services\ShippingService;
use App\Services\OrderStatusService;
use App\Services\StockManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    protected $shippingService;
    protected $orderStatusService;
    protected $stockManagementService;

    public function __construct(ShippingService $shippingService, OrderStatusService $orderStatusService, StockManagementService $stockManagementService)
    {
        $this->shippingService = $shippingService;
        $this->orderStatusService = $orderStatusService;
        $this->stockManagementService = $stockManagementService;
    }

    /**
     * Get all orders for the authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $user = Auth::user();
            $orders = Order::where('user_id', $user->id)
                ->with([
                    'items.product',
                    'vendorStatuses.vendor',
                    'vendorStatusHistory.vendor'
                ])
                ->orderBy('created_at', 'desc')
                ->get();

            // Transform orders to include flattened product data in items
            $orders->transform(function ($order) {
                if ($order->items) {
                    $order->items->transform(function ($item) {
                        // Add flattened product data to the item
                        if ($item->product) {
                            $item->product_name = $item->product->name;
                            $item->product_image = $item->product->image;
                        }

                        // Add flattened vendor data to the item
                        if ($item->vendor) {
                            $item->vendor_name = $item->vendor->name;
                        }

                        return $item;
                    });
                }
                return $order;
            });

            return response()->json([
                'success' => true,
                'orders' => $orders
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching orders: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific order.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $user = Auth::user();
            $order = Order::where('id', $id)
                ->where('user_id', $user->id)
                ->with([
                    'items.product',
                    'items.vendor',
                    'shipment',
                    'vendorStatuses.vendor',
                    'vendorStatusHistory.vendor'
                ])
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            // Transform order to include flattened product data in items
            if ($order->items) {
                $order->items->transform(function ($item) {
                    // Add flattened product data to the item
                    if ($item->product) {
                        $item->product_name = $item->product->name;
                        $item->product_image = $item->product->image;
                    }

                    // Add flattened vendor data to the item
                    if ($item->vendor) {
                        $item->vendor_name = $item->vendor->name;
                    }

                    return $item;
                });
            }

            return response()->json([
                'success' => true,
                'order' => $order
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vendor monthly orders analytics for items belonging to the vendor.
     *
     * GET /vendor/orders/analytics?year=YYYY
     */
    public function vendorAnalytics(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->isVendor()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        try {
            $year = (int) ($request->query('year') ?? now()->year);
            $companyId = Company::where('user_id', $user->id)->value('id');

            if (!$companyId) {
                return response()->json([
                    'success' => true,
                    'year' => $year,
                    'data' => $this->buildEmptyMonthlySeries(),
                ]);
            }

            if (!Schema::hasTable('order_items') || !Schema::hasTable('orders')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Orders tables missing',
                ], 500);
            }

            $dateColumn = Schema::hasColumn('orders', 'created_at') ? 'orders.created_at' : null;
            if ($dateColumn === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Orders date column missing',
                ], 500);
            }

            $hasTotal = Schema::hasColumn('order_items', 'total');
            $incomeSelect = $hasTotal
                ? "SUM(CASE WHEN orders.payment_status = 'paid' THEN order_items.total ELSE 0 END) as income_paid"
                : '0 as income_paid';

            $data = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('order_items.vendor_id', $companyId)
                ->whereYear($dateColumn, $year)
                ->selectRaw(
                    'MONTH(' . $dateColumn . ') as month, COUNT(*) as orders_count, ' . $incomeSelect
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            return response()->json([
                'success' => true,
                'year' => $year,
                'data' => $this->fillMonthlySeries($data, 'orders_count', 'income_paid'),
            ]);
        } catch (\Throwable $e) {
            Log::error('Vendor orders analytics failed', [
                'user_id' => $user?->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server Error',
            ], 500);
        }
    }

    /**
     * Vendor order items list with order/payment info.
     *
     * GET /vendor/order-items/list?date_from=YYYY-MM-DD&page=1
     */
    public function vendorOrderItemsList(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->isVendor()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        try {
            $companyId = Company::where('user_id', $user->id)->value('id');
            if (!$companyId) {
                return response()->json([
                    'success' => true,
                    'items' => [],
                    'pagination' => [
                        'current_page' => 1,
                        'last_page' => 1,
                        'per_page' => 15,
                        'total' => 0,
                    ],
                ]);
            }

            if (!Schema::hasTable('order_items') || !Schema::hasTable('orders')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Orders tables missing',
                ], 500);
            }

            $dateFrom = $request->query('date_from');
            $today = now()->toDateString();

            $query = OrderItem::with(['product', 'order'])
                ->where('order_items.vendor_id', $companyId);

            if ($dateFrom) {
                $query->whereHas('order', function ($q) use ($dateFrom, $today) {
                    $q->whereDate('created_at', '>=', $dateFrom)
                      ->whereDate('created_at', '<=', $today);
                });
            }

            $items = $query->orderBy('order_items.created_at', 'desc')
                ->paginate(15);

            $transformed = $items->getCollection()->map(function (OrderItem $item) {
                $order = $item->order;
                return [
                    'order_number' => $order?->order_number,
                    'product_name' => optional($item->product)->name,
                    'price' => (float) $item->total,
                    'payment_status' => $order?->payment_status,
                    'status' => $item->status ?? $order?->status,
                    'quantity' => (int) $item->quantity,
                    'date' => optional($order?->created_at)->toDateString(),
                ];
            });

            return response()->json([
                'success' => true,
                'items' => $transformed,
                'pagination' => [
                    'current_page' => $items->currentPage(),
                    'last_page' => $items->lastPage(),
                    'per_page' => $items->perPage(),
                    'total' => $items->total(),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Vendor order items list failed', [
                'user_id' => $user?->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server Error',
            ], 500);
        }
    }

    /**
     * Vendor top products for a given month (default current month).
     *
     * GET /vendor/order-items/top-products?year=YYYY&month=MM
     */
    public function vendorTopProducts(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->isVendor()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        try {
            $companyId = Company::where('user_id', $user->id)->value('id');
            if (!$companyId) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                ]);
            }

            if (!Schema::hasTable('order_items') || !Schema::hasTable('orders')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Orders tables missing',
                ], 500);
            }

            $year = (int) ($request->query('year') ?? now()->year);
            $month = (int) ($request->query('month') ?? now()->month);
            $start = \Carbon\Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
            $end = \Carbon\Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

            $top = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
                ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
                ->where('order_items.vendor_id', $companyId)
                ->whereBetween('orders.created_at', [$start, $end])
                ->selectRaw(
                    "products.name as product_name,
                     products.view_count as view_count,
                     products.rating as average_rating,
                     SUM(order_items.quantity) as total,
                     SUM(CASE WHEN orders.payment_status = 'paid' THEN order_items.total ELSE 0 END) as income"
                )
                ->groupBy('products.name', 'products.view_count', 'products.rating')
                ->orderByDesc('total')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $top,
            ]);
        } catch (\Throwable $e) {
            Log::error('Vendor top products failed', [
                'user_id' => $user?->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server Error',
            ], 500);
        }
    }

    private function buildEmptyMonthlySeries(): array
    {
        $series = [];
        for ($month = 1; $month <= 12; $month++) {
            $series[] = [
                'month' => $month,
                'orders_count' => 0,
                'income_paid' => 0.0,
            ];
        }
        return $series;
    }

    private function fillMonthlySeries($data, string $countKey, string $incomeKey): array
    {
        $byMonth = $data->keyBy('month');
        $series = [];
        for ($month = 1; $month <= 12; $month++) {
            $row = $byMonth->get($month);
            $series[] = [
                'month' => $month,
                'orders_count' => $row ? (int) $row->{$countKey} : 0,
                'income_paid' => $row ? (double) $row->{$incomeKey} : 0.0,
            ];
        }
        return $series;
    }

    /**
     * Create a new order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validate the request
            $validated = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.color_id' => 'nullable|integer',
                'items.*.color_name' => 'nullable|string',
                'items.*.color_value' => 'nullable|string',
                'items.*.color_image' => 'nullable|string',
                'items.*.size_id' => 'nullable|integer',
                'items.*.size_name' => 'nullable|string',
                'items.*.size_value' => 'nullable|string',
                'address' => 'required|string',
                'payment_method' => 'required|string',
            ]);

            // Create the order
            $order = new Order();
            $order->user_id = Auth::id();
            $order->order_number = 'ORD-' . strtoupper(Str::random(8));
            $order->status = 'pending';
            $order->payment_status = 'pending';
            $order->payment_method = $validated['payment_method'];
            $order->shipping_address = json_encode(['address' => $validated['address']]);
            $order->billing_address = json_encode(['address' => $validated['address']]);
            $order->save();

            // Process order items
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Get the vendor (company) for this product
                $branch = Branch::findOrFail($product->branch_id);
                $company = Company::findOrFail($branch->company_id);

                // Get color and size information if provided
                $colorId = $item['color_id'] ?? null;
                $sizeId = $item['size_id'] ?? null;
                $colorName = $item['color_name'] ?? null;
                $colorValue = $item['color_value'] ?? null;
                $colorImage = $item['color_image'] ?? null;
                $sizeName = $item['size_name'] ?? null;
                $sizeValue = $item['size_value'] ?? null;

                // Check stock availability before processing
                if (!$this->stockManagementService->checkStockAvailability($product->id, $item['quantity'], $colorId, $sizeId)) {
                    throw new \Exception("Insufficient stock for product {$product->name}. Please check availability.");
                }

                // Calculate item total
                $itemTotal = $product->price * $item['quantity'];
                $subtotal += $itemTotal;

                // Get product specifications
                $productSpecifications = $product->specifications()->get()->toArray();

                $colorInfo = null;
                $sizeInfo = null;

                // If we have color_id, try to get the color info from the database
                if ($colorId) {
                    $colorInfo = $product->colors()->where('id', $colorId)->first();
                }

                // If we have size_id, try to get the size info from the database
                if ($sizeId) {
                    $sizeInfo = $product->sizes()->where('id', $sizeId)->first();
                }

                // If we don't have color info from the database but have color data from the request,
                // create a temporary color info object
                if (!$colorInfo && ($colorName || $colorValue || $colorImage)) {
                    $colorInfo = (object)[
                        'id' => $colorId,
                        'name' => $colorName,
                        'color_code' => $colorValue,
                        'image' => $colorImage
                    ];
                }

                // If we don't have size info from the database but have size data from the request,
                // create a temporary size info object
                if (!$sizeInfo && ($sizeName || $sizeValue)) {
                    $sizeInfo = (object)[
                        'id' => $sizeId,
                        'name' => $sizeName,
                        'value' => $sizeValue
                    ];
                }

                // Create order item with specifications and variant information
                try {
                    // First try with all discount-related fields
                    $orderItem = OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'vendor_id' => $company->id,
                        'quantity' => $item['quantity'],
                        'price' => $product->price,
                        'original_price' => $product->original_price ?? $product->price,
                        'discount_percentage' => 0,
                        'discount_amount' => 0,
                        'total' => $itemTotal,
                        'applied_deal_id' => null,
                        'status' => 'pending',
                        'specifications' => $productSpecifications,
                        'color_id' => $colorInfo ? $colorInfo->id : null,
                        'color_name' => $colorInfo ? $colorInfo->name : null,
                        'color_value' => $colorInfo ? $colorInfo->color_code : null,
                        'color_image' => $colorInfo ? $colorInfo->image : null,
                        'size_id' => $sizeInfo ? $sizeInfo->id : null,
                        'size_name' => $sizeInfo ? $sizeInfo->name : null,
                        'size_value' => $sizeInfo ? $sizeInfo->value : null,
                    ]);

                    if (Schema::hasColumn('order_items', 'branch_id')) {
                        $orderItem->branch_id = $product->branch_id;
                        $orderItem->save();
                    }
                } catch (\Exception $columnException) {
                    // If we get a column not found error, try without the discount-related fields
                    if (strpos($columnException->getMessage(), 'Unknown column') !== false) {
                        Log::warning('Discount columns not found in order_items table. Creating order item without discount fields.');

                        $orderItem = OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $product->id,
                            'vendor_id' => $company->id,
                            'quantity' => $item['quantity'],
                            'price' => $product->price,
                            'total' => $itemTotal,
                            'status' => 'pending',
                            'specifications' => $productSpecifications,
                            'color_id' => $colorInfo ? $colorInfo->id : null,
                            'color_name' => $colorInfo ? $colorInfo->name : null,
                            'color_value' => $colorInfo ? $colorInfo->color_code : null,
                            'color_image' => $colorInfo ? $colorInfo->image : null,
                            'size_id' => $sizeInfo ? $sizeInfo->id : null,
                            'size_name' => $sizeInfo ? $sizeInfo->name : null,
                            'size_value' => $sizeInfo ? $sizeInfo->value : null,
                        ]);

                        if (Schema::hasColumn('order_items', 'branch_id')) {
                            $orderItem->branch_id = $product->branch_id;
                            $orderItem->save();
                        }
                    } else {
                        // If it's a different error, rethrow it
                        throw $columnException;
                    }
                }

                // Update product stock using the stock management service
                $this->stockManagementService->reduceStock($product->id, $item['quantity'], $colorId, $sizeId);
            }

            // Calculate shipping cost (simplified for this example)
            $shippingCost = 10.00; // Fixed shipping cost

            // Update order total and shipping cost
            $order->shipping_cost = $shippingCost;
            $order->total = $subtotal + $shippingCost;
            $order->save();

            // Determine shipping method and handle delivery
            $this->shippingService->assignDelivery($order);

            // Initialize vendor-specific statuses
            $this->orderStatusService->initializeVendorStatuses($order);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'order' => $order->load('items.product')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel an order.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel($id)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $order = Order::where('id', $id)
                ->where('user_id', $user->id)
                ->with('items')
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            // Only allow cancellation of pending or processing orders
            if (!in_array($order->status, ['pending', 'processing'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order cannot be cancelled at this stage'
                ], 400);
            }

            // Restore stock for all order items
            foreach ($order->items as $item) {
                $this->stockManagementService->increaseStock(
                    $item->product_id,
                    $item->quantity,
                    $item->color_id,
                    $item->size_id
                );
            }

            $order->status = 'cancelled';
            $order->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully and stock restored'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error cancelling order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
