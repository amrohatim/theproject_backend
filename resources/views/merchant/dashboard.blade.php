@extends('layouts.merchant')

@section('title', 'Merchant Dashboard')
@section('header', 'Dashboard')

@section('content')
<!-- Welcome Section -->
<div class="discord-card">
    <div class="discord-card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 style="margin: 0; color: var(--discord-lightest); font-size: 28px; font-weight: 700;">
                    Welcome back, {{ auth()->user()->name }}!
                </h2>
                <p style="margin: 8px 0 0 0; color: var(--discord-light); font-size: 16px;">
                    Here's what's happening with your merchant business
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('merchant.products.create') }}" class="discord-btn">
                    <i class="fas fa-plus me-1"></i> Add Product
                </a>
                <a href="{{ route('merchant.services.create') }}" class="discord-btn discord-btn-secondary">
                    <i class="fas fa-plus me-1"></i> Add Service
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <!-- Products Card -->
    <div class="stat-card">
        <div class="stat-info">
            <h3>{{ $totalProducts }}</h3>
            <p>Products</p>
        </div>
        <div class="stat-icon" style="background-color: var(--discord-primary);">
            <i class="fas fa-box"></i>
        </div>
    </div>

    <!-- Services Card -->
    <div class="stat-card">
        <div class="stat-info">
            <h3>{{ $totalServices }}</h3>
            <p>Services</p>
        </div>
        <div class="stat-icon" style="background-color: var(--discord-green);">
            <i class="fas fa-concierge-bell"></i>
        </div>
    </div>

    <!-- Orders Card -->
    <div class="stat-card">
        <div class="stat-info">
            <h3>{{ $totalOrders }}</h3>
            <p>Orders</p>
        </div>
        <div class="stat-icon" style="background-color: var(--discord-yellow);">
            <i class="fas fa-shopping-cart"></i>
        </div>
    </div>

    <!-- Customers Card -->
    <div class="stat-card">
        <div class="stat-info">
            <h3>{{ $totalCustomers }}</h3>
            <p>Customers</p>
        </div>
        <div class="stat-icon" style="background-color: #9b59b6;">
            <i class="fas fa-users"></i>
        </div>
    </div>

    <!-- Rating Card -->
    <div class="stat-card">
        <div class="stat-info">
            <h3>{{ number_format($averageRating, 1) }}</h3>
            <p>Average Rating ({{ $totalRatings }} reviews)</p>
        </div>
        <div class="stat-icon" style="background-color: #f39c12;">
            <i class="fas fa-star"></i>
        </div>
    </div>

    <!-- Views Card -->
    <div class="stat-card">
        <div class="stat-info">
            <h3>{{ $viewCount }}</h3>
            <p>Profile Views</p>
        </div>
        <div class="stat-icon" style="background-color: #3498db;">
            <i class="fas fa-eye"></i>
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
                <a href="{{ route('merchant.products.index') }}" class="discord-btn">
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
                        @forelse($recentProducts as $product)
                        <tr>
                            <td>
                                @if($product->image)
                                    <img src="{{ $product->image }}" alt="{{ $product->name }}" 
                                         style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                @else
                                    <div style="width: 40px; height: 40px; background-color: var(--discord-darkest); border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image" style="color: var(--discord-light);"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div style="font-weight: 500;">{{ $product->name }}</div>
                                <div style="font-size: 12px; color: var(--discord-light);">{{ Str::limit($product->description, 30) }}</div>
                            </td>
                            <td style="font-weight: 600; color: var(--discord-primary);">
                                ${{ number_format($product->price, 2) }}
                            </td>
                            <td>
                                <span class="badge" style="background-color: {{ $product->is_available ? 'var(--discord-green)' : 'var(--discord-light)' }}; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px;">
                                    {{ $product->is_available ? 'Available' : 'Unavailable' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 40px; color: var(--discord-light);">
                                <i class="fas fa-box" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                                <div>No products yet</div>
                                <div style="font-size: 14px; margin-top: 8px;">
                                    <a href="{{ route('merchant.products.create') }}" class="discord-btn" style="margin-top: 12px;">
                                        <i class="fas fa-plus me-1"></i> Add Your First Product
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Services -->
    <div class="col-lg-6 mb-4">
        <div class="discord-card">
            <div class="discord-card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-concierge-bell me-2" style="color: var(--discord-green);"></i>
                    Recent Services
                </div>
                <a href="{{ route('merchant.services.index') }}" class="discord-btn">
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
                        @forelse($recentServices as $service)
                        <tr>
                            <td>
                                @if($service->image)
                                    <img src="{{ $service->image }}" alt="{{ $service->name }}" 
                                         style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                @else
                                    <div style="width: 40px; height: 40px; background-color: var(--discord-darkest); border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-concierge-bell" style="color: var(--discord-light);"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div style="font-weight: 500;">{{ $service->name }}</div>
                                <div style="font-size: 12px; color: var(--discord-light);">{{ Str::limit($service->description, 30) }}</div>
                            </td>
                            <td style="font-weight: 600; color: var(--discord-green);">
                                ${{ number_format($service->price, 2) }}
                            </td>
                            <td>
                                <span class="badge" style="background-color: {{ $service->status === 'active' ? 'var(--discord-green)' : 'var(--discord-light)' }}; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px;">
                                    {{ ucfirst($service->status ?? 'active') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 40px; color: var(--discord-light);">
                                <i class="fas fa-concierge-bell" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                                <div>No services yet</div>
                                <div style="font-size: 14px; margin-top: 8px;">
                                    <a href="{{ route('merchant.services.create') }}" class="discord-btn discord-btn-secondary" style="margin-top: 12px;">
                                        <i class="fas fa-plus me-1"></i> Add Your First Service
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Account Summary -->
<div class="discord-card">
    <div class="discord-card-header">
        <i class="fas fa-chart-line me-2" style="color: var(--discord-primary);"></i>
        Account Summary
    </div>
    <div class="discord-card-body">
        <div class="row">
            <div class="col-md-3">
                <div style="text-align: center; padding: 20px;">
                    <div style="font-size: 24px; font-weight: 700; color: var(--discord-primary); margin-bottom: 8px;">
                        {{ $merchant->merchant_score ?? 0 }}
                    </div>
                    <div style="color: var(--discord-light); font-size: 14px;">Merchant Score</div>
                </div>
            </div>
            <div class="col-md-3">
                <div style="text-align: center; padding: 20px;">
                    <div style="font-size: 24px; font-weight: 700; color: var(--discord-green); margin-bottom: 8px;">
                        {{ $merchant->status === 'active' ? 'Active' : ucfirst($merchant->status) }}
                    </div>
                    <div style="color: var(--discord-light); font-size: 14px;">Account Status</div>
                </div>
            </div>
            <div class="col-md-3">
                <div style="text-align: center; padding: 20px;">
                    <div style="font-size: 24px; font-weight: 700; color: {{ $merchant->is_verified ? 'var(--discord-green)' : 'var(--discord-yellow)' }}; margin-bottom: 8px;">
                        {{ $merchant->is_verified ? 'Verified' : 'Pending' }}
                    </div>
                    <div style="color: var(--discord-light); font-size: 14px;">Verification Status</div>
                </div>
            </div>
            <div class="col-md-3">
                <div style="text-align: center; padding: 20px;">
                    <div style="font-size: 24px; font-weight: 700; color: var(--discord-primary); margin-bottom: 8px;">
                        {{ $merchant->emirate ?? 'Not Set' }}
                    </div>
                    <div style="color: var(--discord-light); font-size: 14px;">Location</div>
                </div>
            </div>
        </div>
        
        @if(!$merchant->is_verified)
        <div style="margin-top: 20px; padding: 16px; background-color: rgba(250, 168, 26, 0.1); border: 1px solid var(--discord-yellow); border-radius: 8px;">
            <div style="color: var(--discord-yellow); font-weight: 600; margin-bottom: 8px;">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Account Verification Pending
            </div>
            <div style="color: var(--discord-light); font-size: 14px;">
                Your merchant account is pending admin verification. You can still manage your products and services, but some features may be limited until verification is complete.
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
