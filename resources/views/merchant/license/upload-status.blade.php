@extends('layouts.merchant')

@section('title', 'License Upload Status')
@section('header', 'License Upload Status')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <!-- Status Card -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <!-- Header with Status Icon -->
            <div class="px-6 py-8 text-center bg-gradient-to-r from-green-50 to-emerald-50">
                <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                
                <h1 class="text-2xl font-bold text-green-800 mb-2">License Uploaded Successfully!</h1>
                <p class="text-lg text-green-700">Your business license has been submitted for review</p>
            </div>

            <!-- Content -->
            <div class="px-6 py-8">
                <div class="space-y-6">
                    <!-- Current Status -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h3 class="font-medium text-yellow-800">Under Review</h3>
                                <p class="text-sm text-yellow-700 mt-1">
                                    Your license is currently being reviewed by our admin team. This process typically takes 24-48 hours.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- License Information -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-medium text-gray-800 mb-3">Uploaded License Details</h3>
                        <div class="space-y-2 text-sm">
                            @if($merchant->license_file)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">File:</span>
                                    <span class="text-gray-800">{{ basename($merchant->license_file) }}</span>
                                </div>
                            @endif
                            @if($merchant->license_start_date)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Start Date:</span>
                                    <span class="text-gray-800">{{ $merchant->license_start_date->format('d/m/Y') }}</span>
                                </div>
                            @endif
                            @if($merchant->license_expiry_date)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">End Date:</span>
                                    <span class="text-gray-800">{{ $merchant->license_expiry_date->format('d/m/Y') }}</span>
                                </div>
                            @endif
                            @if($merchant->license_uploaded_at)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Uploaded:</span>
                                    <span class="text-gray-800">{{ $merchant->license_uploaded_at->format('d/m/Y H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- What Happens Next -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="font-medium text-blue-800 mb-3">What happens next?</h3>
                        <div class="space-y-3">
                            <div class="flex items-start">
                                <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mr-3 mt-0.5">
                                    <span class="text-xs font-medium text-blue-600">1</span>
                                </div>
                                <div>
                                    <p class="text-sm text-blue-700">
                                        <strong>Review Process:</strong> Our admin team will verify your license documents
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mr-3 mt-0.5">
                                    <span class="text-xs font-medium text-blue-600">2</span>
                                </div>
                                <div>
                                    <p class="text-sm text-blue-700">
                                        <strong>Email Notification:</strong> You'll receive an email once your license is approved or if additional information is needed
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mr-3 mt-0.5">
                                    <span class="text-xs font-medium text-blue-600">3</span>
                                </div>
                                <div>
                                    <p class="text-sm text-blue-700">
                                        <strong>Dashboard Access:</strong> Once approved, you'll gain full access to your merchant dashboard
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <h3 class="font-medium text-gray-800 mb-2">Need Help?</h3>
                        <p class="text-sm text-gray-600 mb-3">
                            If you have any questions about your license review or need to update your documents, please contact our support team.
                        </p>
                        <div class="flex flex-wrap gap-3">
                            <a href="mailto:support@theproject.com" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                Email Support
                            </a>
                            <a href="tel:+971-xxx-xxxx" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                Call Support
                            </a>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-center space-x-4 pt-4">
                        <a href="{{ route('merchant.dashboard') }}"
                           class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            Go to Dashboard
                        </a>
                        <button onclick="window.location.reload()"
                                class="px-6 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors duration-200">
                            Refresh Status
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-500">
                Registration completed on {{ $user->created_at->format('F j, Y') }}
            </p>
        </div>
    </div>
</div>

<script>
// Auto-refresh every 30 seconds to check for status updates
setTimeout(function() {
    window.location.reload();
}, 30000);
</script>
@endsection
