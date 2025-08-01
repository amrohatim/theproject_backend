@extends('layouts.merchant')

@section('title', 'Deal Details')
@section('page-title', 'Deal Details')

@section('styles')
<style>
    .deal-header {
        position: relative;
        height: 250px;
        background-size: cover;
        background-position: center;
        border-radius: 0.5rem 0.5rem 0 0;
    }
    .deal-header-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to bottom, rgba(0,0,0,0.1), rgba(0,0,0,0.7));
        border-radius: 0.5rem 0.5rem 0 0;
    }
    .deal-badge {
        position: absolute;
        top: 20px;
        right: 20px;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 1rem;
    }
    .deal-status {
        position: absolute;
        top: 20px;
        left: 20px;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 1rem;
    }
    .deal-title {
        position: absolute;
        bottom: 20px;
        left: 20px;
        right: 20px;
        color: white;
    }
    .detail-section {
        margin-bottom: 2rem;
    }
    .detail-section-title {
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .detail-item {
        display: flex;
        margin-bottom: 0.75rem;
    }
    .detail-label {
        width: 150px;
        font-weight: 600;
    }
    .detail-value {
        flex: 1;
    }
    .selection-list {
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
        padding: 0.5rem;
    }
    .selection-item {
        padding: 0.5rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .selection-item:last-child {
        border-bottom: none;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto">
    <!-- Action buttons -->
    <div class="flex justify-end mb-6 space-x-4">
        <a href="{{ route('merchant.deals.edit', $deal) }}" class="btn-edit-deal">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit Deal
        </a>
        <form action="{{ route('merchant.deals.destroy', $deal) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this deal?')"
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-delete-deal">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Delete Deal
            </button>
        </form>
    </div>

    <!-- Deal card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <!-- Deal header with image -->
        <div class="deal-header" style="background-image: url('{{ $deal->image }}');">
            <div class="deal-header-overlay"></div>

            <!-- Discount badge -->
            <div class="deal-badge bg-{{ $deal->discount_percentage >= 50 ? 'red' : ($deal->discount_percentage >= 25 ? 'orange' : 'green') }}-500 text-white">
                {{ $deal->discount_percentage }}% OFF
            </div>

            <!-- Status badge -->
            <div class="deal-status bg-{{ $deal->status == 'active' ? 'green' : 'gray' }}-500 text-white">
                {{ ucfirst($deal->status) }}
            </div>

            <!-- Deal title -->
            <div class="deal-title">
                <h1 class="text-3xl font-bold">{{ $deal->title }}</h1>
                @if($deal->description)
                    <p class="mt-2 text-gray-200">{{ $deal->description }}</p>
                @endif
            </div>
        </div>

        <!-- Deal details -->
        <div class="p-6">
            <!-- Basic Information -->
            <div class="detail-section">
                <h3 class="detail-section-title text-xl font-bold text-gray-800 dark:text-white">Deal Information</h3>

                <div class="detail-item">
                    <div class="detail-label text-gray-600 dark:text-gray-400">Discount</div>
                    <div class="detail-value text-gray-800 dark:text-white">{{ $deal->discount_percentage }}%</div>
                </div>

                @if($deal->promotional_message)
                <div class="detail-item">
                    <div class="detail-label text-gray-600 dark:text-gray-400">Promotional Message</div>
                    <div class="detail-value">
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                            {{ $deal->promotional_message }}
                        </span>
                        <p class="text-xs text-gray-500 mt-1">This message will appear as a button in the mobile app.</p>
                    </div>
                </div>
                @endif

                <div class="detail-item">
                    <div class="detail-label text-gray-600 dark:text-gray-400">Date Range</div>
                    <div class="detail-value text-gray-800 dark:text-white">
                        {{ \Carbon\Carbon::parse($deal->start_date)->format('M d, Y') }} -
                        {{ \Carbon\Carbon::parse($deal->end_date)->format('M d, Y') }}
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-label text-gray-600 dark:text-gray-400">Status</div>
                    <div class="detail-value">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $deal->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($deal->status) }}
                        </span>
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-label text-gray-600 dark:text-gray-400">Created</div>
                    <div class="detail-value text-gray-800 dark:text-white">
                        {{ \Carbon\Carbon::parse($deal->created_at)->format('M d, Y h:i A') }}
                    </div>
                </div>

                <div class="detail-item">
                    <div class="detail-label text-gray-600 dark:text-gray-400">Last Updated</div>
                    <div class="detail-value text-gray-800 dark:text-white">
                        {{ \Carbon\Carbon::parse($deal->updated_at)->format('M d, Y h:i A') }}
                    </div>
                </div>
            </div>

            <!-- Application Details -->
            <div class="detail-section">
                <h3 class="detail-section-title text-xl font-bold text-gray-800 dark:text-white">Application Details</h3>

                <div class="detail-item">
                    <div class="detail-label text-gray-600 dark:text-gray-400">Applies To</div>
                    <div class="detail-value text-gray-800 dark:text-white">
                        @if($deal->applies_to == 'all')
                            All Products
                        @elseif($deal->applies_to == 'products')
                            Selected Products
                        @elseif($deal->applies_to == 'categories')
                            Selected Categories
                        @endif
                    </div>
                </div>

                @if($deal->applies_to == 'products' && !empty($deal->product_ids))
                    <div class="mt-4">
                        <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Selected Products</h4>
                        <div class="selection-list">
                            @php
                                $productIds = is_string($deal->product_ids) ? json_decode($deal->product_ids, true) : $deal->product_ids;
                                $selectedProducts = \App\Models\Product::whereIn('id', $productIds)->get();
                            @endphp

                            @forelse($selectedProducts as $product)
                                <div class="selection-item">
                                    <div class="font-medium">{{ $product->name }}</div>
                                    <div class="text-sm text-gray-500">
                                        ${{ number_format($product->price, 2) }} - {{ $product->branch->name }}
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-center text-gray-500">No products selected</div>
                            @endforelse
                        </div>
                    </div>
                @endif


            </div>
        </div>
    </div>

    <!-- Back button -->
    <div class="mt-6">
        <a href="{{ route('merchant.deals.index') }}" class="btn-modern-secondary">
            <i class="fas fa-arrow-left mr-2"></i> Back to Deals
        </a>
    </div>
</div>
@endsection
