@extends('layouts.merchant')

@section('title', 'Email Verification')
@section('header', 'Email Verification')

@section('content')
<div class="discord-card">
    <div class="discord-card-body">
        <div class="text-center py-5">
            <i class="fas fa-envelope fa-3x mb-3" style="color: var(--discord-blue);"></i>
            <h4 style="color: var(--discord-lightest);">Email Verification Required</h4>
            <p style="color: var(--discord-light);">Please verify your email address to continue using your merchant account.</p>
            
            <div class="mt-4">
                <button class="discord-btn" onclick="resendVerification()">
                    <i class="fas fa-paper-plane me-1"></i> Resend Verification Email
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function resendVerification() {
    // TODO: Implement email resend functionality
    alert('Verification email sent!');
}
</script>
@endsection
