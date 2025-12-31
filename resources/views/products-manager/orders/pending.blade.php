@extends('layouts.products-manager')

@section('title', __('products_manager.pending_orders'))
@section('page-title', __('products_manager.pending_orders'))

@section('content')
<div class="container mx-auto px-0 sm:px-0 md:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('products_manager.pending_orders') }}</h3>
        </div>
        <div class="p-6">
            <div class="text-center">
                <p class="text-gray-500 dark:text-gray-400">{{ __('products_manager.pending_orders_placeholder') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
