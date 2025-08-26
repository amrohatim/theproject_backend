@extends('layouts.dashboard')

@section('title', 'Branch License Details')
@section('page-title', 'Branch License Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Branch License Details</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $license->branch->name }} - {{ $license->branch->user->name }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.branch-licenses.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to List
                </a>
                @if($license->status === 'pending')
                <button onclick="showApprovalModal()" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-check mr-2"></i>
                    Approve
                </button>
                <button onclick="showRejectionModal()" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-times mr-2"></i>
                    Reject
                </button>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- License Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Branch Details -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Branch Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Branch Name</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $license->branch->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Business Type</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $license->branch->business_type }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $license->branch->address }}</p>
                        </div>
                        @if($license->branch->emirate)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Emirate</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $license->branch->emirate }}</p>
                        </div>
                        @endif
                        @if($license->branch->phone)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $license->branch->phone }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Vendor Details -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Vendor Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Vendor Name</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $license->branch->user->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $license->branch->user->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $license->branch->user->phone ?? 'Not provided' }}</p>
                        </div>
                        @if($license->branch->company)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Company</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $license->branch->company->name }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- License Document -->
            @if($license->license_file_path)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">License Document</h3>
                        <div class="flex space-x-2">
                            <button id="pdf-toggle-btn" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <span id="pdf-toggle-text">View Inline</span>
                            </button>
                            <a href="{{ route('admin.branch-licenses.view', $license->id) }}" target="_blank" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                New Window
                            </a>
                            <a href="{{ route('admin.branch-licenses.download', $license->id) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Full Preview
                            </a>
                        </div>
                    </div>
                    
                    <div id="pdf-viewer" class="hidden">
                        <div class="border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden">
                            <iframe 
                                src="{{ route('admin.branch-licenses.view', $license->id) }}" 
                                class="w-full h-96"
                                frameborder="0">
                                <p>Your browser does not support PDFs. <a href="{{ route('admin.branch-licenses.download', $license->id) }}">Download the PDF</a>.</p>
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Status and Actions -->
        <div class="space-y-6">
            <!-- Current Status -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">License Status</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Current Status</label>
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                    'active' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                    'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                    'expired' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'
                                ];
                            @endphp
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $statusClasses[$license->status] ?? 'bg-gray-100 text-gray-800' }} mt-1">
                                {{ ucfirst($license->status) }}
                            </span>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">License Period</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $license->start_date->format('d-m-Y') }} to {{ $license->end_date->format('d-m-Y') }}
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Submitted</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $license->uploaded_at->format('d-m-Y H:i') }}</p>
                        </div>
                        
                        @if($license->verified_at)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Verified</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $license->verified_at->format('d-m-Y H:i') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            @if($license->status === 'pending')
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <button onclick="showApprovalModal()" class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <i class="fas fa-check mr-2"></i>
                            Approve License
                        </button>
                        <button onclick="showRejectionModal()" class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <i class="fas fa-times mr-2"></i>
                            Reject License
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div id="approval-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Approve Branch License</h3>
                <button onclick="hideApprovalModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.branch-licenses.approve', $license->id) }}">
                @csrf
                <div class="mb-4">
                    <label for="admin_message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Admin Message (Optional)
                    </label>
                    <textarea 
                        id="admin_message" 
                        name="admin_message" 
                        rows="3" 
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Add a message for the vendor (optional)"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideApprovalModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <i class="fas fa-check mr-2"></i>
                        Approve License
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div id="rejection-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Reject Branch License</h3>
                <button onclick="hideRejectionModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.branch-licenses.reject', $license->id) }}">
                @csrf
                <div class="mb-4">
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Rejection Reason <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="rejection_reason" 
                        name="rejection_reason" 
                        rows="4" 
                        required
                        class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Please provide a detailed reason for rejection..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideRejectionModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <i class="fas fa-times mr-2"></i>
                        Reject License
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// PDF Viewer Toggle
document.addEventListener('DOMContentLoaded', function() {
    const pdfToggleBtn = document.getElementById('pdf-toggle-btn');
    const pdfViewer = document.getElementById('pdf-viewer');
    const pdfToggleText = document.getElementById('pdf-toggle-text');

    if (pdfToggleBtn && pdfViewer) {
        pdfToggleBtn.addEventListener('click', function() {
            if (pdfViewer.classList.contains('hidden')) {
                pdfViewer.classList.remove('hidden');
                pdfToggleText.textContent = 'Hide Inline';
            } else {
                pdfViewer.classList.add('hidden');
                pdfToggleText.textContent = 'View Inline';
            }
        });
    }
});

// Modal Functions
function showApprovalModal() {
    document.getElementById('approval-modal').classList.remove('hidden');
}

function hideApprovalModal() {
    document.getElementById('approval-modal').classList.add('hidden');
}

function showRejectionModal() {
    document.getElementById('rejection-modal').classList.remove('hidden');
}

function hideRejectionModal() {
    document.getElementById('rejection-modal').classList.add('hidden');
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const approvalModal = document.getElementById('approval-modal');
    const rejectionModal = document.getElementById('rejection-modal');
    
    if (event.target === approvalModal) {
        hideApprovalModal();
    }
    if (event.target === rejectionModal) {
        hideRejectionModal();
    }
});
</script>
@endsection
