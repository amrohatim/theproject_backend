@extends('layouts.dashboard')

@section('title', 'Image Test')
@section('page-title', 'Image Test')

@section('styles')
<style>
    .image-test-container {
        margin-bottom: 2rem;
    }
    .image-test-card {
        background-color: #fff;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        overflow: hidden;
        margin-bottom: 1rem;
    }
    .image-test-card.dark {
        background-color: #1f2937;
        color: #f3f4f6;
    }
    .card-header {
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }
    .dark .card-header {
        border-bottom: 1px solid #374151;
    }
    .card-body {
        padding: 1rem;
    }
    .image-row {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .image-item {
        flex: 0 0 calc(25% - 1rem);
        min-width: 200px;
    }
    .image-container {
        width: 100%;
        height: 150px;
        position: relative;
        margin-bottom: 0.5rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.25rem;
        overflow: hidden;
    }
    .dark .image-container {
        border-color: #374151;
    }
    .image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .image-info {
        font-size: 0.875rem;
    }
    .image-path {
        font-family: monospace;
        font-size: 0.75rem;
        word-break: break-all;
        margin-top: 0.25rem;
        padding: 0.25rem;
        background-color: #f3f4f6;
        border-radius: 0.25rem;
    }
    .dark .image-path {
        background-color: #374151;
    }
    .success {
        color: #10b981;
    }
    .error {
        color: #ef4444;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Image Display Test</h2>
        <p class="text-gray-600 dark:text-gray-400">This page tests various image display methods to diagnose issues</p>
    </div>

    <!-- Configuration Info -->
    <div class="image-test-container">
        <div class="image-test-card dark:bg-gray-800">
            <div class="card-header">
                <h3 class="text-lg font-semibold">Configuration</h3>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <strong>APP_URL:</strong> <span class="image-path">{{ config('app.url') }}</span>
                </div>
                <div class="mb-2">
                    <strong>FILESYSTEM_DISK:</strong> <span class="image-path">{{ config('filesystems.default') }}</span>
                </div>
                <div class="mb-2">
                    <strong>Storage Path:</strong> <span class="image-path">{{ storage_path('app/public') }}</span>
                </div>
                <div class="mb-2">
                    <strong>Public Path:</strong> <span class="image-path">{{ public_path() }}</span>
                </div>
                <div class="mb-2">
                    <strong>Storage Link Status:</strong> 
                    @if(file_exists(public_path('storage')))
                        <span class="success">✓ Storage link exists</span>
                        @if(is_link(public_path('storage')))
                            <span class="success"> (Symbolic link to {{ readlink(public_path('storage')) }})</span>
                        @else
                            <span class="error"> (Not a symbolic link!)</span>
                        @endif
                    @else
                        <span class="error">✗ Storage link does not exist</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Product Images -->
    <div class="image-test-container">
        <div class="image-test-card dark:bg-gray-800">
            <div class="card-header">
                <h3 class="text-lg font-semibold">Product Images</h3>
            </div>
            <div class="card-body">
                <div class="image-row">
                    @foreach(\App\Models\Product::take(4)->get() as $product)
                    <div class="image-item">
                        <div class="image-container">
                            <!-- Method 1: Direct from model accessor -->
                            <img src="{{ $product->image }}" alt="{{ $product->name }}" onerror="this.onerror=null; this.src='{{ asset('images/placeholder.png') }}'; this.nextElementSibling.style.display='block';">
                            <div class="error" style="display: none; position: absolute; bottom: 0; left: 0; right: 0; background: rgba(239, 68, 68, 0.7); color: white; padding: 0.25rem; text-align: center;">Image failed to load</div>
                        </div>
                        <div class="image-info">
                            <strong>{{ $product->name }}</strong>
                            <div class="image-path">{{ $product->getRawOriginal('image') }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="image-row">
                    @foreach(\App\Models\Product::take(4)->get() as $product)
                    <div class="image-item">
                        <div class="image-container">
                            <!-- Method 2: Using asset helper with path fix -->
                            <img src="{{ asset(str_replace('/storage/', 'storage/', $product->getRawOriginal('image'))) }}" alt="{{ $product->name }}" onerror="this.onerror=null; this.src='{{ asset('images/placeholder.png') }}'; this.nextElementSibling.style.display='block';">
                            <div class="error" style="display: none; position: absolute; bottom: 0; left: 0; right: 0; background: rgba(239, 68, 68, 0.7); color: white; padding: 0.25rem; text-align: center;">Image failed to load</div>
                        </div>
                        <div class="image-info">
                            <strong>Using asset() helper</strong>
                            <div class="image-path">{{ asset(str_replace('/storage/', 'storage/', $product->getRawOriginal('image'))) }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Service Images -->
    <div class="image-test-container">
        <div class="image-test-card dark:bg-gray-800">
            <div class="card-header">
                <h3 class="text-lg font-semibold">Service Images</h3>
            </div>
            <div class="card-body">
                <div class="image-row">
                    @foreach(\App\Models\Service::take(4)->get() as $service)
                    <div class="image-item">
                        <div class="image-container">
                            <img src="{{ $service->image }}" alt="{{ $service->name }}" onerror="this.onerror=null; this.src='{{ asset('images/placeholder.png') }}'; this.nextElementSibling.style.display='block';">
                            <div class="error" style="display: none; position: absolute; bottom: 0; left: 0; right: 0; background: rgba(239, 68, 68, 0.7); color: white; padding: 0.25rem; text-align: center;">Image failed to load</div>
                        </div>
                        <div class="image-info">
                            <strong>{{ $service->name }}</strong>
                            <div class="image-path">{{ $service->getRawOriginal('image') }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
