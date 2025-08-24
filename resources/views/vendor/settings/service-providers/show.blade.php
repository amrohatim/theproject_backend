@extends('layouts.dashboard')

@section('title', 'Service Provider Details')
@section('page-title', 'Service Provider Details')

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $serviceProvider->user->name }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">Service Provider Details</p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
            <a href="{{ route('vendor.settings.service-providers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i> Back to Service Providers
            </a>
            <a href="{{ route('vendor.settings.service-providers.edit', $serviceProvider) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Basic Information -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Basic Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Full Name</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $serviceProvider->user->name }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Email Address</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $serviceProvider->user->email }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Phone Number</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $serviceProvider->user->phone ?: 'Not provided' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                        <div class="mt-1">
                            @if($serviceProvider->user->status === 'active')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    Inactive
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Created Date</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $serviceProvider->created_at->format('M d, Y \a\t H:i') }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $serviceProvider->updated_at->format('M d, Y \a\t H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Branch Access -->
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Branch Access</h3>
                
                @if($branches && $branches->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($branches as $branch)
                            <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                        <i class="fas fa-store text-blue-600 dark:text-blue-400 text-sm"></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $branch->name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $branch->address ?: 'No address provided' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <div class="mx-auto h-12 w-12 text-gray-400">
                            <i class="fas fa-store text-4xl"></i>
                        </div>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No branch access</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">This service provider has no branch access assigned.</p>
                    </div>
                @endif
            </div>

            <!-- Service Access -->
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Service Access</h3>
                
                @if($services && $services->count() > 0)
                    <div class="grid grid-cols-1 gap-3">
                        @foreach($services as $service)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                                            <i class="fas fa-cog text-purple-600 dark:text-purple-400 text-sm"></i>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $service->name }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $service->branch->name ?? 'No branch' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">${{ number_format($service->price, 2) }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $service->duration }} min</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <div class="mx-auto h-12 w-12 text-gray-400">
                            <i class="fas fa-cog text-4xl"></i>
                        </div>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No service access</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">This service provider has no service access assigned.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Statistics & Actions -->
        <div class="lg:col-span-1">
            <!-- Statistics -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Statistics</h3>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Total Branches</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $branches ? $branches->count() : 0 }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Total Services</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $serviceProvider->number_of_services }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Account Status</span>
                        <span class="text-sm font-medium {{ $serviceProvider->user->status === 'active' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ ucfirst($serviceProvider->user->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                
                <div class="space-y-3">
                    <form method="POST" action="{{ route('vendor.settings.service-providers.toggle-status', $serviceProvider) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:border-yellow-900 focus:ring ring-yellow-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <i class="fas fa-{{ $serviceProvider->user->status === 'active' ? 'pause' : 'play' }} mr-2"></i>
                            {{ $serviceProvider->user->status === 'active' ? 'Suspend' : 'Activate' }}
                        </button>
                    </form>
                    
                    <button type="button" onclick="confirmDelete('{{ $serviceProvider->id }}', '{{ $serviceProvider->user->name }}')" class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-trash mr-2"></i> Delete
                    </button>
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
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mt-2">Delete Service Provider</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Are you sure you want to delete <span id="deleteServiceProviderName" class="font-medium"></span>? This action cannot be undone.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                        Delete
                    </button>
                </form>
                <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-24 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancel
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
