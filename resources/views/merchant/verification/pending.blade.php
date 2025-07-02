@extends('layouts.merchant')

@section('title', 'Verification Pending')
@section('header', 'Verification Pending')

@section('content')
<div class="discord-card">
    <div class="discord-card-body">
        <div class="text-center py-5">
            <i class="fas fa-hourglass-half fa-3x mb-3" style="color: var(--discord-yellow);"></i>
            <h4 style="color: var(--discord-lightest);">Account Verification Pending</h4>
            <p style="color: var(--discord-light);">Your merchant account is currently under review by our admin team. You will be notified once the verification is complete.</p>
            
            <div class="mt-4">
                <a href="{{ route('merchant.dashboard') }}" class="discord-btn">
                    <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
