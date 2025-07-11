@extends('layouts.merchant')

@section('title', 'Service Details')
@section('header', 'Service Details')

@section('content')
<!-- Header Section -->
<div class="discord-card">
    <div class="discord-card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 style="margin: 0; color: var(--discord-lightest); font-size: 24px; font-weight: 600;">
                    <i class="fas fa-eye me-2" style="color: var(--discord-primary);"></i>
                    Service Details
                </h2>
                <p style="margin: 8px 0 0 0; color: var(--discord-light); font-size: 14px;">
                    View detailed information about "{{ $service->name }}"
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('merchant.services.edit', $service->id) }}" class="discord-btn" style="background-color: var(--discord-yellow);">
                    <i class="fas fa-edit me-1"></i> Edit Service
                </a>
                <a href="{{ route('merchant.services.index') }}" class="discord-btn discord-btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Services
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Service Information -->
    <div class="col-md-8">
        <!-- Basic Information -->
        <div class="discord-card">
            <div class="discord-card-header">
                <i class="fas fa-info-circle me-2" style="color: var(--discord-primary);"></i>
                Basic Information
            </div>
            <div class="discord-card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" style="color: var(--discord-light); font-size: 12px; text-transform: uppercase; font-weight: 600;">
                            Service Name
                        </label>
                        <div style="color: var(--discord-lightest); font-size: 16px; font-weight: 500;">
                            {{ $service->name }}
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" style="color: var(--discord-light); font-size: 12px; text-transform: uppercase; font-weight: 600;">
                            Category
                        </label>
                        <div style="color: var(--discord-lightest); font-size: 16px; font-weight: 500;">
                            {{ $service->category->name ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" style="color: var(--discord-light); font-size: 12px; text-transform: uppercase; font-weight: 600;">
                            Price
                        </label>
                        <div style="color: var(--discord-green); font-size: 20px; font-weight: 600;">
                            AED {{ number_format($service->price, 2) }}
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" style="color: var(--discord-light); font-size: 12px; text-transform: uppercase; font-weight: 600;">
                            Duration
                        </label>
                        <div style="color: var(--discord-lightest); font-size: 16px; font-weight: 500;">
                            @if($service->duration)
                                @php
                                    $hours = floor($service->duration / 60);
                                    $minutes = $service->duration % 60;
                                @endphp
                                @if($hours > 0)
                                    {{ $hours }} hour{{ $hours > 1 ? 's' : '' }}
                                    @if($minutes > 0)
                                        {{ $minutes }} minute{{ $minutes > 1 ? 's' : '' }}
                                    @endif
                                @else
                                    {{ $service->duration }} minute{{ $service->duration > 1 ? 's' : '' }}
                                @endif
                            @else
                                Not specified
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Description -->
        <div class="discord-card">
            <div class="discord-card-header">
                <i class="fas fa-align-left me-2" style="color: var(--discord-primary);"></i>
                Description
            </div>
            <div class="discord-card-body">
                <div style="color: var(--discord-lightest); line-height: 1.6;">
                    {{ $service->description ?: 'No description provided.' }}
                </div>
            </div>
        </div>

        <!-- Service Options -->
        <div class="discord-card">
            <div class="discord-card-header">
                <i class="fas fa-cogs me-2" style="color: var(--discord-primary);"></i>
                Service Options
            </div>
            <div class="discord-card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" style="color: var(--discord-light); font-size: 12px; text-transform: uppercase; font-weight: 600;">
                            Availability Status
                        </label>
                        <div>
                            @if($service->is_available)
                                <span class="badge" style="background-color: var(--discord-green); color: white; padding: 4px 8px; border-radius: 4px;">
                                    <i class="fas fa-check me-1"></i> Available
                                </span>
                            @else
                                <span class="badge" style="background-color: var(--discord-red); color: white; padding: 4px 8px; border-radius: 4px;">
                                    <i class="fas fa-times me-1"></i> Unavailable
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" style="color: var(--discord-light); font-size: 12px; text-transform: uppercase; font-weight: 600;">
                            Home Service
                        </label>
                        <div>
                            @if($service->home_service)
                                <span class="badge" style="background-color: var(--discord-primary); color: white; padding: 4px 8px; border-radius: 4px;">
                                    <i class="fas fa-home me-1"></i> Available at Home
                                </span>
                            @else
                                <span class="badge" style="background-color: var(--discord-light); color: white; padding: 4px 8px; border-radius: 4px;">
                                    <i class="fas fa-store me-1"></i> In-Store Only
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Image and Metadata -->
    <div class="col-md-4">
        <!-- Service Image -->
        <div class="discord-card">
            <div class="discord-card-header">
                <i class="fas fa-image me-2" style="color: var(--discord-primary);"></i>
                Service Image
            </div>
            <div class="discord-card-body text-center">
                @if($service->image)
                    <img src="{{ $service->image }}"
                         alt="{{ $service->name }}"
                         class="img-fluid"
                         style="max-width: 100%; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.3);">
                @else
                    <div style="background-color: var(--discord-darkest); border-radius: 8px; padding: 40px; color: var(--discord-light);">
                        <i class="fas fa-image" style="font-size: 48px; margin-bottom: 12px;"></i>
                        <div>No image uploaded</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Service Metadata -->
        <div class="discord-card">
            <div class="discord-card-header">
                <i class="fas fa-info me-2" style="color: var(--discord-primary);"></i>
                Service Metadata
            </div>
            <div class="discord-card-body">
                <div class="mb-3">
                    <label class="form-label" style="color: var(--discord-light); font-size: 12px; text-transform: uppercase; font-weight: 600;">
                        Service ID
                    </label>
                    <div style="color: var(--discord-lightest); font-family: monospace;">
                        #{{ $service->id }}
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" style="color: var(--discord-light); font-size: 12px; text-transform: uppercase; font-weight: 600;">
                        Created Date
                    </label>
                    <div style="color: var(--discord-lightest);">
                        {{ $service->created_at->format('M d, Y') }}
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" style="color: var(--discord-light); font-size: 12px; text-transform: uppercase; font-weight: 600;">
                        Last Updated
                    </label>
                    <div style="color: var(--discord-lightest);">
                        {{ $service->updated_at->format('M d, Y') }}
                    </div>
                </div>
                @if($service->rating)
                <div class="mb-3">
                    <label class="form-label" style="color: var(--discord-light); font-size: 12px; text-transform: uppercase; font-weight: 600;">
                        Rating
                    </label>
                    <div style="color: var(--discord-yellow);">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $service->rating)
                                <i class="fas fa-star"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor
                        <span style="color: var(--discord-lightest); margin-left: 8px;">
                            {{ number_format($service->rating, 1) }}
                        </span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="discord-card">
            <div class="discord-card-header">
                <i class="fas fa-bolt me-2" style="color: var(--discord-primary);"></i>
                Quick Actions
            </div>
            <div class="discord-card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('merchant.services.edit', $service->id) }}" class="discord-btn" style="background-color: var(--discord-yellow);">
                        <i class="fas fa-edit me-1"></i> Edit Service
                    </a>
                    <form action="{{ route('merchant.services.destroy', $service->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this service?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="discord-btn w-100" style="background-color: var(--discord-red);">
                            <i class="fas fa-trash me-1"></i> Delete Service
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
