@extends('layouts.products-manager')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')

@section('styles')
<style>
    /* Orange theme override for Products Manager */
    :root {
        --pm-orange: #F46C3F;
        --pm-orange-hover: #e55a2b;
        --pm-orange-light: #fef3f0;
        --pm-orange-dark: #d14d26;
    }

    /* Override blue theme with orange for Products Manager context */
    .products-manager-theme {
        --primary-blue: var(--pm-orange);
        --primary-blue-hover: var(--pm-orange-hover);
        --primary-blue-light: var(--pm-orange-light);
    }

    /* Vue.js specific styles with orange theme */
    .vue-app-container {
        min-height: 100vh;
    }

    .vue-btn-blue-solid {
        background-color: var(--pm-orange) !important;
        border-color: var(--pm-orange) !important;
        color: #ffffff !important;
    }

    .vue-btn-blue-solid:hover {
        background-color: var(--pm-orange-hover) !important;
        border-color: var(--pm-orange-hover) !important;
    }

    .vue-form-control:focus {
        border-color: var(--pm-orange) !important;
        box-shadow: 0 0 0 3px rgba(244, 108, 63, 0.1) !important;
    }

    /* Override primary blue variables in Vue components */
    .vue-card {
        --tw-ring-color: var(--pm-orange) !important;
    }

    .vue-card.ring-2 {
        border-color: var(--pm-orange-light) !important;
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

    /* Vue component base styles with orange theme */
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
        background-color: var(--pm-orange);
        color: #ffffff;
        border-color: var(--pm-orange);
    }

    .vue-btn-primary:hover {
        background-color: var(--pm-orange-hover);
        border-color: var(--pm-orange-hover);
    }

    /* Tab styles with orange theme */
    .vue-tab-active {
        background-color: var(--pm-orange) !important;
        color: #ffffff !important;
        border-color: var(--pm-orange) !important;
    }

    .vue-tab:hover {
        background-color: var(--pm-orange-light) !important;
        color: var(--pm-orange-dark) !important;
    }

    /* Progress bar with orange theme */
    .vue-progress-bar {
        background-color: var(--pm-orange) !important;
    }

    /* Success/error states with orange accents */
    .vue-success {
        border-color: #10b981;
        background-color: #f0fdf4;
    }

    .vue-error {
        border-color: #ef4444;
        background-color: #fef2f2;
    }

    /* Orange theme for color variant cards */
    .color-item .vue-btn-blue-solid {
        background-color: var(--pm-orange) !important;
        border-color: var(--pm-orange) !important;
    }

    .color-item .vue-btn-blue-solid:hover {
        background-color: var(--pm-orange-hover) !important;
        border-color: var(--pm-orange-hover) !important;
    }

    /* Default color badge with orange theme */
    .color-item span[style*="--primary-blue-hover"] {
        color: var(--pm-orange-dark) !important;
    }

    /* Ring colors for default items */
    .color-item[style*="--tw-ring-color"] {
        --tw-ring-color: var(--pm-orange) !important;
    }
</style>
@endsection

@section('content')
<div class="products-manager-theme">
    <!-- Loading indicator -->
    <div id="loading-indicator" class="flex items-center justify-center py-12" style="display: none;">
        <div class="spinner-border text-orange-500"></div>
        <span class="ml-3 text-gray-600">Loading product edit form...</span>
    </div>

    <!-- Content container for AJAX loading -->
    <div id="product-edit-content">
        <!-- This will be populated via AJAX with vendor product edit content -->
    </div>
</div>
@endsection

@section('scripts')
{{-- AJAX navigation is handled by the global Products Manager navigation system --}}
@endsection
