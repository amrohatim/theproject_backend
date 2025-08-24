@extends('layouts.service-provider')

@section('title', __('service_provider.deals'))
@section('page-title', __('service_provider.deals'))

@section('content')
<div class="container mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('service_provider.active_deals') }}</h3>
            <a href="{{ route('service-provider.deals.create') }}" class="inline-flex items-center px-3 py-2 bg-[#53D2DC] text-white rounded-md hover:bg-[#53D2DC]/90 focus:ring-2 focus:ring-[#53D2DC] focus:ring-offset-2 transition-colors text-sm">
                <i class="fas fa-plus mr-2"></i>{{ __('service_provider.create_deal') }}
            </a>
        </div>
        <div class="p-6">
            @if(($activeDeals ?? collect())->count())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($activeDeals as $deal)
                        <div class="p-4 bg-gradient-to-r from-[#53D2DC]/5 to-[#53D2DC]/3 rounded-lg border border-[#53D2DC]/30 hover:shadow-md transition-shadow">
                            <!-- Deal Header -->
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $deal->title }}</h4>
                                    @if($deal->title_arabic)
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1" dir="rtl">{{ $deal->title_arabic }}</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-bold text-[#53D2DC]">{{ $deal->discount_percentage }}% OFF</span>
                                </div>
                            </div>

                            <!-- Deal Image -->
                            @if($deal->image)
                                <div class="mb-3">
                                    <img src="{{ $deal->image }}" alt="{{ $deal->title }}" class="w-full    h-48 object-cover rounded-md">
                                </div>
                            @endif

                            <!-- Deal Description -->
                            @if($deal->description)
                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-2 line-clamp-2">{{ $deal->description }}</p>
                            @endif

                            <!-- Promotional Message -->
                            @if($deal->promotional_message)
                                <div class="mb-3 p-2 bg-[#53D2DC]/10 rounded text-center">
                                    <p class="text-xs font-medium text-[#53D2DC]">{{ $deal->promotional_message }}</p>
                                    @if($deal->promotional_message_arabic)
                                        <p class="text-xs font-medium text-[#53D2DC] mt-1" dir="rtl">{{ $deal->promotional_message_arabic }}</p>
                                    @endif
                                </div>
                            @endif

                            <!-- Deal Services -->
                            <div class="mb-3">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('service_provider.applied_to') }}:</p>
                                <div class="flex flex-wrap gap-1">
                                    @php
                                        $serviceIds = $deal->service_ids ?? [];
                                        $dealServices = \App\Models\Service::whereIn('id', $serviceIds)->limit(3)->get();
                                    @endphp
                                    @foreach($dealServices as $service)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                            {{ $service->name }}
                                        </span>
                                    @endforeach
                                    @if(count($serviceIds) > 3)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                            {{ __('service_provider.more_count', ['count' => count($serviceIds) - 3]) }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Deal Status and Dates -->
                            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-3">
                                <div class="flex items-center">
                                    @if($deal->status === 'active')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            {{ __('service_provider.active') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                            <i class="fas fa-pause-circle mr-1"></i>
                                            {{ __('service_provider.inactive') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p>{{ optional($deal->start_date)->format('M d') }} - {{ optional($deal->end_date)->format('M d, Y') }}</p>
                                </div>
                            </div>

                            <!-- Deal Actions -->
                            <div class="flex items-center justify-end space-x-2 pt-2 border-t border-[#53D2DC]/20">
                                <a href="{{ route('service-provider.deals.edit', $deal) }}"
                                   class="inline-flex items-center px-2 py-1 text-xs font-medium text-[#53D2DC] hover:text-[#53D2DC]/80 hover:bg-[#53D2DC]/10 rounded transition-colors">
                                    <i class="fas fa-edit mr-1"></i>
                                    {{ __('service_provider.edit') }}
                                </a>
                                <form action="{{ route('service-provider.deals.destroy', $deal) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('service_provider.confirm_delete_deal') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-2 py-1 text-xs font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors">
                                        <i class="fas fa-trash mr-1"></i>
                                        {{ __('service_provider.delete') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 text-center">
                    <div class="mx-auto h-14 w-14 rounded-full bg-[#53D2DC]/15 flex items-center justify-center">
                        <i class="fas fa-percent text-[#53D2DC]"></i>
                    </div>
                    <h4 class="mt-3 text-gray-900 dark:text-white font-medium">{{ __('service_provider.no_active_deals') }}</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('service_provider.create_deals_promote') }}</p>
                    <div class="mt-4">
                        <a href="{{ route('service-provider.deals.create') }}" class="inline-flex items-center px-4 py-2 bg-[#53D2DC] text-white rounded-md hover:bg-[#53D2DC]/90 focus:ring-2 focus:ring-[#53D2DC] focus:ring-offset-2 transition-colors text-sm">
                            <i class="fas fa-plus mr-2"></i>{{ __('service_provider.create_first_deal') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
