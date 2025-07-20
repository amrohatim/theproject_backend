@extends('layouts.dashboard')

@section('title', 'Add Product')
@section('page-title', 'Add Product')

@section('styles')
<style>
    /* Vue.js specific styles */
    .vue-app-container {
        min-height: 100vh;
    }

    /* Loading spinner styles */
    .spinner-border {
        width: 3rem;
        height: 3rem;
        border: 0.25em solid currentColor;
        border-right-color: transparent;
        border-radius: 50%;
        animation: spinner-border 0.75s linear infinite;
    }

    @keyframes spinner-border {
        to {
            transform: rotate(360deg);
        }
    }

    /* Vue component base styles */
    .vue-text-lg {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
    }

    .vue-text-sm {
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
    }

    .vue-form-control {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        background-color: #ffffff;
        color: #1f2937;
        font-size: 0.875rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .vue-form-control:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .vue-btn {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.15s ease-in-out;
        cursor: pointer;
        border: 1px solid transparent;
    }

    .vue-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .vue-btn-primary {
        background-color: #6366f1;
        color: #ffffff;
        border-color: #6366f1;
    }

    .vue-btn-primary:hover:not(:disabled) {
        background-color: #5b21b6;
        border-color: #5b21b6;
    }

    .vue-btn-secondary {
        background-color: #6b7280;
        color: #ffffff;
        border-color: #6b7280;
    }

    .vue-btn-secondary:hover:not(:disabled) {
        background-color: #4b5563;
        border-color: #4b5563;
    }

    .vue-btn-success {
        background-color: #10b981;
        color: #ffffff;
        border-color: #10b981;
    }

    .vue-btn-success:hover:not(:disabled) {
        background-color: #059669;
        border-color: #059669;
    }

    .vue-card {
        background-color: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }

    /* Tab styles */
    .vue-tab-content {
        min-height: 400px;
    }

    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
        .vue-text-lg {
            color: #f9fafb;
        }

        .vue-text-sm {
            color: #d1d5db;
        }

        .vue-form-control {
            background-color: #374151;
            border-color: #4b5563;
            color: #f9fafb;
        }

        .vue-form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .vue-card {
            background-color: #1f2937;
            border-color: #374151;
        }
    }

    /* Animation classes */
    .fade-enter-active, .fade-leave-active {
        transition: opacity 0.3s;
    }

    .fade-enter-from, .fade-leave-to {
        opacity: 0;
    }

    /* Tab transition */
    .tab-transition-enter-active, .tab-transition-leave-active {
        transition: all 0.3s ease;
    }

    .tab-transition-enter-from {
        opacity: 0;
        transform: translateX(10px);
    }

    .tab-transition-leave-to {
        opacity: 0;
        transform: translateX(-10px);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .vue-app-container {
            padding: 1rem;
        }
    }
</style>
@endsection

@section('content')
<div id="vendor-product-create-app" 
     class="vue-app-container"
     data-back-url="{{ route('vendor.products.index') }}"
     data-create-data-url="{{ route('vendor.products.create.data') }}"
     data-store-url="{{ route('vendor.products.store') }}"
     data-session-store-url="{{ route('vendor.products.session.store') }}"
     data-session-get-url="{{ route('vendor.products.session.get') }}"
     data-session-clear-url="{{ route('vendor.products.session.clear') }}">
    <!-- Loading state while Vue app initializes -->
    <div class="d-flex justify-content-center align-items-center" style="min-height: 50vh;">
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Loading product creation form...</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Vue App Script -->
@vite(['resources/js/vendor-product-create.js'])
@endpush

@section('scripts')
<!-- Additional scripts if needed -->
<script>
    // Global error handler for Vue.js
    window.addEventListener('unhandledrejection', function(event) {
        console.error('Unhandled promise rejection:', event.reason);
    });

    // CSRF token setup for AJAX requests
    window.axios = window.axios || {};
    if (window.axios.defaults) {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }

    // Set up global fetch defaults
    const originalFetch = window.fetch;
    window.fetch = function(url, options = {}) {
        options.headers = options.headers || {};
        if (!options.headers['X-CSRF-TOKEN']) {
            options.headers['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }
        return originalFetch(url, options);
    };
</script>
@endsection
