@extends('layouts.merchant')

@section('title', 'Services')
@section('header', 'Services')

@section('content')
<!-- Header Section -->
<div class="discord-card">
    <div class="discord-card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 style="margin: 0; color: var(--discord-lightest); font-size: 24px; font-weight: 600;">
                    <i class="fas fa-concierge-bell me-2" style="color: var(--discord-green);"></i>
                    My Services
                </h2>
                <p style="margin: 8px 0 0 0; color: var(--discord-light); font-size: 14px;">
                    Manage your service offerings and bookings
                </p>
            </div>
            <div>
                <a href="{{ route('merchant.services.create') }}" class="discord-btn">
                    <i class="fas fa-plus me-1"></i> Add New Service
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Services Content -->
<div class="discord-card">
    <div class="discord-card-header">
        <i class="fas fa-list me-2" style="color: var(--discord-green);"></i>
        Services List
    </div>
    
    @if($services->count() > 0)
    <div class="table-responsive">
        <table class="discord-table">
            <thead>
                <tr>
                    <th style="width: 80px;">Image</th>
                    <th>Service Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($services as $service)
                <tr>
                    <td>
                        @if($service->image)
                            <img src="{{ $service->image }}" alt="{{ $service->name }}"
                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                        @else
                            <div style="width: 60px; height: 60px; background-color: var(--discord-darkest); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-concierge-bell" style="color: var(--discord-light); font-size: 20px;"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight: 600; color: var(--discord-lightest); margin-bottom: 4px;">
                            {{ $service->name }}
                        </div>
                        <div style="font-size: 12px; color: var(--discord-light);">
                            {{ Str::limit($service->description, 50) }}
                        </div>
                    </td>
                    <td>
                        <span style="background-color: var(--discord-darkest); color: var(--discord-light); padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                            {{ $service->category->name ?? 'Uncategorized' }}
                        </span>
                    </td>
                    <td>
                        <div style="font-weight: 600; color: var(--discord-green); font-size: 16px;">
                            ${{ number_format($service->price, 2) }}
                        </div>
                    </td>
                    <td>
                        @if($service->duration)
                            <span style="color: var(--discord-light);">
                                {{ $service->duration }} {{ $service->duration_unit ?? 'hours' }}
                            </span>
                        @else
                            <span style="color: var(--discord-light); font-size: 12px;">Not specified</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge" style="background-color: {{ $service->is_available ? 'var(--discord-green)' : 'var(--discord-red)' }}; color: white; padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                            {{ $service->is_available ? 'Available' : 'Unavailable' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('merchant.services.show', $service->id) }}" 
                               class="btn btn-sm" 
                               style="background-color: var(--discord-primary); color: white; border: none; padding: 4px 8px; border-radius: 4px;"
                               title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('merchant.services.edit', $service->id) }}" 
                               class="btn btn-sm" 
                               style="background-color: var(--discord-yellow); color: white; border: none; padding: 4px 8px; border-radius: 4px;"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('merchant.services.destroy', $service->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this service?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-sm" 
                                        style="background-color: var(--discord-red); color: white; border: none; padding: 4px 8px; border-radius: 4px;"
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <!-- Empty State -->
    <div class="discord-card-body" style="text-align: center; padding: 60px 20px;">
        <i class="fas fa-concierge-bell" style="font-size: 64px; color: var(--discord-light); opacity: 0.5; margin-bottom: 20px;"></i>
        <h3 style="color: var(--discord-lightest); margin-bottom: 12px;">No Services Yet</h3>
        <p style="color: var(--discord-light); margin-bottom: 24px; font-size: 16px;">
            Start offering services to your customers by adding your first service.
        </p>
        <a href="{{ route('merchant.services.create') }}" class="discord-btn" style="font-size: 16px; padding: 12px 24px;">
            <i class="fas fa-plus me-2"></i> Add Your First Service
        </a>
    </div>
    @endif
</div>
@endsection
