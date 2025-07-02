@extends('layouts.merchant')

@section('title', 'Registration Status')
@section('header', 'Registration Status')

@section('content')
<div class="discord-card">
    <div class="discord-card-body">
        <div class="text-center py-5">
            <i class="fas fa-clock fa-3x mb-3" style="color: var(--discord-yellow);"></i>
            <h4 style="color: var(--discord-lightest);">Registration Pending</h4>
            <p style="color: var(--discord-light);">Your merchant registration is currently being processed. Please complete all required steps.</p>
            
            <div class="mt-4">
                <a href="{{ route('register.merchant') }}" class="discord-btn">
                    <i class="fas fa-edit me-1"></i> Complete Registration
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
