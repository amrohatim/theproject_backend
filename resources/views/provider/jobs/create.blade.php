@extends('layouts.provider')

@section('title', __('messages.add_job'))

@section('styles')
<style>
    

    [dir="rtl"] .onsite-check {
        flex-direction: row-reverse;
          display: flex;
        align-items: center;
        gap: 24px;
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <div style="width: 40px; height: 40px; background-color: var(--discord-primary); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
            <i class="fas fa-briefcase text-white"></i>
        </div>
        <div>
            <h4 class="mb-0">{{ __('messages.post_job') }}</h4>
            <p class="text-muted mb-0" style="font-size: 14px; color: var(--discord-light);">{{ __('messages.manage_jobs') }}</p>
        </div>
    </div>
</div>

@if ($errors->any())
    <div class="alert-container" style="margin-bottom: 20px;">
        <div class="discord-alert discord-alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>
            <span>{{ __('messages.error') }}</span>
        </div>
        <div class="mt-2" style="color: var(--discord-light);">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="discord-card">
    <div class="discord-card-header">
        <i class="fas fa-plus me-2" style="color: var(--discord-primary);"></i>
        {{ __('messages.add_job') }}
    </div>

    <form method="POST" action="{{ route('provider.jobs.store') }}">
        @csrf

        <div class="row g-3">
            <div class="col-md-6">
                <label for="title" class="form-label">{{ __('messages.title') }}</label>
                <input id="title" name="title" type="text" value="{{ old('title') }}" required  class="discord-input bg-white border border-gray-300 w-100">
            </div>
            <div class="col-md-6">
                <label for="salary" class="form-label">{{ __('messages.salary') }} ({{ __('messages.optional') }})</label>
                <input id="salary" name="salary" type="number" min="0" value="{{ old('salary') }}" class="discord-input bg-white border border-gray-300 w-100">
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-12">
                <label for="description" class="form-label">{{ __('messages.description') }}</label>
                <textarea id="description" name="description" rows="4" required class="discord-input bg-white border border-gray-300 w-100">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label for="nice_to_have" class="form-label">{{ __('messages.nice_to_have') }} ({{ __('messages.optional') }})</label>
                <textarea id="nice_to_have" name="nice_to_have" rows="3" class="discord-input bg-white border border-gray-300 w-100">{{ old('nice_to_have') }}</textarea>
            </div>
            <div class="col-md-6">
                <label for="deadline" class="form-label">{{ __('messages.deadline') }} ({{ __('messages.optional') }})</label>
                <input id="deadline" name="deadline" type="date" value="{{ old('deadline') }}" class="discord-input bg-white border border-gray-300 w-100">
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label for="type" class="form-label">{{ __('messages.job_type') }}</label>
                <select id="type" name="type" required class="discord-input bg-white border border-gray-300 w-100">
                    <option value="" disabled {{ old('type') ? '' : 'selected' }}>{{ __('messages.select_job_type') }}</option>
                    @foreach ($parentCategories as $category)
                        <option value="{{ $category->id }}" {{ old('type') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                    <option value="other" {{ old('type') === 'other' ? 'selected' : '' }}>{{ __('messages.other') }}</option>
                </select>
            </div>
            <div class="col-md-6" id="type-other-wrapper" style="display: none;">
                <label for="type_other" class="form-label">{{ __('messages.job_type_other') }}</label>
                <input id="type_other" name="type_other" type="text" value="{{ old('type_other') }}" class="discord-input bg-white border border-gray-300 w-100">
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label for="location" class="form-label">{{ __('messages.location') }}</label>
                <input id="location" name="location" type="text" value="{{ old('location') }}" required class="discord-input bg-white border border-gray-300 w-100">
            </div>
            <div class="col-md-6 d-flex align-items-center" style="padding-top: 30px;">
                <div class="onsite-check">
                    <input class="form-check-input" type="checkbox" name="onsite" value="1" id="onsite" {{ old('onsite') ? 'checked' : '' }}>
                    <label class="form-check-label" for="onsite">{{ __('messages.onsite') }}</label>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="discord-btn">
                <i class="fas fa-save me-2"></i> {{ __('messages.save_job') }}
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    const typeSelect = document.getElementById('type');
    const typeOtherWrapper = document.getElementById('type-other-wrapper');
    const typeOtherInput = document.getElementById('type_other');

    function toggleTypeOther() {
        const isOther = typeSelect.value === 'other';
        typeOtherWrapper.style.display = isOther ? 'block' : 'none';
        typeOtherInput.required = isOther;
        if (!isOther) {
            typeOtherInput.value = '';
        }
    }

    typeSelect.addEventListener('change', toggleTypeOther);
    toggleTypeOther();
</script>
@endsection
