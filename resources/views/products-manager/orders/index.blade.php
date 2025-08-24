@extends('layouts.products-manager')

@section('title', __('products_manager.all_orders'))
@section('page-title', __('products_manager.all_orders'))

@section('content')
<div class="container mx-auto">
    <!-- Filters -->
    <div class="mb-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <div class="md:col-span-2">
                <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">{{ __('products_manager.search') }}</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('products_manager.search_customer_order') }}"
                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-0 focus:border-[#F46C3F]" />
            </div>
            <div>
                <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">{{ __('products_manager.status') }}</label>
                <select name="status" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-0 focus:border-[#F46C3F]">
                    <option value="">{{ __('products_manager.all_statuses') }}</option>
                    <option value="pending" @selected(request('status')=='pending')>{{ __('products_manager.pending') }}</option>
                    <option value="processing" @selected(request('status')=='processing')>{{ __('products_manager.processing') }}</option>
                    <option value="completed" @selected(request('status')=='completed')>{{ __('products_manager.completed') }}</option>
                    <option value="cancelled" @selected(request('status')=='cancelled')>{{ __('products_manager.cancelled') }}</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">{{ __('products_manager.from_date') }}</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-0 focus:border-[#F46C3F]" />
            </div>
            <div>
                <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">{{ __('products_manager.to_date') }}</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-0 focus:border-[#F46C3F]" />
            </div>
            <div class="flex items-end">
                <button class="inline-flex items-center px-4 py-2 bg-[#F46C3F] text-white rounded-md hover:opacity-90 active:opacity-80">
                    <i class="fas fa-filter mr-2"></i> {{ __('products_manager.filter') }}
                </button>
            </div>
        </form>
    </div>

    <!-- List -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('products_manager.all_orders') }}</h3>
        </div>
        <div class="p-6">
            <div class="text-center">
                <p class="text-gray-500 dark:text-gray-400">{{ __('products_manager.orders_placeholder') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
