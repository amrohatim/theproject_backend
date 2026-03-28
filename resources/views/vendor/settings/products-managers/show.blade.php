@extends('layouts.dashboard')

@section('title', __('vendor.products_manager_details'))
@section('page-title', __('vendor.products_manager_details'))

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $productsManager->user->name }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __('vendor.products_manager_details') }}</p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
            <a href="{{ route('vendor.settings.products-managers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i> {{ __('vendor.back_to_products_managers') }}
            </a>
            <a href="{{ route('vendor.settings.products-managers.edit', $productsManager) }}" class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 active:bg-orange-900 focus:outline-none focus:border-orange-900 focus:ring ring-orange-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-edit mr-2"></i> {{ __('messages.edit') }}
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Basic Information -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('vendor.basic_information') }}</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('vendor.full_name') }}</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $productsManager->user->name }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('vendor.email_address') }}</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $productsManager->user->email }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('vendor.phone_number') }}</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $productsManager->user->phone ?: __('vendor.not_provided') }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('vendor.status') }}</label>
                        <div class="mt-1">
                            @if($productsManager->user->status === 'active')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    {{ __('vendor.active') }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    {{ __('vendor.inactive') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('vendor.created_date') }}</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $productsManager->created_at->translatedFormat('M d, Y H:i') }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('vendor.last_updated') }}</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $productsManager->updated_at->translatedFormat('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Access & Permissions -->
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('vendor.access_and_permissions') }}</h3>
                
                <div class="border  rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 rounded-full flex items-center justify-center">
                                <i class="fas fa-box  text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium">{{ __('vendor.full_products_access') }}</h4>
                            <div class="mt-2 text-sm">
                                <p>{{ __('vendor.this_products_manager_has_access') }}</p>
                                <ul class="mt-2 list-disc list-inside space-y-1">
                                    <li>{{ __('vendor.all_company_products_access') }}</li>
                                    <li>{{ __('vendor.add_products_any_branch') }}</li>
                                    <li>{{ __('vendor.update_product_information') }}</li>
                                    <li>{{ __('vendor.manage_product_categories') }}</li>
                                    <li>{{ __('vendor.update_order_statuses') }}</li>
                                    <li>{{ __('vendor.view_product_analytics') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Company Information -->
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('messages.company_information') }}</h3>
                
                <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full bg-[var(--primary-light)] dark:bg-[var(--primary-light)] flex items-center justify-center">
                            <i class="fas fa-building text-[var(--primary)] dark:text-[var(--primary)]"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $productsManager->company->name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $productsManager->company->business_type ?: __('vendor.company') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics & Actions -->
        <div class="lg:col-span-1">
            <!-- Statistics -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('vendor.statistics') }}</h3>
                
                <div class="space-y-4">
                    @foreach($statistics as $key => $value)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __("vendor.$key") !== "vendor.$key" ? __("vendor.$key") : ucwords(str_replace('_', ' ', $key)) }}
                            </span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $value }}</span>
                        </div>
                    @endforeach
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('vendor.account_status') }}</span>
                        <span class="text-sm font-medium {{ $productsManager->user->status === 'active' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ __("vendor.{$productsManager->user->status}") !== "vendor.{$productsManager->user->status}" ? __("vendor.{$productsManager->user->status}") : ucfirst($productsManager->user->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('vendor.quick_actions') }}</h3>
                
                <div class="space-y-3">
                    <form method="POST" action="{{ route('vendor.settings.products-managers.toggle-status', $productsManager) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:border-yellow-900 focus:ring ring-yellow-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <i class="fas fa-{{ $productsManager->user->status === 'active' ? 'pause' : 'play' }} mr-2"></i>
                            {{ $productsManager->user->status === 'active' ? __('vendor.suspend') : __('messages.activate') }}
                        </button>
                    </form>
                    
                    <button type="button" onclick="confirmDelete('{{ $productsManager->id }}', '{{ $productsManager->user->name }}')" class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-trash mr-2"></i> {{ __('vendor.delete') }}
                    </button>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('vendor.recent_activity') }}</h3>
                
                <div class="text-center py-4">
                    <div class="mx-auto h-8 w-8 text-gray-400">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('vendor.activity_tracking_coming_soon') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900">
                <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mt-2">{{ __('vendor.delete_products_manager') }}</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('vendor.delete_products_manager_confirmation_prefix') }}
                    <span id="deleteProductsManagerName" class="font-medium"></span>{{ __('vendor.delete_products_manager_confirmation_suffix') }}
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
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
function confirmDelete(productsManagerId, productsManagerName) {
    document.getElementById('deleteProductsManagerName').textContent = productsManagerName;
    document.getElementById('deleteForm').action = `/vendor/settings/products-managers/${productsManagerId}`;
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
