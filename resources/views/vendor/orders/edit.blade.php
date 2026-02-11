@extends('layouts.dashboard')

@section('title', 'Edit Order')
@section('page-title', 'Edit Order')

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
    
    /* Size Display Enhancement - Prominent Display */
    .size-display {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        border-radius: 0.5rem;
        font-size: 1.125rem;
        font-weight: 700;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 2px solid #e5e7eb;
    }
    
    .size-display i {
        font-size: 1.25rem;
        color: #fbbf24;
    }
    
    /* Individual Status Dropdowns */
    .status-dropdown {
        min-width: 150px;
    }
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
        .product-image {
            width: 200px;
            height: 200px;
        }
        
        .tab-button {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
        
        .product-image-container {
            width: 100%;
            max-width: 200px;
        }
        
        .status-dropdown {
            min-width: 120px;
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
    
    
    // Individual Status Update
    function updateItemStatus(itemId) {
        const statusDropdown = document.getElementById('item_status_' + itemId);
        const notesField = document.getElementById('item_notes_' + itemId);
        const status = statusDropdown.value;
        const notes = notesField.value;
        
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/vendor/order-items/${itemId}/update-status`;
        
        // CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Method
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';
        form.appendChild(methodInput);
        
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
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Order #{{ $order->order_number ?? 'N/A' }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $order->created_at ? $order->created_at->format('F d, Y h:i A') : 'N/A' }}</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('vendor.orders.show', $order->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i> Back to Order
            </a>
        </div>
    </div>


    <!-- Order Status Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700 mb-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Update Order Status</h3>
        <form action="{{ route('vendor.orders.update', $order->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Order Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Order Status <span class="text-red-500">*</span></label>
                    <select id="status" name="status" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                    <textarea id="notes" name="notes" rows="3" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="Add notes about this status update...">{{ $order->notes }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-save mr-2"></i> Update Order
                </button>
            </div>
        </form>
    </div>

    <!-- Order Items -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Order Items</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Details & Size</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantity</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($order->items ?? [] as $item)
                    <tr>
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
                        
                        <!-- Enhanced Product Details with Prominent Size -->
                        <td class="px-6 py-4">
                            <div class="space-y-3">
                                <!-- Prominent Size Display -->
                                @if($item->size_name)
                                <div style="width: 100px;" class="size-display">
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
                                    <i class="fas fa-ruler-combined"></i>
                                    <span>SIZE: {{ strtoupper($sizeDisplay) }}</span>
                                </div>
                                @endif
                                
                                <!-- Color Display -->
                                @if($item->color_name)
                                <div class="inline-flex items-center gap-2 px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-md">
                                    @if($item->color_value)
                                    <span class="inline-block h-4 w-4 rounded-full border border-gray-300" style="background-color: {{ $item->color_value }};"></span>
                                    @endif
                                    <span class="text-sm font-medium">{{ $item->color_name }}</span>
                                </div>
                                @endif
                                
                                <!-- Expandable Details Button -->
                                <button onclick="toggleProductDetails('product-details-{{ $item->id }}')" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 text-sm flex items-center gap-1 mt-2">
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
                        
                        <!-- Status Display -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="status-indicator status-{{ $item->status }}">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($item->status == 'pending') bg-gray-100 text-gray-800
                                    @elseif($item->status == 'processing') bg-yellow-100 text-yellow-800
                                    @elseif($item->status == 'shipped') bg-blue-100 text-blue-800
                                    @elseif($item->status == 'delivered') bg-green-100 text-green-800
                                    @elseif($item->status == 'cancelled') bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </div>
                            <select id="item_status_{{ $item->id }}" class="status-dropdown mt-2 px-2 py-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md text-xs">
                                <option value="pending" {{ $item->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $item->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="shipped" {{ $item->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered" {{ $item->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ $item->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            <input type="text" id="item_notes_{{ $item->id }}" class="mt-1 w-full px-2 py-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md text-xs" placeholder="Notes...">
                        </td>
                        
                        <!-- Actions -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="updateItemStatus({{ $item->id }})" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                <i class="fas fa-save mr-1"></i>
                                Update
                            </button>
                        </td>
                    </tr>
                    
                    <!-- Expandable Product Details Row -->
                    <tr id="product-details-{{ $item->id }}" class="hidden">
                        <td colspan="7" class="px-6 py-4 bg-gray-50 dark:bg-gray-700">
                            <div class="max-w-4xl">
                                <!-- Tabbed Interface -->
                                <div class="border-b border-gray-200 dark:border-gray-600">
                                    <nav class="-mb-px flex space-x-8">
                                        <button class="tab-button" onclick="switchTab('specifications-{{ $item->id }}', this)">
                                            <i class="fas fa-list mr-2"></i>
                                            Specifications
                                        </button>
                                        <button class="tab-button" onclick="switchTab('vendor-notes-{{ $item->id }}', this)">
                                            <i class="fas fa-sticky-note mr-2"></i>
                                            Vendor Notes
                                        </button>
                                        <button class="tab-button" onclick="switchTab('status-history-{{ $item->id }}', this)">
                                            <i class="fas fa-history mr-2"></i>
                                            Status History
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
                                        <p class="text-gray-500 dark:text-gray-400">No specifications available.</p>
                                    @endif
                                    
                                    <!-- Additional Product Info -->
                                    <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                        <div>
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Category:</span>
                                            <p class="text-gray-600 dark:text-gray-400">{{ $item->product->category->name ?? 'N/A' }}</p>
                                        </div>
                                        <!-- <div>
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Stock:</span>
                                            <p class="text-gray-600 dark:text-gray-400">{{ $item->product->stock ?? 'N/A' }}</p>
                                        </div> -->
                                        <div>
                                            <span class="font-medium text-gray-700 dark:text-gray-300">Rating:</span>
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
                                                    No rating
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
                                        <span class="font-medium text-gray-700 dark:text-gray-300">Description:</span>
                                        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $item->product->description }}</p>
                                    </div>
                                    @endif
                                </div>
                                
                                <div id="vendor-notes-{{ $item->id }}" class="tab-content">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Add Vendor Note</label>
                                            <textarea class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" rows="3" placeholder="Add notes about this item..."></textarea>
                                            <button class="mt-2 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                                                <i class="fas fa-save mr-2"></i>
                                                Save Note
                                            </button>
                                        </div>
                                        
                                        <!-- Existing Notes -->
                                        <div>
                                            <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Previous Notes</h4>
                                            <div class="space-y-2">
                                                <!-- Sample note - replace with actual notes from database -->
                                                <div class="p-3 bg-gray-100 dark:bg-gray-600 rounded-md">
                                                    <p class="text-sm text-gray-700 dark:text-gray-300">Item processed and ready for shipping.</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Added on {{ now()->format('M d, Y h:i A') }}</p>
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
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">Status updated to: {{ ucfirst($item->status) }}</p>
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
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">Item added to order</p>
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
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            No items found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right text-sm font-medium text-gray-500 dark:text-gray-300">Total:</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">${{ number_format($order->total, 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>


    <!-- Order Summary -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Order Summary</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Customer Information</h4>
                    <div class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                        <p><strong>Name:</strong> {{ $order->user->name ?? 'N/A' }}</p>
                        <p><strong>Email:</strong> {{ $order->user->email ?? 'N/A' }}</p>
                        <p><strong>Phone:</strong> {{ $order->user->phone ?? 'N/A' }}</p>
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Order Details</h4>
                    <div class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                        <p><strong>Order Date:</strong> {{ $order->created_at ? $order->created_at->format('M d, Y h:i A') : 'N/A' }}</p>
                        <p><strong>Payment Status:</strong> {{ ucfirst($order->payment_status ?? 'pending') }}</p>
                        <p><strong>Payment Method:</strong> {{ ucfirst($order->payment_method ?? 'N/A') }}</p>
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Branch Information</h4>
                    <div class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                        @php
                            $branches = $order->items
                                ->map(fn($item) => $item->product->branch ?? null)
                                ->filter()
                                ->unique('id')
                                ->values();
                            $branchName = $branches->count() === 1
                                ? $branches->first()->name
                                : ($branches->isNotEmpty() ? 'Multiple branches' : 'N/A');
                            $branchAddress = $branches->count() === 1 ? ($branches->first()->address ?? 'N/A') : ($branches->isNotEmpty() ? 'Multiple branches' : 'N/A');
                            $branchPhone = $branches->count() === 1 ? ($branches->first()->phone ?? 'N/A') : ($branches->isNotEmpty() ? 'Multiple branches' : 'N/A');
                        @endphp
                        <p><strong>Branch:</strong> {{ $branchName }}</p>
                        <p><strong>Address:</strong> {{ $branchAddress }}</p>
                        <p><strong>Phone:</strong> {{ $branchPhone }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
