@extends('layouts.dashboard')

@section('title', 'Provider License Management')
@section('page-title', 'Provider License Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Provider License Management</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Review and approve provider license uploads</p>
            </div>
            <div class="flex space-x-2">
                <form method="GET" action="{{ route('admin.provider-licenses.index') }}" class="flex space-x-2">
                    <select name="status" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending Review</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="expired" {{ $status === 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Filter
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        @php
            $pendingCount = \App\Models\License::whereHas('user', function($q) { $q->where('role', 'provider'); })->where('status', 'pending')->count();
            $approvedCount = \App\Models\License::whereHas('user', function($q) { $q->where('role', 'provider'); })->where('status', 'active')->count();
            $rejectedCount = \App\Models\License::whereHas('user', function($q) { $q->where('role', 'provider'); })->where('status', 'rejected')->count();
            $expiredCount = \App\Models\License::whereHas('user', function($q) { $q->where('role', 'provider'); })->where('status', 'expired')->count();
        @endphp
        
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending Review</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $pendingCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Approved</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $approvedCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 dark:bg-red-900 rounded-lg">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Rejected</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $rejectedCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex items-center">
                    <div class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Expired</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $expiredCount }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    @if($status === 'pending' && $licenses->where('status', 'pending')->count() > 0)
    <div class="mb-6">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Bulk Actions</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Select multiple licenses to approve them at once</p>
                    </div>
                    <div class="flex space-x-2">
                        <button id="select-all-btn" type="button" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Select All
                        </button>
                        <button id="bulk-approve-btn" type="button" disabled class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Approve Selected
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Licenses Table -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            @if($licenses->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                @if($status === 'pending')
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <input type="checkbox" id="select-all-checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </th>
                                @endif
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Provider</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Business Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Upload Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Expiry Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($licenses as $license)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                @if($status === 'pending' && $license->status === 'pending')
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" name="license_ids[]" value="{{ $license->id }}" class="license-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </td>
                                @elseif($status === 'pending')
                                <td class="px-6 py-4 whitespace-nowrap"></td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    {{ substr($license->user->name, 0, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $license->user->name }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $license->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ $license->user->provider->business_name ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                            'active' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                            'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                            'expired' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                                        ];
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$license->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($license->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $license->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $license->end_date ? \Carbon\Carbon::parse($license->end_date)->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.provider-licenses.show', $license->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $licenses->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No licenses found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No provider licenses match the current filter criteria.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Bulk Approve Modal -->
<div id="bulk-approve-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3 text-center">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Bulk Approve Licenses</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Are you sure you want to approve <span id="selected-count">0</span> selected license(s)?
                </p>
                <form id="bulk-approve-form" method="POST" action="{{ route('admin.provider-licenses.bulk-approve') }}" class="mt-4">
                    @csrf
                    <div id="selected-licenses"></div>
                    <div class="mb-4">
                        <label for="admin_message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Admin Message (Optional)</label>
                        <textarea name="admin_message" id="admin_message" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Optional message for approved providers..."></textarea>
                    </div>
                    <div class="flex justify-center space-x-3">
                        <button type="button" id="cancel-bulk-approve" class="px-4 py-2 bg-gray-300 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                            Approve Selected
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    const licenseCheckboxes = document.querySelectorAll('.license-checkbox');
    const bulkApproveBtn = document.getElementById('bulk-approve-btn');
    const selectAllBtn = document.getElementById('select-all-btn');
    const bulkApproveModal = document.getElementById('bulk-approve-modal');
    const cancelBulkApprove = document.getElementById('cancel-bulk-approve');

    function updateBulkApproveButton() {
        const checkedBoxes = document.querySelectorAll('.license-checkbox:checked');
        bulkApproveBtn.disabled = checkedBoxes.length === 0;
        
        if (selectAllCheckbox) {
            if (checkedBoxes.length === 0) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = false;
            } else if (checkedBoxes.length === licenseCheckboxes.length) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = true;
            } else {
                selectAllCheckbox.indeterminate = true;
                selectAllCheckbox.checked = false;
            }
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            licenseCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkApproveButton();
        });
    }

    licenseCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkApproveButton);
    });

    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            const allChecked = Array.from(licenseCheckboxes).every(cb => cb.checked);
            licenseCheckboxes.forEach(checkbox => {
                checkbox.checked = !allChecked;
            });
            updateBulkApproveButton();
        });
    }

    if (bulkApproveBtn) {
        bulkApproveBtn.addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('.license-checkbox:checked');
            const selectedCount = document.getElementById('selected-count');
            const selectedLicenses = document.getElementById('selected-licenses');
            
            selectedCount.textContent = checkedBoxes.length;
            selectedLicenses.innerHTML = '';
            
            checkedBoxes.forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'license_ids[]';
                input.value = checkbox.value;
                selectedLicenses.appendChild(input);
            });
            
            bulkApproveModal.classList.remove('hidden');
        });
    }

    if (cancelBulkApprove) {
        cancelBulkApprove.addEventListener('click', function() {
            bulkApproveModal.classList.add('hidden');
        });
    }

    // Close modal when clicking outside
    bulkApproveModal?.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
});
</script>
@endpush
@endsection
