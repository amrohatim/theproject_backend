@extends('layouts.dashboard')

@section('title', 'Merchant License Management')
@section('page-title', 'Merchant License Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Merchant License Management</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Review and approve merchant license uploads</p>
            </div>
            <div class="flex space-x-2">
                <form method="GET" action="{{ route('admin.merchant-licenses.index') }}" class="flex space-x-2">
                    <select name="status" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="checking" {{ $status === 'checking' ? 'selected' : '' }}>Pending Review</option>
                        <option value="verified" {{ $status === 'verified' ? 'selected' : '' }}>Approved</option>
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
            $pendingCount = \App\Models\Merchant::where('license_status', 'checking')->count();
            $approvedCount = \App\Models\Merchant::where('license_status', 'verified')->count();
            $rejectedCount = \App\Models\Merchant::where('license_status', 'rejected')->count();
            $expiredCount = \App\Models\Merchant::where('license_status', 'expired')->count();
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
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $pendingCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Approved</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $approvedCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 dark:bg-red-900 rounded-lg">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Rejected</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $rejectedCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex items-center">
                    <div class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Expired</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $expiredCount }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    @if($status === 'checking' && $merchants->count() > 0)
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
        <div class="p-6">
            <form id="bulk-approve-form" method="POST" action="{{ route('admin.merchant-licenses.bulk-approve') }}">
                @csrf
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <label for="select-all" class="text-sm font-medium text-gray-700 dark:text-gray-300">Select All</label>
                    </div>
                    <button type="submit" id="bulk-approve-btn" disabled class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Bulk Approve Selected
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Licenses Table -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            @if($merchants->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                @if($status === 'checking')
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Select
                                </th>
                                @endif
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Merchant
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Business Name
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Expiry Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Uploaded
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($merchants as $merchant)
                            <tr>
                                @if($status === 'checking')
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" name="merchant_ids[]" value="{{ $merchant->id }}" class="merchant-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    {{ substr($merchant->user->name, 0, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $merchant->user->name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $merchant->user->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ $merchant->business_name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $merchant->business_type }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusInfo = $merchant->getLicenseStatusWithColor();
                                        $badgeClass = match($merchant->license_status) {
                                            'verified' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                            'checking' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                            'expired' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                            'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
                                        };
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $badgeClass }}">
                                        {{ $statusInfo['text'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    @if($merchant->license_expiry_date)
                                        {{ $merchant->license_expiry_date->format('M d, Y') }}
                                        @if($merchant->license_expiry_date->isFuture())
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                ({{ $merchant->daysUntilLicenseExpiration() }} days)
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-gray-400">Not set</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    @if($merchant->license_uploaded_at)
                                        {{ $merchant->license_uploaded_at->format('M d, Y H:i') }}
                                    @else
                                        <span class="text-gray-400">Never</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.merchant-licenses.show', $merchant->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                            View
                                        </a>
                                        @if($merchant->license_file)
                                        <a href="{{ route('admin.merchant-licenses.download', $merchant->id) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                            Download
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $merchants->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No licenses found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        @if($status === 'checking')
                            No licenses are currently pending review.
                        @else
                            No licenses found with the selected status.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const merchantCheckboxes = document.querySelectorAll('.merchant-checkbox');
    const bulkApproveBtn = document.getElementById('bulk-approve-btn');
    const bulkApproveForm = document.getElementById('bulk-approve-form');

    if (selectAllCheckbox && merchantCheckboxes.length > 0) {
        // Handle select all
        selectAllCheckbox.addEventListener('change', function() {
            merchantCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkApproveButton();
        });

        // Handle individual checkboxes
        merchantCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectAllState();
                updateBulkApproveButton();
            });
        });

        function updateSelectAllState() {
            const checkedCount = document.querySelectorAll('.merchant-checkbox:checked').length;
            selectAllCheckbox.checked = checkedCount === merchantCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < merchantCheckboxes.length;
        }

        function updateBulkApproveButton() {
            const checkedCount = document.querySelectorAll('.merchant-checkbox:checked').length;
            if (bulkApproveBtn) {
                bulkApproveBtn.disabled = checkedCount === 0;
            }
        }

        // Handle bulk approve form submission
        if (bulkApproveForm) {
            bulkApproveForm.addEventListener('submit', function(e) {
                const checkedCount = document.querySelectorAll('.merchant-checkbox:checked').length;
                if (checkedCount === 0) {
                    e.preventDefault();
                    alert('Please select at least one license to approve.');
                    return;
                }

                if (!confirm(`Are you sure you want to approve ${checkedCount} license(s)?`)) {
                    e.preventDefault();
                }
            });
        }
    }
});
</script>
@endsection
