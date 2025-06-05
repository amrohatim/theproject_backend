@extends('layouts.provider')

@section('title', 'Order Details')

@section('header', 'Order Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <div style="width: 40px; height: 40px; background-color: var(--discord-primary); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
            <i class="fas fa-receipt text-white"></i>
        </div>
        <div>
            <h4 class="mb-0">Order #{{ $order->order_number }}</h4>
            <p class="text-muted mb-0" style="font-size: 14px; color: var(--discord-light);">{{ $order->created_at->format('F d, Y h:i A') }}</p>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="#" onclick="window.print(); return false;" class="discord-btn discord-btn-secondary me-2">
            <i class="fas fa-print me-2"></i> Print
        </a>
        <a href="{{ route('provider.orders.index') }}" class="discord-btn discord-btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Orders
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert-container" style="margin-bottom: 20px;">
    <div class="discord-alert discord-alert-success">
        <i class="fas fa-check-circle me-2"></i>
        <span>{{ session('success') }}</span>
        <button type="button" class="close-btn" onclick="this.parentElement.style.display='none';">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
@endif

<div class="row g-4">
    <!-- Order Information -->
    <div class="col-lg-6">
        <div class="discord-card mb-4">
            <div class="discord-card-header">
                <i class="fas fa-info-circle me-2" style="color: var(--discord-primary);"></i>
                Order Information
                <div class="order-status ms-2">
                    @if($order->status == 'completed')
                    <span class="badge" style="background-color: rgba(87, 242, 135, 0.1); color: var(--discord-green); font-size: 12px; padding: 4px 8px; border-radius: 4px;">
                        <i class="fas fa-check-circle me-1"></i> Completed
                    </span>
                    @elseif($order->status == 'processing')
                    <span class="badge" style="background-color: rgba(88, 101, 242, 0.1); color: var(--discord-info); font-size: 12px; padding: 4px 8px; border-radius: 4px;">
                        <i class="fas fa-spinner me-1 fa-spin"></i> Processing
                    </span>
                    @elseif($order->status == 'pending')
                    <span class="badge" style="background-color: rgba(255, 184, 108, 0.1); color: var(--discord-warning); font-size: 12px; padding: 4px 8px; border-radius: 4px;">
                        <i class="fas fa-clock me-1"></i> Pending
                    </span>
                    @elseif($order->status == 'cancelled')
                    <span class="badge" style="background-color: rgba(237, 66, 69, 0.1); color: var(--discord-red); font-size: 12px; padding: 4px 8px; border-radius: 4px;">
                        <i class="fas fa-times-circle me-1"></i> Cancelled
                    </span>
                    @endif
                </div>
            </div>
            <div class="p-4">
                <div class="info-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="info-item" style="background-color: var(--discord-darkest); border-radius: 6px; padding: 12px;">
                        <div style="color: var(--discord-light); font-size: 12px; margin-bottom: 4px;">Order Date</div>
                        <div style="color: var(--discord-lightest); font-weight: 500;">{{ $order->created_at->format('M d, Y h:i A') }}</div>
                    </div>
                    
                    <div class="info-item" style="background-color: var(--discord-darkest); border-radius: 6px; padding: 12px;">
                        <div style="color: var(--discord-light); font-size: 12px; margin-bottom: 4px;">Total Amount</div>
                        <div style="color: var(--discord-lightest); font-weight: 600; font-size: 16px;">${{ number_format($providerItems->sum(function($item) { return $item->price * $item->quantity; }), 2) }}</div>
                    </div>
                    
                    <div class="info-item" style="background-color: var(--discord-darkest); border-radius: 6px; padding: 12px;">
                        <div style="color: var(--discord-light); font-size: 12px; margin-bottom: 4px;">Payment Method</div>
                        <div style="color: var(--discord-lightest); font-weight: 500;">
                            <i class="fas fa-credit-card me-1" style="color: var(--discord-primary);"></i>
                            {{ $order->payment_method ?? 'Credit Card' }}
                        </div>
                    </div>
                    
                    <div class="info-item" style="background-color: var(--discord-darkest); border-radius: 6px; padding: 12px;">
                        <div style="color: var(--discord-light); font-size: 12px; margin-bottom: 4px;">Payment Status</div>
                        <div>
                            @if($order->payment_status == 'paid')
                            <span style="color: var(--discord-green); font-weight: 500;">
                                <i class="fas fa-check-circle me-1"></i> Paid
                            </span>
                            @elseif($order->payment_status == 'pending')
                            <span style="color: var(--discord-warning); font-weight: 500;">
                                <i class="fas fa-clock me-1"></i> Pending
                            </span>
                            @elseif($order->payment_status == 'failed')
                            <span style="color: var(--discord-red); font-weight: 500;">
                                <i class="fas fa-times-circle me-1"></i> Failed
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Update Order Status -->
                <div style="background-color: var(--discord-darkest); border-radius: 6px; padding: 16px; margin-top: 20px;">
                    <form action="{{ route('provider.orders.update-status', $order->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="status" style="display: block; margin-bottom: 8px; color: var(--discord-lightest); font-weight: 500;">
                                Update Order Status
                            </label>
                            <select class="form-select" id="status" name="status" 
                                style="background-color: var(--discord-dark); border: none; color: var(--discord-lightest); padding: 10px 12px; border-radius: 4px; width: 100%;">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }} style="background-color: var(--discord-dark); color: var(--discord-warning);">Pending</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }} style="background-color: var(--discord-dark); color: var(--discord-info);">Processing</option>
                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }} style="background-color: var(--discord-dark); color: var(--discord-green);">Completed</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }} style="background-color: var(--discord-dark); color: var(--discord-red);">Cancelled</option>
                            </select>
                        </div>
                        <button type="submit" class="discord-btn" style="width: 100%;">
                            <i class="fas fa-sync-alt me-2"></i> Update Status
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Customer Information -->
    <div class="col-lg-6">
        <div class="discord-card mb-4">
            <div class="discord-card-header">
                <i class="fas fa-user me-2" style="color: var(--discord-primary);"></i>
                Customer Information
            </div>
            <div class="p-4">
                <div class="d-flex align-items-center mb-4" style="background-color: var(--discord-darkest); border-radius: 6px; padding: 16px;">
                    <div style="width: 50px; height: 50px; background-color: var(--discord-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 16px;">
                        <span style="color: white; font-size: 18px; text-transform: uppercase;">{{ substr($order->user->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <div style="font-weight: 600; font-size: 16px; color: var(--discord-lightest);">{{ $order->user->name }}</div>
                        <div style="color: var(--discord-light); font-size: 14px;">Customer since {{ $order->user->created_at->format('M d, Y') }}</div>
                    </div>
                </div>
                
                <div class="info-grid" style="display: grid; grid-template-columns: 1fr; gap: 12px;">
                    <div class="info-item" style="background-color: var(--discord-darkest); border-radius: 6px; padding: 12px;">
                        <div style="color: var(--discord-light); font-size: 12px; margin-bottom: 4px;">Email Address</div>
                        <div style="color: var(--discord-lightest); font-weight: 500;">
                            <i class="fas fa-envelope me-2" style="color: var(--discord-primary);"></i>
                            {{ $order->user->email }}
                        </div>
                    </div>
                    
                    <div class="info-item" style="background-color: var(--discord-darkest); border-radius: 6px; padding: 12px;">
                        <div style="color: var(--discord-light); font-size: 12px; margin-bottom: 4px;">Phone Number</div>
                        <div style="color: var(--discord-lightest); font-weight: 500;">
                            <i class="fas fa-phone me-2" style="color: var(--discord-primary);"></i>
                            {{ $order->user->phone ?? 'N/A' }}
                        </div>
                    </div>
                    
                    <div class="info-item" style="background-color: var(--discord-darkest); border-radius: 6px; padding: 12px;">
                        <div style="color: var(--discord-light); font-size: 12px; margin-bottom: 4px;">Shipping Address</div>
                        <div style="color: var(--discord-lightest); font-weight: 500;">
                            <i class="fas fa-map-marker-alt me-2" style="color: var(--discord-primary);"></i>
                            @if(isset($order->shipping_address))
                                {{ $order->shipping_address }}
                            @else
                                No shipping address provided
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="mt-4" style="background-color: var(--discord-darkest); border-radius: 6px; padding: 12px;">
                    <div style="color: var(--discord-light); font-size: 12px; margin-bottom: 8px;">Actions</div>
                    <div class="d-flex gap-2">
                        <a href="mailto:{{ $order->user->email }}" class="discord-btn discord-btn-sm discord-btn-secondary" style="font-size: 12px; padding: 6px 10px;">
                            <i class="fas fa-envelope me-1"></i> Email Customer
                        </a>
                        <a href="#" class="discord-btn discord-btn-sm discord-btn-secondary" style="font-size: 12px; padding: 6px 10px;">
                            <i class="fas fa-history me-1"></i> View Order History
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    
<!-- Order Items -->
<div class="discord-card mb-4">
    <div class="discord-card-header">
        <i class="fas fa-box me-2" style="color: var(--discord-primary);"></i>
        Order Items
        <span class="badge" style="background-color: var(--discord-primary); color: white; font-size: 12px; padding: 4px 8px; border-radius: 20px; margin-left: 10px;">{{ count($providerItems) }}</span>
    </div>
    <div class="table-responsive">
        <table class="table" style="color: var(--discord-lightest); margin-bottom: 0;">
            <thead>
                <tr style="border-bottom: 1px solid var(--discord-dark); background-color: var(--discord-darkest);">
                    <th style="padding: 12px 16px; font-weight: 600; font-size: 14px; border: none;">Product</th>
                    <th style="padding: 12px 16px; font-weight: 600; font-size: 14px; border: none;">SKU</th>
                    <th style="padding: 12px 16px; font-weight: 600; font-size: 14px; border: none;">Price</th>
                    <th style="padding: 12px 16px; font-weight: 600; font-size: 14px; border: none;">Quantity</th>
                    <th style="padding: 12px 16px; font-weight: 600; font-size: 14px; border: none;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($providerItems as $item)
                <tr style="border-bottom: 1px solid var(--discord-dark);">
                    <td style="padding: 12px 16px; border: none;">
                        <div class="d-flex align-items-center">
                            <div style="width: 50px; height: 50px; background-color: var(--discord-darkest); border-radius: 8px; overflow: hidden; margin-right: 12px;">
                                <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div>
                                <p style="margin-bottom: 2px; font-weight: 500; color: var(--discord-lightest);">{{ $item->product->name }}</p>
                                @if($item->options)
                                    <small style="color: var(--discord-light); font-size: 12px;">
                                        @foreach($item->options as $key => $value)
                                            {{ ucfirst($key) }}: {{ $value }}@if(!$loop->last), @endif
                                        @endforeach
                                    </small>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="padding: 12px 16px; border: none; color: var(--discord-light); font-size: 14px;">{{ $item->product->sku }}</td>
                    <td style="padding: 12px 16px; border: none; font-weight: 500;">${{ number_format($item->price, 2) }}</td>
                    <td style="padding: 12px 16px; border: none;">
                        <span class="badge" style="background-color: var(--discord-darkest); color: var(--discord-light); font-size: 12px; padding: 4px 8px; border-radius: 4px;">
                            × {{ $item->quantity }}
                        </span>
                    </td>
                    <td style="padding: 12px 16px; border: none; font-weight: 600; color: var(--discord-primary);">${{ number_format($item->price * $item->quantity, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div style="padding: 16px; background-color: var(--discord-darkest); border-top: 1px solid var(--discord-dark);">
        <div class="d-flex justify-content-between align-items-center">
            <div style="font-weight: 600; font-size: 16px; color: var(--discord-lightest);">Order Summary</div>
        </div>
        
        <div class="mt-3">
            <div class="d-flex justify-content-between mb-2">
                <div style="color: var(--discord-light);">Subtotal</div>
                <div style="color: var(--discord-lightest);">${{ number_format($providerItems->sum(function($item) { return $item->price * $item->quantity; }), 2) }}</div>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <div style="color: var(--discord-light);">Tax</div>
                <div style="color: var(--discord-lightest);">${{ number_format(($providerItems->sum(function($item) { return $item->price * $item->quantity; }) * 0.1), 2) }}</div>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <div style="color: var(--discord-light);">Shipping</div>
                <div style="color: var(--discord-lightest);">$0.00</div>
            </div>
            <hr style="border-color: var(--discord-dark); margin: 12px 0;">
            <div class="d-flex justify-content-between">
                <div style="font-weight: 600; color: var(--discord-lightest);">Total</div>
                <div style="font-weight: 600; font-size: 18px; color: var(--discord-primary);">
                    ${{ number_format(($providerItems->sum(function($item) { return $item->price * $item->quantity; }) * 1.1), 2) }}
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
