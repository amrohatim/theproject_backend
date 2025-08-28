@extends('layouts.merchant')

@section('title', __('merchant.add_new_service_title'))
@section('header', __('merchant.add_new_service_title'))

@push('styles')
@if(app()->getLocale() === 'ar')
<link href="{{ asset('css/merchant-services-rtl.css') }}" rel="stylesheet">
@endif
@endpush

@section('content')
<div class="merchant-services-page" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<!-- Header Section -->
<div class="discord-card">
    <div class="discord-card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 style="margin: 0; color: var(--discord-lightest); font-size: 24px; font-weight: 600;">
                    <i class="fas fa-plus me-2" style="color: var(--discord-green);"></i>
                    {{ __('merchant.add_new_service_title') }}
                </h2>
                <p style="margin: 8px 0 0 0; color: var(--discord-light); font-size: 14px;">
                    {{ __('merchant.create_new_service_description') }}
                </p>
            </div>
            <div>
                <a href="{{ route('merchant.services.index') }}" class="discord-btn discord-btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> {{ __('merchant.back_to_services') }}
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Service Form -->
<div class="discord-card">
    <div class="discord-card-header">
        <i class="fas fa-edit me-2" style="color: var(--discord-green);"></i>
        {{ __('merchant.service_information') }}
    </div>
    <div class="discord-card-body">
        <form action="{{ route('merchant.services.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <!-- Service Image -->
                <div class="col-md-4 mb-4">
                    <x-image-upload
                        name="image"
                        label="{{ __('merchant.service_image') }}"
                        :error="$errors->first('image')"
                        container-class="mb-0" />
                </div>

                <!-- Service Details -->
                <div class="col-md-8">
                    <div class="row">
                        <!-- Service Name (Bilingual) -->
                        <div class="col-md-12 mb-3">
                            <label class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                                {{ __('merchant.service_name_required') }}
                            </label>

                            <!-- Language Switcher -->
                            <x-form-language-switcher field-name="service_name" />

                            <!-- English Service Name -->
                            <div data-language-field="service_name" data-language="en" class="active-language-field">
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name') }}"
                                       placeholder="{{ __('merchant.enter_service_name_english') }}"
                                       required
                                       style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                                @error('name')
                                    <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Arabic Service Name -->
                            <div data-language-field="service_name" data-language="ar" style="display: none;">
                                <input type="text"
                                       class="form-control @error('service_name_arabic') is-invalid @enderror"
                                       id="service_name_arabic"
                                       name="service_name_arabic"
                                       value="{{ old('service_name_arabic') }}"
                                       placeholder="{{ __('merchant.enter_service_name_arabic') }}"
                                       required
                                       dir="rtl"
                                       style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                                @error('service_name_arabic')
                                    <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Category and Price -->
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                                {{ __('merchant.category_required') }}
                            </label>
                            <select class="form-select @error('category_id') is-invalid @enderror" 
                                    id="category_id" 
                                    name="category_id" 
                                    required
                                    style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                                <option value="">{{ __('merchant.select_category') }}</option>
                                @foreach($parentCategories as $parentCategory)
                                    <optgroup label="{{ $parentCategory->name }}">
                                        @foreach($parentCategory->children as $childCategory)
                                            <option value="{{ $childCategory->id }}" 
                                                    {{ old('category_id') == $childCategory->id ? 'selected' : '' }}>
                                                {{ $childCategory->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                                {{ __('merchant.price_aed_required') }}
                            </label>
                            <input type="text" 
                                   class="form-control @error('price') is-invalid @enderror" 
                                   id="price" 
                                   name="price" 
                                   value="{{ old('price') }}" 
                                   required
                                   pattern="[0-9]*"
                                   inputmode="numeric"
                                   onkeypress="return /[0-9]/i.test(event.key)"
                                   style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                            @error('price')
                                <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Duration and Availability -->
                        <div class="col-md-6 mb-3">
                            <label for="duration" class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                                {{ __('merchant.duration_minutes') }}
                            </label>
                            <input type="number" 
                                   class="form-control @error('duration') is-invalid @enderror" 
                                   id="duration" 
                                   name="duration" 
                                   value="{{ old('duration') }}" 
                                   min="1"
                                   style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                            @error('duration')
                                <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                                {{ __('merchant.service_options') }}
                            </label>
                            <div class="d-flex gap-3">
                                <div class="form-check {{app()->getLocale() === 'ar' ? 'space-x-6' : ''}}">
                                    <input class="form-check-input" type="checkbox" id="is_available" name="is_available" value="1" {{ old('is_available', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_available" style="color: var(--discord-light);">
                                        {{ __('merchant.available') }}
                                    </label>
                                </div>
                                <div class="form-check {{app()->getLocale() === 'ar' ? 'space-x-6' : ''}}">
                                    <input class="form-check-input" type="checkbox" id="home_service" name="home_service" value="1" {{ old('home_service') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="home_service" style="color: var(--discord-light);">
                                        {{ __('merchant.home_service') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description (Bilingual) -->
            <div class="mb-4">
                <label class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                    {{ __('merchant.service_description') }}
                </label>

                <!-- Language Switcher -->
                <x-form-language-switcher field-name="service_description" />

                <!-- English Description -->
                <div data-language-field="service_description" data-language="en" class="active-language-field">
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              id="description"
                              name="description"
                              rows="4"
                              placeholder="{{ __('merchant.enter_service_description_english') }}"
                              style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">{{ old('description') }}</textarea>
                    @error('description')
                        <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Arabic Description -->
                <div data-language-field="service_description" data-language="ar" style="display: none;">
                    <textarea class="form-control @error('service_description_arabic') is-invalid @enderror"
                              id="service_description_arabic"
                              name="service_description_arabic"
                              rows="4"
                              placeholder="{{ __('merchant.enter_service_description_arabic') }}"
                              dir="rtl"
                              style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">{{ old('service_description_arabic') }}</textarea>
                    @error('service_description_arabic')
                        <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('merchant.services.index') }}" class="discord-btn discord-btn-secondary">
                    <i class="fas fa-times me-1"></i> {{ __('merchant.cancel') }}
                </a>
                <button type="submit" class="discord-btn">
                    <i class="fas fa-save me-1"></i> {{ __('merchant.create_service') }}
                </button>
            </div>
        </form>
    </div>
</div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Duration helper text
    $('#duration').on('input', function() {
        const minutes = parseInt($(this).val());
        if (minutes) {
            const hours = Math.floor(minutes / 60);
            const remainingMinutes = minutes % 60;
            let durationText = '';

            if (hours > 0) {
                durationText += hours + ' ' + (hours > 1 ? '{{ __('merchant.hours') }}' : '{{ __('merchant.hour') }}');
                if (remainingMinutes > 0) {
                    durationText += ' ' + remainingMinutes + ' ' + (remainingMinutes > 1 ? '{{ __('merchant.minutes') }}' : '{{ __('merchant.minute') }}');
                }
            } else {
                durationText = minutes + ' ' + (minutes > 1 ? '{{ __('merchant.minutes') }}' : '{{ __('merchant.minute') }}');
            }

            // Show duration text below input
            let helperText = $(this).siblings('.duration-helper');
            if (helperText.length === 0) {
                helperText = $('<small class="duration-helper" style="color: var(--discord-light); font-size: 11px;"></small>');
                $(this).after(helperText);
            }
            helperText.text('(' + durationText + ')');
        } else {
            $(this).siblings('.duration-helper').remove();
        }
    });
});
</script>
@endsection
