@extends('layouts.dashboard')

@section('title', 'Subscription Management')
@section('page-title', 'Subscription Management')

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Subscription Management</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">Manage subscription plans for vendors, merchants, and service providers</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('admin.subscription-types.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i> Add Subscription Plan
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Success!</strong>
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Error!</strong>
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <!-- Search and filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 p-4 border border-gray-200 dark:border-gray-700">
        <form action="{{ route('admin.subscription-types.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="Search plans...">
                    </div>
                </div>
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">User Type</label>
                    <select name="type" id="type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">All Types</option>
                        <option value="vendor" {{ request('type') == 'vendor' ? 'selected' : '' }}>Vendor</option>
                        <option value="merchant" {{ request('type') == 'merchant' ? 'selected' : '' }}>Merchant</option>
                        <option value="provider" {{ request('type') == 'provider' ? 'selected' : '' }}>Provider</option>
                    </select>
                </div>
                <div>
                    <label for="period" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Period</label>
                    <select name="period" id="period" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">All Periods</option>
                        <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="yearly" {{ request('period') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-filter mr-2"></i> Filter
                    </button>
                    @if(request()->hasAny(['search', 'type', 'period']))
                    <a href="{{ route('admin.subscription-types.index') }}" class="ml-2 inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-times mr-2"></i> Clear
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Subscription Types list -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">User Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Period</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Charge</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($subscriptionTypes as $subscriptionType)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-md 
                                        @if($subscriptionType->type === 'vendor') bg-blue-100 dark:bg-blue-900
                                        @elseif($subscriptionType->type === 'merchant') bg-amber-100 dark:bg-amber-900
                                        @else bg-green-100 dark:bg-green-900
                                        @endif
                                        flex items-center justify-center">
                                        <i class="fas 
                                            @if($subscriptionType->type === 'vendor') fa-building text-blue-600 dark:text-blue-400
                                            @elseif($subscriptionType->type === 'merchant') fa-store text-amber-600 dark:text-amber-400
                                            @else fa-user-tie text-green-600 dark:text-green-400
                                            @endif
                                        "></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $subscriptionType->type_label }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($subscriptionType->period === 'monthly') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                @else bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200
                                @endif
                            ">
                                {{ $subscriptionType->period_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $subscriptionType->formatted_charge }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 dark:text-white">
                                {{ $subscriptionType->title ?? '-' }}
                            </div>
                            @if($subscriptionType->description)
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ Str::limit($subscriptionType->description, 50) }}
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.subscription-types.edit', $subscriptionType) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 mr-3" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.subscription-types.destroy', $subscriptionType) }}" method="POST" class="inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 delete-btn" data-name="{{ $subscriptionType->type_label }} - {{ $subscriptionType->period_label }}" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center py-4">
                                <i class="fas fa-credit-card text-gray-300 dark:text-gray-600 text-5xl mb-4"></i>
                                <p>No subscription plans found</p>
                                <div class="mt-2">
                                    <a href="{{ route('admin.subscription-types.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        <i class="fas fa-plus mr-2"></i> Add Subscription Plan
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $subscriptionTypes->links() }}
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Confirm Deletion</h3>
                <button onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Are you sure you want to delete the subscription plan for <strong id="deleteName"></strong>?
                </p>
                <p class="text-sm text-red-600 dark:text-red-400 mt-2">
                    This action cannot be undone.
                </p>
            </div>
            <div class="flex justify-end space-x-3 px-7 py-3">
                <button onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancel
                </button>
                <button onclick="confirmDelete()" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let deleteForm = null;

    // Add event listeners to all delete buttons
    document.addEventListener('DOMContentLoaded', function() {
        const deleteForms = document.querySelectorAll('.delete-form');

        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                deleteForm = form;

                const btn = form.querySelector('.delete-btn');
                const name = btn.getAttribute('data-name');

                document.getElementById('deleteName').textContent = name;
                document.getElementById('deleteModal').classList.remove('hidden');
            });
        });
    });

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        deleteForm = null;
    }

    function confirmDelete() {
        if (deleteForm) {
            deleteForm.submit();
        }
    }

    // Close modal when clicking outside
    document.getElementById('deleteModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteModal();
        }
    });
</script>
@endsection

