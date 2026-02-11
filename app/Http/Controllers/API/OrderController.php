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
