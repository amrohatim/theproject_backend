<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemOption;
use App\Models\GiftOption;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Category;
use App\Models\Deal;
use App\Models\ProductOptionType;
use App\Models\ProductOptionValue;
use App\Services\ShippingService;
use App\Services\StockManagementService;
use App\Services\TrendingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * The shipping service instance.
     *
     * @var \App\Services\ShippingService
     */
    protected $shippingService;
    protected $stockManagementService;
    protected $trendingService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\ShippingService  $shippingService
     * @param  \App\Services\StockManagementService  $stockManagementService
     * @param  \App\Services\TrendingService  $trendingService
     * @return void
     */
    public function __construct(
        ShippingService $shippingService,
        StockManagementService $stockManagementService,
        TrendingService $trendingService
    )
    {
        $this->shippingService = $shippingService;
        $this->stockManagementService = $stockManagementService;
        $this->trendingService = $trendingService;
    }

    /**
     * Process a checkout and place an order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function placeOrder(Request $request)
    {
        try {
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
                'items.*.options' => 'nullable|array',
                'items.*.options.*.option_type_id' => 'nullable|exists:product_option_types,id',
                'items.*.options.*.option_value_id' => 'nullable|exists:product_option_values,id',
                'items.*.options.*.custom_value' => 'nullable|string|max:255',
                'items.*.gift_options' => 'nullable|array',
                'items.*.gift_options.is_gift' => 'nullable|boolean',
                'items.*.gift_options.gift_wrap' => 'nullable|boolean',
                'items.*.gift_options.gift_message' => 'nullable|string',
                'items.*.gift_options.gift_from' => 'nullable|string',
                'items.*.gift_options.gift_to' => 'nullable|string',
                'shipping_address' => 'required|array',
                'shipping_address.name' => 'required|string',
                'shipping_address.address' => 'required|string',
                'shipping_address.city' => 'required|string',
                'shipping_address.country' => 'required|string',
                'shipping_address.phone' => 'required|string',
                'billing_address' => 'nullable|array',
                'payment_method' => 'required|string',
                'notes' => 'nullable|string',
            ]);

            // Start a database transaction
            DB::beginTransaction();

            // Create the order
            $order = new Order();
            $order->user_id = Auth::id();
            $order->order_number = 'ORD-' . strtoupper(Str::random(8));
            $order->status = 'pending';
            $order->payment_status = 'pending';
            $order->payment_method = $validated['payment_method'];
            $order->shipping_address = $validated['shipping_address'];
            $order->billing_address = $validated['billing_address'] ?? $validated['shipping_address'];
            $order->notes = $validated['notes'] ?? null;
            $order->customer_name = $validated['shipping_address']['name'];
            $order->customer_phone = $validated['shipping_address']['phone'];
            $order->shipping_city = $validated['shipping_address']['city'];
            $order->shipping_country = $validated['shipping_address']['country'];
            $order->shipping_cost = 0; // Will be calculated later
            $order->total = 0; // Will be calculated from items
            $order->save();

            // Process order items
            $total = 0;
            $totalDiscount = 0;

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

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

                // Get the vendor (company) for this product
                $branch = Branch::findOrFail($product->branch_id);
                $company = Company::findOrFail($branch->company_id);

                // Get the product category
                $categoryId = $product->category_id;

                // Find applicable deals
                $applicableDeals = $this->findApplicableDeals($product->id, $categoryId, $company->user_id);

                // Calculate the best discount
                $discountPercentage = 0;
                $appliedDealId = null;

                if (!empty($applicableDeals) && $applicableDeals->count() > 0) {
                    // Find the best deal (highest discount)
                    $bestDeal = $applicableDeals->sortByDesc('discount_percentage')->first();
                    if ($bestDeal) {
                        $discountPercentage = $bestDeal->discount_percentage;
                        $appliedDealId = $bestDeal->id;
                    }
                }

                // Calculate base item price and discount
                $originalPrice = $product->price ?? 0;

                // Ensure discount percentage is a valid number
                $discountPercentage = is_numeric($discountPercentage) ? $discountPercentage : 0;

                // Calculate discount amount and discounted price
                $discountAmount = $originalPrice * ($discountPercentage / 100) * $item['quantity'];
                $discountedPrice = $originalPrice * (1 - ($discountPercentage / 100));

                // Process product options and calculate price adjustments
                $optionsPriceAdjustment = 0;
                $selectedOptions = $item['options'] ?? [];

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
                        'quantity' => $item['quantity'] ?? 1,
                        'price' => $discountedPrice ?? 0,
                        'original_price' => $originalPrice ?? 0,
                        'discount_percentage' => $discountPercentage ?? 0,
                        'discount_amount' => $discountAmount ?? 0,
                        'total' => 0, // Will be updated after processing options
                        'applied_deal_id' => $appliedDealId,
                        'status' => 'pending',
                        'specifications' => $productSpecifications ?? [],
                        'color_id' => $colorInfo?->id,
                        'color_name' => $colorInfo?->name,
                        'color_value' => $colorInfo?->color_code,
                        'color_image' => $colorInfo?->image,
                        'size_id' => $sizeInfo?->id,
                        'size_name' => $sizeInfo?->name,
                        'size_value' => $sizeInfo?->value,
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
                            'quantity' => $item['quantity'] ?? 1,
                            'price' => $discountedPrice ?? 0,
                            'total' => 0, // Will be updated after processing options
                            'status' => 'pending',
                            'specifications' => $productSpecifications ?? [],
                            'color_id' => $colorInfo?->id,
                            'color_name' => $colorInfo?->name,
                            'color_value' => $colorInfo?->color_code,
                            'color_image' => $colorInfo?->image,
                            'size_id' => $sizeInfo?->id,
                            'size_name' => $sizeInfo?->name,
                            'size_value' => $sizeInfo?->value,
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

                // Increment product order count for trending calculations
                try {
                    $this->trendingService->incrementProductOrder($orderItem->product_id);
                } catch (\Exception $e) {
                    Log::warning('Failed to increment product order count for trending', [
                        'product_id' => $orderItem->product_id ?? null,
                        'error' => $e->getMessage(),
                    ]);
                }

                // Process selected options
                if (!empty($selectedOptions)) {
                    foreach ($selectedOptions as $option) {
                        $optionTypeId = $option['option_type_id'];
                        $optionValueId = $option['option_value_id'] ?? null;
                        $customValue = $option['custom_value'] ?? null;

                        // Get option type
                        $optionType = ProductOptionType::find($optionTypeId);

                        if (!$optionType) {
                            continue;
                        }

                        // For options with predefined values (color, size, etc.)
                        if ($optionValueId) {
                            $optionValue = ProductOptionValue::find($optionValueId);

                            if ($optionValue) {
                                // Add price adjustment
                                $priceAdjustment = $optionValue->price_adjustment;
                                $optionsPriceAdjustment += $priceAdjustment;

                                // Create order item option
                                OrderItemOption::create([
                                    'order_item_id' => $orderItem->id,
                                    'option_type_id' => $optionTypeId,
                                    'option_value_id' => $optionValueId,
                                    'option_name' => $optionType->display_name,
                                    'option_value' => $optionValue->value,
                                    'price_adjustment' => $priceAdjustment,
                                    'custom_value' => null,
                                ]);
                            }
                        }
                        // For custom text options
                        elseif ($customValue && $optionType->type === 'text') {
                            // Create order item option with custom value
                            OrderItemOption::create([
                                'order_item_id' => $orderItem->id,
                                'option_type_id' => $optionTypeId,
                                'option_value_id' => null,
                                'option_name' => $optionType->display_name,
                                'option_value' => 'Custom',
                                'price_adjustment' => 0,
                                'custom_value' => $customValue,
                            ]);
                        }
                    }
                }

                // Process gift options if provided
                if (isset($item['gift_options']) && !empty($item['gift_options'])) {
                    $giftOptions = $item['gift_options'];
                    $isGift = $giftOptions['is_gift'] ?? false;

                    if ($isGift) {
                        $giftWrap = $giftOptions['gift_wrap'] ?? false;
                        $giftWrapPrice = $giftWrap ? 5.00 : 0; // Fixed gift wrap price

                        // Add gift wrap price to total adjustment
                        $optionsPriceAdjustment += $giftWrapPrice;

                        // Create gift option record
                        GiftOption::create([
                            'order_item_id' => $orderItem->id,
                            'is_gift' => true,
                            'gift_wrap' => $giftWrap,
                            'gift_wrap_price' => $giftWrapPrice,
                            'gift_wrap_type' => $giftWrap ? 'standard' : null,
                            'gift_message' => $giftOptions['gift_message'] ?? null,
                            'gift_from' => $giftOptions['gift_from'] ?? null,
                            'gift_to' => $giftOptions['gift_to'] ?? null,
                        ]);
                    }
                }

                // Calculate final item total with options
                $itemPriceWithOptions = $discountedPrice + $optionsPriceAdjustment;
                $itemTotal = $itemPriceWithOptions * $item['quantity'];

                // Update order item with final total
                $orderItem->total = $itemTotal;
                $orderItem->save();

                $total += $itemTotal;
                $totalDiscount += $discountAmount;

                // Update product stock using the stock management service
                $this->stockManagementService->reduceStock($product->id, $item['quantity'], $colorId, $sizeId);
            }

            // Calculate shipping cost (simplified for this example)
            $shippingCost = 10.00; // Fixed shipping cost

            // Update order total, discount, and shipping cost
            $order->shipping_cost = $shippingCost;
            $order->total = $total + $shippingCost;

            // Try to set the discount field, but handle the case where it might not exist yet
            try {
                $order->discount = $totalDiscount;
                $order->save();
            } catch (\Exception $columnException) {
                // If we get a column not found error, save without the discount field
                if (strpos($columnException->getMessage(), 'Unknown column') !== false) {
                    Log::warning('Discount column not found in orders table. Saving order without discount field.');

                    // Remove discount from attributes and save
                    $order->offsetUnset('discount');
                    $order->save();
                } else {
                    // If it's a different error, rethrow it
                    throw $columnException;
                }
            }

            // Determine shipping method and handle delivery
            $this->shippingService->assignDelivery($order);

            // Commit the transaction
            DB::commit();

            // Load order with items and options for the response
            $order->load(['items.options', 'items.giftOption']);

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'total' => $order->total,
                    'shipping_method' => $order->shipping_method,
                    'shipping_status' => $order->shipping_status,
                    'tracking_number' => $order->tracking_number,
                    'items' => $order->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'product_id' => $item->product_id,
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'total' => $item->total,
                            'options' => $item->options->map(function ($option) {
                                return [
                                    'name' => $option->option_name,
                                    'value' => $option->option_value,
                                    'custom_value' => $option->custom_value,
                                    'price_adjustment' => $option->price_adjustment,
                                ];
                            }),
                            'gift_option' => $item->giftOption ? [
                                'is_gift' => $item->giftOption->is_gift,
                                'gift_wrap' => $item->giftOption->gift_wrap,
                                'gift_message' => $item->giftOption->gift_message,
                                'gift_from' => $item->giftOption->gift_from,
                                'gift_to' => $item->giftOption->gift_to,
                            ] : null,
                        ];
                    }),
                ],
            ]);
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();

            Log::error('Error placing order: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to place order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Find applicable deals for a product.
     *
     * @param  int  $productId
     * @param  int  $categoryId
     * @param  int  $vendorId
     * @return \Illuminate\Support\Collection
     */
    protected function findApplicableDeals($productId, $categoryId, $vendorId)
    {
        // Handle null inputs
        if (!$productId || !$vendorId) {
            return collect([]);
        }

        // Get current date
        $today = now()->format('Y-m-d');

        // Find active deals from this vendor
        $deals = Deal::where('user_id', $vendorId)
            ->where('status', 'active')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->get();

        // If no deals found, return empty collection
        if ($deals->isEmpty()) {
            return collect([]);
        }

        // Filter deals based on application scope
        $applicableDeals = $deals->filter(function ($deal) use ($productId, $categoryId) {
            if (!$deal) {
                return false;
            }

            // Deal applies to all products
            if ($deal->applies_to === 'all') {
                return true;
            }

            // Deal applies to specific products
            if ($deal->applies_to === 'products') {
                $productIds = is_string($deal->product_ids)
                    ? json_decode($deal->product_ids, true)
                    : $deal->product_ids;

                if (empty($productIds)) {
                    return false;
                }

                return in_array($productId, $productIds);
            }

            // Deal applies to specific categories
            if ($deal->applies_to === 'categories' && $categoryId) {
                $categoryIds = is_string($deal->category_ids)
                    ? json_decode($deal->category_ids, true)
                    : $deal->category_ids;

                if (empty($categoryIds)) {
                    return false;
                }

                return in_array($categoryId, $categoryIds);
            }

            return false;
        });

        return $applicableDeals;
    }
}
