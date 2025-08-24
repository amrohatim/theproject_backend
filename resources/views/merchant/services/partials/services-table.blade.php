{{-- Services Table Partial for AJAX responses --}}
@if($services->count() > 0)
<div class="overflow-x-auto">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('merchant.service') }}</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('merchant.category') }}</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('merchant.price') }}</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('merchant.duration') }}</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('merchant.status') }}</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('merchant.actions') }}</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($services as $service)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12">
                            @if($service->image)
                                <img class="h-12 w-12 rounded-lg object-cover border border-gray-200" src="{{ asset($service->image) }}" alt="{{ $service->name }}">
                            @else
                                <div class="h-12 w-12 rounded-lg bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-concierge-bell text-gray-400 text-lg"></i>
                                </div>
                            @endif
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900 flex items-center">
                                {{ $service->name }}
                                @if($service->home_service)
                                    <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        HOME
                                    </span>
                                @endif
                                @if($service->featured)
                                    <span class="ml-1 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-star mr-1"></i>
                                        FEATURED
                                    </span>
                                @endif
                            </div>
                            @if($service->description)
                                <div class="text-sm text-gray-500">{{ Str::limit($service->description, 50) }}</div>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        {{ $service->category->name ?? 'Uncategorized' }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm font-medium text-gray-900">${{ number_format($service->price, 2) }}</td>
                <td class="px-6 py-4">
                    @if($service->duration)
                        <span class="text-sm text-gray-900">{{ $service->duration }} min</span>
                    @else
                        <span class="text-sm text-gray-500">Not specified</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    @if($service->is_available)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-400 mr-1.5"></span>
                            {{ __('merchant.available') }}
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-400 mr-1.5"></span>
                            {{ __('merchant.unavailable') }}
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('merchant.services.show', $service->id) }}"
                           class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors"
                           title="{{ __('merchant.view') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </a>
                        <a href="{{ route('merchant.services.edit', $service->id) }}"
                           class="p-2 text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg transition-colors"
                           title="{{ __('merchant.edit') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                        <form action="{{ route('merchant.services.destroy', $service->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('{{ __('merchant.confirm_delete_service') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors"
                                    title="{{ __('merchant.delete') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<!-- Empty State -->
<div class="text-center py-16 px-6">
    <svg class="mx-auto h-16 w-16 text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="64" height="64">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
    </svg>
    <h3 class="text-lg font-semibold text-gray-900 mb-3">{{ __('merchant.no_services_found') }}</h3>
    <p class="text-gray-600 mb-6 max-w-md mx-auto">
        {{ __('merchant.try_adjusting_search') }}
    </p>
    <button type="button" id="clearAllFilters" class="discord-btn" onclick="clearAllFilters()">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
        {{ __('merchant.clear_search_filters') }}
    </button>
</div>
@endif



