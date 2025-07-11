@extends('layouts.dashboard')

@section('title', 'License Details - ' . $merchant->business_name)
@section('page-title', 'License Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">License Details</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $merchant->business_name }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.merchant-licenses.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- License Information -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">License Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Current Status</label>
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
                            <div class="mt-1">
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $badgeClass }}">
                                    {{ $statusInfo['text'] }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expiry Date</label>
                            <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                @if($merchant->license_expiry_date)
                                    {{ $merchant->license_expiry_date->format('M d, Y') }}
                                    @if($merchant->license_expiry_date->isFuture())
                                        <span class="text-green-600 dark:text-green-400">
                                            ({{ $merchant->daysUntilLicenseExpiration() }} days remaining)
                                        </span>
                                    @else
                                        <span class="text-red-600 dark:text-red-400">
                                            (Expired {{ abs($merchant->daysUntilLicenseExpiration()) }} days ago)
                                        </span>
                                    @endif
                                @else
                                    <span class="text-gray-400">Not set</span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Uploaded Date</label>
                            <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                @if($merchant->license_uploaded_at)
                                    {{ $merchant->license_uploaded_at->format('M d, Y H:i') }}
                                @else
                                    <span class="text-gray-400">Never uploaded</span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Approved Date</label>
                            <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                @if($merchant->license_approved_at)
                                    {{ $merchant->license_approved_at->format('M d, Y H:i') }}
                                @else
                                    <span class="text-gray-400">Not approved</span>
                                @endif
                            </div>
                        </div>

                        @if($merchant->licenseApprovedBy)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Approved By</label>
                            <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $merchant->licenseApprovedBy->name }}
                            </div>
                        </div>
                        @endif

                        @if($merchant->license_rejection_reason)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rejection Reason</label>
                            <div class="mt-1 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md">
                                <p class="text-sm text-red-800 dark:text-red-200">{{ $merchant->license_rejection_reason }}</p>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- License File -->
                    @if($merchant->license_file)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">License Document</label>
                        <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">License Document</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">PDF Document</p>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <button onclick="togglePdfViewer()" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <span id="pdf-toggle-text">View Inline</span>
                                    </button>
                                    <a href="{{ route('admin.merchant-licenses.view', $merchant->id) }}" target="_blank" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                        Open in New Tab
                                    </a>
                                    <a href="{{ route('admin.merchant-licenses.download', $merchant->id) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Download
                                    </a>
                                </div>
                            </div>

                            <!-- Inline PDF Viewer -->
                            <div id="pdf-viewer-container" class="hidden">
                                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">PDF Preview</h4>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Use browser controls to zoom and navigate</span>
                                            <button onclick="togglePdfViewer()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="border border-gray-200 dark:border-gray-700 rounded">
                                        <iframe id="pdf-iframe"
                                                src="{{ route('admin.merchant-licenses.view', $merchant->id) }}#toolbar=1&navpanes=1&scrollbar=1"
                                                class="w-full h-96 rounded"
                                                style="min-height: 600px;">
                                            <p class="p-4 text-center text-gray-500 dark:text-gray-400">
                                                Your browser does not support PDF viewing.
                                                <a href="{{ route('admin.merchant-licenses.download', $merchant->id) }}" class="text-indigo-600 hover:text-indigo-500">Click here to download the PDF</a>
                                            </p>
                                        </iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Merchant Information & Actions -->
        <div class="space-y-6">
            <!-- Merchant Info -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Merchant Information</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Merchant Name</label>
                            <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $merchant->user->name }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $merchant->user->email }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Business Name</label>
                            <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $merchant->business_name }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Business Type</label>
                            <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $merchant->business_type ?? 'Not specified' }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Location</label>
                            <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                @if($merchant->city && $merchant->emirate)
                                    {{ $merchant->city }}, {{ $merchant->emirate }}
                                @else
                                    Not specified
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Verification Status</label>
                            <div class="mt-1">
                                @if($merchant->is_verified)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Verified
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        Not Verified
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            @if($merchant->license_status === 'checking')
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Actions</h3>
                    
                    <div class="space-y-3">
                        <!-- Approve Button -->
                        <form method="POST" action="{{ route('admin.merchant-licenses.approve', $merchant->id) }}" class="w-full">
                            @csrf
                            <button type="submit" onclick="return confirm('Are you sure you want to approve this license?')" class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Approve License
                            </button>
                        </form>

                        <!-- Reject Button -->
                        <button type="button" onclick="showRejectModal()" class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Reject License
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="reject-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Reject License</h3>
            <form method="POST" action="{{ route('admin.merchant-licenses.reject', $merchant->id) }}">
                @csrf
                <div class="mb-4">
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Rejection Reason *
                    </label>
                    <textarea id="rejection_reason" name="rejection_reason" rows="4" required 
                              class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                              placeholder="Please provide a detailed reason for rejecting this license..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideRejectModal()" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Reject License
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showRejectModal() {
    document.getElementById('reject-modal').classList.remove('hidden');
}

function hideRejectModal() {
    document.getElementById('reject-modal').classList.add('hidden');
}

function togglePdfViewer() {
    const container = document.getElementById('pdf-viewer-container');
    const toggleText = document.getElementById('pdf-toggle-text');

    if (container.classList.contains('hidden')) {
        container.classList.remove('hidden');
        toggleText.textContent = 'Hide Inline';
        // Scroll to the PDF viewer
        container.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        container.classList.add('hidden');
        toggleText.textContent = 'View Inline';
    }
}

// Close modal when clicking outside
document.getElementById('reject-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideRejectModal();
    }
});
</script>
@endsection
