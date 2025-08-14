@extends('layouts.products-manager')

@section('title', __('products_manager.deals'))
@section('page-title', __('products_manager.deals'))

@section('content')
<div class="container mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('products_manager.active_deals') }}</h3>
            <a href="{{ route('products-manager.deals.create') }}" class="inline-flex items-center px-3 py-2 bg-[#F46C3F] text-white rounded-md hover:opacity-90 active:opacity-80 text-sm">
                <i class="fas fa-plus mr-2"></i>{{ __('products_manager.create_deal') }}
            </a>
        </div>
        <div class="p-6">
            @if($deals->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($deals as $deal)
                        <div class="bg-white dark:bg-gray-700 rounded-lg shadow overflow-hidden hover:shadow-lg transition-shadow duration-200">
                            <!-- Deal image or placeholder -->
                            <div class="h-40 bg-gray-200 dark:bg-gray-600 relative" style="background-image: url('{{ $deal->image }}'); background-size: cover; background-position: center;">
                                <!-- Discount badge -->
                                <div class="absolute top-2 right-2 bg-[#F46C3F] text-white px-2 py-1 rounded-full text-sm font-bold">
                                    {{ $deal->discount_percentage }}% {{ __('products_manager.off') }}
                                </div>
                                <!-- Status badge -->
                                <div class="absolute top-2 left-2 px-2 py-1 rounded-full text-xs font-medium {{ $deal->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ __('products_manager.' . $deal->status) }}
                                </div>
                            </div>

                            <!-- Deal content -->
                            <div class="p-4">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                    {{ app()->getLocale() === 'ar' ? $deal->title_arabic : $deal->title }}
                                </h4>

                                @if($deal->description || $deal->description_arabic)
                                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-3 line-clamp-2">
                                        {{ app()->getLocale() === 'ar' ? $deal->description_arabic : $deal->description }}
                                    </p>
                                @endif

                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                                    <p>{{ __('products_manager.valid_until') }} {{ $deal->end_date->format('Y-m-d') }}</p>
                                    <p class="mt-1">{{ __('products_manager.applies_to') }}: {{ __('products_manager.' . $deal->applies_to) }}</p>
                                </div>

                                <!-- Action buttons -->
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('products-manager.deals.edit', $deal) }}" class="inline-flex items-center px-3 py-1 bg-[#F46C3F] text-white rounded text-sm hover:opacity-90">
                                        <i class="fas fa-edit mr-1"></i>{{ __('products_manager.edit') }}
                                    </a>
                                    <form action="{{ route('products-manager.deals.destroy', $deal) }}" method="POST" class="inline-block" onsubmit="return confirm('{{ __('products_manager.confirm_delete_deal') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">
                                            <i class="fas fa-trash mr-1"></i>{{ __('products_manager.delete') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $deals->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-tags text-gray-400 dark:text-gray-600 text-6xl mb-4"></i>
                    <h4 class="text-xl font-medium text-gray-900 dark:text-white mb-2">{{ __('products_manager.no_active_deals') }}</h4>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">{{ __('products_manager.create_deals_description') }}</p>
                    <a href="{{ route('products-manager.deals.create') }}" class="inline-flex items-center px-4 py-2 bg-[#F46C3F] text-white rounded-md hover:opacity-90">
                        <i class="fas fa-plus mr-2"></i>{{ __('products_manager.create_deal') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
