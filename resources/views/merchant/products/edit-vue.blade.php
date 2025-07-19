@extends('layouts.merchant')

@section('title', 'Edit Product')
@section('header', 'Edit Product')

@section('styles')
<style>
    /* Ensure Vue app container takes full height */
    #product-edit-app {
        min-height: 100vh;
    }
    
    /* Hide any flash messages that might interfere with Vue */
    .alert {
        display: none;
    }
</style>
@endsection

@section('content')
<div id="product-edit-app" 
     data-product-id="{{ $product->id }}"
     data-back-url="{{ route('merchant.products.index') }}">
    <!-- Loading state while Vue app initializes -->
    <div class="d-flex justify-content-center align-items-center" style="min-height: 50vh;">
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Loading product editor...</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@vite(['resources/js/product-edit.js'])

<script>
// Ensure axios is available before configuring it
document.addEventListener('DOMContentLoaded', function() {
    // Wait for axios to be available
    const waitForAxios = () => {
        if (window.axios && window.axios.defaults) {
            // Add CSRF token to axios defaults for Vue app
            const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
            if (csrfTokenElement) {
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfTokenElement.getAttribute('content');
            } else {
                console.warn('CSRF token meta tag not found');
            }
        } else {
            // Retry after a short delay
            setTimeout(waitForAxios, 50);
        }
    };

    waitForAxios();
});

// Add any global configuration needed for the Vue app
window.productEditConfig = {
    apiBaseUrl: '{{ url("/") }}',
    merchantProductsUrl: '{{ route("merchant.products.index") }}',
    csrfToken: '{{ csrf_token() }}'
};
</script>
@endsection
