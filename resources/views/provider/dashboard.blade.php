@extends('layouts.provider')

@section('title', 'Dashboard')

@section('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/improved-dashboard.css') }}">
@endsection

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header">
        <h1>{{ __('provider.provider_dashboard') }}</h1>
        <p>{{ __('provider.welcome_back') }}</p>
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
                <p class="stat-card-title">{{ __('provider.total_products') }}</p>
            </div>
            <h2 class="stat-card-value">{{ $totalProducts }}</h2>
            <img src="{{ asset('assets/package-box.png') }}" alt="Package Box" class="stat-card-icon-image">
        </div>
        
        <div class="enhanced-card">
            <h3 class="stat-card-title">{{ __('provider.total_orders') }}</h3>
            <h2 class="stat-card-value">{{ $totalOrders }}</h2>
            <img src="{{ asset('assets/orders.png') }}" alt="Package Box" class="stat-card-icon-image">
        </div>
        
        <div class="revenue-card">
            <div class="card-background"></div>
            <div class="card-decorative-shape-1 "></div>
            <div class="card-decorative-shape-2"></div>
            <div class="card-content">
                <h3 class="card-title">{{ __('provider.revenue') }}</h3>
                <h2 class="card-value">${{ isset($totalRevenue) ? number_format($totalRevenue, 2) : '0.00' }}</h2>
            </div>
            <img src="{{ asset('assets/revenue.png') }}" alt="Package Box" class="stat-card-icon-image">
            
        </div>
        
        <div class="customers-card">
            <div class="card-background"></div>
            <div class="card-decorative-shape-1"></div>
            <div class="card-decorative-shape-2"></div>
            <div class="card-content">
                <h3 class="card-title">{{ __('provider.customers') }}</h3>
                <h2 class="card-value">{{ isset($totalCustomers) ? $totalCustomers : '0' }}</h2>
                <img src="{{ asset('assets/woman.png') }}" alt="Package Box" class="stat-card-icon-image">

            </div>
        </div>
    </div>

<!-- Main Content Sections -->
<div class="row">
    <!-- Recent Products -->
    <div class="col-lg-6 mb-4">
        <div class="content-section">
            <div class="section-header">
                <h3 class="section-title"><i class="fas fa-box me-2"></i> {{ __('provider.recent_products') }}</h3>
                <a href="{{ route('provider.provider-products.index') }}" class="section-action">{{ __('provider.view_all') }}</a>
            </div>
            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">{{ __('provider.image') }}</th>
                            <th>{{ __('provider.product_name') }}</th>
                            <th>{{ __('provider.price') }}</th>
                            <th style="width: 100px;">{{ __('provider.status') }}</th>
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
                                <span class="status-badge status-available">{{ __('provider.available') }}</span>
                                @else
                                <span class="status-badge status-unavailable">{{ __('provider.unavailable') }}</span>
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
                                    <h4 class="empty-state-title">{{ __('provider.no_products_yet') }}</h4>
                                    <p class="empty-state-description">{{ __('provider.add_products_to_your_store') }}</p>
                                    <a href="{{ route('provider.provider-products.create') }}" class="dashboard-btn btn-primary">
                                        <i class="fas fa-plus me-1"></i> {{ __('provider.add_product') }}
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
                <h3 class="section-title"><i class="fas fa-shopping-cart me-2"></i> {{ __('provider.recent_orders') }}</h3>
                <a href="{{ route('provider.orders.index') }}" class="section-action">{{ __('provider.view_all') }}</a>
            </div>
            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>{{ __('provider.order_number') }}</th>
                             <th>{{ __('provider.customer') }}</th>
                             <th>{{ __('provider.total') }}</th>
                             <th>{{ __('provider.status') }}</th>
                             <th>{{ __('provider.date') }}</th>
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
                                <span class="status-badge status-completed">{{ __('provider.completed') }}</span>
                                @elseif($order->status == 'processing')
                                <span class="status-badge status-processing">{{ __('provider.processing') }}</span>
                                @elseif($order->status == 'pending')
                                <span class="status-badge status-pending">{{ __('provider.pending') }}</span>
                                @elseif($order->status == 'cancelled')
                                <span class="status-badge status-cancelled">{{ __('provider.cancelled') }}</span>
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
                                    <h4 class="empty-state-title">{{ __('provider.no_orders_yet') }}</h4>
                                    <p class="empty-state-description">{{ __('provider.orders_will_appear_here_once_customers_start_purchasing') }}</p>
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
                <h3 class="section-title"><i class="fas fa-chart-line me-2"></i> {{ __('provider.store_activity') }}</h3>
            </div>
            <div class="welcome-message">
                <div class="welcome-icon">
                    <i class="fas fa-rocket"></i>
                </div>
                <div class="welcome-content">
                    <h4>{{ __('provider.welcome_to_dashboard') }}</h4>
                    <p>{{ __('provider.manage_products_track_orders') }}</p>
                </div>
                <a href="{{ route('provider.provider-products.create') }}" class="dashboard-btn btn-primary ms-auto">
                    <i class="fas fa-plus me-1"></i> {{ __('provider.add_new_product') }}
                </a>
            </div>

            <div class="activity-grid">
                <div class="activity-card">
                    <div class="activity-icon bg-primary-light">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="activity-value">{{ isset($totalViews) ? $totalViews : '0' }}</div>
                    <div class="activity-label">{{ __('provider.product_views') }}</div>
                </div>
                <div class="activity-card">
                    <div class="activity-icon bg-success-light">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="activity-value">{{ isset($conversionRate) ? $conversionRate : '0' }}%</div>
                    <div class="activity-label">{{ __('provider.conversion_rate') }}</div>
                </div>
                <div class="activity-card">
                    <div class="activity-icon bg-warning-light">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="activity-value">{{ isset($avgRating) ? number_format($avgRating, 1) : '0.0' }}</div>
                    <div class="activity-label">{{ __('provider.avg_rating') }}</div>
                </div>
                <div class="activity-card">
                    <div class="activity-icon bg-danger-light">
                        <i class="fas fa-redo"></i>
                    </div>
                    <div class="activity-value">{{ isset($returnRate) ? $returnRate : '0' }}%</div>
                    <div class="activity-label">{{ __('provider.return_rate') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
