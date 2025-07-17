@extends('layouts.merchant')

@section('title', 'License Status')
@section('header', 'License Status')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <!-- Status Card -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <!-- Header with Status Icon -->
            <div class="px-6 py-8 text-center {{ $license_status === 'checking' ? 'bg-gradient-to-r from-yellow-50 to-orange-50' : ($license_status === 'rejected' ? 'bg-gradient-to-r from-red-50 to-red-100' : 'bg-gradient-to-r from-gray-50 to-gray-100') }}">
                <div class="mx-auto w-16 h-16 {{ $license_status === 'checking' ? 'bg-yellow-100' : ($license_status === 'rejected' ? 'bg-red-100' : 'bg-gray-100') }} rounded-full flex items-center justify-center mb-4">
                    @if($license_status === 'checking')
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    @elseif($license_status === 'rejected')
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    @elseif($license_status === 'expired')
                        <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    @else
                        <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    @endif
                </div>
                
                <h1 class="text-2xl font-bold {{ $license_status === 'checking' ? 'text-yellow-800' : ($license_status === 'rejected' ? 'text-red-800' : 'text-gray-800') }} mb-2">
                    @if($license_status === 'checking')
                        License Under Review
                    @elseif($license_status === 'rejected')
                        License Rejected
                    @elseif($license_status === 'expired')
                        License Expired
                    @else
                        License Status Issue
                    @endif
                </h1>
                
                <p class="text-lg {{ $license_status === 'checking' ? 'text-yellow-700' : ($license_status === 'rejected' ? 'text-red-700' : 'text-gray-700') }}">
                    {{ $message }}
                </p>
            </div>

            <!-- Content -->
            <div class="px-6 py-8">
                @if($license_status === 'checking')
                    <!-- Checking Status Content -->
                    <div class="space-y-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h3 class="text-sm font-medium text-blue-800">What happens next?</h3>
                                    <p class="mt-1 text-sm text-blue-700">
                                        Our admin team is reviewing your license documentation. This process typically takes 1-3 business days. 
                                        You'll receive an email notification once your license is approved.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <div class="inline-flex items-center px-4 py-2 bg-yellow-100 text-yellow-800 text-sm font-medium rounded-lg">
                                <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Review in Progress
                            </div>
                        </div>
                    </div>

                @elseif($license_status === 'rejected')
                    <!-- Rejected Status Content -->
                    <div class="space-y-6">
                        @if($rejection_reason)
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-red-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <h3 class="text-sm font-medium text-red-800">Rejection Reason</h3>
                                        <p class="mt-1 text-sm text-red-700">{{ $rejection_reason }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="text-center space-y-4">
                            <a href="{{ route('merchant.license.upload') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                Upload New License
                            </a>
                            
                            <p class="text-sm text-gray-600">
                                Need help? <a href="mailto:support@theproject.com" class="text-blue-600 hover:text-blue-700 font-medium">Contact Support</a>
                            </p>
                        </div>
                    </div>

                @elseif($license_status === 'expired')
                    <!-- Expired Status Content -->
                    <div class="space-y-6">
                        @if($license_expiry_date)
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-gray-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-800">License Expired On</h3>
                                        <p class="mt-1 text-sm text-gray-700">{{ $license_expiry_date->format('d-m-Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="text-center space-y-4">
                            <a href="{{ route('merchant.license.upload') }}" class="inline-flex items-center px-6 py-3 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Renew License
                            </a>
                            
                            <p class="text-sm text-gray-600">
                                Upload your renewed license to regain full access to your merchant dashboard.
                            </p>
                        </div>
                    </div>

                @else
                    <!-- Default/Unknown Status Content -->
                    <div class="text-center space-y-4">
                        <p class="text-gray-600">
                            There seems to be an issue with your license status. Please contact our support team for assistance.
                        </p>
                        
                        <a href="mailto:support@theproject.com" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"></path>
                            </svg>
                            Contact Support
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Additional Actions -->
        <div class="mt-6 text-center">
            <a href="{{ route('merchant.settings.global') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                ← Back to Settings
            </a>
        </div>
    </div>
</div>
@endsection
