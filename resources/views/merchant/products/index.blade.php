@extends('layouts.merchant')

@section('title', 'Products')
@section('header', 'Products')

@section('content')
<!-- Header Section -->
<div class="discord-card">
    <div class="discord-card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 style="margin: 0; color: var(--discord-lightest); font-size: 24px; font-weight: 600;">
                    <i class="fas fa-box me-2" style="color: var(--discord-primary);"></i>
                    My Products
                </h2>
                <p style="margin: 8px 0 0 0; color: var(--discord-light); font-size: 14px;">
                    Manage your product inventory and listings
                </p>
            </div>
            <div>
                <a href="{{ route('merchant.products.create') }}" class="discord-btn">
                    <i class="fas fa-plus me-1"></i> Add New Product
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Products Table -->
<div class="discord-card">
    <div class="discord-card-header d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-list me-2" style="color: var(--discord-primary);"></i>
            Products List ({{ $products->total() }} total)
        </div>
        <div class="d-flex gap-2">
            <input type="text" placeholder="Search products..." class="form-control" style="width: 200px; background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
            <select class="form-select" style="width: 150px; background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div>
    
    @if($products->count() > 0)
    <div class="table-responsive">
        <table class="discord-table">
            <thead>
                <tr>
                    <th style="width: 80px;">Image</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td>
                        @if($product->image)
                            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}"
                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                        @else
                            <div style="width: 60px; height: 60px; background-color: var(--discord-darkest); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-image" style="color: var(--discord-light); font-size: 20px;"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight: 600; color: var(--discord-lightest); margin-bottom: 4px;">
                            {{ $product->name }}
                        </div>
                        <div style="font-size: 12px; color: var(--discord-light);">
                            {{ Str::limit($product->description, 50) }}
                        </div>
                        @if($product->sku)
                        <div style="font-size: 11px; color: var(--discord-light); margin-top: 2px;">
                            SKU: {{ $product->sku }}
                        </div>
                        @endif
                    </td>
                    <td>
                        <span style="background-color: var(--discord-darkest); color: var(--discord-light); padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                            {{ $product->category->name ?? 'Uncategorized' }}
                        </span>
                    </td>
                    <td>
                        <div style="font-weight: 600; color: var(--discord-primary); font-size: 16px;">
                            ${{ number_format($product->price, 2) }}
                        </div>
                    </td>
                    <td>
                        @if($product->stock !== null)
                            <div style="font-weight: 500; color: {{ $product->stock > 10 ? 'var(--discord-green)' : ($product->stock > 0 ? 'var(--discord-yellow)' : 'var(--discord-red)') }};">
                                {{ $product->stock }} units
                            </div>
                        @else
                            <span style="color: var(--discord-light); font-size: 12px;">Not tracked</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge" style="background-color: {{ $product->is_available ? 'var(--discord-green)' : 'var(--discord-light)' }}; color: white; padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                            {{ $product->is_available ? 'Available' : 'Unavailable' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('merchant.products.show', $product->id) }}" 
                               class="btn btn-sm" 
                               style="background-color: var(--discord-primary); color: white; border: none; padding: 4px 8px; border-radius: 4px;"
                               title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('merchant.products.edit', $product->id) }}" 
                               class="btn btn-sm" 
                               style="background-color: var(--discord-yellow); color: white; border: none; padding: 4px 8px; border-radius: 4px;"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('merchant.products.destroy', $product->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-sm" 
                                        style="background-color: var(--discord-red); color: white; border: none; padding: 4px 8px; border-radius: 4px;"
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
    <div class="discord-card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div style="color: var(--discord-light); font-size: 14px;">
                Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} results
            </div>
            <div>
                {{ $products->links() }}
            </div>
        </div>
    </div>
    @endif

    @else
    <!-- Empty State -->
    <div class="discord-card-body" style="text-align: center; padding: 60px 20px;">
        <i class="fas fa-box" style="font-size: 64px; color: var(--discord-light); opacity: 0.5; margin-bottom: 20px;"></i>
        <h3 style="color: var(--discord-lightest); margin-bottom: 12px;">No Products Yet</h3>
        <p style="color: var(--discord-light); margin-bottom: 24px; font-size: 16px;">
            Start building your product catalog by adding your first product.
        </p>
        <a href="{{ route('merchant.products.create') }}" class="discord-btn" style="font-size: 16px; padding: 12px 24px;">
            <i class="fas fa-plus me-2"></i> Add Your First Product
        </a>
    </div>
    @endif
</div>

<!-- Quick Stats -->
@if($products->count() > 0)
<div class="row mt-4">
    <div class="col-md-3">
        <div class="discord-card">
            <div class="discord-card-body" style="text-align: center;">
                <div style="font-size: 24px; font-weight: 700; color: var(--discord-primary); margin-bottom: 8px;">
                    {{ $products->where('is_available', true)->count() }}
                </div>
                <div style="color: var(--discord-light); font-size: 14px;">Active Products</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="discord-card">
            <div class="discord-card-body" style="text-align: center;">
                <div style="font-size: 24px; font-weight: 700; color: var(--discord-yellow); margin-bottom: 8px;">
                    {{ $products->where('is_available', false)->count() }}
                </div>
                <div style="color: var(--discord-light); font-size: 14px;">Inactive Products</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="discord-card">
            <div class="discord-card-body" style="text-align: center;">
                <div style="font-size: 24px; font-weight: 700; color: var(--discord-green); margin-bottom: 8px;">
                    ${{ number_format($products->avg('price'), 2) }}
                </div>
                <div style="color: var(--discord-light); font-size: 14px;">Average Price</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="discord-card">
            <div class="discord-card-body" style="text-align: center;">
                <div style="font-size: 24px; font-weight: 700; color: var(--discord-lightest); margin-bottom: 8px;">
                    {{ $products->whereNotNull('stock')->sum('stock') }}
                </div>
                <div style="color: var(--discord-light); font-size: 14px;">Total Stock</div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Search functionality (placeholder for future implementation)
    $('input[placeholder="Search products..."]').on('keyup', function() {
        // TODO: Implement search functionality
    });

    // Status filter (placeholder for future implementation)
    $('select').on('change', function() {
        // TODO: Implement status filtering
    });
});
</script>
@endsection
