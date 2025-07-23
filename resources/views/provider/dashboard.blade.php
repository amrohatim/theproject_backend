@extends('layouts.provider')

@section('title', 'Dashboard')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/improved-dashboard.css') }}">
@endsection

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header">
        <h1>Provider Dashboard</h1>
        <p>Welcome back! Here's an overview of your store performance.</p>
    </div>
    
    @if(isset($message))
    <div class="alert-message">
        <div class="alert" style="background-color: var(--discord-darker); color: var(--discord-lightest); border-left: 4px solid var(--discord-primary);">
            {{ $message }}
        </div>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-icon bg-primary-light">
                    <i class="fas fa-box"></i>
                </div>
                <p class="stat-card-title">Total Products</p>
            </div>
            <h2 class="stat-card-value">{{ $totalProducts }}</h2>
            <div class="stat-card-comparison positive">
                <i class="fas fa-arrow-up mr-1"></i> 12% from last month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-icon bg-secondary-light">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <p class="stat-card-title">Total Orders</p>
            </div>
            <h2 class="stat-card-value">{{ $totalOrders }}</h2>
            <div class="stat-card-comparison positive">
                <i class="fas fa-arrow-up mr-1"></i> 8% from last month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-icon bg-warning-light">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <p class="stat-card-title">Revenue</p>
            </div>
            <h2 class="stat-card-value">${{ isset($totalRevenue) ? number_format($totalRevenue, 2) : '0.00' }}</h2>
            <div class="stat-card-comparison positive">
                <i class="fas fa-arrow-up mr-1"></i> 15% from last month
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-icon bg-danger-light">
                    <i class="fas fa-users"></i>
                </div>
                <p class="stat-card-title">Customers</p>
            </div>
            <h2 class="stat-card-value">{{ isset($totalCustomers) ? $totalCustomers : '0' }}</h2>
            <div class="stat-card-comparison positive">
                <i class="fas fa-arrow-up mr-1"></i> 5% from last month
            </div>
        </div>
    </div>

<!-- Main Content Sections -->
<div class="row">
    <!-- Recent Products -->
    <div class="col-lg-6 mb-4">
        <div class="content-section">
            <div class="section-header">
                <h3 class="section-title"><i class="fas fa-box me-2"></i> Recent Products</h3>
                <a href="{{ route('provider.provider-products.index') }}" class="section-action">View All</a>
            </div>
            <div class="table-responsive">
                <table class="modern-table">
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
                                    <div class="empty-image-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="product-name">{{ $product->product_name }}</td>
                            <td class="product-price">${{ number_format($product->price, 2) }}</td>
                            <td>
                                @if($product->is_active)
                                <span class="status-badge status-available">Available</span>
                                @else
                                <span class="status-badge status-unavailable">Unavailable</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach

                        @if(count($recentProducts) == 0)
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-box-open"></i>
                                    </div>
                                    <h4 class="empty-state-title">No products yet</h4>
                                    <p class="empty-state-description">Start adding products to your store</p>
                                    <a href="{{ route('provider.provider-products.create') }}" class="dashboard-btn btn-primary">
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
        <div class="content-section">
            <div class="section-header">
                <h3 class="section-title"><i class="fas fa-shopping-cart me-2"></i> Recent Orders</h3>
                <a href="{{ route('provider.orders.index') }}" class="section-action">View All</a>
            </div>
            <div class="table-responsive">
                <table class="modern-table">
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
                                <a href="{{ route('provider.orders.show', $order->id) }}" class="order-link">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td class="customer-name">{{ $order->customer_name }}</td>
                            <td class="order-total">${{ number_format($order->total, 2) }}</td>
                            <td>
                                @if($order->status == 'completed')
                                <span class="status-badge status-completed">Completed</span>
                                @elseif($order->status == 'processing')
                                <span class="status-badge status-processing">Processing</span>
                                @elseif($order->status == 'pending')
                                <span class="status-badge status-pending">Pending</span>
                                @elseif($order->status == 'cancelled')
                                <span class="status-badge status-cancelled">Cancelled</span>
                                @endif
                            </td>
                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                        </tr>
                        @endforeach

                        @if(count($recentOrders) == 0)
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-shopping-cart"></i>
                                    </div>
                                    <h4 class="empty-state-title">No orders yet</h4>
                                    <p class="empty-state-description">Orders will appear here once customers start purchasing</p>
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
        <div class="content-section">
            <div class="section-header">
                <h3 class="section-title"><i class="fas fa-chart-line me-2"></i> Store Activity</h3>
            </div>
            <div class="welcome-message">
                <div class="welcome-icon">
                    <i class="fas fa-rocket"></i>
                </div>
                <div class="welcome-content">
                    <h4>Welcome to your Dashboard!</h4>
                    <p>Manage your products, track orders, and grow your business</p>
                </div>
                <a href="{{ route('provider.provider-products.create') }}" class="dashboard-btn btn-primary ms-auto">
                    <i class="fas fa-plus me-1"></i> Add New Product
                </a>
            </div>

            <div class="activity-grid">
                <div class="activity-card">
                    <div class="activity-icon bg-primary-light">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="activity-value">{{ isset($totalViews) ? $totalViews : '0' }}</div>
                    <div class="activity-label">Product Views</div>
                </div>
                <div class="activity-card">
                    <div class="activity-icon bg-success-light">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="activity-value">{{ isset($conversionRate) ? $conversionRate : '0' }}%</div>
                    <div class="activity-label">Conversion Rate</div>
                </div>
                <div class="activity-card">
                    <div class="activity-icon bg-warning-light">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="activity-value">{{ isset($avgRating) ? number_format($avgRating, 1) : '0.0' }}</div>
                    <div class="activity-label">Avg. Rating</div>
                </div>
                <div class="activity-card">
                    <div class="activity-icon bg-danger-light">
                        <i class="fas fa-redo"></i>
                    </div>
                    <div class="activity-value">{{ isset($returnRate) ? $returnRate : '0' }}%</div>
                    <div class="activity-label">Return Rate</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
