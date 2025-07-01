@extends('layouts.merchant')

@section('title', 'Edit Service')
@section('header', 'Edit Service')

@section('content')
<!-- Header Section -->
<div class="discord-card">
    <div class="discord-card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 style="margin: 0; color: var(--discord-lightest); font-size: 24px; font-weight: 600;">
                    <i class="fas fa-edit me-2" style="color: var(--discord-yellow);"></i>
                    Edit Service
                </h2>
                <p style="margin: 8px 0 0 0; color: var(--discord-light); font-size: 14px;">
                    Update service information for "{{ $service->name }}"
                </p>
            </div>
            <div>
                <a href="{{ route('merchant.services.index') }}" class="discord-btn discord-btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Services
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Service Form -->
<div class="discord-card">
    <div class="discord-card-header">
        <i class="fas fa-edit me-2" style="color: var(--discord-yellow);"></i>
        Service Information
    </div>
    <div class="discord-card-body">
        <form action="{{ route('merchant.services.update', $service->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <!-- Service Image -->
                <div class="col-md-4 mb-4">
                    <x-image-upload
                        name="image"
                        label="Service Image"
                        :current-image="$service->image"
                        :error="$errors->first('image')"
                        container-class="mb-0" />
                </div>

                <!-- Service Details -->
                <div class="col-md-8">
                    <div class="row">
                        <!-- Service Name -->
                        <div class="col-md-12 mb-3">
                            <label for="name" class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                                Service Name *
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $service->name) }}" 
                                   required
                                   style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                            @error('name')
                                <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Category and Price -->
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                                Category *
                            </label>
                            <select class="form-select @error('category_id') is-invalid @enderror" 
                                    id="category_id" 
                                    name="category_id" 
                                    required
                                    style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $service->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                                Price (AED) *
                            </label>
                            <input type="number" 
                                   class="form-control @error('price') is-invalid @enderror" 
                                   id="price" 
                                   name="price" 
                                   value="{{ old('price', $service->price) }}" 
                                   step="0.01" 
                                   min="0" 
                                   required
                                   style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                            @error('price')
                                <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Duration and Availability -->
                        <div class="col-md-6 mb-3">
                            <label for="duration" class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                                Duration (minutes)
                            </label>
                            <input type="number" 
                                   class="form-control @error('duration') is-invalid @enderror" 
                                   id="duration" 
                                   name="duration" 
                                   value="{{ old('duration', $service->duration) }}" 
                                   min="1"
                                   style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">
                            @error('duration')
                                <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                                Service Options
                            </label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_available" name="is_available" value="1" {{ old('is_available', $service->is_available) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_available" style="color: var(--discord-light);">
                                        Available
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="home_service" name="home_service" value="1" {{ old('home_service', $service->home_service) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="home_service" style="color: var(--discord-light);">
                                        Home Service
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label for="description" class="form-label" style="color: var(--discord-lightest); font-weight: 600;">
                    Service Description *
                </label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" 
                          name="description" 
                          rows="4" 
                          required
                          style="background-color: var(--discord-darkest); border: 1px solid var(--discord-darkest); color: var(--discord-lightest);">{{ old('description', $service->description) }}</textarea>
                @error('description')
                    <div style="color: var(--discord-red); font-size: 12px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('merchant.services.index') }}" class="discord-btn discord-btn-secondary">
                    <i class="fas fa-times me-1"></i> Cancel
                </a>
                <button type="submit" class="discord-btn">
                    <i class="fas fa-save me-1"></i> Update Service
                </button>
            </div>
        </form>
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
                durationText += hours + ' hour' + (hours > 1 ? 's' : '');
                if (remainingMinutes > 0) {
                    durationText += ' ' + remainingMinutes + ' minute' + (remainingMinutes > 1 ? 's' : '');
                }
            } else {
                durationText = minutes + ' minute' + (minutes > 1 ? 's' : '');
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

    // Trigger duration helper on page load if duration has value
    if ($('#duration').val()) {
        $('#duration').trigger('input');
    }
});
</script>
@endsection
