@extends('layouts.merchant')

@section('title', 'Phone Verification')
@section('header', 'Phone Verification')

@section('content')
<div class="discord-card">
    <div class="discord-card-body">
        <div class="text-center py-5">
            <i class="fas fa-mobile-alt fa-3x mb-3" style="color: var(--discord-green);"></i>
            <h4 style="color: var(--discord-lightest);">Phone Verification Required</h4>
            <p style="color: var(--discord-light);">Please verify your phone number to continue using your merchant account.</p>
            
            <div class="mt-4">
                <button class="discord-btn" onclick="resendOTP()">
                    <i class="fas fa-sms me-1"></i> Resend OTP
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function resendOTP() {
    // TODO: Implement OTP resend functionality
    alert('OTP sent!');
}
</script>
@endsection
