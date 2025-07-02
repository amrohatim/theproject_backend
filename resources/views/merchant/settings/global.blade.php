@extends('layouts.merchant')

@section('title', 'Global Settings')
@section('header', 'Global Settings')

@section('content')
<!-- Business Information Section -->
<div class="discord-card">
    <div class="discord-card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 style="margin: 0; color: var(--discord-lightest); font-size: 24px; font-weight: 600;">
                    <i class="fas fa-building me-2"></i>Business Information
                </h3>
                <p style="margin: 8px 0 0 0; color: var(--discord-light); font-size: 14px;">
                    Update your business details and settings
                </p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center mb-4" style="background-color: var(--discord-green); border: none; color: white;">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('merchant.settings.global.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <!-- Business Name -->
                <div class="col-md-6 mb-3">
                    <label for="business_name" class="form-label" style="color: var(--discord-lightest); font-weight: 500;">
                        <i class="fas fa-store me-1"></i>Business Name *
                    </label>
                    <input type="text" 
                           class="form-control @error('business_name') is-invalid @enderror" 
                           id="business_name" 
                           name="business_name" 
                           value="{{ old('business_name', $merchant->business_name ?? '') }}" 
                           required
                           style="background-color: var(--discord-darkest); border: 1px solid #ddd; color: var(--discord-lightest);">
                    @error('business_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Business Type -->
                <div class="col-md-6 mb-3">
                    <label for="business_type" class="form-label" style="color: var(--discord-lightest); font-weight: 500;">
                        <i class="fas fa-tag me-1"></i>Business Type
                    </label>
                    <select class="form-control @error('business_type') is-invalid @enderror" 
                            id="business_type" 
                            name="business_type"
                            style="background-color: var(--discord-darkest); border: 1px solid #ddd; color: var(--discord-lightest);">
                        <option value="">Select Business Type</option>
                        <option value="retail" {{ old('business_type', $merchant->business_type ?? '') == 'retail' ? 'selected' : '' }}>Retail</option>
                        <option value="restaurant" {{ old('business_type', $merchant->business_type ?? '') == 'restaurant' ? 'selected' : '' }}>Restaurant</option>
                        <option value="services" {{ old('business_type', $merchant->business_type ?? '') == 'services' ? 'selected' : '' }}>Services</option>
                        <option value="fashion" {{ old('business_type', $merchant->business_type ?? '') == 'fashion' ? 'selected' : '' }}>Fashion</option>
                        <option value="electronics" {{ old('business_type', $merchant->business_type ?? '') == 'electronics' ? 'selected' : '' }}>Electronics</option>
                        <option value="health" {{ old('business_type', $merchant->business_type ?? '') == 'health' ? 'selected' : '' }}>Health & Beauty</option>
                        <option value="other" {{ old('business_type', $merchant->business_type ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('business_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Website -->
                <div class="col-md-6 mb-3">
                    <label for="website" class="form-label" style="color: var(--discord-lightest); font-weight: 500;">
                        <i class="fas fa-globe me-1"></i>Website URL
                    </label>
                    <input type="url"
                           class="form-control @error('website') is-invalid @enderror"
                           id="website"
                           name="website"
                           value="{{ old('website', $merchant->website ?? '') }}"
                           placeholder="https://example.com"
                           style="background-color: var(--discord-darkest); border: 1px solid #ddd; color: var(--discord-lightest);">
                    @error('website')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- City -->
                <div class="col-md-6 mb-3">
                    <label for="city" class="form-label" style="color: var(--discord-lightest); font-weight: 500;">
                        <i class="fas fa-city me-1"></i>City
                    </label>
                    <input type="text"
                           class="form-control @error('city') is-invalid @enderror"
                           id="city"
                           name="city"
                           value="{{ old('city', $merchant->city ?? '') }}"
                           placeholder="Enter your city"
                           style="background-color: var(--discord-darkest); border: 1px solid #ddd; color: var(--discord-lightest);">
                    @error('city')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Emirate -->
                <div class="col-md-6 mb-3">
                    <label for="emirate" class="form-label" style="color: var(--discord-lightest); font-weight: 500;">
                        <i class="fas fa-map-marker-alt me-1"></i>Emirate
                    </label>
                    <select class="form-control @error('emirate') is-invalid @enderror"
                            id="emirate"
                            name="emirate"
                            style="background-color: var(--discord-darkest); border: 1px solid #ddd; color: var(--discord-lightest);">
                        <option value="">Select Emirate</option>
                        <option value="Abu Dhabi" {{ old('emirate', $merchant->emirate ?? '') == 'Abu Dhabi' ? 'selected' : '' }}>Abu Dhabi</option>
                        <option value="Dubai" {{ old('emirate', $merchant->emirate ?? '') == 'Dubai' ? 'selected' : '' }}>Dubai</option>
                        <option value="Sharjah" {{ old('emirate', $merchant->emirate ?? '') == 'Sharjah' ? 'selected' : '' }}>Sharjah</option>
                        <option value="Ajman" {{ old('emirate', $merchant->emirate ?? '') == 'Ajman' ? 'selected' : '' }}>Ajman</option>
                        <option value="Umm Al Quwain" {{ old('emirate', $merchant->emirate ?? '') == 'Umm Al Quwain' ? 'selected' : '' }}>Umm Al Quwain</option>
                        <option value="Ras Al Khaimah" {{ old('emirate', $merchant->emirate ?? '') == 'Ras Al Khaimah' ? 'selected' : '' }}>Ras Al Khaimah</option>
                        <option value="Fujairah" {{ old('emirate', $merchant->emirate ?? '') == 'Fujairah' ? 'selected' : '' }}>Fujairah</option>
                    </select>
                    @error('emirate')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Address -->
                <div class="col-md-6 mb-3">
                    <label for="address" class="form-label" style="color: var(--discord-lightest); font-weight: 500;">
                        <i class="fas fa-map-pin me-1"></i>Business Address
                    </label>
                    <input type="text"
                           class="form-control @error('address') is-invalid @enderror"
                           id="address"
                           name="address"
                           value="{{ old('address', $merchant->address ?? '') }}"
                           placeholder="Enter your business address"
                           style="background-color: var(--discord-darkest); border: 1px solid #ddd; color: var(--discord-lightest);">
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Logo -->
                <div class="col-md-6 mb-3">
                    <label for="logo" class="form-label" style="color: var(--discord-lightest); font-weight: 500;">
                        <i class="fas fa-image me-1"></i>Business Logo
                    </label>
                    <input type="file"
                           class="form-control @error('logo') is-invalid @enderror"
                           id="logo"
                           name="logo"
                           accept="image/*"
                           style="background-color: var(--discord-darkest); border: 1px solid #ddd; color: var(--discord-lightest);">
                    <small class="form-text text-muted" style="color: var(--discord-light);">
                        Max size: 2MB. Formats: JPEG, PNG, JPG, GIF
                    </small>
                    @if($merchant && $merchant->logo)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $merchant->logo) }}" alt="Current Logo"
                                 style="max-width: 100px; max-height: 100px; object-fit: cover; border-radius: 8px;">
                        </div>
                    @endif
                    @error('logo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Description -->
                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label" style="color: var(--discord-lightest); font-weight: 500;">
                        <i class="fas fa-align-left me-1"></i>Business Description
                    </label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" 
                              name="description" 
                              rows="4"
                              placeholder="Describe your business, products, and services..."
                              style="background-color: var(--discord-darkest); border: 1px solid #ddd; color: var(--discord-lightest);">{{ old('description', $merchant->description ?? '') }}</textarea>
                    @error('description')
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

<!-- Delivery Settings Section -->
<div class="discord-card mt-4">
    <div class="discord-card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 style="margin: 0; color: var(--discord-lightest); font-size: 24px; font-weight: 600;">
                    <i class="fas fa-truck me-2"></i>Delivery Settings
                </h3>
                <p style="margin: 8px 0 0 0; color: var(--discord-light); font-size: 14px;">
                    Configure your delivery options and fees
                </p>
            </div>
        </div>

        <form action="{{ route('merchant.settings.global.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <!-- Delivery Capability -->
                <div class="col-md-12 mb-4">
                    <div class="form-check">
                        <input class="form-check-input" 
                               type="checkbox" 
                               id="delivery_capability" 
                               name="delivery_capability" 
                               value="1"
                               {{ old('delivery_capability', $merchant->delivery_capability ?? false) ? 'checked' : '' }}
                               style="background-color: var(--discord-darkest); border: 1px solid #ddd;">
                        <label class="form-check-label" for="delivery_capability" style="color: var(--discord-lightest); font-weight: 500;">
                            <i class="fas fa-shipping-fast me-1"></i>We offer delivery services
                        </label>
                    </div>
                    <small class="text-muted" style="color: var(--discord-light);">
                        Check this if your business provides delivery services to customers
                    </small>
                </div>

                <!-- Delivery Fees (shown when delivery is enabled) -->
                <div id="delivery-fees-section" class="col-md-12" style="display: {{ old('delivery_capability', $merchant->delivery_capability ?? false) ? 'block' : 'none' }};">
                    <h5 style="color: var(--discord-lightest); margin-bottom: 15px;">
                        <i class="fas fa-dollar-sign me-1"></i>Delivery Fees
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="local_delivery_fee" class="form-label" style="color: var(--discord-lightest); font-weight: 500;">
                                Local Delivery (within city)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text" style="background-color: var(--discord-darkest); border: 1px solid #ddd; color: var(--discord-lightest);">$</span>
                                <input type="number" 
                                       class="form-control" 
                                       id="local_delivery_fee" 
                                       name="delivery_fees[local]" 
                                       value="{{ old('delivery_fees.local', $merchant->delivery_fees['local'] ?? '') }}"
                                       step="0.01"
                                       min="0"
                                       style="background-color: var(--discord-darkest); border: 1px solid #ddd; color: var(--discord-lightest);">
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="regional_delivery_fee" class="form-label" style="color: var(--discord-lightest); font-weight: 500;">
                                Regional Delivery
                            </label>
                            <div class="input-group">
                                <span class="input-group-text" style="background-color: var(--discord-darkest); border: 1px solid #ddd; color: var(--discord-lightest);">$</span>
                                <input type="number" 
                                       class="form-control" 
                                       id="regional_delivery_fee" 
                                       name="delivery_fees[regional]" 
                                       value="{{ old('delivery_fees.regional', $merchant->delivery_fees['regional'] ?? '') }}"
                                       step="0.01"
                                       min="0"
                                       style="background-color: var(--discord-darkest); border: 1px solid #ddd; color: var(--discord-lightest);">
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="national_delivery_fee" class="form-label" style="color: var(--discord-lightest); font-weight: 500;">
                                National Delivery
                            </label>
                            <div class="input-group">
                                <span class="input-group-text" style="background-color: var(--discord-darkest); border: 1px solid #ddd; color: var(--discord-lightest);">$</span>
                                <input type="number" 
                                       class="form-control" 
                                       id="national_delivery_fee" 
                                       name="delivery_fees[national]" 
                                       value="{{ old('delivery_fees.national', $merchant->delivery_fees['national'] ?? '') }}"
                                       step="0.01"
                                       min="0"
                                       style="background-color: var(--discord-darkest); border: 1px solid #ddd; color: var(--discord-lightest);">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="discord-btn">
                    <i class="fas fa-save me-1"></i> Save Delivery Settings
                </button>
            </div>
        </form>
    </div>
</div>

@if($merchant)
<!-- Business Status Section -->
<div class="discord-card mt-4">
    <div class="discord-card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 style="margin: 0; color: var(--discord-lightest); font-size: 24px; font-weight: 600;">
                    <i class="fas fa-chart-line me-2"></i>Business Status
                </h3>
                <p style="margin: 8px 0 0 0; color: var(--discord-light); font-size: 14px;">
                    Your current business verification and status information
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="border-color: var(--discord-darkest);">
                    <span style="color: var(--discord-light);">Business Status:</span>
                    <span style="color: {{ $merchant->is_verified ? 'var(--discord-green)' : 'var(--discord-yellow)' }}; font-weight: 500;">
                        <i class="fas fa-{{ $merchant->is_verified ? 'check-circle' : 'clock' }} me-1"></i>
                        {{ $merchant->is_verified ? 'Verified' : 'Pending Verification' }}
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="border-color: var(--discord-darkest);">
                    <span style="color: var(--discord-light);">Registration Date:</span>
                    <span style="color: var(--discord-lightest); font-weight: 500;">
                        {{ $merchant->created_at->format('M d, Y') }}
                    </span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="border-color: var(--discord-darkest);">
                    <span style="color: var(--discord-light);">Delivery Service:</span>
                    <span style="color: {{ $merchant->delivery_capability ? 'var(--discord-green)' : 'var(--discord-light)' }}; font-weight: 500;">
                        <i class="fas fa-{{ $merchant->delivery_capability ? 'check' : 'times' }} me-1"></i>
                        {{ $merchant->delivery_capability ? 'Enabled' : 'Disabled' }}
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2">
                    <span style="color: var(--discord-light);">Last Updated:</span>
                    <span style="color: var(--discord-lightest); font-weight: 500;">
                        {{ $merchant->updated_at->format('M d, Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deliveryCheckbox = document.getElementById('delivery_capability');
    const deliveryFeesSection = document.getElementById('delivery-fees-section');
    
    if (deliveryCheckbox && deliveryFeesSection) {
        deliveryCheckbox.addEventListener('change', function() {
            deliveryFeesSection.style.display = this.checked ? 'block' : 'none';
        });
    }
});
</script>
@endsection
