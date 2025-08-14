@extends('layouts.service-provider')

@section('title', 'Deals')
@section('page-title', 'Deals')

@section('content')
<div class="container mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Active Deals</h3>
            <a href="#" class="inline-flex items-center px-3 py-2 bg-[#53D2DC] text-white rounded-md hover:opacity-90 active:opacity-80 text-sm">
                <i class="fas fa-plus mr-2"></i>Create Deal
            </a>
        </div>
        <div class="p-6">
            @if(($activeDeals ?? collect())->count())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($activeDeals as $deal)
                        <div class="p-4 bg-gradient-to-r from-[#53D2DC]/10 to-[#53D2DC]/20 rounded-lg border border-[#53D2DC]/30">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $deal->title }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $deal->service->name ?? 'Service' }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $deal->discount_percentage }}% OFF</span>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Valid until {{ optional($deal->end_date)->format('Y-m-d') }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 text-center">
                    <div class="mx-auto h-14 w-14 rounded-full bg-[#53D2DC]/15 flex items-center justify-center">
                        <i class="fas fa-percent text-[#53D2DC]"></i>
                    </div>
                    <h4 class="mt-3 text-gray-900 dark:text-white font-medium">No active deals</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Create deals to promote your services.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
