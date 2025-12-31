@if($services->count())
    <div class="block md:hidden space-y-4 px-4 py-4">
        @foreach($services as $service)
            <x-mobile-product-card
                :product="$service"
                :edit-url="route('service-provider.services.edit', $service)"
                :delete-url="route('service-provider.services.destroy', $service)"
                :delete-confirm="__('service_provider.confirm_delete_service')"
                edit-button-class="bg-[#53d2dc] hover:bg-[#46c1cb]"
            />
        @endforeach
    </div>

    <div class="hidden md:block overflow-x-auto">
        <table class="sp-responsive-table min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('service_provider.service') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('service_provider.branch') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('service_provider.category') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('service_provider.price') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('service_provider.duration') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('service_provider.status') }}</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($services as $service)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-4 py-3" data-label="{{ __('service_provider.service') }}">
                            <div class="flex items-center">
                                @if($service->image)
                                    <img src="{{ $service->image }}" alt="{{ $service->name }}" class="h-10 w-10 rounded-lg object-cover mr-3">
                                @else
                                    <div class="h-10 w-10 rounded-lg bg-[#53D2DC]/15 flex items-center justify-center mr-3">
                                        <i class="fas fa-cog text-[#53D2DC] text-sm"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $service->name }}</div>
                                    @if($service->service_name_arabic)
                                        <div class="text-xs text-gray-500 dark:text-gray-400" dir="rtl">{{ $service->service_name_arabic }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300" data-label="{{ __('service_provider.branch') }}">
                            {{ $service->branch->name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300" data-label="{{ __('service_provider.category') }}">
                            {{ $service->category->name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white" data-label="{{ __('service_provider.price') }}">
                            ${{ number_format($service->price, 2) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300" data-label="{{ __('service_provider.duration') }}">
                            {{ $service->duration }} min
                        </td>
                        <td class="px-4 py-3" data-label="{{ __('service_provider.status') }}">
                            @if($service->is_available)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    {{ __('service_provider.available') }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    {{ __('service_provider.unavailable') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right" data-label="{{ __('service_provider.actions') }}">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('service-provider.services.show', $service) }}"
                                   class="inline-flex items-center px-2 py-1 text-xs font-medium text-[#53D2DC] hover:text-[#53D2DC]/80 hover:bg-[#53D2DC]/10 rounded transition-colors">
                                    <i class="fas fa-eye mr-1"></i>
                                    {{ __('service_provider.view') }}
                                </a>
                                <a href="{{ route('service-provider.services.edit', $service) }}"
                                   class="inline-flex items-center px-2 py-1 text-xs font-medium text-[#53D2DC] hover:text-[#53D2DC]/80 hover:bg-[#53D2DC]/10 rounded transition-colors">
                                    <i class="fas fa-edit mr-1"></i>
                                    {{ __('service_provider.edit') }}
                                </a>
                                <button onclick="deleteService({{ $service->id }}, '{{ addslashes($service->name) }}')"
                                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors">
                                    <i class="fas fa-trash mr-1"></i>
                                    {{ __('service_provider.delete') }}
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="p-8 text-center">
        <div class="mx-auto h-14 w-14 rounded-full bg-[#53D2DC]/15 flex items-center justify-center">
            <i class="fas fa-search text-[#53D2DC]"></i>
        </div>
        <h4 class="mt-3 text-gray-900 dark:text-white font-medium">{{ __('service_provider.no_services_found') }}</h4>
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('service_provider.try_adjusting_filters') }}</p>
        <button onclick="clearAllFilters()" class="mt-3 inline-flex items-center px-3 py-2 text-sm font-medium text-[#53D2DC] hover:text-[#53D2DC]/80">
            <i class="fas fa-times mr-2"></i>
            {{ __('service_provider.clear_all_filters') }}
        </button>
    </div>
@endif
