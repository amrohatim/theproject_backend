@extends('layouts.dashboard')

@section('title', __('messages.order_details'))
@section('page-title', __('messages.order_details'))

@push('styles')
<style>
    /* Enhanced Product Images */
    .product-image-container {
        position: relative;
        overflow: hidden;
        border-radius: 0.5rem;
        transition: transform 0.3s ease;
        cursor: pointer;
    }
    
    .product-image-container:hover {
        transform: scale(1.05);
    }
    
    .product-image {
        width: 300px;
        height: 300px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .product-image-small {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 0.5rem;
    }
    
    .product-image-container:hover .product-image {
        transform: scale(1.1);
    }
    
    /* Hover Zoom Modal */
    .image-zoom-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        cursor: pointer;
    }
    
    .image-zoom-modal img {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
    }
    
    /* Tabbed Interface */
    .tab-button {
        padding: 0.75rem 1.5rem;
        border-bottom: 2px solid transparent;
        transition: all 0.3s ease;
        cursor: pointer;
        background: transparent;
        border: none;
    }
    
    .tab-button.active {
        border-bottom-color: #4f46e5;
        color: #4f46e5;
        background-color: #f8fafc;
    }
    
    .tab-content {
        display: none;
        padding: 1.5rem;
        background: #f8fafc;
        border-radius: 0 0 0.5rem 0.5rem;
    }
    
    .tab-content.active {
        display: block;
    }
    
    /* Status Indicators */
    .status-indicator {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .status-indicator::before {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }
    
    .status-pending::before { background-color: #6b7280; }
    .status-processing::before { background-color: #f59e0b; }
    .status-shipped::before { background-color: #3b82f6; }
    .status-delivered::before { background-color: #10b981; }
    .status-cancelled::before { background-color: #ef4444; }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    /* Progress Tracking */
    .progress-tracker {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 2rem 0;
        position: relative;
    }
    
    .progress-tracker::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 2px;
        background: #e5e7eb;
        z-index: 1;
    }
    
    .progress-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        background: white;
        padding: 0.5rem;
        border-radius: 50%;
        z-index: 2;
        position: relative;
        min-width: 60px;
        border: 2px solid #e5e7eb;
    }
    
    .progress-step.completed {
        background: #10b981;
        color: white;
        border-color: #10b981;
    }
    
    .progress-step.current {
        background: #3b82f6;
        color: white;
        border-color: #3b82f6;
        animation: pulse 2s infinite;
    }
    
    /* Size Display Enhancement */
    .size-display {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.5rem;
        background: #f3f4f6;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    /* Bulk Actions */
    .bulk-actions-panel {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
        .product-image {
            width: 200px;
            height: 200px;
        }
        
        .progress-tracker {
            flex-direction: column;
            gap: 1rem;
        }
        
        .progress-tracker::before {
            display: none;
        }
        
        .tab-button {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
        
        .product-image-container {
            width: 100%;
            max-width: 200px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Enhanced Product Details Toggle
    function toggleProductDetails(elementId) {
        const element = document.getElementById(elementId);
        if (element) {
            element.classList.toggle('hidden');
        }
    }
    
    // Image Zoom Functionality
    function showImageZoom(imageSrc, altText) {
        const modal = document.getElementById('imageZoomModal');
        const modalImg = document.getElementById('zoomedImage');
        modal.style.display = 'flex';
        modalImg.src = imageSrc;
        modalImg.alt = altText;
    }
    
    function hideImageZoom() {
        document.getElementById('imageZoomModal').style.display = 'none';
    }
    
    // Tab Functionality
    function switchTab(tabName, element) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        
        // Remove active class from all tab buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active');
        });
        
        // Show selected tab content
        document.getElementById(tabName).classList.add('active');
        
        // Add active class to clicked button
        element.classList.add('active');
    }
    
    // Bulk Status Update
    function toggleBulkActions() {
        const panel = document.getElementById('bulkActionsPanel');
        panel.classList.toggle('hidden');
    }
    
    function selectAllItems() {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        const selectAll = document.getElementById('selectAllItems');
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });
        updateBulkActionsVisibility();
    }
    
    function updateBulkActionsVisibility() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        const bulkActions = document.getElementById('bulkActionsControls');
        if (bulkActions) {
            bulkActions.style.display = checkedBoxes.length > 0 ? 'block' : 'none';
        }
    }
    
    function bulkUpdateStatus() {
        const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
        const status = document.getElementById('bulkStatus').value;
        const notes = document.getElementById('bulkNotes').value;
        
        if (checkedBoxes.length === 0) {
            alert('Please select at least one item to update.');
            return;
        }
        
        if (!status) {
            alert('Please select a status.');
            return;
        }
        
        const itemIds = Array.from(checkedBoxes).map(cb => cb.value);
        
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("vendor.orders.update-multiple-status") }}';
        
        // CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Item IDs
        itemIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'item_ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        // Status
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = status;
        form.appendChild(statusInput);
        
        // Notes
        if (notes) {
            const notesInput = document.createElement('input');
            notesInput.type = 'hidden';
            notesInput.name = 'notes';
            notesInput.value = notes;
            form.appendChild(notesInput);
        }
        
        document.body.appendChild(form);
        form.submit();
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Set first tab as active by default
        const firstTab = document.querySelector('.tab-button');
        const firstContent = document.querySelector('.tab-content');
        if (firstTab && firstContent) {
            firstTab.classList.add('active');
            firstContent.classList.add('active');
        }
        
        // Add event listeners for item checkboxes
        document.querySelectorAll('.item-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkActionsVisibility);
        });
    });
</script>
@endpush

@section('content')
<div class="container mx-auto">
    <!-- Image Zoom Modal -->
    <div id="imageZoomModal" class="image-zoom-modal" onclick="hideImageZoom()">
        <img id="zoomedImage" src="" alt="">
    </div>

    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('messages.order_number') }} #{{ $order->order_number ?? 'N/A' }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $order->created_at ? $order->created_at->format('F d, Y h:i A') : 'N/A' }}</p>
        </div>
        <div class="mt-4 md:mt-0 flex flex-wrap gap-2">
            @if(isset($allItemsBelongToVendor) && $allItemsBelongToVendor)
            <a href="{{ route('vendor.orders.edit', $order->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-edit mr-2"></i> {{ __('messages.edit_order') }}
            </a>
            @endif
            <a href="{{ route('vendor.orders.invoice', $order->id) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-file-invoice mr-2"></i> {{ __('messages.invoice') }}
            </a>
            <!-- <button onclick="toggleBulkActions()" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-900 focus:outline-none focus:border-purple-900 focus:ring ring-purple-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-tasks mr-2"></i> Bulk Actions
            </button> -->
            <a href="{{ route('vendor.orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i> {{ __('messages.back') }}
            </a>
        </div>
    </div>

    <!-- Progress Tracker -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700 mb-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('messages.order_progress') }}</h3>
        <div class="progress-tracker">
            <div class="progress-step {{ in_array($order->status, ['pending', 'processing', 'shipped', 'delivered']) ? 'completed' : '' }}">
                <i class="fas fa-clock"></i>
                <span class="text-xs mt-1">{{ __('messages.pending') }}</span>
            </div>
            <div class="progress-step {{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'completed' : ($order->status == 'processing' ? 'current' : '') }}">
                <i class="fas fa-cog"></i>
                <span class="text-xs mt-1">{{ __('messages.processing') }}</span>
            </div>
            <div class="progress-step {{ in_array($order->status, ['shipped', 'delivered']) ? 'completed' : ($order->status == 'shipped' ? 'current' : '') }}">
                <i class="fas fa-truck"></i>
                <span class="text-xs mt-1">{{ __('messages.shipped') }}</span>
            </div>
            <div class="progress-step {{ $order->status == 'delivered' ? 'completed current' : '' }}">
                <i class="fas fa-check"></i>
                <span class="text-xs mt-1">{{ __('messages.delivered') }}</span>
            </div>
        </div>
    </div>

    <!-- Bulk Actions Panel -->
    <div id="bulkActionsPanel" class="bulk-actions-panel hidden">
        <h3 class="text-lg font-semibold mb-4">
            <i class="fas fa-tasks mr-2"></i>
            {{ __('messages.bulk_status_update') }}
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium mb-2">{{ __('messages.select_status') }}</label>
                <select id="bulkStatus" class="w-full px-3 py-2 border border-gray-300 rounded-md text-gray-900">
                    <option value="">{{ __('messages.choose_status') }}</option>
                    <option value="processing">{{ __('messages.processing') }}</option>
                    <option value="shipped">{{ __('messages.shipped') }}</option>
                    <option value="delivered">{{ __('messages.delivered') }}</option>
                    <option value="cancelled">{{ __('messages.cancelled') }}</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-2">{{ __('messages.notes_optional') }}</label>
                <input type="text" id="bulkNotes" class="w-full px-3 py-2 border border-gray-300 rounded-md text-gray-900" placeholder="{{ __('messages.add_notes_for_status_update') }}">
            </div>
            <div class="flex items-end">
                <button onclick="bulkUpdateStatus()" class="w-full px-4 py-2 bg-white text-purple-600 rounded-md font-semibold hover:bg-gray-100 transition">
                    <i class="fas fa-save mr-2"></i>
                    {{ __('messages.update_selected') }}
                </button>
            </div>
        </div>
        <div id="bulkActionsControls" style="display: none;" class="mt-4 p-3 bg-white bg-opacity-20 rounded-md">
            <p class="text-sm">Selected items will be updated with the chosen status and notes.</p>
        </div>
    </div>

    <!-- Order Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Order Status -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Order Status</h3>

            <!-- Overall Order Status -->
            <div class="mb-4">
                <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">Overall Order Status:</div>
                <div class="status-indicator status-{{ $order->status }}">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        @if($order->status == 'delivered') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                        @elseif($order->status == 'shipped') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                        @elseif($order->status == 'processing') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                        @elseif($order->status == 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                        @elseif($order->status == 'partially_shipped') bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200
                        @elseif($order->status == 'partially_delivered') bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200
                        @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                        {{ ucfirst(str_replace('_', ' ', $order->status ?? 'pending')) }}
                    </span>
                </div>
            </div>

            <!-- Vendor-specific Status -->
            @if(isset($vendorStatus))
            <div class="mb-4">
                <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">Your Status for This Order:</div>
                <div class="status-indicator status-{{ $vendorStatus->status }}">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        @if($vendorStatus->status == 'delivered') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                        @elseif($vendorStatus->status == 'shipped') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                        @elseif($vendorStatus->status == 'processing') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                        @elseif($vendorStatus->status == 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                        @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                        {{ ucfirst($vendorStatus->status ?? 'pending') }}
                    </span>
                </div>
                @if($vendorStatus->notes)
                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400 italic">
                    Note: {{ $vendorStatus->notes }}
                </div>
                @endif
            </div>
            @endif

            <div class="space-y-2">
            <div class="flex justify-between">
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.order_date') }}:</span>
                <span class="text-sm text-gray-900 dark:text-white">{{ $order->created_at ? $order->created_at->format('M d, Y') : 'N/A' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.payment_status') }}:</span>
                <span class="text-sm text-gray-900 dark:text-white">{{ ucfirst($order->payment_status ?? __('messages.pending')) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.payment_method') }}:</span>
                <span class="text-sm text-gray-900 dark:text-white">{{ ucfirst($order->payment_method ?? 'N/A') }}</span>
            </div>
        </div>
        </div>

        <!-- Customer Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('messages.customer_information') }}</h3>
            <div class="space-y-2">
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Name:</span>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $order->user->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Email:</span>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $order->user->email ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.phone') }}:</span>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $order->user->phone ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Branch Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('messages.branch_information') }}</h3>
            <div class="space-y-2">
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.branch') }}:</span>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $order->branch->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('messages.address') }}:</span>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $order->branch->address ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Phone:</span>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $order->branch->phone ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    @if(isset($allItemsBelongToVendor) && !$allItemsBelongToVendor)
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    <strong>{{ __('messages.note') }}:</strong> {{ __('messages.multiple_vendors_note') }}
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Update All Vendor Items Form -->
    @if(isset($companyId) && $order->items->where('vendor_id', $companyId)->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('messages.update_all_your_items') }}</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('vendor.order-items.update-vendor-items-status', $order->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('messages.status') }}</label>
                        <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="pending" {{ isset($vendorStatus) && $vendorStatus->status == 'pending' ? 'selected' : '' }}>{{ __('messages.pending') }}</option>
                            <option value="processing" {{ isset($vendorStatus) && $vendorStatus->status == 'processing' ? 'selected' : '' }}>{{ __('messages.processing') }}</option>
                            <option value="shipped" {{ isset($vendorStatus) && $vendorStatus->status == 'shipped' ? 'selected' : '' }}>{{ __('messages.shipped') }}</option>
                            <option value="delivered" {{ isset($vendorStatus) && $vendorStatus->status == 'delivered' ? 'selected' : '' }}>{{ __('messages.delivered') }}</option>
                            <option value="cancelled" {{ isset($vendorStatus) && $vendorStatus->status == 'cancelled' ? 'selected' : '' }}>{{ __('messages.cancelled') }}</option>
                        </select>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('messages.notes') }}</label>
                        <textarea id="notes" name="notes" rows="1" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"></textarea>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('messages.update_all_your_items') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Enhanced Order Items -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                @if(isset($allItemsBelongToVendor) && !$allItemsBelongToVendor)
                    Your Products in This Order
                @else
                    Order Items
                @endif
            </h3>
            <div class="flex items-center">
                <input type="checkbox" id="selectAllItems" onchange="selectAllItems()" class="mr-2">
                <label for="selectAllItems" class="text-sm text-gray-600 dark:text-gray-400">Select All</label>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Select</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Details</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantity</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @php
                        // Filter items to only show those belonging to this vendor if companyId is set
                        $itemsToShow = isset($companyId) ? $order->items->where('vendor_id', $companyId) : $order->items;
                        $subtotal = 0;
                    @endphp

                    @forelse($itemsToShow ?? [] as $item)
                    @php
                        $subtotal += $item->price * $item->quantity;
                    @endphp
                    <tr>
                        <!-- Checkbox -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" class="item-checkbox" value="{{ $item->id }}" onchange="updateBulkActionsVisibility()">
                        </td>
                        
                        <!-- Product Image and Basic Info -->
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($item->product && $item->product->image)
                                <div class="product-image-container mr-4" onclick="showImageZoom('{{ $item->product->image }}', '{{ $item->product->name }}')">
                                    <img class="product-image-small" src="{{ $item->product->image }}" alt="{{ $item->product->name }}" loading="lazy">
                                </div>
                                @elseif($item->color_image)
                                <div class="product-image-container mr-4" onclick="showImageZoom('{{ $item->color_image }}', '{{ $item->product->name }}')">
                                    <img class="product-image-small" src="{{ $item->color_image }}" alt="{{ $item->product->name }}" loading="lazy">
                                </div>
                                @else
                                <div class="product-image-container mr-4">
                                    <div class="product-image-small bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                </div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $item->product->name ?? 'Unknown Product' }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        SKU: {{ $item->product->sku ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        
                        <!-- Enhanced Product Details -->
                        <td class="px-6 py-4">
                            <div class="space-y-2">
                                <!-- Color and Size Display -->
                                @if($item->color_name || $item->size_name)
                                <div class="flex flex-wrap gap-2">
                                    @if($item->color_name)
                                    <div class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded-md text-xs">
                                        @if($item->color_value)
                                        <span class="inline-block h-3 w-3 rounded-full border border-gray-300" style="background-color: {{ $item->color_value }};"></span>
                                        @endif
                                        {{ $item->color_name }}
                                    </div>
                                    @endif
                                    
                                    @if($item->size_name)
                                    <div class="size-display">
                                        @php
                                            // Enhanced size display with symbol
                                            $sizeDisplay = $item->size_value.' ' .$item->size_name;
                                        if ($item->product && $item->product->sizes) {
                                            $size = $item->product->sizes->where('name', $item->size_name)->first();
                                            if ($size && $size->symbol) {
                                                $sizeDisplay = $item->size_value.'' .$item->size_name. ' ' . $size->symbol;
                                            }
                                            }
                                        @endphp
                                        <i class="fas fa-ruler-combined text-xs"></i>
                                        {{ $sizeDisplay }}
                                    </div>
                                    @endif
                                </div>
                                @endif
                                
                                <!-- Expandable Details Button -->
                                <button onclick="toggleProductDetails('product-details-{{ $item->id }}')" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 text-xs flex items-center gap-1">
                                    <i class="fas fa-info-circle"></i>
                                    View Details
                                </button>
                            </div>
                        </td>
                        
                        <!-- Price -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">${{ number_format($item->price, 2) }}</div>
                        </td>
                        
                        <!-- Quantity -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">{{ $item->quantity }}</div>
                        </td>
                        
                        <!-- Total -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">${{ number_format($item->price * $item->quantity, 2) }}</div>
                        </td>
                        
                        <!-- Status -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="status-indicator status-{{ $item->status }}">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($item->status == 'delivered') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($item->status == 'shipped') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @elseif($item->status == 'processing') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @elseif($item->status == 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                    {{ ucfirst($item->status ?? 'pending') }}
                                </span>
                            </div>
                        </td>
                        
                        <!-- Actions -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('vendor.order-items.edit', $item->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                <i class="fas fa-edit mr-1"></i>
                                Update Status
                            </a>
                        </td>
                    </tr>
                    
                    <!-- Expandable Product Details Row -->
                    <tr id="product-details-{{ $item->id }}" class="hidden">
                        <td colspan="8" class="px-6 py-4 bg-gray-50 dark:bg-gray-700">
                            <div class="max-w-4xl">
                                <!-- Tabbed Interface -->
                                <div class="border-b border-gray-200 dark:border-gray-600">
                                    <nav class="-mb-px flex space-x-8">
                                        <button class="tab-button" onclick="switchTab('specifications-{{ $item->id }}', this)">
                                            <i class="fas fa-list mr-2"></i>
                                            {{ __('messages.specifications') }}
                                        </button>
                                        <button class="tab-button" onclick="switchTab('vendor-notes-{{ $item->id }}', this)">
                                            <i class="fas fa-sticky-note mr-2"></i>
                                            {{ __('messages.vendor_notes') }}
                                        </button>
                                        <button class="tab-button" onclick="switchTab('status-history-{{ $item->id }}', this)">
                                            <i class="fas fa-history mr-2"></i>
                                            {{ __('messages.status_history') }}
                                        </button>
                                    </nav>
                                </div>
                                
                                <!-- Tab Contents -->
                                <div id="specifications-{{ $item->id }}" class="tab-content">
                                    @if($item->product && $item->product->specifications()->count() > 0)
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @foreach($item->product->specifications as $spec)
                                            <div class="flex justify-between py-2 border-b border-gray-200 dark:border-gray-600">
                                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ $spec->key }}:</span>
                                                <span class="text-gray-600 dark:text-gray-400">{{ $spec->value }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-gray-500 dark:text-gray-400">{{ __('messages.no_specifications_available') }}</p>
                                    @endif
                                    
                                    <!-- Additional Product Info -->
                                    <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                        <div>
                                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ __('messages.category') }}:</span>
                                            <p class="text-gray-600 dark:text-gray-400">{{ $item->product->category->name ?? 'N/A' }}</p>
                                        </div>
                                        <!-- <div>
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Stock:</span>
                                            <p class="text-gray-600 dark:text-gray-400">{{ $item->product->stock ?? 'N/A' }}</p>
                                        </div> -->
                                        <div>
                                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ __('messages.rating') }}:</span>
                                            <p class="text-gray-600 dark:text-gray-400">
                                                @if($item->product->rating)
                                                    {{ number_format($item->product->rating, 1) }}/5
                                                    <span class="text-yellow-400">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= $item->product->rating)
                                                                <i class="fas fa-star"></i>
                                                            @else
                                                                <i class="far fa-star"></i>
                                                            @endif
                                                        @endfor
                                                    </span>
                                                @else
                                                    {{ __('messages.no_rating') }}
                                                @endif
                                            </p>
                                        </div>
                                        <!-- <div>
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Available:</span>
                                            <p class="text-gray-600 dark:text-gray-400">
                                                @if($item->product->is_available)
                                                    <span class="text-green-600"><i class="fas fa-check"></i> Yes</span>
                                                @else
                                                    <span class="text-red-600"><i class="fas fa-times"></i> No</span>
                                                @endif
                                            </p>
                                        </div> -->
                                    </div>
                                    
                                    @if($item->product && $item->product->description)
                                    <div class="mt-4">
                                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ __('messages.description') }}:</span>
                                        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $item->product->description }}</p>
                                    </div>
                                    @endif
                                </div>
                                
                                <div id="vendor-notes-{{ $item->id }}" class="tab-content">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('messages.add_vendor_note') }}</label>
                                            <textarea class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" rows="3" placeholder="{{ __('messages.add_notes_placeholder') }}"></textarea>
                                            <button class="mt-2 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                                                <i class="fas fa-save mr-2"></i>
                                                {{ __('messages.save_note') }}
                                            </button>
                                        </div>
                                        
                                        <!-- Existing Notes -->
                                        <div>
                                            <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('messages.previous_notes') }}</h4>
                                            <div class="space-y-2">
                                                <!-- Sample note - replace with actual notes from database -->
                                                <div class="p-3 bg-gray-100 dark:bg-gray-600 rounded-md">
                                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ __('messages.item_processed_ready_shipping') }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('messages.added_on') }} {{ now()->format('M d, Y h:i A') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="status-history-{{ $item->id }}" class="tab-content">
                                    <div class="space-y-3">
                                        <!-- Sample status history - replace with actual history from database -->
                                        <div class="flex items-center space-x-3 p-3 bg-gray-100 dark:bg-gray-600 rounded-md">
                                            <div class="flex-shrink-0">
                                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-check text-white text-xs"></i>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('messages.status_updated_to') }}: {{ ucfirst($item->status) }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->updated_at ? $item->updated_at->format('M d, Y h:i A') : 'N/A' }}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center space-x-3 p-3 bg-gray-100 dark:bg-gray-600 rounded-md">
                                            <div class="flex-shrink-0">
                                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-plus text-white text-xs"></i>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('messages.item_added_to_order') }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->created_at ? $item->created_at->format('M d, Y h:i A') : 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ __('messages.no_items_found') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-right text-sm font-medium text-gray-500 dark:text-gray-300">
                            @if(isset($allItemsBelongToVendor) && !$allItemsBelongToVendor)
                                {{ __('messages.your_subtotal') }}:
                            @else
                                {{ __('messages.total') }}:
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            @if(isset($allItemsBelongToVendor) && !$allItemsBelongToVendor)
                                ${{ number_format($subtotal, 2) }}
                            @else
                                ${{ number_format($order->total, 2) }}
                            @endif
                        </td>
                        <td colspan="2"></td>
                    </tr>
                    @if(isset($allItemsBelongToVendor) && !$allItemsBelongToVendor)
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-right text-sm font-medium text-gray-500 dark:text-gray-300">{{ __('messages.order_total_all_vendors') }}:</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">${{ number_format($order->total, 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                    @endif
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Vendor Status History -->
    @if(isset($vendorStatus) && isset($vendorStatusHistory))
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('messages.your_status_history') }}</h3>
        </div>
        <div class="p-6">
            @if($vendorStatusHistory->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.date') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.status') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.previous_status') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.notes') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('messages.updated_by') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($vendorStatusHistory as $history)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $history->created_at->format('M d, Y h:i A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($history->status == 'delivered') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($history->status == 'shipped') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                        @elseif($history->status == 'processing') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @elseif($history->status == 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                        {{ ucfirst($history->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    @if($history->previous_status)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($history->previous_status == 'delivered') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($history->previous_status == 'shipped') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                        @elseif($history->previous_status == 'processing') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @elseif($history->previous_status == 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                        {{ ucfirst($history->previous_status) }}
                                    </span>
                                    @else
                                    <span class="text-gray-500 dark:text-gray-400">{{ __('messages.none') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                    {{ $history->notes ?? __('messages.no_notes') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                    {{ $history->updatedByUser->name ?? __('messages.system') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-gray-700 dark:text-gray-300">{{ __('messages.no_status_history_available') }}</p>
            @endif
        </div>
    </div>
    @endif

    <!-- Notes -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('messages.order_notes') }}</h3>
        </div>
        <div class="p-6">
            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $order->notes ?? __('messages.no_notes_available') }}</p>
        </div>
    </div>
</div>
@endsection
