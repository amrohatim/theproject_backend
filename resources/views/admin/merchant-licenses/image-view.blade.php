@extends('layouts.dashboard')

@section('title', $imageTitle . ' - ' . $merchant->business_name)

@push('styles')
<style>
    /* Minimal full-screen image viewer */
    .image-viewer-container {
        background: #1a1a1a;
        min-height: 100vh;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .image-display {
        max-width: 100%;
        max-height: 100vh;
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }

    .back-button {
        position: fixed;
        top: 20px;
        left: 20px;
        background: rgba(59, 130, 246, 0.9);
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        z-index: 1000;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(59, 130, 246, 0.3);
    }

    .back-button:hover {
        background: rgba(59, 130, 246, 1);
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .image-viewer-container {
            padding: 10px;
        }

        .back-button {
            top: 10px;
            left: 10px;
            padding: 10px 16px;
            font-size: 14px;
        }
    }
</style>
@endpush

@section('content')
<div class="image-viewer-container">
    <!-- Back Button -->
    <a href="{{ route('admin.merchant-licenses.show', $merchant->id) }}" class="back-button">
        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to License Details
    </a>

    <!-- Full-Size Image Display -->
    <img src="{{ $imageUrl }}"
         alt="{{ $imageTitle }}"
         class="image-display"
         loading="lazy">
</div>

<script>
// Simple keyboard shortcut for ESC to go back
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        window.history.back();
    }
});
</script>
@endsection
