@extends('layouts.merchant')

@section('title', 'Registration Status')
@section('header', 'Registration Status')

@section('content')
<div class="discord-card">
    <div class="discord-card-body">
        <div class="text-center py-5">
            @php
                $user = Auth::user();
                $merchant = $user->merchantRecord;
                $registrationStep = $user->registration_step;
            @endphp

            @if($registrationStep === 'pending')
                <i class="fas fa-clock fa-3x mb-3" style="color: var(--discord-yellow);"></i>
                <h4 style="color: var(--discord-lightest);">Registration Pending</h4>
                <p style="color: var(--discord-light);">Your merchant registration is currently being processed. Please complete all required steps.</p>

                <div class="mt-4">
                    <a href="{{ route('register.merchant') }}" class="discord-btn">
                        <i class="fas fa-edit me-1"></i> Complete Registration
                    </a>
                </div>
            @elseif($registrationStep === 'info_completed')
                <i class="fas fa-building fa-3x mb-3" style="color: var(--discord-blue);"></i>
                <h4 style="color: var(--discord-lightest);">Complete Company Information</h4>
                <p style="color: var(--discord-light);">Please complete your company information to proceed with registration.</p>

                <div class="mt-4">
                    <a href="{{ route('register.merchant') }}" class="discord-btn">
                        <i class="fas fa-edit me-1"></i> Complete Company Info
                    </a>
                </div>
            @elseif($registrationStep === 'company_completed')
                <i class="fas fa-file-upload fa-3x mb-3" style="color: var(--discord-purple);"></i>
                <h4 style="color: var(--discord-lightest);">Upload License Documents</h4>
                <p style="color: var(--discord-light);">Please upload your business license documents to complete registration.</p>

                <div class="mt-4">
                    <a href="{{ route('register.merchant') }}" class="discord-btn">
                        <i class="fas fa-upload me-1"></i> Upload License
                    </a>
                </div>
            @elseif($registrationStep === 'license_completed')
                <i class="fas fa-hourglass-half fa-3x mb-3" style="color: var(--discord-yellow);"></i>
                <h4 style="color: var(--discord-lightest);">Pending Admin Verification</h4>
                <p style="color: var(--discord-light);">Your registration is complete and pending admin verification. You will be notified once approved.</p>

                @if($merchant && $merchant->license_status === 'rejected')
                    <div class="mt-3 p-3 bg-red-100 border border-red-300 rounded" style="color: var(--discord-red);">
                        <strong>License Rejected:</strong> {{ $merchant->license_rejection_reason ?? 'Please contact support for details.' }}
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('merchant.settings.global') }}" class="discord-btn">
                            <i class="fas fa-upload me-1"></i> Upload New License
                        </a>
                    </div>
                @endif
            @else
                <i class="fas fa-check-circle fa-3x mb-3" style="color: var(--discord-green);"></i>
                <h4 style="color: var(--discord-lightest);">Registration Complete</h4>
                <p style="color: var(--discord-light);">Your merchant registration has been completed and verified.</p>

                <div class="mt-4">
                    <a href="{{ route('merchant.dashboard') }}" class="discord-btn">
                        <i class="fas fa-tachometer-alt me-1"></i> Go to Dashboard
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
