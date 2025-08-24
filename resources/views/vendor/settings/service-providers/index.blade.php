@extends('layouts.dashboard')

@section('title', __('vendor.service_providers_management'))
@section('page-title', __('vendor.service_providers_management'))

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ __('vendor.service_providers') }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('vendor.manage_service_providers_description') }}</p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3 {{ app()->getLocale() === 'ar' ? 'rtl:space-x-reverse' : '' }}">
            <a href="{{ route('vendor.settings') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left {{ app()->getLocale() === 'ar' ? 'ml-2 rtl:rotate-180' : 'mr-2' }}"></i> {{ __('vendor.back_to_settings') }}
            </a>
            <a href="{{ route('vendor.settings.service-providers.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none disabled:opacity-25 transition ease-in-out duration-150" style="background-color: #53D2DC; border-color: #53D2DC;" onmouseover="this.style.backgroundColor='#42B8C2'" onmouseout="this.style.backgroundColor='#53D2DC'" onfocus="this.style.boxShadow='0 0 0 3px rgba(83, 210, 220, 0.3)'" onblur="this.style.boxShadow='none'">
                <i class="fas fa-plus {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i> {{ __('vendor.add_service_provider') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Service Providers Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('vendor.service_providers_list') }}</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ trans_choice('vendor.total_service_providers', $serviceProviders->total(), ['count' => $serviceProviders->total()]) }}
            </p>
        </div>

        @if($serviceProviders->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }} text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('vendor.name_and_email') }}
                            </th>
                            <th scope="col" class="px-6 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }} text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('vendor.related_branches') }}
                            </th>
                            <th scope="col" class="px-6 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }} text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('vendor.number_of_services') }}
                            </th>
                            <th scope="col" class="px-6 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }} text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('vendor.status') }}
                            </th>
                            <th scope="col" class="px-6 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }} text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('vendor.creation_date') }}
                            </th>
                            <th scope="col" class="px-6 py-3 {{ app()->getLocale() === 'ar' ? 'text-left' : 'text-right' }} text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('vendor.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($serviceProviders as $serviceProvider)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full flex items-center justify-center" style="background-color: rgba(83, 210, 220, 0.1);">
                                                <i class="fas fa-user" style="color: #53D2DC;"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $serviceProvider->user->name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $serviceProvider->user->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        @if($serviceProvider->branch_ids && count($serviceProvider->branch_ids) > 0)
                                            @php
                                                $branches = $serviceProvider->branches();
                                                $branchNames = $branches->pluck('name')->toArray();
                                            @endphp
                                            {{ implode(', ', $branchNames) }}
                                        @else
                                            <span class="text-gray-400">{{ __('vendor.no_branches_assigned') }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: rgba(83, 210, 220, 0.1); color: #2D7A82;">
                                            {{ trans_choice('vendor.services_count', $serviceProvider->number_of_services, ['count' => $serviceProvider->number_of_services]) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($serviceProvider->user->status === 'active')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            {{ __('vendor.active') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            {{ __('vendor.inactive') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $serviceProvider->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('vendor.settings.service-providers.show', $serviceProvider) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('vendor.settings.service-providers.edit', $serviceProvider) }}" style="color: #53D2DC;" onmouseover="this.style.color='#42B8C2'" onmouseout="this.style.color='#53D2DC'">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('vendor.settings.service-providers.toggle-status', $serviceProvider) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300" title="{{ $serviceProvider->user->status === 'active' ? 'Suspend' : 'Activate' }}">
                                                <i class="fas fa-{{ $serviceProvider->user->status === 'active' ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </form>
                                        <button type="button" onclick="confirmDelete('{{ $serviceProvider->id }}', '{{ $serviceProvider->user->name }}')" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($serviceProviders->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $serviceProviders->links() }}
                </div>
            @endif
        @else
            <div class="px-6 py-12 text-center">
                <div class="mx-auto h-12 w-12 text-gray-400">
                    <i class="fas fa-users text-4xl"></i>
                </div>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('vendor.no_service_providers') }}</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('vendor.no_service_providers_description') }}</p>
                <div class="mt-6">
                    <a href="{{ route('vendor.settings.service-providers.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none disabled:opacity-25 transition ease-in-out duration-150" style="background-color: #53D2DC; border-color: #53D2DC;" onmouseover="this.style.backgroundColor='#42B8C2'" onmouseout="this.style.backgroundColor='#53D2DC'" onfocus="this.style.boxShadow='0 0 0 3px rgba(83, 210, 220, 0.3)'" onblur="this.style.boxShadow='none'">
                        <i class="fas fa-plus {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i> {{ __('vendor.add_service_provider') }}
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900">
                <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mt-2">{{ __('vendor.delete_service_provider') }}</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('vendor.delete_service_provider_confirmation_prefix') }} <span id="deleteServiceProviderName" class="font-medium"></span>{{ __('vendor.delete_service_provider_confirmation_suffix') }}
                </p>
            </div>
            <div class="items-center px-4 py-3 {{ app()->getLocale() === 'ar' ? 'rtl:space-x-reverse' : '' }}">
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-24 {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }} hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                        {{ __('vendor.delete') }}
                    </button>
                </form>
                <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-24 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    {{ __('vendor.cancel') }}
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function confirmDelete(serviceProviderId, serviceProviderName) {
    document.getElementById('deleteServiceProviderName').textContent = serviceProviderName;
    document.getElementById('deleteForm').action = `/vendor/settings/service-providers/${serviceProviderId}`;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>
@endsection
