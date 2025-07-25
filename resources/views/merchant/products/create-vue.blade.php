@extends('layouts.merchant')

@section('title', 'Create Product')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/vue-styles.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/mdbassit/Coloris@latest/dist/coloris.min.css">
<style>
    :root {
        --primary-blue: #3b82f6;
        --primary-blue-hover: #2563eb;
        --primary-blue-light: #eff6ff;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --gray-900: #111827;
        --green-50: #f0fdf4;
        --green-200: #bbf7d0;
        --green-500: #22c55e;
        --green-600: #16a34a;
        --green-700: #15803d;
        --red-500: #ef4444;
        --red-600: #dc2626;
    }
</style>
@endsection

@section('content')
<div id="product-create-app" 
     data-back-url="{{ route('merchant.products.index') }}">
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

@section('scripts')
@vite(['resources/js/product-create.js'])
<script src="https://cdn.jsdelivr.net/gh/mdbassit/Coloris@latest/dist/coloris.min.js"></script>
<script src="{{ asset('js/color-picker.js') }}"></script>
<script src="{{ asset('js/enhanced-size-selection.js') }}"></script>
<script src="{{ asset('js/enhanced-color-selection.js') }}"></script>
<script src="{{ asset('js/color-specific-size-selection.js') }}"></script>
<script src="{{ asset('js/dynamic-color-size-management.js') }}"></script>
<script src="{{ asset('js/merchant-stock-validation.js') }}"></script>
@endsection
