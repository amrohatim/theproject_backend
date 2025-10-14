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

    /* Enhanced Form Controls with Modern Design - Vendor (Blue Theme) */
    .vue-form-control-vendor {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        background-color: #ffffff;
        color: #1f2937;
        font-size: 0.9375rem;
        font-weight: 500;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .vue-form-control-vendor:hover {
        border-color: #d1d5db;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.06);
    }

    .vue-form-control-vendor:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1), 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transform: translateY(-1px);
    }

    .vue-form-control-vendor::placeholder {
        color: #9ca3af;
        font-weight: 400;
    }

    /* Enhanced Form Controls with Modern Design - Products Manager (Orange Theme) */
    .vue-form-control-pm {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.75rem;
        background-color: #ffffff;
        color: #1f2937;
        font-size: 0.9375rem;
        font-weight: 500;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }

    .vue-form-control-pm:hover {
        border-color: #d1d5db;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.06);
    }

    .vue-form-control-pm:focus {
        outline: none;
        border-color: #f59e0b;
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1), 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transform: translateY(-1px);
    }

    .vue-form-control-pm::placeholder {
        color: #9ca3af;
        font-weight: 400;
    }

    /* Modern Button Styles */
    .vue-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.75rem 1.5rem;
        border-radius: 0.75rem;
        font-size: 0.9375rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        border: 2px solid transparent;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.06);
        letter-spacing: -0.01em;
    }

    .vue-btn:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px 0 rgba(0, 0, 0, 0.15);
    }

    .vue-btn:active:not(:disabled) {
        transform: translateY(0);
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.06);
    }

    .vue-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
    }

    /* Vendor Primary Button (Blue Theme) */
    .vue-btn-primary-vendor {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: #ffffff;
        border-color: transparent;
    }

    .vue-btn-primary-vendor:hover:not(:disabled) {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        box-shadow: 0 6px 16px 0 rgba(59, 130, 246, 0.4);
    }

    /* Products Manager Primary Button (Orange Theme) */
    .vue-btn-primary-pm {
        background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
        color: #ffffff;
        border-color: transparent;
    }

    .vue-btn-primary-pm:hover:not(:disabled) {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        box-shadow: 0 6px 16px 0 rgba(245, 158, 11, 0.4);
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
