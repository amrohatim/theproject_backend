@extends('layouts.dashboard')

@section('title', 'Provider Details')
@section('page-title', 'Provider Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Provider Details</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Viewing details for {{ $provider->business_name }}</p>
        </div>
        <div>
            <a href="{{ route('admin.providers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i> Back to List
            </a>
        </div>
    </div>
    
    <nav class="mb-6">
        <ol class="flex text-sm text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('admin.dashboard') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">Dashboard</a></li>
            <li><span class="mx-2">/</span></li>
            <li><a href="{{ route('admin.providers.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">Providers</a></li>
            <li><span class="mx-2">/</span></li>
            <li class="text-gray-700 dark:text-gray-300">{{ $provider->business_name }}</li>
        </ol>
    </nav>
    
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-1">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mb-6">
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                    <h2 class="text-lg font-medium text-gray-800 dark:text-white flex items-center">
                        <i class="fas fa-user-tie mr-2"></i>
                        Provider Information
                    </h2>
                </div>
                <div class="p-6">
                    <div class="text-center mb-6">
                        @if($provider->logo)
                            <img src="{{ $provider->logo }}" alt="{{ $provider->business_name }}" class="h-32 w-32 object-cover rounded-full mx-auto mb-4">
                        @else
                            <div class="h-32 w-32 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-building text-4xl text-indigo-600 dark:text-indigo-300"></i>
                            </div>
                        @endif
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $provider->business_name }}</h3>
                        <div class="mt-2 flex justify-center gap-2">
                            @if($provider->status == 'active')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    Active
                                </span>
                            @elseif($provider->status == 'inactive')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    Inactive
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    Pending
                                </span>
                            @endif
                            
                            @if($provider->is_verified)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    <i class="fas fa-check mr-1"></i> Verified
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                        <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                            <div class="py-3 grid grid-cols-3">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Business Type</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-200 col-span-2">{{ $provider->business_type ?? 'Not specified' }}</dd>
                            </div>
                            
                            <div class="py-3 grid grid-cols-3">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Registration #</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-200 col-span-2">{{ $provider->registration_number ?? 'Not specified' }}</dd>
                            </div>
                            
                            <div class="py-3 grid grid-cols-3">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Website</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-200 col-span-2">
                                    @if($provider->website)
                                        <a href="{{ $provider->website }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline">{{ $provider->website }}</a>
                                    @else
                                        Not specified
                                    @endif
                                </dd>
                            </div>
                            
                            <div class="py-3 grid grid-cols-3">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created On</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-200 col-span-2">{{ $provider->created_at->format('M d, Y') }}</dd>
                            </div>
                            
                            <div class="py-3 grid grid-cols-3">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-200 col-span-2">{{ $provider->updated_at->format('M d, Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                    
                    <div class="mt-6 flex space-x-3">
                        <a href="{{ route('admin.providers.edit', $provider->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-edit mr-2"></i> Edit Provider
                        </a>
                        <button type="button" onclick="toggleDeleteModal()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <i class="fas fa-trash mr-2"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user me-1"></i>
                    User Account Information
                </div>
                <div class="card-body">
                    @if($provider->user)
                        <dl class="row">
                            <dt class="col-sm-4">Name</dt>
                            <dd class="col-sm-8">{{ $provider->user->name }}</dd>

                            <dt class="col-sm-4">Email</dt>
                            <dd class="col-sm-8">{{ $provider->user->email }}</dd>

                            <dt class="col-sm-4">Phone</dt>
                            <dd class="col-sm-8">{{ $provider->user->phone ?? 'Not specified' }}</dd>

                            <dt class="col-sm-4">Account Status</dt>
                            <dd class="col-sm-8">
                                @if($provider->user->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </dd>
                        </dl>
                    @else
                        <p class="text-muted">No user account associated with this provider.</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="xl:col-span-2">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mb-6">
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                    <h2 class="text-lg font-medium text-gray-800 dark:text-white flex items-center">
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        Address Information
                    </h2>
                </div>
                <div class="p-6">
                    @if($provider->address || $provider->city || $provider->state || $provider->postal_code || $provider->country)
                        <address>
                            @if($provider->address)
                                {{ $provider->address }}<br>
                            @endif
                            
                            @if($provider->city || $provider->state || $provider->postal_code)
                                {{ $provider->city }}{{ $provider->city && ($provider->state || $provider->postal_code) ? ',' : '' }}
                                {{ $provider->state }}{{ $provider->state && $provider->postal_code ? ',' : '' }}
                                {{ $provider->postal_code }}<br>
                            @endif
                            
                            @if($provider->country)
                                {{ $provider->country }}
                            @endif
                        </address>
                    @else
                        <p class="text-muted">No address information provided.</p>
                    @endif
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mb-6">
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                    <h2 class="text-lg font-medium text-gray-800 dark:text-white flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        Description
                    </h2>
                </div>
                <div class="p-6">
                    @if($provider->description)
                        <p class="text-gray-700 dark:text-gray-300">{{ $provider->description }}</p>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 italic">No description provided.</p>
                    @endif
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mb-6">
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                    <h2 class="text-lg font-medium text-gray-800 dark:text-white flex items-center">
                        <i class="fas fa-box mr-2"></i>
                        Products
                    </h2>
                </div>
                <div class="p-6">
                    @if(count($provider->products) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($provider->products as $product)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $product->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ number_format($product->price, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($product->is_available)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Available</span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Unavailable</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('admin.products.show', $product->id) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 italic">No products available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Delete Modal -->    
    <div class="fixed inset-0 overflow-y-auto hidden" id="deleteModal" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" id="modalBackground"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-200"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">Confirm Delete</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Are you sure you want to delete the provider <strong>{{ $provider->business_name }}</strong>?</p>
                                <p class="text-sm text-red-500 mt-2">This will also delete the associated user account and all provider data. This action cannot be undone.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <form action="{{ route('admin.providers.destroy', $provider->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Delete
                        </button>
                    </form>
                    <button type="button" onclick="toggleDeleteModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    function toggleDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.toggle('hidden');
    }
    
    // Close modal when clicking outside of it
    document.getElementById('modalBackground')?.addEventListener('click', function() {
        toggleDeleteModal();
    });
</script>
@endsection

@endsection
