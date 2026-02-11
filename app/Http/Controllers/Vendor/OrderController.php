<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Order;
use App\Services\OrderStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected $orderStatusService;

    public function __construct(OrderStatusService $orderStatusService)
    {
        $this->orderStatusService = $orderStatusService;
    }

    /**
     * Display a listing of pending orders for the vendor.
     */
    public function pendingOrders(Request $request)
    {
        // Get the vendor's company ID
        $companyId = Auth::user()->company->id ?? null;

        // If no company found, return empty results
        if (!$companyId) {
            return view('vendor.orders.pending', [
                'orderItems' => collect([]),
                'companyId' => null
            ]);
        }

        // Get all order items that belong to this vendor and have a status of 'pending'
        $orderItems = \App\Models\OrderItem::with(['order.user', 'product'])
            ->where('vendor_id', $companyId)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('vendor.orders.pending', [
            'orderItems' => $orderItems,
            'companyId' => $companyId
        ]);
    }

    /**
     * Update the status of multiple order items.
     */
    public function updateMultipleStatus(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'item_ids' => 'required|array',
            'item_ids.*' => 'required|integer|exists:order_items,id',
            'status' => 'required|string|in:processing,shipped,delivered,cancelled',
            'notes' => 'nullable|string',
        ]);

        // Get the vendor's company ID
        $companyId = Auth::user()->company->id ?? null;
        $userId = Auth::id();

        if (!$companyId) {
            return redirect()->route('vendor.orders.pending')
                ->with('error', 'You do not have a company associated with your account.');
        }

        $successCount = 0;
        $failCount = 0;
        $affectedOrders = [];

        // Update each order item
        foreach ($validated['item_ids'] as $itemId) {
            $orderItem = \App\Models\OrderItem::where('id', $itemId)
                ->where('vendor_id', $companyId)
                ->first();

            if ($orderItem) {
                // Update the order item status
                $result = $this->orderStatusService->updateOrderItemStatus(
                    $orderItem,
                    $validated['status'],
                    $validated['notes'] ?? null,
                    $userId
                );

                if ($result) {
                    $successCount++;

                    // Add the order ID to the affected orders list if not already there
                    if (!in_array($orderItem->order_id, $affectedOrders)) {
                        $affectedOrders[] = $orderItem->order_id;
                    }
                } else {
                    $failCount++;
                }
            } else {
                $failCount++;
            }
        }

        // Update the vendor status for each affected order
        foreach ($affectedOrders as $orderId) {
            $order = \App\Models\Order::find($orderId);
            if ($order) {
                // First, update the vendor status
                $this->orderStatusService->updateVendorOrderStatus(
                    $order,
                    $companyId,
                    $validated['status'],
                    $validated['notes'] ?? null,
                    $userId
                );

                // Then, explicitly update the order status based on all items
                // This ensures the overall order status is correctly updated
                \Log::info("Explicitly updating order #{$order->id} status from items after bulk update");
                $order->refresh(); // Refresh the order to get the latest item statuses
                $order->updateStatusFromItems();

                // Log the final status for debugging
                \Log::info("Final order status after bulk update: {$order->status}");
            }
        }

        // Prepare the response message
        $message = '';
        if ($successCount > 0) {
            $message .= $successCount . ' item(s) updated successfully. ';
        }
        if ($failCount > 0) {
            $message .= $failCount . ' item(s) failed to update.';
        }

        return redirect()->route('vendor.orders.pending')
            ->with('success', $message);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get branches that belong to the vendor's company
        $branches = Branch::whereHas('company', function ($query) {
            $query->where('user_id', Auth::id());
        })->orderBy('name')->get();

        // Get branch IDs that belong to the vendor
        $branchIds = $branches->pluck('id')->toArray();

        // Get the vendor's company ID
        $companyId = Auth::user()->company->id ?? null;

        // If no company found, return empty results
        if (!$companyId) {
            // Create an empty paginator instead of a collection
            $orders = Order::where('id', 0)->paginate(10); // This creates an empty paginator
            $stats = (object)[
                'total_orders' => 0,
                'completed_orders' => 0,
                'pending_orders' => 0,
                'total_revenue' => 0,
            ];
            return view('vendor.orders.index', compact('orders', 'branches', 'stats'));
        }

        // Build the query for orders
        // Show orders where either:
        // 1. The order's branch belongs to the vendor's company, OR
        // 2. The vendor has products in the order (vendor_id in order_items matches the vendor's company ID)
        $query = Order::with(['user', 'items.product'])
            ->whereHas('items', function ($itemQuery) use ($companyId, $branchIds) {
                $itemQuery->where('vendor_id', $companyId)
                    ->orWhereIn('branch_id', $branchIds);
            })
            ->orderBy('created_at', 'desc');

        // Debug the query
        \Log::info('Vendor Orders Query: ' . $query->toSql());
        \Log::info('Vendor Orders Query Bindings: ' . json_encode($query->getBindings()));
        \Log::info('Company ID: ' . $companyId);
        \Log::info('Branch IDs: ' . json_encode($branchIds));

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('branch')) {
            $branchId = $request->branch;
            $query->whereHas('items', function ($itemQuery) use ($branchId) {
                $itemQuery->where('branch_id', $branchId);
            });
        }

        if ($request->filled('date_range')) {
            $dateRange = $request->date_range;

            switch ($dateRange) {
                case 'today':
                    $query->whereDate('created_at', now()->toDateString());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', now()->subDay()->toDateString());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'last_week':
                    $query->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                    break;
                case 'last_month':
                    $query->whereMonth('created_at', now()->subMonth()->month)->whereYear('created_at', now()->subMonth()->year);
                    break;
            }
        }

        // Get paginated results
        $orders = $query->paginate(10);

        // Calculate stats with the same query conditions as above
        $statsQuery = Order::whereHas('items', function ($itemQuery) use ($companyId, $branchIds) {
            $itemQuery->where('vendor_id', $companyId)
                ->orWhereIn('branch_id', $branchIds);
        });

        // Debug the stats query
        \Log::info('Stats Query: ' . $statsQuery->toSql());

        // Get the counts
        $totalOrders = (clone $statsQuery)->count();
        $completedOrders = (clone $statsQuery)->where('status', 'delivered')->count();
        $pendingOrders = (clone $statsQuery)->where('status', 'pending')->count();
        $totalRevenue = (clone $statsQuery)->where('status', 'delivered')->sum('total');

        // Log the results
        \Log::info("Stats: Total: $totalOrders, Completed: $completedOrders, Pending: $pendingOrders, Revenue: $totalRevenue");

        $stats = (object)[
            'total_orders' => $totalOrders,
            'completed_orders' => $completedOrders,
            'pending_orders' => $pendingOrders,
            'total_revenue' => $totalRevenue,
        ];

        return view('vendor.orders.index', compact('orders', 'branches', 'stats'));
    }

    /**
     * Export orders.
     */
    public function export()
    {
        // In a real application, this would generate a CSV or Excel file
        return redirect()->route('vendor.orders.index')->with('success', 'Orders exported successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        // Get the vendor's company ID
        $companyId = Auth::user()->company->id ?? null;

        if (!$companyId) {
            return redirect()->route('vendor.orders.index')
                ->with('error', 'You do not have a company associated with your account.');
        }

        // Check if the order belongs to the vendor's branch OR if the vendor has products in this order
        $userBranches = Branch::whereHas('company', function ($query) {
            $query->where('user_id', Auth::id());
        })->pluck('id')->toArray();

        // Debug information
        \Log::info('Order ID: ' . $order->id);
        \Log::info('Company ID: ' . $companyId);
        \Log::info('User Branches: ' . json_encode($userBranches));
        // Check if vendor has items in this order
        $vendorItems = $order->items()
            ->where(function ($itemQuery) use ($companyId, $userBranches) {
                $itemQuery->where('vendor_id', $companyId)
                    ->orWhereIn('branch_id', $userBranches);
            })
            ->get();
        $hasVendorItems = $vendorItems->isNotEmpty();

        \Log::info('Vendor Items Count: ' . $vendorItems->count());
        \Log::info('Has Vendor Items: ' . ($hasVendorItems ? 'Yes' : 'No'));

        // Log all order items for debugging
        $allItems = $order->items()->get();
        \Log::info('All Order Items: ' . json_encode($allItems->map(function($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'vendor_id' => $item->vendor_id
            ];
        })));

        if (!$hasVendorItems) {
            return redirect()->route('vendor.orders.index')
                ->with('error', 'You do not have permission to view this order.');
        }

        // Load relationships
        $order->load([
            'user',
            'items.product.specifications',
            'items.product.category',
            'vendorStatuses.vendor',
            'vendorStatusHistory' => function($query) {
                $query->with('vendor')->orderBy('created_at', 'desc');
            }
        ]);

        // Log loaded data for debugging
        \Log::info('Order #' . $order->id . ' loaded with relationships');
        \Log::info('Vendor Statuses Count: ' . $order->vendorStatuses->count());
        \Log::info('Vendor Status History Count: ' . $order->vendorStatusHistory->count());

        // Check if all items belong to this vendor
        $vendorItemsCount = $order->items()->where('vendor_id', $companyId)->count();
        $totalItemsCount = $order->items()->count();
        $allItemsBelongToVendor = ($vendorItemsCount === $totalItemsCount);

        // Get this vendor's status for the order
        $vendorStatus = $order->vendorStatuses()->where('vendor_id', $companyId)->first();

        if ($vendorStatus) {
            \Log::info('Vendor #' . $companyId . ' status for Order #' . $order->id . ': ' . $vendorStatus->status);
        } else {
            \Log::info('No vendor status found for Vendor #' . $companyId . ' in Order #' . $order->id);
        }

        // Get this vendor's status history for the order
        $vendorStatusHistory = $order->vendorStatusHistory->where('vendor_id', $companyId);
        \Log::info('Vendor #' . $companyId . ' status history count for Order #' . $order->id . ': ' . $vendorStatusHistory->count());

        // Get all vendors involved in this order
        $orderVendors = $order->getVendors();

        return view('vendor.orders.show', compact('order', 'allItemsBelongToVendor', 'companyId', 'vendorStatus', 'vendorStatusHistory', 'orderVendors'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        // Get the vendor's company ID
        $companyId = Auth::user()->company->id ?? null;

        if (!$companyId) {
            return redirect()->route('vendor.orders.index')
                ->with('error', 'You do not have a company associated with your account.');
        }

        // Check if the vendor has any items in this order
        $hasVendorItems = $order->items()->where('vendor_id', $companyId)->exists();

        if (!$hasVendorItems) {
            return redirect()->route('vendor.orders.index')
                ->with('error', 'You do not have any products in this order.');
        }

        // Check if all items belong to this vendor
        $vendorItemsCount = $order->items()->where('vendor_id', $companyId)->count();
        $totalItemsCount = $order->items()->count();
        $allItemsBelongToVendor = ($vendorItemsCount === $totalItemsCount);

        // If not all items belong to this vendor, show a warning
        if (!$allItemsBelongToVendor) {
            return redirect()->route('vendor.orders.show', $order->id)
                ->with('warning', 'You can only update the status of your own products in this order. Please contact the marketplace administrator to update the overall order status.');
        }

        // Load relationships
        $order->load(['user', 'branch', 'items.product']);

        return view('vendor.orders.edit', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        // Get the vendor's company ID
        $companyId = Auth::user()->company->id ?? null;

        if (!$companyId) {
            return redirect()->route('vendor.orders.index')
                ->with('error', 'You do not have a company associated with your account.');
        }

        // Check if the vendor has any items in this order
        $vendorItemsCount = $order->items()->where('vendor_id', $companyId)->count();

        if ($vendorItemsCount === 0) {
            return redirect()->route('vendor.orders.index')
                ->with('error', 'You do not have any products in this order.');
        }

        // Check if all items in the order belong to this vendor
        $totalItemsCount = $order->items()->count();
        $allItemsBelongToVendor = ($vendorItemsCount === $totalItemsCount);

        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'notes' => 'nullable|string',
        ]);

        // Update the vendor-specific status for this order
        $success = $this->orderStatusService->updateVendorOrderStatus(
            $order,
            $companyId,
            $request->status,
            $request->notes,
            Auth::id()
        );

        if ($success) {
            return redirect()->route('vendor.orders.show', $order->id)
                ->with('success', 'Your order status has been updated successfully.');
        } else {
            return redirect()->route('vendor.orders.show', $order->id)
                ->with('error', 'Failed to update your order status.');
        }
    }

    /**
     * Generate an invoice for the order.
     */
    public function invoice(Order $order)
    {
        // Get the vendor's company ID
        $companyId = Auth::user()->company->id ?? null;

        if (!$companyId) {
            return redirect()->route('vendor.orders.index')
                ->with('error', 'You do not have a company associated with your account.');
        }

        // Check if the vendor has any items in this order
        $hasVendorItems = $order->items()->where('vendor_id', $companyId)->exists();

        if (!$hasVendorItems) {
            return redirect()->route('vendor.orders.index')
                ->with('error', 'You do not have any products in this order.');
        }

        // Load relationships
        $order->load(['user', 'branch', 'items.product']);

        // Filter items to only show those belonging to this vendor
        $vendorItems = $order->items->where('vendor_id', $companyId);

        // Calculate subtotal for vendor's items only
        $vendorSubtotal = $vendorItems->sum(function($item) {
            return $item->price * $item->quantity;
        });

        return view('vendor.orders.invoice', compact('order', 'companyId', 'vendorItems', 'vendorSubtotal'));
    }

    /**
     * Get search suggestions for orders.
     */
    public function searchSuggestions(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        // Get branches that belong to the vendor's company
        $branches = Branch::whereHas('company', function ($query) {
            $query->where('user_id', Auth::id());
        })->orderBy('name')->get();

        $branchIds = $branches->pluck('id')->toArray();
        $companyId = Auth::user()->company->id ?? null;

        if (empty($branchIds) && !$companyId) {
            return response()->json([]);
        }

        $suggestions = Order::query()
            ->with(['user'])
            ->whereHas('items', function ($itemQuery) use ($companyId, $branchIds) {
                $itemQuery->where('vendor_id', $companyId)
                    ->orWhereIn('branch_id', $branchIds);
            })
            ->where(function ($q) use ($query) {
                $q->where('order_number', 'like', "%{$query}%")
                  ->orWhere('customer_name', 'like', "%{$query}%")
                  ->orWhereHas('user', function ($userQuery) use ($query) {
                      $userQuery->where('name', 'like', "%{$query}%")
                          ->orWhere('email', 'like', "%{$query}%");
                  });
            })
            ->limit(10)
            ->get()
            ->map(function ($order) use ($query) {
                return [
                    'id' => $order->id,
                    'text' => $order->order_number,
                    'type' => 'order',
                    'icon' => 'fas fa-shopping-cart',
                    'subtitle' => $order->customer_name ?: ($order->user ? $order->user->name : ''),
                    'highlight' => $this->highlightMatch($order->order_number, $query),
                ];
            });

        return response()->json($suggestions);
    }

    /**
     * Highlight matching text in search results.
     */
    private function highlightMatch($text, $query)
    {
        return preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark>$1</mark>', $text);
    }
}
