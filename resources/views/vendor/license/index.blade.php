@extends('layouts.dashboard')

@section('title', __('messages.license_management'))

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('messages.license_management') }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">{{ __('messages.manage_license_description') }}</p>
    </div>

    <!-- License Status Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('messages.current_license_status') }}</h2>
                @if($license)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($licenseStatus === 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                        @elseif($licenseStatus === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                        @elseif($licenseStatus === 'expired') bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                        @elseif($licenseStatus === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                        @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                        @endif">
                        {{ ucfirst($licenseStatus) }}
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                        No License
                    </span>
                @endif
            </div>

            @if($license)
                <!-- License Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.license_start_date') }}</label>
                        <p class="text-gray-900 dark:text-white">{{ $license->start_date->format('d-m-Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.license_end_date') }}</label>
                        <p class="text-gray-900 dark:text-white">{{ $license->end_date->format('d-m-Y') }}</p>
                    </div>
                    @if($daysUntilExpiry !== null)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.license_days_until_expiry') }}</label>
                            <p class="text-gray-900 dark:text-white 
                                @if($daysUntilExpiry <= 30) text-orange-600 dark:text-orange-400 font-semibold
                                @elseif($daysUntilExpiry <= 7) text-red-600 dark:text-red-400 font-semibold
                                @endif">
                                {{ $daysUntilExpiry }} days
                            </p>
                        </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.license_type') }}</label>
                        <p class="text-gray-900 dark:text-white">{{ ucfirst($license->license_type) }}</p>
                    </div>
                </div>

                <!-- License Document Preview -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">{{ __('messages.license_document') }}</label>
                    <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mr-4">
                                    <i class="fas fa-file-pdf text-blue-600 dark:text-blue-400 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $license->license_file_name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Uploaded {{ $license->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('vendor.license.view', $license->id) }}" 
                                   target="_blank"
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-eye mr-2"></i>
                                    {{ __('messages.view') }}
                                </a>
                                <a href="{{ route('vendor.license.preview', $license->id) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-expand mr-2"></i>
                                    {{ __('messages.full_preview') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Messages -->
                @if($licenseStatus === 'pending')
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-clock text-yellow-400 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">{{ __('messages.license_under_review') }}</h3>
                            <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">{{ __('messages.license_review_message') }}</p>
                            </div>
                        </div>
                    </div>
                @elseif($licenseStatus === 'rejected')
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-times-circle text-red-400 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800 dark:text-red-200">{{ __('messages.license_rejected') }}</h3>
                            <p class="text-sm text-red-700 dark:text-red-300 mt-1">{{ __('messages.license_rejected_message') }}</p>
                                @if($license->notes)
                                    <p class="text-sm text-red-700 dark:text-red-300 mt-2"><strong>{{ __('messages.reason') }}:</strong> {{ $license->notes }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @elseif($licenseStatus === 'expired')
                    <div class="bg-gray-50 dark:bg-gray-900/20 border border-gray-200 dark:border-gray-800 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-gray-400 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ __('messages.license_expired') }}</h3>
                            <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ __('messages.license_expired_message') }}</p>
                            </div>
                        </div>
                    </div>
                @elseif($daysUntilExpiry !== null && $daysUntilExpiry <= 30)
                    <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-orange-400 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-orange-800 dark:text-orange-200">{{ __('messages.license_expiring_soon') }}</h3>
                            <p class="text-sm text-orange-700 dark:text-orange-300 mt-1">{{ __('messages.license_expiring_message', ['days' => $daysUntilExpiry]) }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <!-- No License -->
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-certificate text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ __('messages.no_license_found') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">{{ __('messages.no_license_message') }}</p>
                    <a href="{{ route('vendor.license.upload') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                        <i class="fas fa-upload mr-2"></i>
                        {{ __('messages.upload_license') }}
                    </a>
                </div>
            @endif

            <!-- Pending Renewal Warning -->
            @if($hasPendingRenewal)
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400 text-lg"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                {{ __('messages.pending_renewal_title', ['default' => 'Renewal Pending']) }}
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                <p>{{ __('messages.pending_renewal_message', ['default' => 'You have a license renewal request pending admin approval. Your account will be frozen until the new license is approved. Please wait for the admin to review your submission.']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            @if($license && $canUploadNew)
                <div class="flex justify-end space-x-3">
                    <button onclick="showRenewalModal()" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700 transition-colors">
                        <i class="fas fa-sync-alt mr-2"></i>
                        {{ __('messages.renew_license') }}
                    </button>
                </div>
            @elseif($license && $hasPendingRenewal)
                <div class="flex justify-end space-x-3">
                    <button disabled 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-500 bg-gray-100 cursor-not-allowed">
                        <i class="fas fa-clock mr-2"></i>
                        {{ __('messages.renewal_pending', ['default' => 'Renewal Pending']) }}
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- License Renewal Modal -->
@if($license && $canUploadNew)
    @include('vendor.license.renewal-modal')
@endif

<!-- Success/Error Messages -->
@if(session('success'))
    <div id="success-message" class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div id="error-message" class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50">
        {{ session('error') }}
    </div>
@endif

@push('scripts')
<script>
function showRenewalModal() {
    document.getElementById('renewalModal').classList.remove('hidden');
}

function hideRenewalModal() {
    document.getElementById('renewalModal').classList.add('hidden');
}

// Auto-hide messages after 5 seconds
setTimeout(function() {
    const successMsg = document.getElementById('success-message');
    const errorMsg = document.getElementById('error-message');
    if (successMsg) successMsg.style.display = 'none';
    if (errorMsg) errorMsg.style.display = 'none';
}, 5000);
</script>
@endpush
@endsection
