@extends('layouts.dashboard')

@section('title', 'License Details - ' . $license->user->name)
@section('page-title', 'Provider License Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Provider License Details</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $license->user->name }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.provider-licenses.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
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
                                $badgeClass = match($license->status) {
                                    'active' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                    'expired' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                    'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
                                };
                            @endphp
                            <div class="mt-1">
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $badgeClass }}">
                                    {{ ucfirst($license->status) }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">License Type</label>
                            <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ ucfirst($license->license_type) }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
                            <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $license->start_date ? \Carbon\Carbon::parse($license->start_date)->format('M d, Y') : 'N/A' }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expiry Date</label>
                            <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                @if($license->end_date)
                                    {{ \Carbon\Carbon::parse($license->end_date)->format('M d, Y') }}
                                    @if(\Carbon\Carbon::parse($license->end_date)->isFuture())
                                        <span class="text-green-600 dark:text-green-400">
                                            ({{ $license->daysUntilExpiration() }} days remaining)
                                        </span>
                                    @else
                                        <span class="text-red-600 dark:text-red-400">
                                            (Expired {{ abs($license->daysUntilExpiration()) }} days ago)
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
                                {{ $license->created_at->format('M d, Y H:i') }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Duration</label>
                            <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $license->duration_days }} days
                            </div>
                        </div>

                        @if($license->notes)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                            <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $license->notes }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- License Document -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">License Document</h3>
                        <div class="flex space-x-2">
                            <button id="toggle-pdf-viewer" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <span id="toggle-text">View Inline</span>
                            </button>
                            <a href="{{ asset('storage/' . $license->license_file_path) }}" target="_blank" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                Open in New Tab
                            </a>
                            <a href="{{ asset('storage/' . $license->license_file_path) }}" download="{{ $license->license_file_name }}" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download
                            </a>
                        </div>
                    </div>
                    
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        <strong>File:</strong> {{ $license->license_file_name }}
                    </div>

                    <!-- PDF Viewer -->
                    <div id="pdf-viewer" class="hidden">
                        <div class="border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden">
                            <iframe 
                                src="{{ asset('storage/' . $license->license_file_path) }}" 
                                class="w-full h-96"
                                title="License Document">
                                <p>Your browser does not support PDFs. 
                                   <a href="{{ asset('storage/' . $license->license_file_path) }}" target="_blank">Download the PDF</a>.
                                </p>
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Provider Information & Actions -->
        <div class="space-y-6">
            <!-- Provider Information -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Provider Information</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Provider Name</label>
                            <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $license->user->name }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $license->user->email }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                            <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $license->user->phone ?? 'N/A' }}</div>
                        </div>

                        @if($license->user->provider)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Business Name</label>
                            <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $license->user->provider->business_name ?? 'N/A' }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Provider Status</label>
                            <div class="mt-1">
                                @php
                                    $providerBadgeClass = match($license->user->provider->status) {
                                        'active' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                        default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
                                    };
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $providerBadgeClass }}">
                                    {{ ucfirst($license->user->provider->status) }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">User Status</label>
                            <div class="mt-1">
                                @php
                                    $userBadgeClass = match($license->user->status) {
                                        'active' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                        default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
                                    };
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $userBadgeClass }}">
                                    {{ ucfirst($license->user->status) }}
                                </span>
                            </div>
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Registration Date</label>
                            <div class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $license->user->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            @if($license->status === 'pending')
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Actions</h3>
                    
                    <div class="space-y-3">
                        <button id="approve-btn" class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Approve License
                        </button>
                        
                        <button id="reject-btn" class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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

<!-- Approve Modal -->
<div id="approve-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3 text-center">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Approve License</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Are you sure you want to approve this license for {{ $license->user->name }}?
                </p>
                <form method="POST" action="{{ route('admin.provider-licenses.approve', $license->id) }}" class="mt-4">
                    @csrf
                    <div class="mb-4">
                        <label for="admin_message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Admin Message (Optional)</label>
                        <textarea name="admin_message" id="admin_message" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Optional message for the provider..."></textarea>
                    </div>
                    <div class="flex justify-center space-x-3">
                        <button type="button" id="cancel-approve" class="px-4 py-2 bg-gray-300 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                            Approve License
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="reject-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3 text-center">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Reject License</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Please provide a reason for rejecting this license.
                </p>
                <form method="POST" action="{{ route('admin.provider-licenses.reject', $license->id) }}" class="mt-4">
                    @csrf
                    <div class="mb-4">
                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rejection Reason *</label>
                        <textarea name="rejection_reason" id="rejection_reason" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Please explain why this license is being rejected..."></textarea>
                    </div>
                    <div class="flex justify-center space-x-3">
                        <button type="button" id="cancel-reject" class="px-4 py-2 bg-gray-300 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            Reject License
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
    // PDF Viewer Toggle
    const toggleBtn = document.getElementById('toggle-pdf-viewer');
    const pdfViewer = document.getElementById('pdf-viewer');
    const toggleText = document.getElementById('toggle-text');

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            if (pdfViewer.classList.contains('hidden')) {
                pdfViewer.classList.remove('hidden');
                toggleText.textContent = 'Hide Inline';
            } else {
                pdfViewer.classList.add('hidden');
                toggleText.textContent = 'View Inline';
            }
        });
    }

    // Modal handlers
    const approveBtn = document.getElementById('approve-btn');
    const rejectBtn = document.getElementById('reject-btn');
    const approveModal = document.getElementById('approve-modal');
    const rejectModal = document.getElementById('reject-modal');
    const cancelApprove = document.getElementById('cancel-approve');
    const cancelReject = document.getElementById('cancel-reject');

    if (approveBtn) {
        approveBtn.addEventListener('click', () => approveModal.classList.remove('hidden'));
    }

    if (rejectBtn) {
        rejectBtn.addEventListener('click', () => rejectModal.classList.remove('hidden'));
    }

    if (cancelApprove) {
        cancelApprove.addEventListener('click', () => approveModal.classList.add('hidden'));
    }

    if (cancelReject) {
        cancelReject.addEventListener('click', () => rejectModal.classList.add('hidden'));
    }

    // Close modals when clicking outside
    [approveModal, rejectModal].forEach(modal => {
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
        }
    });
});
</script>
@endpush
@endsection
