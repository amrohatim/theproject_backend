@extends('layouts.merchant')

@section('title', __('merchant.add_new_service_title'))
@section('header', __('merchant.add_new_service_title'))

@push('styles')
@if(app()->getLocale() === 'ar')
<link href="{{ asset('css/merchant-services-rtl.css') }}" rel="stylesheet">
@endif
<style>
    .merchant-availability-section {
        background: var(--discord-dark);
        border: 1px solid var(--discord-darkest);
        border-radius: 12px;
        padding: 20px;
        margin-top: 10px;
    }

    .merchant-availability-section .form-label {
        color: var(--discord-lightest);
        font-weight: 600;
    }

    .availability-hint {
        color: var(--discord-light);
        font-size: 13px;
        margin-bottom: 16px;
    }

    .merchant-day-checkbox {
        display: none;
    }

    .merchant-day-label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border: 1px solid var(--discord-darkest);
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 14px;
        font-weight: 600;
        color: var(--discord-lightest);
        background: linear-gradient(145deg, rgba(32, 34, 37, 0.9), rgba(44, 47, 51, 0.9));
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        text-align: center;
        position: relative;
        min-height: 54px;
        box-shadow: inset 0 0 0 0 rgba(67, 181, 129, 0.35);
    }

    .merchant-day-label .merchant-day-text {
        display: flex;
        flex-direction: column;
        gap: 2px;
        text-align: inherit;
        line-height: 1.2;
    }

    .merchant-day-label .merchant-day-check {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        border: 2px solid rgba(67, 181, 129, 0.35);
        color: rgba(67, 181, 129, 0.35);
        transition: all 0.2s ease-in-out;
    }

    .merchant-day-label:hover {
        border-color: rgba(67, 181, 129, 0.6);
        transform: translateY(-2px);
        box-shadow: 0 12px 18px rgba(67, 181, 129, 0.08);
    }

    .merchant-day-checkbox:checked + .merchant-day-label,
    .merchant-day-label.selected {
        background: linear-gradient(145deg, rgba(67, 181, 129, 0.18), rgba(67, 181, 129, 0.08));
        border-color: var(--discord-green);
        color: var(--discord-lightest);
        box-shadow: 0 12px 18px rgba(67, 181, 129, 0.18);
    }

    .merchant-day-checkbox:checked + .merchant-day-label .merchant-day-check,
    .merchant-day-label.selected .merchant-day-check {
        border-color: var(--discord-green);
        background: var(--discord-green);
        color: #ffffff;
    }

    #merchantDaysGrid.has-error-border .merchant-day-label {
        border-color: var(--discord-red);
    }

    [dir="rtl"] .day-name-en {
        display: none;
    }

    [dir="ltr"] .day-name-ar {
        display: none;
    }

    .time-picker-overlay {
        position: fixed !important;
        inset: 0 !important;
        background-color: rgba(0, 0, 0, 0.55) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        z-index: 1300 !important;
        backdrop-filter: blur(2px) !important;
    }

    .time-picker-overlay.hidden {
        display: none !important;
    }

    .time-picker-dialog {
        background: var(--discord-darkest, #eeeff1) !important;
        border: 1px solid var(--discord-dark, #e0e1e1) !important;
        border-radius: 12px !important;
        box-shadow: 0 20px 45px rgba(0, 0, 0, 0.35) !important;
        padding: 20px !important;
        width: 320px !important;
        max-width: 90vw !important;
        color: var(--discord-lightest, #ffffff) !important;
        font-family: inherit !important;
    }

    .time-picker-title {
        font-size: 16px !important;
        font-weight: 600 !important;
        margin-bottom: 16px !important;
        text-align: center !important;
        color: var(--discord-lightest, #ffffff) !important;
    }

    .time-picker-selectors {
        display: flex !important;
        gap: 12px !important;
        margin-bottom: 18px !important;
    }

    .time-picker-selectors label {
        flex: 1 !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 6px !important;
        font-size: 13px !important;
        color: var(--discord-light, #b9bbbe) !important;
    }

    .time-picker-selectors label span {
        font-weight: 500 !important;
        margin-bottom: 4px !important;
    }

    .time-picker-selectors select {
        border-radius: 8px !important;
        background: var(--discord-dark, #36393f) !important;
        border: 1px solid var(--discord-darkest, #2f3136) !important;
        color: var(--discord-lightest, #ffffff) !important;
        padding: 10px !important;
        font-size: 15px !important;
        width: 100% !important;
        outline: none !important;
        transition: border-color 0.2s ease !important;
    }

    .time-picker-selectors select:focus {
        border-color: var(--discord-green, #337cdb) !important;
        box-shadow: 0 0 0 2px rgba(67, 181, 129, 0.2) !important;
    }

    .time-picker-selectors select option {
        background: var(--discord-dark, #36393f) !important;
        color: var(--discord-lightest, #ffffff) !important;
        padding: 8px !important;
    }

    .time-picker-actions {
        display: flex !important;
        justify-content: flex-end !important;
        gap: 10px !important;
    }

    .time-picker-actions button {
        border-radius: 8px !important;
        padding: 8px 14px !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        border: none !important;
        cursor: pointer !important;
        transition: all 0.2s ease-in-out !important;
        outline: none !important;
    }

    .time-picker-cancel {
        background: transparent !important;
        color: var(--discord-light, #b9bbbe) !important;
        border: 1px solid var(--discord-dark, #36393f) !important;
    }

    .time-picker-cancel:hover {
        background: var(--discord-dark, #36393f) !important;
        color: var(--discord-lightest, #ffffff) !important;
    }

    .time-picker-apply {
        background: var(--discord-green, #337cdb) !important;
        color: #ffffff !important;
    }

    .time-picker-apply:hover {
        background: var(--discord-green, #337cdb) !important;
        filter: brightness(1.05) !important;
    }

    .form-control.time-picker-input {
        cursor: pointer;
    }

    #availability-validation-error ul {
        margin-bottom: 0;
    }
</style>
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

            <!-- Service Availability -->
            <div class="merchant-availability-section">
                <label class="form-label">
                    <i class="fas fa-clock me-2" style="color: var(--discord-green);"></i>
                    {{ __('merchant.service_availability') }}
                </label>
                <p class="availability-hint">{{ __('merchant.select_service_days_hint') }}</p>

                @php
                    $days = [
                        0 => ['en' => 'Sunday', 'ar' => 'الأحد'],
                        1 => ['en' => 'Monday', 'ar' => 'الإثنين'],
                        2 => ['en' => 'Tuesday', 'ar' => 'الثلاثاء'],
                        3 => ['en' => 'Wednesday', 'ar' => 'الأربعاء'],
                        4 => ['en' => 'Thursday', 'ar' => 'الخميس'],
                        5 => ['en' => 'Friday', 'ar' => 'الجمعة'],
                        6 => ['en' => 'Saturday', 'ar' => 'السبت'],
                    ];
                    $selectedDays = old('available_days', []);
                @endphp

                <div class="row g-2" id="merchantDaysGrid">
                    @foreach($days as $dayIndex => $dayNames)
                        <div class="col-6 col-md-4 col-lg-3">
                            <input type="checkbox"
                                   class="merchant-day-checkbox"
                                   id="day_{{ $dayIndex }}"
                                   name="available_days[]"
                                   value="{{ $dayIndex }}"
                                   {{ in_array($dayIndex, $selectedDays ?? []) ? 'checked' : '' }}>
                            <label for="day_{{ $dayIndex }}"
                                   class="merchant-day-label"
                                   role="button"
                                   tabindex="0"
                                   aria-pressed="{{ in_array($dayIndex, $selectedDays ?? []) ? 'true' : 'false' }}"
                                   data-day-index="{{ $dayIndex }}">
                                <span class="merchant-day-text">
                                    <span class="day-name-en">{{ $dayNames['en'] }}</span>
                                    <span class="day-name-ar">{{ $dayNames['ar'] }}</span>
                                </span>
                                <span class="merchant-day-check" aria-hidden="true">
                                    <i class="fas fa-check"></i>
                                </span>
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('available_days')
                    <div style="color: var(--discord-red); font-size: 12px; margin-top: 8px;">{{ $message }}</div>
                @enderror

                <div id="availability-validation-error" class="alert alert-danger d-none mt-3" role="alert"></div>

                <div class="row gy-3 mt-1">
                    <div class="col-md-6">
                        <label for="start_time" class="form-label">{{ __('merchant.start_time') }} <span style="color: var(--discord-red);">*</span></label>
                        <input type="time"
                               class="form-control time-picker-input @error('start_time') is-invalid @enderror"
                               id="start_time"
                               name="start_time"
                               value="{{ old('start_time', '09:00') }}"
                               required
                               style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                        @error('start_time')
                            <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="end_time" class="form-label">{{ __('merchant.end_time') }} <span style="color: var(--discord-red);">*</span></label>
                        <input type="time"
                               class="form-control time-picker-input @error('end_time') is-invalid @enderror"
                               id="end_time"
                               name="end_time"
                               value="{{ old('end_time', '17:00') }}"
                               required
                               style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                        @error('end_time')
                            <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
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
(function() {
    const availabilityMessages = {
        selectDay: @json(__('merchant.select_at_least_one_day')),
        invalidRange: @json(__('merchant.end_time_after_start_time'))
    };

    function setupDurationHelper() {
        const durationInput = document.getElementById('duration');
        if (!durationInput) {
            return;
        }

        const findHelper = function() {
            const siblings = durationInput.parentElement ? Array.from(durationInput.parentElement.children) : [];
            return siblings.find((element) => element !== durationInput && element.classList && element.classList.contains('duration-helper')) || null;
        };

        durationInput.addEventListener('input', function() {
            const minutes = parseInt(this.value, 10);
            const helperText = findHelper();

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

                const helper = helperText || (function() {
                    const small = document.createElement('small');
                    small.className = 'duration-helper';
                    small.style.color = 'var(--discord-light)';
                    small.style.fontSize = '11px';
                    durationInput.insertAdjacentElement('afterend', small);
                    return small;
                })();

                helper.textContent = `(${durationText})`;
            } else if (helperText) {
                helperText.remove();
            }
        });
    }

    function getDayCheckboxes() {
        return document.querySelectorAll('.merchant-day-checkbox');
    }

    function toggleDayLabelState(checkbox) {
        const label = checkbox ? checkbox.nextElementSibling : null;
        if (!label) {
            return;
        }

        if (checkbox.checked) {
            label.classList.add('selected');
        } else {
            label.classList.remove('selected');
        }

        label.setAttribute('aria-pressed', checkbox.checked ? 'true' : 'false');
    }

    function updateDaysGridBorder() {
        const daysGrid = document.getElementById('merchantDaysGrid');
        if (!daysGrid) {
            return;
        }
        const selectedDays = document.querySelectorAll('.merchant-day-checkbox:checked').length;
        daysGrid.classList.toggle('has-error-border', selectedDays === 0);
    }

    function setupAvailabilityControls() {
        const dayCheckboxes = getDayCheckboxes();
        dayCheckboxes.forEach((checkbox) => {
            toggleDayLabelState(checkbox);
            checkbox.addEventListener('change', function() {
                toggleDayLabelState(this);
                updateDaysGridBorder();
                hideAvailabilityErrors();
            });
            const label = checkbox.nextElementSibling;
            if (label) {
                label.addEventListener('keydown', function(event) {
                    if (event.key === ' ' || event.key === 'Enter') {
                        event.preventDefault();
                        checkbox.checked = !checkbox.checked;
                        checkbox.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });
            }
        });
        updateDaysGridBorder();
    }

    function isEndTimeAfterStart(start, end) {
        if (!start || !end) {
            return true;
        }
        const startDate = new Date(`2000-01-01T${start}`);
        const endDate = new Date(`2000-01-01T${end}`);
        return endDate > startDate;
    }

    function validateAvailability() {
        const errors = [];
        const selectedDays = document.querySelectorAll('.merchant-day-checkbox:checked').length;
        if (selectedDays === 0) {
            errors.push(availabilityMessages.selectDay);
        }

        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');

        if (startTimeInput && endTimeInput && startTimeInput.value && endTimeInput.value) {
            if (!isEndTimeAfterStart(startTimeInput.value, endTimeInput.value)) {
                errors.push(availabilityMessages.invalidRange);
            }
        }

        return errors;
    }

    function showAvailabilityErrors(errors) {
        const alertBox = document.getElementById('availability-validation-error');
        const daysGrid = document.getElementById('merchantDaysGrid');
        if (!alertBox) {
            return;
        }

        if (!errors || errors.length === 0) {
            alertBox.classList.add('d-none');
            alertBox.innerHTML = '';
            if (daysGrid) {
                daysGrid.classList.remove('has-error-border');
            }
            return;
        }

        alertBox.classList.remove('d-none');
        alertBox.innerHTML = '<ul class="mb-0 ps-3">' + errors.map((error) => `<li>${error}</li>`).join('') + '</ul>';

        if (daysGrid) {
            const hasDayError = errors.includes(availabilityMessages.selectDay);
            daysGrid.classList.toggle('has-error-border', hasDayError);
        }
    }

    function hideAvailabilityErrors() {
        showAvailabilityErrors([]);
    }

    function setupAvailabilityValidation() {
        const form = document.querySelector('form');
        if (!form) {
            return;
        }

        form.addEventListener('submit', function(event) {
            const errors = validateAvailability();
            if (errors.length > 0) {
                event.preventDefault();
                showAvailabilityErrors(errors);
            } else {
                hideAvailabilityErrors();
            }
        });

        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');

        if (startTimeInput && endTimeInput) {
            const handleTimeChange = function() {
                if (startTimeInput.value && endTimeInput.value) {
                    if (!isEndTimeAfterStart(startTimeInput.value, endTimeInput.value)) {
                        endTimeInput.setCustomValidity(availabilityMessages.invalidRange);
                    } else {
                        endTimeInput.setCustomValidity('');
                    }
                } else {
                    endTimeInput.setCustomValidity('');
                }
                hideAvailabilityErrors();
            };

            startTimeInput.addEventListener('change', handleTimeChange);
            endTimeInput.addEventListener('change', handleTimeChange);
        }

        getDayCheckboxes().forEach((checkbox) => {
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    hideAvailabilityErrors();
                }
            });
        });
    }

    function initializeTimePickerControls() {
        ['start_time', 'end_time'].forEach((id) => {
            const input = document.getElementById(id);
            if (!input) {
                return;
            }
            setupTimePickerInput(input);
        });
    }

    function setupTimePickerInput(input) {
        // Make input readonly to prevent direct editing
        input.setAttribute('readonly', 'readonly');
        
        // Add visual cue that this is clickable
        input.style.cursor = 'pointer';
        
        // Add a data attribute to mark this as a time picker input
        input.setAttribute('data-time-picker', 'true');

        const allowedKeys = new Set(['Tab', 'Shift', 'ArrowLeft', 'ArrowRight', 'Home', 'End', 'Escape']);

        input.addEventListener('keydown', function(event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                openTimePicker(input);
                return;
            }

            if (!allowedKeys.has(event.key)) {
                event.preventDefault();
            }
        });

        const activatePicker = function(event) {
            console.log('Time picker activation triggered by:', event.type);
            
            if (event && event.cancelable) {
                event.preventDefault();
            }
            
            if (document.body.dataset.timePickerOpen === 'true') {
                console.log('Time picker already open, ignoring activation');
                return;
            }
            
            input.focus({ preventScroll: true });
            
            // Try native picker first
            if (typeof input.showPicker === 'function') {
                try {
                    input.showPicker();
                    console.log('Native time picker opened');
                    return;
                } catch (error) {
                    console.log('Native time picker failed, falling back to custom picker', error);
                    // Fallback to custom picker
                }
            }
            
            console.log('Opening custom time picker for', input.id);
            openTimePicker(input);
        };

        // Multiple event handlers to ensure it works across all devices and browsers
        input.addEventListener('mousedown', activatePicker);
        input.addEventListener('pointerdown', activatePicker);
        input.addEventListener('click', activatePicker);
        input.addEventListener('touchstart', activatePicker);
        input.addEventListener('touchend', activatePicker);
        
        // Also handle focus events
        input.addEventListener('focus', function(event) {
            console.log('Focus event on time input:', input.id);
            
            if (document.activeElement === input) {
                if (document.body.dataset.timePickerOpen === 'true') {
                    console.log('Time picker already open on focus, ignoring');
                    return;
                }
                
                // Try native picker first
                if (typeof input.showPicker === 'function') {
                    try {
                        input.showPicker();
                        console.log('Native time picker opened on focus');
                        return;
                    } catch (error) {
                        console.log('Native time picker failed on focus, falling back to custom picker', error);
                        // Fallback to custom picker
                    }
                }
                
                console.log('Opening custom time picker on focus for', input.id);
                openTimePicker(input);
            }
        });

        // Prevent paste and drop to maintain control over the input format
        ['paste', 'drop'].forEach((evt) => {
            input.addEventListener(evt, function(event) {
                event.preventDefault();
            });
        });
    }

    function openTimePicker(input) {
        console.log('Opening time picker for input:', input.id);
        
        // Get or create the overlay
        const overlay = getOrCreateTimePickerOverlay(input);
        if (!overlay) {
            console.error('Failed to create time picker overlay');
            return;
        }
        
        const { hourSelect, minuteSelect, close } = overlay._timePicker;

        // Parse the current time value or use default
        const defaultTime = input.value || input.getAttribute('value') || '09:00';
        console.log('Current time value:', defaultTime);
        
        try {
            const [hourValue, minuteValue] = defaultTime.split(':');
            hourSelect.value = String(hourValue).padStart(2, '0');
            minuteSelect.value = String(minuteValue).padStart(2, '0');
            
            console.log('Set hour to:', hourSelect.value, 'and minute to:', minuteSelect.value);
        } catch (error) {
            console.error('Error parsing time value:', error);
            // Use safe defaults
            hourSelect.value = '09';
            minuteSelect.value = '00';
        }

        // Show the overlay
        overlay.classList.remove('hidden');
        overlay.style.display = 'flex'; // Ensure it's displayed as flex
        overlay.dataset.activeInput = input.id;
        overlay.setAttribute('aria-hidden', 'false');
        document.body.dataset.timePickerOpen = 'true';
        document.body.style.overflow = 'hidden';
        
        console.log('Time picker overlay shown for:', input.id);

        // Focus the dialog
        const dialog = overlay.querySelector('.time-picker-dialog');
        dialog.setAttribute('tabindex', '-1');

        // Use setTimeout to ensure the DOM has updated
        setTimeout(() => {
            try {
                dialog.focus();
                hourSelect.focus();
                console.log('Focus set on hour select');
            } catch (error) {
                console.error('Error setting focus:', error);
            }
        }, 50);

        // Handle escape key
        const handleEscape = function(event) {
            if (event.key === 'Escape') {
                event.preventDefault();
                close();
                document.removeEventListener('keydown', handleEscape);
                console.log('Time picker closed via Escape key');
            }
        };

        document.addEventListener('keydown', handleEscape);
        overlay._timePicker.handleEscape = handleEscape;
    }

    function getOrCreateTimePickerOverlay(input) {
        console.log('Getting or creating time picker overlay for:', input.id);
        
        const overlayId = `${input.id}-time-picker-overlay`;
        let overlay = document.getElementById(overlayId);

        if (overlay) {
            console.log('Existing overlay found for:', input.id);
            return overlay;
        }

        console.log('Creating new time picker overlay for:', input.id);
        
        try {
            overlay = document.createElement('div');
            overlay.id = overlayId;
            overlay.className = 'time-picker-overlay hidden';
            
            // Ensure proper styling
            overlay.style.position = 'fixed';
            overlay.style.inset = '0';
            overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.55)';
            overlay.style.display = 'none'; // Start hidden
            overlay.style.alignItems = 'center';
            overlay.style.justifyContent = 'center';
            overlay.style.zIndex = '1300';
            overlay.style.backdropFilter = 'blur(2px)';
            
            overlay.setAttribute('aria-hidden', 'true');
            overlay.setAttribute('role', 'presentation');
            
            // Create the dialog content
            overlay.innerHTML = `
                <div class="time-picker-dialog" role="dialog" aria-modal="true">
                    <h3 class="time-picker-title">{{ __('merchant.start_time') }} / {{ __('merchant.end_time') }}</h3>
                    <div class="time-picker-selectors">
                        <label>
                            <span>Hour</span>
                            <select class="time-picker-hour"></select>
                        </label>
                        <label>
                            <span>Minute</span>
                            <select class="time-picker-minute"></select>
                        </label>
                    </div>
                    <div class="time-picker-actions">
                        <button type="button" class="time-picker-cancel">{{ __('merchant.cancel') }}</button>
                        <button type="button" class="time-picker-apply">{{ __('merchant.save_changes') }}</button>
                    </div>
                </div>
            `;

            // Append to body
            document.body.appendChild(overlay);
            console.log('Overlay appended to document body');

            // Get references to elements
            const dialog = overlay.querySelector('.time-picker-dialog');
            const hourSelect = overlay.querySelector('.time-picker-hour');
            const minuteSelect = overlay.querySelector('.time-picker-minute');
            const cancelButton = overlay.querySelector('.time-picker-cancel');
            const applyButton = overlay.querySelector('.time-picker-apply');
            
            // Ensure dialog has proper styling
            if (dialog) {
                dialog.style.backgroundColor = 'white';
                dialog.style.border = '1px solid #2f3136';
                dialog.style.borderRadius = '12px';
                dialog.style.padding = '20px';
                dialog.style.width = '320px';
                dialog.style.maxWidth = '90vw';
                dialog.style.color = '#ffffff';
                dialog.style.boxShadow = '0 20px 45px rgba(0, 0, 0, 0.35)';
                dialog.style.fontFamily = 'inherit';
            }

            // Style the title
            const title = overlay.querySelector('.time-picker-title');
            if (title) {
                title.style.fontSize = '16px';
                title.style.fontWeight = '600';
                title.style.marginBottom = '16px';
                title.style.textAlign = 'center';
                title.style.color = 'black';
            }

            // Style the selectors container
            const selectors = overlay.querySelector('.time-picker-selectors');
            if (selectors) {
                selectors.style.display = 'flex';
                selectors.style.gap = '12px';
                selectors.style.marginBottom = '18px';
            }

            // Style the labels
            const labels = overlay.querySelectorAll('.time-picker-selectors label');
            labels.forEach(label => {
                label.style.flex = '1';
                label.style.display = 'flex';
                label.style.flexDirection = 'column';
                label.style.gap = '6px';
                label.style.fontSize = '13px';
                label.style.color = 'black';
                
                const span = label.querySelector('span');
                if (span) {
                    span.style.fontWeight = '500';
                    span.style.marginBottom = '4px';
                }
            });

            // Style the select elements
            const selects = overlay.querySelectorAll('.time-picker-selectors select');
            selects.forEach(select => {
                select.style.borderRadius = '8px';
                select.style.background = 'white';
                select.style.border = '1px solid gray';
                select.style.color = 'black';
                select.style.padding = '10px';
                select.style.fontSize = '15px';
                select.style.width = '100%';
                select.style.outline = 'none';
                select.style.transition = 'border-color 0.2s ease';
                
                // Focus styles
                select.addEventListener('focus', function() {
                    this.style.borderColor = '#337cdb';
                    this.style.boxShadow = '0 0 0 2px rgba(51, 124, 219, 0.2)';
                });
                
                select.addEventListener('blur', function() {
                    this.style.borderColor = '#2f3136';
                    this.style.boxShadow = 'none';
                });
            });

            // Style the actions container
            const actions = overlay.querySelector('.time-picker-actions');
            if (actions) {
                actions.style.display = 'flex';
                actions.style.justifyContent = 'flex-end';
                actions.style.gap = '10px';
            }

            // Style the buttons
            const buttons = overlay.querySelectorAll('.time-picker-actions button');
            buttons.forEach(button => {
                button.style.borderRadius = '8px';
                button.style.padding = '8px 14px';
                button.style.fontSize = '14px';
                button.style.fontWeight = '600';
                button.style.border = 'none';
                button.style.cursor = 'pointer';
                button.style.transition = 'all 0.2s ease-in-out';
                button.style.outline = 'none';
            });

            // Style cancel button specifically
            if (cancelButton) {
                cancelButton.style.background = 'white';
                cancelButton.style.color = 'black';
                cancelButton.style.border = '1px solid gray';
                
                cancelButton.addEventListener('mouseenter', function() {
                    this.style.background = '#36393f';
                    this.style.color = '#ffffff';
                });
                
                cancelButton.addEventListener('mouseleave', function() {
                    this.style.background = 'white';
                    this.style.color = 'black';
                });
            }

            // Style apply button specifically
            if (applyButton) {
                applyButton.style.background = '#337cdb';
                applyButton.style.color = '#ffffff';
                
                applyButton.addEventListener('mouseenter', function() {
                    this.style.background = '#337cdb';
                });
                
                applyButton.addEventListener('mouseleave', function() {
                    this.style.background = '#337cdb';
                });
            }

            // Populate hour options
            for (let hour = 0; hour < 24; hour++) {
                const option = document.createElement('option');
                option.value = String(hour).padStart(2, '0');
                option.textContent = String(hour).padStart(2, '0');
                hourSelect.appendChild(option);
            }

            // Populate minute options
            for (let minute = 0; minute < 60; minute++) {
                const option = document.createElement('option');
                option.value = String(minute).padStart(2, '0');
                option.textContent = String(minute).padStart(2, '0');
                minuteSelect.appendChild(option);
            }

            // Define close function
            const close = function() {
                console.log('Closing time picker overlay for:', input.id);
                overlay.classList.add('hidden');
                overlay.style.display = 'none';
                overlay.setAttribute('aria-hidden', 'true');
                
                const escapeHandler = overlay._timePicker && overlay._timePicker.handleEscape;
                if (escapeHandler) {
                    document.removeEventListener('keydown', escapeHandler);
                    delete overlay._timePicker.handleEscape;
                }
                
                delete overlay.dataset.activeInput;
                delete document.body.dataset.timePickerOpen;
                document.body.style.overflow = '';
            };

            // Cancel button handler
            cancelButton.addEventListener('click', function() {
                console.log('Cancel button clicked');
                close();
            });

            // Apply button handler
            applyButton.addEventListener('click', function() {
                console.log('Apply button clicked');
                const hour = hourSelect.value.padStart(2, '0');
                const minute = minuteSelect.value.padStart(2, '0');
                const formatted = `${hour}:${minute}`;

                console.log('Setting time value to:', formatted);
                input.value = formatted;
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.dispatchEvent(new Event('change', { bubbles: true }));
                close();
            });

            // Close when clicking outside
            overlay.addEventListener('click', function(event) {
                if (event.target === overlay) {
                    console.log('Clicked outside dialog, closing');
                    close();
                }
            });

            // Store references
            overlay._timePicker = {
                hourSelect,
                minuteSelect,
                close
            };

            console.log('Time picker overlay created successfully for:', input.id);
            return overlay;
        } catch (error) {
            console.error('Error creating time picker overlay:', error);
            return null;
        }
    }

    const initializePageScripts = function() {
        setupDurationHelper();
        setupAvailabilityControls();
        setupAvailabilityValidation();
        initializeTimePickerControls();
        hideAvailabilityErrors();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializePageScripts);
    } else {
        initializePageScripts();
    }
})();
</script>
@endsection
