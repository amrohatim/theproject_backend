@extends('layouts.merchant')

@section('title', 'Product Details')
@section('header', 'Product Details')

@section('content')
<!-- Header Section -->
<div class="discord-card">
    <div class="discord-card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 style="margin: 0; color: var(--discord-lightest); font-size: 24px; font-weight: 600;">
                    <i class="fas fa-eye me-2" style="color: var(--discord-primary);"></i>
                    Product Details
                </h2>
                <p style="margin: 8px 0 0 0; color: var(--discord-light); font-size: 14px;">
                    View detailed information about "{{ $product->name }}"
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('merchant.products.edit', $product->id) }}" class="discord-btn" style="background-color: var(--discord-yellow);">
                    <i class="fas fa-edit me-1"></i> Edit Product
                </a>
                <form action="{{ route('merchant.products.destroy', $product->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="discord-btn" style="background-color: var(--discord-red);">
                        <i class="fas fa-trash me-1"></i> Delete Product
                    </button>
                </form>
                <a href="{{ route('merchant.products.index') }}" class="discord-btn" style="background-color: var(--discord-light);">
                    <i class="fas fa-arrow-left me-1"></i> Back to Products
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Product Information -->
    <div class="col-md-8">
        <!-- Basic Information -->
        <div class="discord-card">
            <div class="discord-card-header">
                <i class="fas fa-info-circle me-2" style="color: var(--discord-primary);"></i>
                Basic Information
            </div>
            <div class="discord-card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" style="color: var(--discord-light); font-size: 12px; text-transform: uppercase; font-weight: 600;">
                            Product Name
                        </label>
                        <div style="color: var(--discord-lightest); font-size: 16px; font-weight: 500;">
                            {{ $product->name }}
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" style="color: var(--discord-light); font-size: 12px; text-transform: uppercase; font-weight: 600;">
                            Category
                        </label>
                        <div style="color: var(--discord-lightest); font-size: 16px;">
                            {{ $product->category->name ?? 'No category assigned' }}
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" style="color: var(--discord-light); font-size: 12px; text-transform: uppercase; font-weight: 600;">
                            SKU
                        </label>
                        <div style="color: var(--discord-lightest); font-family: monospace; font-size: 14px;">
                            {{ $product->sku ?? 'Not assigned' }}
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" style="color: var(--discord-light); font-size: 12px; text-transform: uppercase; font-weight: 600;">
                            Status
                        </label>
                        <div>
                            @if($product->is_available)
                                <span class="badge" style="background-color: var(--discord-green); color: white; padding: 4px 8px; border-radius: 4px;">
                                    <i class="fas fa-check-circle me-1"></i> Available
                                </span>
                            @else
                                <span class="badge" style="background-color: var(--discord-red); color: white; padding: 4px 8px; border-radius: 4px;">
                                    <i class="fas fa-times-circle me-1"></i> Unavailable
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="discord-card">
            <div class="discord-card-header">
                <i class="fas fa-align-left me-2" style="color: var(--discord-primary);"></i>
                Description
            </div>
            <div class="discord-card-body">
                <div style="color: var(--discord-lightest); line-height: 1.6;">
                    {{ $product->description ?? 'No description provided.' }}
                </div>
            </div>
        </div>

        <!-- Pricing and Stock -->
        <div class="discord-card">
            <div class="discord-card-header">
                <i class="fas fa-dollar-sign me-2" style="color: var(--discord-primary);"></i>
                Pricing & Stock Information
            </div>
            <div class="discord-card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label" style="color: var(--discord-light); font-size: 12px; text-transform: uppercase; font-weight: 600;">
                            Current Price
                        </label>
                        <div style="color: var(--discord-lightest); font-size: 20px; font-weight: 600;">
                            ${{ number_format($product->price, 2) }}
                        </div>
                    </div>
                    @if($product->original_price && $product->original_price > $product->price)
                    <div class="col-md-4 mb-3">
                        <label class="form-label" style="color: var(--discord-light); font-size: 12px; text-transform: uppercase; font-weight: 600;">
                            Original Price
                        </label>
                        <div style="color: var(--discord-light); font-size: 16px; text-decoration: line-through;">
                            ${{ number_format($product->original_price, 2) }}
                        </div>
                        <div style="color: var(--discord-green); font-size: 12px; font-weight: 600;">
                            {{ round((($product->original_price - $product->price) / $product->original_price) * 100) }}% OFF
                        </div>
                    </div>
                    @endif
                    <div class="col-md-4 mb-3">
                        <label class="form-label" style="color: var(--discord-light); font-size: 12px; text-transform: uppercase; font-weight: 600;">
                            Stock Quantity
                        </label>
                        <div style="color: var(--discord-lightest); font-size: 16px;">
                            @if($product->stock !== null)
                                {{ $product->stock }} units
                                @if($product->stock <= 5 && $product->stock > 0)
                                    <span style="color: var(--discord-yellow); font-size: 12px; margin-left: 8px;">
                                        <i class="fas fa-exclamation-triangle"></i> Low stock
                                    </span>
                                @elseif($product->stock == 0)
                                    <span style="color: var(--discord-red); font-size: 12px; margin-left: 8px;">
                                        <i class="fas fa-times-circle"></i> Out of stock
                                    </span>
                                @endif
                            @else
                                <span style="color: var(--discord-light);">Not tracked</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Specifications -->
        @if($product->specifications && $product->specifications->count() > 0)
        <div class="discord-card">
            <div class="discord-card-header">
                <i class="fas fa-list me-2" style="color: var(--discord-primary);"></i>
                Product Specifications
            </div>
            <div class="discord-card-body">
                <div class="row">
                    @foreach($product->specifications as $spec)
                    <div class="col-md-6 mb-3">
                        <label class="form-label" style="color: var(--discord-light); font-size: 12px; text-transform: uppercase; font-weight: 600;">
                            {{ $spec->key }}
                        </label>
                        <div style="color: var(--discord-lightest); font-size: 14px;">
                            {{ $spec->value }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Product Colors -->
        @if($product->colors && $product->colors->count() > 0)
        <div class="discord-card">
            <div class="discord-card-header">
                <i class="fas fa-palette me-2" style="color: var(--discord-primary);"></i>
                Available Colors
            </div>
            <div class="discord-card-body">
                <div class="row">
                    @foreach($product->colors as $color)
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center">
                            @if($color->color_code)
                                <div style="width: 20px; height: 20px; background-color: {{ $color->color_code }}; border-radius: 50%; margin-right: 8px; border: 1px solid var(--discord-light);"></div>
                            @endif
                            <div>
                                <div style="color: var(--discord-lightest); font-weight: 500;">{{ $color->name }}</div>
                                @if($color->price_adjustment != 0)
                                    <div style="color: var(--discord-light); font-size: 12px;">
                                        {{ $color->price_adjustment > 0 ? '+' : '' }}${{ number_format($color->price_adjustment, 2) }}
                                    </div>
                                @endif
                                @if($color->stock !== null)
                                    <div style="color: var(--discord-light); font-size: 12px;">
                                        Stock: {{ $color->stock }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Product Sizes -->
        @if($product->sizes && $product->sizes->count() > 0)
        <div class="discord-card">
            <div class="discord-card-header">
                <i class="fas fa-ruler me-2" style="color: var(--discord-primary);"></i>
                Available Sizes
            </div>
            <div class="discord-card-body">
                <div class="row">
                    @foreach($product->sizes as $size)
                    <div class="col-md-4 mb-3">
                        <div>
                            <div style="color: var(--discord-lightest); font-weight: 500;">{{ $size->name }}</div>
                            @if($size->value)
                                <div style="color: var(--discord-light); font-size: 12px;">{{ $size->value }}</div>
                            @endif
                            @if($size->additional_info)
                                <div style="color: var(--discord-light); font-size: 12px;">{{ $size->additional_info }}</div>
                            @endif
                            @if($size->price_adjustment != 0)
                                <div style="color: var(--discord-light); font-size: 12px;">
                                    {{ $size->price_adjustment > 0 ? '+' : '' }}${{ number_format($size->price_adjustment, 2) }}
                                </div>
                            @endif
                            @if($size->stock !== null)
                                <div style="color: var(--discord-light); font-size: 12px;">
                                    Stock: {{ $size->stock }}
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Product Image and Metadata -->
    <div class="col-md-4">
        <!-- Product Image -->
        <div class="discord-card">
            <div class="discord-card-header">
                <i class="fas fa-image me-2" style="color: var(--discord-primary);"></i>
                Product Image
            </div>
            <div class="discord-card-body text-center">
                @if($product->image)
                    <img src="{{ $product->image }}"
                         alt="{{ $product->name }}"
                         class="img-fluid"
                         style="max-width: 100%; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.3);">
                @else
                    <div style="background-color: var(--discord-darkest); border-radius: 8px; padding: 40px; color: var(--discord-light);">
                        <i class="fas fa-image" style="font-size: 48px; margin-bottom: 12px;"></i>
                        <div>No image uploaded</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Product Metadata -->
        <div class="discord-card">
            <div class="discord-card-header">
                <i class="fas fa-info me-2" style="color: var(--discord-primary);"></i>
                Product Metadata
            </div>
            <div class="discord-card-body">
                <div class="mb-3">
                    <label class="form-label" style="color: var(--discord-light); font-size: 12px; text-transform: uppercase; font-weight: 600;">
                        Product ID
                    </label>
                    <div style="color: var(--discord-lightest); font-family: monospace;">
                        #{{ $product->id }}
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" style="color: var(--discord-light); font-size: 12px; text-transform: uppercase; font-weight: 600;">
                        Created Date
                    </label>
                    <div style="color: var(--discord-lightest);">
                        {{ $product->created_at->format('M d, Y') }}
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" style="color: var(--discord-light); font-size: 12px; text-transform: uppercase; font-weight: 600;">
                        Last Updated
                    </label>
                    <div style="color: var(--discord-lightest);">
                        {{ $product->updated_at->format('M d, Y') }}
                    </div>
                </div>
                @if($product->featured)
                <div class="mb-3">
                    <label class="form-label" style="color: var(--discord-light); font-size: 12px; text-transform: uppercase; font-weight: 600;">
                        Featured Product
                    </label>
                    <div>
                        <span class="badge" style="background-color: var(--discord-yellow); color: white; padding: 4px 8px; border-radius: 4px;">
                            <i class="fas fa-star me-1"></i> Featured
                        </span>
                    </div>
                </div>
                @endif
                @if($product->rating && $product->rating > 0)
                <div class="mb-3">
                    <label class="form-label" style="color: var(--discord-light); font-size: 12px; text-transform: uppercase; font-weight: 600;">
                        Average Rating
                    </label>
                    <div style="color: var(--discord-lightest);">
                        <span style="color: var(--discord-yellow);">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $product->rating)
                                    <i class="fas fa-star"></i>
                                @elseif($i - 0.5 <= $product->rating)
                                    <i class="fas fa-star-half-alt"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </span>
                        <span style="margin-left: 8px;">{{ number_format($product->rating, 1) }}/5</span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="discord-card">
            <div class="discord-card-header">
                <i class="fas fa-bolt me-2" style="color: var(--discord-primary);"></i>
                Quick Actions
            </div>
            <div class="discord-card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('merchant.products.edit', $product->id) }}" class="discord-btn" style="background-color: var(--discord-primary);">
                        <i class="fas fa-edit me-1"></i> Edit Product
                    </a>
                    @if($product->is_available)
                        <form action="{{ route('merchant.products.update', $product->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="is_available" value="0">
                            <input type="hidden" name="name" value="{{ $product->name }}">
                            <input type="hidden" name="description" value="{{ $product->description }}">
                            <input type="hidden" name="price" value="{{ $product->price }}">
                            <input type="hidden" name="category_id" value="{{ $product->category_id }}">
                            <button type="submit" class="discord-btn w-100" style="background-color: var(--discord-yellow);" onclick="return confirm('Are you sure you want to mark this product as unavailable?');">
                                <i class="fas fa-eye-slash me-1"></i> Mark Unavailable
                            </button>
                        </form>
                    @else
                        <form action="{{ route('merchant.products.update', $product->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="is_available" value="1">
                            <input type="hidden" name="name" value="{{ $product->name }}">
                            <input type="hidden" name="description" value="{{ $product->description }}">
                            <input type="hidden" name="price" value="{{ $product->price }}">
                            <input type="hidden" name="category_id" value="{{ $product->category_id }}">
                            <button type="submit" class="discord-btn w-100" style="background-color: var(--discord-green);" onclick="return confirm('Are you sure you want to mark this product as available?');">
                                <i class="fas fa-eye me-1"></i> Mark Available
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('merchant.products.create') }}" class="discord-btn" style="background-color: var(--discord-light);">
                        <i class="fas fa-plus me-1"></i> Add New Product
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Breadcrumb Navigation -->
<div class="discord-card mt-3">
    <div class="discord-card-body">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="margin: 0; background: none;">
                <li class="breadcrumb-item">
                    <a href="{{ route('merchant.dashboard') }}" style="color: var(--discord-primary); text-decoration: none;">
                        <i class="fas fa-home me-1"></i> Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('merchant.products.index') }}" style="color: var(--discord-primary); text-decoration: none;">
                        Products
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page" style="color: var(--discord-light);">
                    {{ $product->name }}
                </li>
            </ol>
        </nav>
    </div>
</div>
@endsection
