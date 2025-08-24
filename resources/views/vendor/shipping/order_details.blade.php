@extends('layouts.vendor')

@section('title', 'Order Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Order #{{ $order->order_number }}</h5>
                    <div class="card-tools">
                        <a href="{{ route('vendor.shipping.orders') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Orders
                        </a>
                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#updateStatusModal">
                            <i class="fas fa-truck"></i> Update Status
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title">Order Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Order Number</th>
                                            <td>{{ $order->order_number }}</td>
                                        </tr>
                                        <tr>
                                            <th>Date</th>
                                            <td>{{ $order->created_at->format('M d, Y h:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>{{ ucfirst($order->status) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Payment Status</th>
                                            <td>{{ ucfirst($order->payment_status) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Payment Method</th>
                                            <td>{{ ucfirst($order->payment_method) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Shipping Method</th>
                                            <td>{{ ucfirst($order->shipping_method) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Shipping Status</th>
                                            <td>
                                                <span class="badge badge-{{ getShippingStatusBadgeClass($order->shipping_status) }}">
                                                    {{ ucfirst($order->shipping_status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title">Customer Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Name</th>
                                            <td>{{ $order->customer_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>{{ $order->user->email }}</td>
                                        </tr>
                                        <tr>
                                            <th>Phone</th>
                                            <td>{{ $order->customer_phone }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h6 class="card-title">Shipping Address</h6>
                                </div>
                                <div class="card-body">
                                    <address>
                                        {{ $order->shipping_address['name'] ?? $order->customer_name }}<br>
                                        {{ $order->shipping_address['address'] ?? '' }}<br>
                                        {{ $order->shipping_address['city'] ?? $order->shipping_city }} {{ $order->shipping_address['state'] ?? '' }} {{ $order->shipping_address['zip_code'] ?? '' }}<br>
                                        {{ $order->shipping_address['country'] ?? $order->shipping_country }}<br>
                                        Phone: {{ $order->shipping_address['phone'] ?? $order->customer_phone }}
                                    </address>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="card-title">Order Items</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $subtotal = 0; @endphp
                                        @foreach ($order->items as $item)
                                            @if ($item->vendor_id == Auth::user()->company->id)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            @if ($item->product && $item->product->image)
                                                                <img src="{{ asset($item->product->image) }}" alt="{{ $item->product->name }}" class="img-thumbnail mr-3" style="width: 50px; height: 50px;">
                                                            @endif
                                                            <div>
                                                                {{ $item->product ? $item->product->name : 'Product Not Found' }}
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>${{ number_format($item->price, 2) }}</td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>${{ number_format($item->total, 2) }}</td>
                                                </tr>
                                                @php $subtotal += $item->total; @endphp
                                            @endif
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3" class="text-right">Subtotal</th>
                                            <td>${{ number_format($subtotal, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th colspan="3" class="text-right">Shipping</th>
                                            <td>${{ number_format($order->shipping_cost, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th colspan="3" class="text-right">Total</th>
                                            <td>${{ number_format($subtotal + $order->shipping_cost, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    @if ($order->notes)
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="card-title">Order Notes</h6>
                            </div>
                            <div class="card-body">
                                {{ $order->notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStatusModalLabel">Update Shipping Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('vendor.shipping.update-status', $order->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="shipping_status">Shipping Status</label>
                        <select class="form-control" id="shipping_status" name="shipping_status" required>
                            <option value="pending" {{ $order->shipping_status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $order->shipping_status == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ $order->shipping_status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ $order->shipping_status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@php
function getShippingStatusBadgeClass($status) {
    switch ($status) {
        case 'pending':
            return 'warning';
        case 'processing':
            return 'info';
        case 'shipped':
            return 'primary';
        case 'delivered':
            return 'success';
        default:
            return 'secondary';
    }
}
@endphp
