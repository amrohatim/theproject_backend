@extends('layouts.dashboard')

@section('title', 'Vendor License Details')
@section('page-title', 'Vendor License Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Vendor License Details</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $license->user->name }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.vendor-licenses.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
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
        <div class="lg:col-span-2 space-y-6">
            <!-- License Status Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">License Status</h3>
                    <div class="grid grid-cols-2 gap-4">
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
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $badgeClass }} mt-1">
                                {{ ucfirst($license->status) }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">License Type</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($license->license_type) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- License Details Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">License Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $license->start_date ? $license->start_date->format('M d, Y') : 'Not set' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $license->end_date ? $license->end_date->format('M d, Y') : 'Not set' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Duration</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $license->duration_days }} days</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Submitted</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $license->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                    
                    @if($license->notes)
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                        <div class="mt-1 p-3 bg-gray-50 dark:bg-gray-700 rounded-md">
                            <p class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $license->notes }}</p>
                        </div>
                    </div>
                    @endif
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
                            <a href="{{ route('admin.vendor-licenses.view', $license->id) }}" target="_blank" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                Open in New Tab
                            </a>
                            <a href="{{ route('admin.vendor-licenses.download', $license->id) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download
                            </a>
                            <a href="{{ route('admin.vendor-licenses.image', $license->id) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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
                                src="{{ route('admin.vendor-licenses.view', $license->id) }}" 
                                class="w-full h-96"
                                frameborder="0">
                                <p>Your browser does not support PDFs. <a href="{{ route('admin.vendor-licenses.download', $license->id) }}">Download the PDF</a>.</p>
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Vendor Information Sidebar -->
        <div class="space-y-6">
            <!-- Vendor Profile -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Vendor Information</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $license->user->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $license->user->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $license->user->phone ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Registration Step</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ ucfirst(str_replace('_', ' ', $license->user->registration_step)) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Company Information -->
            @if($license->user->company)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Company Information</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Company Name</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $license->user->company->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Location</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $license->user->company->city }}, {{ $license->user->company->emirate }}</p>
                        </div>
                        @if($license->user->company->contact_number_1)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Primary Contact</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $license->user->company->contact_number_1 }}</p>
                        </div>
                        @endif
                        @if($license->user->company->contact_number_2)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Secondary Contact</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $license->user->company->contact_number_2 }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            @if($license->status === 'pending')
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Actions</h3>
                    
                    <!-- Approve Form -->
                    <form method="POST" action="{{ route('admin.vendor-licenses.approve', $license->id) }}" class="mb-4">
                        @csrf
                        <div class="mb-3">
                            <label for="admin_message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Approval Message (Optional)</label>
                            <textarea id="admin_message" name="admin_message" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Optional message for the vendor..."></textarea>
                        </div>
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150" onclick="return confirm('Are you sure you want to approve this license?')">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Approve License
                        </button>
                    </form>

                    <!-- Reject Form -->
                    <form method="POST" action="{{ route('admin.vendor-licenses.reject', $license->id) }}">
                        @csrf
                        <div class="mb-3">
                            <label for="rejection_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rejection Reason *</label>
                            <textarea id="rejection_reason" name="rejection_reason" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Please provide a reason for rejection..."></textarea>
                        </div>
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150" onclick="return confirm('Are you sure you want to reject this license?')">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Reject License
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
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
</script>
@endsection
