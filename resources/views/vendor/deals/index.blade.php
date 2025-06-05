@extends('layouts.dashboard')

@section('title', 'Manage Deals')
@section('page-title', 'Manage Deals')

@section('styles')
<style>
    .deal-card {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .deal-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .deal-image {
        height: 160px;
        background-size: cover;
        background-position: center;
        position: relative;
    }
    .deal-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 5px 10px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 0.8rem;
    }
    .deal-status {
        position: absolute;
        top: 10px;
        left: 10px;
        padding: 5px 10px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 0.8rem;
    }
    .deal-content {
        padding: 1.5rem;
    }
    .deal-dates {
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }
    .deal-applies-to {
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto">
    <!-- Header with Add Deal button -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Your Deals</h2>
            <p class="text-gray-600 dark:text-gray-400">Create and manage special offers for your customers</p>
        </div>
        <a href="{{ route('vendor.deals.create') }}" class="btn-primary">
            <i class="fas fa-plus mr-2"></i> Add Deal
        </a>
    </div>

    <!-- Deals grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @forelse ($deals as $deal)
            <div class="deal-card bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <!-- Deal image or placeholder -->
                <div class="deal-image" style="background-image: url('{{ $deal->image }}');">
                    <!-- Discount badge -->
                    <div class="deal-badge bg-{{ $deal->discount_percentage >= 50 ? 'red' : ($deal->discount_percentage >= 25 ? 'orange' : 'green') }}-500 text-white">
                        {{ $deal->discount_percentage }}% OFF
                    </div>

                    <!-- Status badge -->
                    <div class="deal-status bg-{{ $deal->status == 'active' ? 'green' : 'gray' }}-500 text-white">
                        {{ ucfirst($deal->status) }}
                    </div>
                </div>

                <!-- Deal content -->
                <div class="deal-content">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">{{ $deal->title }}</h3>

                    @if ($deal->description)
                        <p class="text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">{{ $deal->description }}</p>
                    @endif

                    @if ($deal->promotional_message)
                        <div class="mb-3">
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                {{ $deal->promotional_message }}
                            </span>
                        </div>
                    @endif

                    <div class="deal-dates text-gray-600 dark:text-gray-400">
                        <i class="far fa-calendar-alt mr-2"></i>
                        {{ \Carbon\Carbon::parse($deal->start_date)->format('M d, Y') }} -
                        {{ \Carbon\Carbon::parse($deal->end_date)->format('M d, Y') }}
                    </div>

                    <div class="deal-applies-to text-gray-600 dark:text-gray-400">
                        <i class="fas fa-tag mr-2"></i>
                        @if ($deal->applies_to == 'all')
                            All Products
                        @elseif ($deal->applies_to == 'products')
                            Selected Products
                        @elseif ($deal->applies_to == 'categories')
                            Selected Categories
                        @endif
                    </div>

                    <!-- Action buttons -->
                    <div class="flex justify-end mt-4">
                        <a href="{{ route('vendor.deals.edit', $deal->id) }}" class="btn-outline-primary mr-2">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>

                        <form action="{{ route('vendor.deals.destroy', $deal->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-outline-danger" onclick="return confirm('Are you sure you want to delete this deal?')">
                                <i class="fas fa-trash-alt mr-1"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center">
                <div class="flex flex-col items-center">
                    <i class="fas fa-tags text-gray-400 dark:text-gray-600 text-6xl mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">No deals yet</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">Create your first deal to attract more customers</p>
                    <a href="{{ route('vendor.deals.create') }}" class="btn-primary">
                        <i class="fas fa-plus mr-2"></i> Create Deal
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $deals->links() }}
    </div>
</div>
@endsection
