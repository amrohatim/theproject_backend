@extends('layouts.provider')

@section('title', 'Dashboard')

@section('header', 'Provider Dashboard')

@section('content')
<div class="row mb-4">
    @if(isset($message))
    <div class="col-12">
        <div class="alert" style="background-color: var(--discord-darker); color: var(--discord-lightest); border-left: 4px solid var(--discord-primary);">
            {{ $message }}
        </div>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="discord-card" style="border-left: 4px solid var(--discord-primary);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-xs font-weight-bold mb-1" style="color: var(--discord-primary); text-transform: uppercase; letter-spacing: 0.05em;">
                        Total Products
                    </div>
                    <div class="h3 mb-0 font-weight-bold">{{ $totalProducts }}</div>
                </div>
                <div>
                    <i class="fas fa-box fa-2x" style="color: var(--discord-primary); opacity: 0.8;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="discord-card" style="border-left: 4px solid var(--discord-green);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-xs font-weight-bold mb-1" style="color: var(--discord-green); text-transform: uppercase; letter-spacing: 0.05em;">
                        Total Orders
                    </div>
                    <div class="h3 mb-0 font-weight-bold">{{ $totalOrders }}</div>
                </div>
                <div>
                    <i class="fas fa-shopping-cart fa-2x" style="color: var(--discord-green); opacity: 0.8;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="discord-card" style="border-left: 4px solid var(--discord-yellow);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-xs font-weight-bold mb-1" style="color: var(--discord-yellow); text-transform: uppercase; letter-spacing: 0.05em;">
                        Revenue
                    </div>
                    <div class="h3 mb-0 font-weight-bold">${{ isset($totalRevenue) ? number_format($totalRevenue, 2) : '0.00' }}</div>
                </div>
                <div>
                    <i class="fas fa-dollar-sign fa-2x" style="color: var(--discord-yellow); opacity: 0.8;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="discord-card" style="border-left: 4px solid var(--discord-red);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-xs font-weight-bold mb-1" style="color: var(--discord-red); text-transform: uppercase; letter-spacing: 0.05em;">
                        Customers
                    </div>
                    <div class="h3 mb-0 font-weight-bold">{{ isset($totalCustomers) ? $totalCustomers : '0' }}</div>
                </div>
                <div>
                    <i class="fas fa-users fa-2x" style="color: var(--discord-red); opacity: 0.8;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Area -->
<div class="row">
    <!-- Recent Products -->
    <div class="col-lg-6 mb-4">
        <div class="discord-card">
            <div class="discord-card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-box me-2" style="color: var(--discord-primary);"></i>
                    Recent Products
                </div>
                <a href="{{ route('provider.provider-products.index') }}" class="discord-btn">
                    <i class="fas fa-eye me-1"></i> View All
                </a>
            </div>
            <div class="table-responsive">
                <table class="discord-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">Image</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th style="width: 100px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentProducts as $product)
                        <tr>
                            <td>
                                @if($product->image)
                                    <img src="@providerProductImage($product->image)" alt="{{ $product->product_name }}" width="40" height="40" class="rounded" style="object-fit: cover;">
                                @else
                                    <div style="width: 40px; height: 40px; background-color: var(--discord-dark); border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image" style="color: var(--discord-light);"></i>
                                    </div>
                                @endif
                            </td>
                            <td style="font-weight: 500;">{{ $product->product_name }}</td>
                            <td style="color: var(--discord-green)">${{ number_format($product->price, 2) }}</td>
                            <td>
                                @if($product->is_active)
                                <span style="background-color: var(--discord-green); color: white; padding: 2px 8px; border-radius: 10px; font-size: 12px; display: inline-block;">
                                    Available
                                </span>
                                @else
                                <span style="background-color: var(--discord-red); color: white; padding: 2px 8px; border-radius: 10px; font-size: 12px; display: inline-block;">
                                    Unavailable
                                </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach

                        @if(count($recentProducts) == 0)
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <div style="color: var(--discord-light);">
                                    <i class="fas fa-box-open mb-2" style="font-size: 24px;"></i>
                                    <p>No products yet</p>
                                    <a href="{{ route('provider.provider-products.create') }}" class="discord-btn">
                                        <i class="fas fa-plus me-1"></i> Add Product
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="col-lg-6 mb-4">
        <div class="discord-card">
            <div class="discord-card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-shopping-cart me-2" style="color: var(--discord-green);"></i>
                    Recent Orders
                </div>
                <a href="{{ route('provider.orders.index') }}" class="discord-btn">
                    <i class="fas fa-eye me-1"></i> View All
                </a>
            </div>
            <div class="table-responsive">
                <table class="discord-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                        <tr>
                            <td>
                                <a href="{{ route('provider.orders.show', $order->id) }}" style="color: var(--discord-primary); text-decoration: none;">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td style="font-weight: 500;">{{ $order->customer_name }}</td>
                            <td style="color: var(--discord-green)">${{ number_format($order->total, 2) }}</td>
                            <td>
                                @if($order->status == 'completed')
                                <span style="background-color: var(--discord-green); color: white; padding: 2px 8px; border-radius: 10px; font-size: 12px; display: inline-block;">
                                    Completed
                                </span>
                                @elseif($order->status == 'processing')
                                <span style="background-color: var(--discord-primary); color: white; padding: 2px 8px; border-radius: 10px; font-size: 12px; display: inline-block;">
                                    Processing
                                </span>
                                @elseif($order->status == 'pending')
                                <span style="background-color: var(--discord-yellow); color: white; padding: 2px 8px; border-radius: 10px; font-size: 12px; display: inline-block;">
                                    Pending
                                </span>
                                @elseif($order->status == 'cancelled')
                                <span style="background-color: var(--discord-red); color: white; padding: 2px 8px; border-radius: 10px; font-size: 12px; display: inline-block;">
                                    Cancelled
                                </span>
                                @endif
                            </td>
                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                        </tr>
                        @endforeach

                        @if(count($recentOrders) == 0)
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div style="color: var(--discord-light);">
                                    <i class="fas fa-shopping-cart mb-2" style="font-size: 24px;"></i>
                                    <p>No orders yet</p>
                                </div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Activity Overview -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="discord-card">
            <div class="discord-card-header">
                <i class="fas fa-chart-line me-2" style="color: var(--discord-primary);"></i>
                Store Activity
            </div>
            <div class="p-3">
                <div class="d-flex align-items-center mb-3">
                    <div style="width: 50px; height: 50px; background-color: var(--discord-darkest); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                        <i class="fas fa-rocket" style="color: var(--discord-primary); font-size: 20px;"></i>
                    </div>
                    <div>
                        <h5 style="margin: 0; font-size: 16px; font-weight: 600;">Welcome to your Dashboard!</h5>
                        <p style="margin: 0; color: var(--discord-light); font-size: 14px;">Manage your products, track orders, and grow your business</p>
                    </div>
                    <a href="{{ route('provider.provider-products.create') }}" class="discord-btn ms-auto">
                        <i class="fas fa-plus me-1"></i> Add New Product
                    </a>
                </div>

                <div class="row text-center mt-4">
                    <div class="col-md-3 mb-3">
                        <div style="background-color: var(--discord-darkest); border-radius: 12px; padding: 15px;">
                            <i class="fas fa-eye mb-2" style="color: var(--discord-primary); font-size: 24px;"></i>
                            <h5 style="font-size: 24px; margin-bottom: 5px;">{{ isset($totalViews) ? $totalViews : '0' }}</h5>
                            <p style="color: var(--discord-light); margin: 0;">Product Views</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div style="background-color: var(--discord-darkest); border-radius: 12px; padding: 15px;">
                            <i class="fas fa-shopping-bag mb-2" style="color: var(--discord-green); font-size: 24px;"></i>
                            <h5 style="font-size: 24px; margin-bottom: 5px;">{{ isset($conversionRate) ? $conversionRate : '0' }}%</h5>
                            <p style="color: var(--discord-light); margin: 0;">Conversion Rate</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div style="background-color: var(--discord-darkest); border-radius: 12px; padding: 15px;">
                            <i class="fas fa-star mb-2" style="color: var(--discord-yellow); font-size: 24px;"></i>
                            <h5 style="font-size: 24px; margin-bottom: 5px;">{{ isset($avgRating) ? number_format($avgRating, 1) : '0.0' }}</h5>
                            <p style="color: var(--discord-light); margin: 0;">Avg. Rating</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div style="background-color: var(--discord-darkest); border-radius: 12px; padding: 15px;">
                            <i class="fas fa-redo mb-2" style="color: var(--discord-red); font-size: 24px;"></i>
                            <h5 style="font-size: 24px; margin-bottom: 5px;">{{ isset($returnRate) ? $returnRate : '0' }}%</h5>
                            <p style="color: var(--discord-light); margin: 0;">Return Rate</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
