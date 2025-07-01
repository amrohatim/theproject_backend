@extends('layouts.merchant')

@section('title', 'Personal Settings')
@section('header', 'Personal Settings')

@section('content')
<!-- Personal Information Section -->
<div class="discord-card">
    <div class="discord-card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 style="margin: 0; color: var(--discord-lightest); font-size: 24px; font-weight: 600;">
                    <i class="fas fa-user-cog me-2"></i>Personal Information
                </h3>
                <p style="margin: 8px 0 0 0; color: var(--discord-light); font-size: 14px;">
                    Update your personal details and account settings
                </p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center mb-4" style="background-color: var(--discord-green); border: none; color: white;">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('merchant.settings.personal.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <!-- Name -->
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label" style="color: var(--discord-lightest); font-weight: 500;">
                        <i class="fas fa-user me-1"></i>Full Name
                    </label>
                    <input type="text" 
                           class="form-control @error('name') is-invalid @enderror" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $user->name) }}" 
                           required
                           style="background-color: var(--discord-darkest); border: 1px solid #ddd; color: var(--discord-lightest);">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label" style="color: var(--discord-lightest); font-weight: 500;">
                        <i class="fas fa-envelope me-1"></i>Email Address
                    </label>
                    <input type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email" 
                           value="{{ old('email', $user->email) }}" 
                           required
                           style="background-color: var(--discord-darkest); border: 1px solid #ddd; color: var(--discord-lightest);">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Phone -->
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label" style="color: var(--discord-lightest); font-weight: 500;">
                        <i class="fas fa-phone me-1"></i>Phone Number
                    </label>
                    <input type="text" 
                           class="form-control @error('phone') is-invalid @enderror" 
                           id="phone" 
                           name="phone" 
                           value="{{ old('phone', $user->phone) }}"
                           style="background-color: var(--discord-darkest); border: 1px solid #ddd; color: var(--discord-lightest);">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="discord-btn">
                    <i class="fas fa-save me-1"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Password Change Section -->
<div class="discord-card mt-4">
    <div class="discord-card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 style="margin: 0; color: var(--discord-lightest); font-size: 24px; font-weight: 600;">
                    <i class="fas fa-lock me-2"></i>Change Password
                </h3>
                <p style="margin: 8px 0 0 0; color: var(--discord-light); font-size: 14px;">
                    Update your account password for security
                </p>
            </div>
        </div>

        <form action="{{ route('merchant.settings.personal.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <!-- Current Password -->
                <div class="col-md-12 mb-3">
                    <label for="current_password" class="form-label" style="color: var(--discord-lightest); font-weight: 500;">
                        <i class="fas fa-key me-1"></i>Current Password
                    </label>
                    <input type="password" 
                           class="form-control @error('current_password') is-invalid @enderror" 
                           id="current_password" 
                           name="current_password"
                           style="background-color: var(--discord-darkest); border: 1px solid #ddd; color: var(--discord-lightest);">
                    @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- New Password -->
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label" style="color: var(--discord-lightest); font-weight: 500;">
                        <i class="fas fa-lock me-1"></i>New Password
                    </label>
                    <input type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           id="password" 
                           name="password"
                           style="background-color: var(--discord-darkest); border: 1px solid #ddd; color: var(--discord-lightest);">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label" style="color: var(--discord-lightest); font-weight: 500;">
                        <i class="fas fa-lock me-1"></i>Confirm New Password
                    </label>
                    <input type="password" 
                           class="form-control" 
                           id="password_confirmation" 
                           name="password_confirmation"
                           style="background-color: var(--discord-darkest); border: 1px solid #ddd; color: var(--discord-lightest);">
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="discord-btn">
                    <i class="fas fa-save me-1"></i> Update Password
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Account Information -->
<div class="discord-card mt-4">
    <div class="discord-card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 style="margin: 0; color: var(--discord-lightest); font-size: 24px; font-weight: 600;">
                    <i class="fas fa-info-circle me-2"></i>Account Information
                </h3>
                <p style="margin: 8px 0 0 0; color: var(--discord-light); font-size: 14px;">
                    Your account details and verification status
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="border-color: var(--discord-darkest);">
                    <span style="color: var(--discord-light);">Account Type:</span>
                    <span style="color: var(--discord-lightest); font-weight: 500;">
                        <i class="fas fa-store me-1"></i>Merchant
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="border-color: var(--discord-darkest);">
                    <span style="color: var(--discord-light);">Email Status:</span>
                    <span style="color: {{ $user->email_verified_at ? 'var(--discord-green)' : 'var(--discord-red)' }}; font-weight: 500;">
                        <i class="fas fa-{{ $user->email_verified_at ? 'check-circle' : 'times-circle' }} me-1"></i>
                        {{ $user->email_verified_at ? 'Verified' : 'Not Verified' }}
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2">
                    <span style="color: var(--discord-light);">Phone Status:</span>
                    <span style="color: {{ $user->phone_verified_at ? 'var(--discord-green)' : 'var(--discord-red)' }}; font-weight: 500;">
                        <i class="fas fa-{{ $user->phone_verified_at ? 'check-circle' : 'times-circle' }} me-1"></i>
                        {{ $user->phone_verified_at ? 'Verified' : 'Not Verified' }}
                    </span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="border-color: var(--discord-darkest);">
                    <span style="color: var(--discord-light);">Member Since:</span>
                    <span style="color: var(--discord-lightest); font-weight: 500;">
                        {{ $user->created_at->format('M d, Y') }}
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="border-color: var(--discord-darkest);">
                    <span style="color: var(--discord-light);">Last Updated:</span>
                    <span style="color: var(--discord-lightest); font-weight: 500;">
                        {{ $user->updated_at->format('M d, Y') }}
                    </span>
                </div>
                @if($merchant)
                <div class="d-flex justify-content-between align-items-center py-2">
                    <span style="color: var(--discord-light);">Merchant Status:</span>
                    <span style="color: {{ $merchant->is_verified ? 'var(--discord-green)' : 'var(--discord-yellow)' }}; font-weight: 500;">
                        <i class="fas fa-{{ $merchant->is_verified ? 'check-circle' : 'clock' }} me-1"></i>
                        {{ $merchant->is_verified ? 'Verified' : 'Pending' }}
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
