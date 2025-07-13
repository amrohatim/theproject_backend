@extends('layouts.dashboard')

@section('title', 'License Details - ' . $merchant->business_name)
@section('page-title', 'License Details')

@push('styles')
<style>
    /* Enhanced Image Modal Styles */
    #image-modal {
        backdrop-filter: blur(4px);
    }

    #image-modal-img {
        max-width: none;
        max-height: none;
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
    }

    #image-container {
        cursor: grab;
    }

    #image-container:active {
        cursor: grabbing;
    }

    /* Smooth transitions for zoom controls */
    .zoom-control {
        transition: all 0.2s ease;
    }

    .zoom-control:hover {
        transform: scale(1.1);
    }

    /* Custom scrollbar for image container */
    #image-container::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    #image-container::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.1);
        border-radius: 4px;
    }

    #image-container::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.3);
        border-radius: 4px;
    }

    #image-container::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 0, 0, 0.5);
    }

    /* Prevent text selection during drag */
    .no-select {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
</style>
@endpush

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

                    <!-- UAE ID Images and Logo -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Merchant Documentation</label>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <!-- UAE ID Front -->
                            <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">UAE ID Front</h4>
                                @if($merchant->uae_id_front)
                                    <div class="relative group">
                                        <img src="{{ \App\Helpers\ImageHelper::getFullImageUrl($merchant->uae_id_front) }}"
                                             alt="UAE ID Front"
                                             class="w-full h-32 object-cover rounded cursor-pointer hover:opacity-75 transition-opacity"
                                             onclick="showImageModal('{{ \App\Helpers\ImageHelper::getFullImageUrl($merchant->uae_id_front) }}', 'UAE ID Front')">
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 rounded flex items-center justify-center">
                                            <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('admin.merchant-licenses.image', ['id' => $merchant->id, 'type' => 'uae_front']) }}"
                                           class="inline-flex items-center px-3 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4a1 1 0 011-1h4m0 0l-3 3m3-3v3m6-3h4a1 1 0 011 1v4m0 0l-3-3m3 3h-3m-3 6v4a1 1 0 01-1 1H8m0 0l3-3m-3 3h3m3-3v-4a1 1 0 011-1h4"></path>
                                            </svg>
                                            Full Preview
                                        </a>
                                    </div>
                                @else
                                    <div class="w-full h-32 bg-gray-100 dark:bg-gray-700 rounded flex items-center justify-center">
                                        <span class="text-gray-500 dark:text-gray-400 text-sm">No image uploaded</span>
                                    </div>
                                @endif
                            </div>

                            <!-- UAE ID Back -->
                            <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">UAE ID Back</h4>
                                @if($merchant->uae_id_back)
                                    <div class="relative group">
                                        <img src="{{ \App\Helpers\ImageHelper::getFullImageUrl($merchant->uae_id_back) }}"
                                             alt="UAE ID Back"
                                             class="w-full h-32 object-cover rounded cursor-pointer hover:opacity-75 transition-opacity"
                                             onclick="showImageModal('{{ \App\Helpers\ImageHelper::getFullImageUrl($merchant->uae_id_back) }}', 'UAE ID Back')">
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 rounded flex items-center justify-center">
                                            <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('admin.merchant-licenses.image', ['id' => $merchant->id, 'type' => 'uae_back']) }}"
                                           class="inline-flex items-center px-3 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4a1 1 0 011-1h4m0 0l-3 3m3-3v3m6-3h4a1 1 0 011 1v4m0 0l-3-3m3 3h-3m-3 6v4a1 1 0 01-1 1H8m0 0l3-3m-3 3h3m3-3v-4a1 1 0 011-1h4"></path>
                                            </svg>
                                            Full Preview
                                        </a>
                                    </div>
                                @else
                                    <div class="w-full h-32 bg-gray-100 dark:bg-gray-700 rounded flex items-center justify-center">
                                        <span class="text-gray-500 dark:text-gray-400 text-sm">No image uploaded</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Merchant Logo -->
                            <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">Business Logo</h4>
                                @if($merchant->logo)
                                    <div class="relative group">
                                        <img src="{{ \App\Helpers\ImageHelper::getFullImageUrl($merchant->logo) }}"
                                             alt="Business Logo"
                                             class="w-full h-32 object-cover rounded cursor-pointer hover:opacity-75 transition-opacity"
                                             onclick="showImageModal('{{ \App\Helpers\ImageHelper::getFullImageUrl($merchant->logo) }}', 'Business Logo')">
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 rounded flex items-center justify-center">
                                            <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('admin.merchant-licenses.image', ['id' => $merchant->id, 'type' => 'logo']) }}"
                                           class="inline-flex items-center px-3 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4a1 1 0 011-1h4m0 0l-3 3m3-3v3m6-3h4a1 1 0 011 1v4m0 0l-3-3m3 3h-3m-3 6v4a1 1 0 01-1 1H8m0 0l3-3m-3 3h3m3-3v-4a1 1 0 011-1h4"></path>
                                            </svg>
                                            Full Preview
                                        </a>
                                    </div>
                                @else
                                    <div class="w-full h-32 bg-gray-100 dark:bg-gray-700 rounded flex items-center justify-center">
                                        <span class="text-gray-500 dark:text-gray-400 text-sm">No logo uploaded</span>
                                    </div>
                                @endif
                            </div>
                        </div>
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

<!-- Enhanced Image Modal -->
<div id="image-modal" class="fixed inset-0 bg-black bg-opacity-90 overflow-auto h-full w-full hidden z-50" onclick="hideImageModal()">
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <!-- Modal Content -->
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-2xl max-w-7xl max-h-full overflow-hidden" onclick="event.stopPropagation()">
            <!-- Header -->
            <div class="flex justify-between items-center p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 id="image-modal-title" class="text-lg font-semibold text-gray-900 dark:text-gray-100"></h3>
                <div class="flex items-center space-x-2">
                    <!-- Zoom Controls -->
                    <button onclick="zoomOut()" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors" title="Zoom Out">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"></path>
                        </svg>
                    </button>
                    <span id="zoom-level" class="text-sm text-gray-600 dark:text-gray-400 min-w-12 text-center">100%</span>
                    <button onclick="zoomIn()" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors" title="Zoom In">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                        </svg>
                    </button>
                    <button onclick="resetZoom()" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors" title="Reset Zoom">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </button>
                    <!-- Download Button -->
                    <a id="download-image" href="" download="" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors" title="Download Image">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </a>
                    <!-- Close Button -->
                    <button onclick="hideImageModal()" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors" title="Close">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Image Container -->
            <div id="image-container" class="relative overflow-auto bg-gray-50 dark:bg-gray-900" style="max-height: 80vh; max-width: 90vw;">
                <img id="image-modal-img"
                     src=""
                     alt=""
                     class="block mx-auto transition-transform duration-200 cursor-move"
                     style="transform-origin: center center;"
                     draggable="false"
                     onload="resetImagePosition()">
            </div>

            <!-- Footer with Image Info -->
            <div class="p-3 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                <div class="flex justify-between items-center text-sm text-gray-600 dark:text-gray-400">
                    <span id="image-info">Loading image information...</span>
                    <span class="text-xs">Click and drag to pan • Scroll to zoom • ESC to close</span>
                </div>
            </div>
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

// Image Modal Variables
let currentZoom = 1;
let isDragging = false;
let startX, startY, scrollLeft, scrollTop;

function showImageModal(imageSrc, title) {
    const modal = document.getElementById('image-modal');
    const img = document.getElementById('image-modal-img');
    const titleElement = document.getElementById('image-modal-title');
    const downloadLink = document.getElementById('download-image');
    const imageInfo = document.getElementById('image-info');

    // Set image source and title
    img.src = imageSrc;
    titleElement.textContent = title;

    // Set download link
    downloadLink.href = imageSrc;
    downloadLink.download = title.replace(/\s+/g, '_').toLowerCase() + '.jpg';

    // Reset zoom and position
    currentZoom = 1;
    updateZoomDisplay();

    // Show modal
    modal.classList.remove('hidden');

    // Update image info when image loads
    img.onload = function() {
        const naturalWidth = this.naturalWidth;
        const naturalHeight = this.naturalHeight;
        const fileSize = 'Unknown size'; // We can't get file size from img element
        imageInfo.textContent = `${naturalWidth} × ${naturalHeight} pixels`;
    };

    // Add keyboard event listener
    document.addEventListener('keydown', handleKeyPress);
}

function hideImageModal() {
    const modal = document.getElementById('image-modal');
    modal.classList.add('hidden');

    // Remove keyboard event listener
    document.removeEventListener('keydown', handleKeyPress);

    // Reset zoom
    currentZoom = 1;
    updateZoomDisplay();
}

function handleKeyPress(event) {
    if (event.key === 'Escape') {
        hideImageModal();
    } else if (event.key === '+' || event.key === '=') {
        zoomIn();
    } else if (event.key === '-') {
        zoomOut();
    } else if (event.key === '0') {
        resetZoom();
    }
}

function zoomIn() {
    if (currentZoom < 3) {
        currentZoom += 0.25;
        applyZoom();
    }
}

function zoomOut() {
    if (currentZoom > 0.25) {
        currentZoom -= 0.25;
        applyZoom();
    }
}

function resetZoom() {
    currentZoom = 1;
    applyZoom();
    resetImagePosition();
}

function applyZoom() {
    const img = document.getElementById('image-modal-img');
    img.style.transform = `scale(${currentZoom})`;
    updateZoomDisplay();
}

function updateZoomDisplay() {
    const zoomLevel = document.getElementById('zoom-level');
    zoomLevel.textContent = Math.round(currentZoom * 100) + '%';
}

function resetImagePosition() {
    const container = document.getElementById('image-container');
    container.scrollLeft = 0;
    container.scrollTop = 0;
}

// Mouse wheel zoom functionality
document.getElementById('image-container').addEventListener('wheel', function(e) {
    if (e.ctrlKey) {
        e.preventDefault();
        if (e.deltaY < 0) {
            zoomIn();
        } else {
            zoomOut();
        }
    }
});

// Drag to pan functionality
document.getElementById('image-modal-img').addEventListener('mousedown', function(e) {
    if (currentZoom > 1) {
        isDragging = true;
        const container = document.getElementById('image-container');
        startX = e.pageX - container.offsetLeft;
        startY = e.pageY - container.offsetTop;
        scrollLeft = container.scrollLeft;
        scrollTop = container.scrollTop;
        this.style.cursor = 'grabbing';
    }
});

document.addEventListener('mousemove', function(e) {
    if (!isDragging) return;
    e.preventDefault();
    const container = document.getElementById('image-container');
    const x = e.pageX - container.offsetLeft;
    const y = e.pageY - container.offsetTop;
    const walkX = (x - startX) * 2;
    const walkY = (y - startY) * 2;
    container.scrollLeft = scrollLeft - walkX;
    container.scrollTop = scrollTop - walkY;
});

document.addEventListener('mouseup', function() {
    if (isDragging) {
        isDragging = false;
        const img = document.getElementById('image-modal-img');
        img.style.cursor = currentZoom > 1 ? 'grab' : 'default';
    }
});

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

// Close modals when clicking outside
document.getElementById('reject-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideRejectModal();
    }
});

document.getElementById('image-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideImageModal();
    }
});

// Close image modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideImageModal();
        hideRejectModal();
    }
});
</script>
@endsection
