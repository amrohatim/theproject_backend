@extends('layouts.merchant')

@section('title', __('messages.deals_management'))
@section('page-title', __('messages.deals_management'))

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
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('messages.your_deals') }}</h2>
            <p class="text-gray-600 dark:text-gray-400">{{ __('messages.create_and_manage_special_offers') }}</p>
        </div>
        <a href="{{ route('merchant.deals.create') }}" class="btn-create-deal flex flex-row items-center justify-center px-3 py-2 rounded-[4px]">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
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
                        {{ $deal->discount_percentage }}% {{ __('messages.off') }}
                    </div>

                    <!-- Status badge -->
                    <div class="deal-status bg-{{ $deal->status == 'active' ? 'green' : 'gray' }}-500 text-white">
                        @if($deal->status == 'active')
                            {{ __('messages.active') }}
                        @else
                            {{ __('messages.inactive') }}
                        @endif
                    </div>
                </div>

                <!-- Deal content -->
                <div class="deal-content">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">
                        {{ app()->getLocale() == 'ar' && $deal->title_arabic ? $deal->title_arabic : $deal->title }}
                    </h3>

                    @if ($deal->description || $deal->description_arabic)
                        <p class="text-gray-600 dark:text-gray-400 mb-3 line-clamp-2 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                            {{ app()->getLocale() == 'ar' && $deal->description_arabic ? $deal->description_arabic : $deal->description }}
                        </p>
                    @endif

                    @if ($deal->promotional_message || $deal->promotional_message_arabic)
                        <div class="mb-3">
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                                <i class="fas fa-bullhorn {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                                {{ app()->getLocale() == 'ar' && $deal->promotional_message_arabic ? $deal->promotional_message_arabic : $deal->promotional_message }}
                            </span>
                        </div>
                    @endif

                    <div class="deal-dates text-gray-600 dark:text-gray-400 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                        <i class="far fa-calendar-alt {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                        {{ \Carbon\Carbon::parse($deal->start_date)->format('M d, Y') }} -
                        {{ \Carbon\Carbon::parse($deal->end_date)->format('M d, Y') }}
                    </div>

                    <div class="deal-applies-to text-gray-600 dark:text-gray-400">
                        <i class="fas fa-tag {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                        @if ($deal->applies_to == 'products')
                            {{ __('messages.selected_products') }}
                        @elseif ($deal->applies_to == 'services')
                            {{ __('messages.selected_services') }}
                        @endif
                    </div>

                    <!-- Action buttons -->
                    <div class="flex justify-end mt-4 gap-2">
                        <a href="{{ route('merchant.deals.edit', $deal) }}" class="flex flex-row items-center gap-1 btn-edit-deal {{ app()->getLocale() == 'ar' ? 'ml-2' : 'mr-2' }}">
                            {{ __('messages.edit') }}
                            <svg class="w-4 h-6 {{ app()->getLocale() == 'ar' ? 'ml-1.5' : 'mr-1.5' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>

                        <form action="{{ route('merchant.deals.destroy', $deal) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('messages.confirm_delete_deal') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="flex flex-row gap-1 items-center btn-delete-deal">
                                {{ __('messages.delete') }}
                                <svg class="w-4 h-4 {{ app()->getLocale() == 'ar' ? 'ml-1.5' : 'mr-1.5' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center">
                <div class="flex flex-col items-center">
                    <i class="fas fa-tags text-gray-400 dark:text-gray-600 text-6xl mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">{{ __('messages.no_deals_yet') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">{{ __('messages.create_first_deal_message') }}</p>
                    <a href="{{ route('merchant.deals.create') }}" class="btn-create-deal flex flex-row items-center  rounded-full">
                        
                        {{ __('messages.create_deal') }}
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
