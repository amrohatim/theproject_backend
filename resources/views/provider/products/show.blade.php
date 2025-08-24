@extends('layouts.app')

@section('title', 'Product Details')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Product Details</h1>
        <div>
            <a href="{{ route('provider.products.edit', $product->id) }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm mr-2">
                <i class="fas fa-edit fa-sm text-white-50"></i> Edit Product
            </a>
            <a href="{{ route('provider.products.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Products
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="row">
        <!-- Product Image -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Product Image</h6>
                </div>
                <div class="card-body text-center">
                    <img src="{{ $product->image }}" alt="{{ $product->name }}" class="img-fluid mb-3" style="max-height: 300px;">
                    <div class="mt-3">
                        <span class="badge {{ $product->is_available ? 'badge-success' : 'badge-danger' }} p-2">
                            {{ $product->is_available ? 'Available' : 'Unavailable' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Product Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Product Information</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3 font-weight-bold">Product Name:</div>
                        <div class="col-md-9">{{ $product->name }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 font-weight-bold">SKU:</div>
                        <div class="col-md-9">{{ $product->sku ?? 'N/A' }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 font-weight-bold">Price:</div>
                        <div class="col-md-9">${{ number_format($product->price, 2) }}</div>
                    </div>
                    
                    @if($product->compare_price)
                    <div class="row mb-3">
                        <div class="col-md-3 font-weight-bold">Compare at Price:</div>
                        <div class="col-md-9">${{ number_format($product->compare_price, 2) }}</div>
                    </div>
                    @endif
                    
                    <div class="row mb-3">
                        <div class="col-md-3 font-weight-bold">Category:</div>
                        <div class="col-md-9">{{ $product->category->name ?? 'Uncategorized' }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 font-weight-bold">Quantity:</div>
                        <div class="col-md-9">{{ $product->quantity ?? 0 }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 font-weight-bold">Created:</div>
                        <div class="col-md-9">{{ $product->created_at->format('M d, Y h:i A') }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 font-weight-bold">Last Updated:</div>
                        <div class="col-md-9">{{ $product->updated_at->format('M d, Y h:i A') }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 font-weight-bold">Description:</div>
                        <div class="col-md-9">
                            <div class="p-3 bg-light rounded">
                                {!! $product->description !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sales Statistics (Placeholder) -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Sales Statistics</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Views</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">245</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-eye fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Total Sales</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">12</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Conversion Rate</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">4.9%</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-percent fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Revenue</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">$345.50</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
