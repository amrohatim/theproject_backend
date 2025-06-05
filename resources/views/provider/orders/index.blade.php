@extends('layouts.provider')

@section('title', 'Orders')

@section('header', 'Manage Orders')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <div style="width: 40px; height: 40px; background-color: var(--discord-primary); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
            <i class="fas fa-shopping-cart text-white"></i>
        </div>
        <div>
            <h4 class="mb-0">Orders</h4>
            <p class="text-muted mb-0" style="font-size: 14px; color: var(--discord-light);">Manage and track your customer orders</p>
        </div>
    </div>
    
    <div class="order-statistics d-flex gap-3">
        <div class="stat-item" style="background-color: var(--discord-darkest); border-radius: 8px; padding: 10px 15px; min-width: 120px;">
            <div style="color: var(--discord-light); font-size: 12px;">Pending</div>
            <div class="d-flex align-items-center">
                <span style="font-size: 18px; font-weight: bold; color: var(--discord-warning);">{{ $orders->where('status', 'pending')->count() }}</span>
                <i class="fas fa-clock ms-2" style="color: var(--discord-warning);"></i>
            </div>
        </div>
        <div class="stat-item" style="background-color: var(--discord-darkest); border-radius: 8px; padding: 10px 15px; min-width: 120px;">
            <div style="color: var(--discord-light); font-size: 12px;">Processing</div>
            <div class="d-flex align-items-center">
                <span style="font-size: 18px; font-weight: bold; color: var(--discord-info);">{{ $orders->where('status', 'processing')->count() }}</span>
                <i class="fas fa-spinner ms-2" style="color: var(--discord-info);"></i>
            </div>
        </div>
        <div class="stat-item" style="background-color: var(--discord-darkest); border-radius: 8px; padding: 10px 15px; min-width: 120px;">
            <div style="color: var(--discord-light); font-size: 12px;">Completed</div>
            <div class="d-flex align-items-center">
                <span style="font-size: 18px; font-weight: bold; color: var(--discord-green);">{{ $orders->where('status', 'completed')->count() }}</span>
                <i class="fas fa-check-circle ms-2" style="color: var(--discord-green);"></i>
            </div>
        </div>
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

<!-- Search and Filter -->
<div class="discord-card mb-4">
    <div class="discord-card-header">
        <i class="fas fa-search me-2" style="color: var(--discord-primary);"></i>
        Search & Filter
    </div>
    <div class="p-4">
        <form action="{{ route('provider.orders.index') }}" method="GET">
            <div class="row g-3">
                <div class="col-md-4 mb-3">
                    <label for="search" style="display: block; margin-bottom: 8px; color: var(--discord-lightest); font-weight: 500;">
                        Search Orders
                    </label>
                    <div style="position: relative;">
                        <i class="fas fa-search" style="position: absolute; left: 12px; top: 11px; color: var(--discord-light);"></i>
                        <input type="text" class="form-control" id="search" name="search" 
                            placeholder="Order #, customer name..." value="{{ request('search') }}" 
                            style="background-color: var(--discord-dark); border: none; color: var(--discord-lightest); padding: 10px 12px 10px 35px; border-radius: 4px; width: 100%;">
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="status" style="display: block; margin-bottom: 8px; color: var(--discord-lightest); font-weight: 500;">
                        Order Status
                    </label>
                    <select class="form-select" id="status" name="status" 
                        style="background-color: var(--discord-dark); border: none; color: var(--discord-lightest); padding: 10px 12px; border-radius: 4px; width: 100%;">
                        <option value="" style="background-color: var(--discord-dark);">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }} style="background-color: var(--discord-dark); color: var(--discord-warning);">Pending</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }} style="background-color: var(--discord-dark); color: var(--discord-info);">Processing</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }} style="background-color: var(--discord-dark); color: var(--discord-green);">Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }} style="background-color: var(--discord-dark); color: var(--discord-red);">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="date_range" style="display: block; margin-bottom: 8px; color: var(--discord-lightest); font-weight: 500;">
                        Date Range
                    </label>
                    <div style="position: relative;">
                        <i class="fas fa-calendar" style="position: absolute; left: 12px; top: 11px; color: var(--discord-light);"></i>
                        <input type="text" class="form-control" id="date_range" name="date_range" 
                            placeholder="Select date range" value="{{ request('date_range') }}" 
                            style="background-color: var(--discord-dark); border: none; color: var(--discord-lightest); padding: 10px 12px 10px 35px; border-radius: 4px; width: 100%;">
                    </div>
                </div>
                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <button type="submit" class="discord-btn" style="width: 100%;">
                        <i class="fas fa-filter me-2"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Orders Table -->
<div class="discord-card mb-4">
    <div class="discord-card-header">
        <i class="fas fa-list me-2" style="color: var(--discord-primary);"></i>
        Your Orders
        <span class="badge" style="background-color: var(--discord-primary); color: white; font-size: 12px; padding: 4px 8px; border-radius: 20px; margin-left: 10px;">{{ $orders->total() }}</span>
    </div>
    <div>
        <div class="table-responsive">
            <table class="table" style="color: var(--discord-lightest); margin-bottom: 0;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--discord-dark); background-color: var(--discord-darkest);">
                        <th style="padding: 12px 16px; font-weight: 600; font-size: 14px; border: none;">Order #</th>
                        <th style="padding: 12px 16px; font-weight: 600; font-size: 14px; border: none;">Customer</th>
                        <th style="padding: 12px 16px; font-weight: 600; font-size: 14px; border: none;">Items</th>
                        <th style="padding: 12px 16px; font-weight: 600; font-size: 14px; border: none;">Total</th>
                        <th style="padding: 12px 16px; font-weight: 600; font-size: 14px; border: none;">Status</th>
                        <th style="padding: 12px 16px; font-weight: 600; font-size: 14px; border: none;">Date</th>
                        <th style="padding: 12px 16px; font-weight: 600; font-size: 14px; border: none;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr style="border-bottom: 1px solid var(--discord-dark); transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='var(--discord-dark)'" onmouseout="this.style.backgroundColor='transparent'">
                        <td style="padding: 12px 16px; border: none;">
                            <a href="{{ route('provider.orders.show', $order->id) }}" style="color: var(--discord-primary); text-decoration: none; font-weight: 500;">
                                #{{ $order->order_number }}
                            </a>
                        </td>
                        <td style="padding: 12px 16px; border: none;">
                            <div class="d-flex align-items-center">
                                <div style="width: 28px; height: 28px; background-color: var(--discord-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                                    <span style="color: white; font-size: 12px; text-transform: uppercase;">{{ substr($order->user->name, 0, 1) }}</span>
                                </div>
                                <span>{{ $order->user->name }}</span>
                            </div>
                        </td>
                        <td style="padding: 12px 16px; border: none;">
                            <span class="badge" style="background-color: var(--discord-darkest); color: var(--discord-light); font-size: 12px; padding: 4px 8px; border-radius: 4px;">
                                {{ $order->items_count }} item(s)
                            </span>
                        </td>
                        <td style="padding: 12px 16px; border: none; font-weight: 600;">${{ number_format($order->total, 2) }}</td>
                        <td style="padding: 12px 16px; border: none;">
                            @if($order->status == 'completed')
                            <span class="badge" style="background-color: rgba(87, 242, 135, 0.1); color: var(--discord-green); font-size: 12px; padding: 6px 10px; border-radius: 4px; display: inline-flex; align-items: center;">
                                <i class="fas fa-check-circle me-1"></i> Completed
                            </span>
                            @elseif($order->status == 'processing')
                            <span class="badge" style="background-color: rgba(88, 101, 242, 0.1); color: var(--discord-info); font-size: 12px; padding: 6px 10px; border-radius: 4px; display: inline-flex; align-items: center;">
                                <i class="fas fa-spinner me-1 fa-spin"></i> Processing
                            </span>
                            @elseif($order->status == 'pending')
                            <span class="badge" style="background-color: rgba(255, 184, 108, 0.1); color: var(--discord-warning); font-size: 12px; padding: 6px 10px; border-radius: 4px; display: inline-flex; align-items: center;">
                                <i class="fas fa-clock me-1"></i> Pending
                            </span>
                            @elseif($order->status == 'cancelled')
                            <span class="badge" style="background-color: rgba(237, 66, 69, 0.1); color: var(--discord-red); font-size: 12px; padding: 6px 10px; border-radius: 4px; display: inline-flex; align-items: center;">
                                <i class="fas fa-times-circle me-1"></i> Cancelled
                            </span>
                            @endif
                        </td>
                        <td style="padding: 12px 16px; border: none; color: var(--discord-light); font-size: 13px;">
                            {{ $order->created_at->format('M d, Y') }}
                        </td>
                        <td style="padding: 12px 16px; border: none;">
                            <a href="{{ route('provider.orders.show', $order->id) }}" class="discord-btn discord-btn-sm" style="font-size: 12px; padding: 6px 10px;">
                                <i class="fas fa-eye me-1"></i> View Details
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="padding: 30px; text-align: center; color: var(--discord-light); border: none;">
                            <i class="fas fa-shopping-cart fa-3x mb-3" style="color: var(--discord-dark);"></i>
                            <p style="font-size: 16px; margin: 0;">No orders found</p>
                            <p style="font-size: 14px; margin-top: 5px;">When customers place orders, they will appear here</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
        <div class="discord-card-footer" style="padding: 15px 20px; display: flex; justify-content: flex-end; border-top: 1px solid var(--discord-dark);">
            <div class="discord-pagination">
                <a href="{{ $orders->withQueryString()->previousPageUrl() }}" class="discord-pagination-btn {{ $orders->onFirstPage() ? 'disabled' : '' }}" {{ $orders->onFirstPage() ? 'aria-disabled=true' : '' }}>
                    <i class="fas fa-chevron-left"></i>
                </a>
                <span class="discord-pagination-info">
                    Page {{ $orders->currentPage() }} of {{ $orders->lastPage() }}
                </span>
                <a href="{{ $orders->withQueryString()->nextPageUrl() }}" class="discord-pagination-btn {{ !$orders->hasMorePages() ? 'disabled' : '' }}" {{ !$orders->hasMorePages() ? 'aria-disabled=true' : '' }}>
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize date range picker if available
        if($.fn.daterangepicker) {
            $('#date_range').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                }
            });

            $('#date_range').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            });

            $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
        }
    });
</script>
@endsection
