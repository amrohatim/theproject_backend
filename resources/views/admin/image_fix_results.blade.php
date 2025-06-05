@extends('layouts.dashboard')

@section('title', 'Image Fix Results')
@section('page-title', 'Image Fix Results')

@section('styles')
<style>
    .result-card {
        margin-bottom: 1.5rem;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .card-header {
        padding: 1rem;
        font-weight: 600;
    }
    .card-body {
        padding: 1rem;
    }
    .success {
        background-color: #d1fae5;
        color: #065f46;
    }
    .fixed {
        background-color: #dbeafe;
        color: #1e40af;
    }
    .error {
        background-color: #fee2e2;
        color: #b91c1c;
    }
    .details-list {
        margin-top: 0.5rem;
        padding-left: 1.5rem;
        list-style-type: disc;
    }
    .details-list li {
        margin-bottom: 0.25rem;
    }
    .action-buttons {
        margin-top: 2rem;
        display: flex;
        gap: 1rem;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Image Fix Results</h2>
        <p class="text-gray-600 dark:text-gray-400">The following actions were taken to fix image display issues</p>
    </div>

    <!-- Results -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 border border-gray-200 dark:border-gray-700 mb-6">
        <div class="grid grid-cols-1 gap-4">
            <!-- Storage Link -->
            <div class="result-card">
                <div class="card-header {{ $results['storage_link']['status'] === 'success' ? 'success' : ($results['storage_link']['status'] === 'fixed' ? 'fixed' : 'error') }}">
                    Storage Link
                </div>
                <div class="card-body bg-white dark:bg-gray-800">
                    <p class="font-medium">{{ $results['storage_link']['message'] }}</p>
                    @if(count($results['storage_link']['details']) > 0)
                        <ul class="details-list">
                            @foreach($results['storage_link']['details'] as $detail)
                                <li>{{ $detail }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <!-- Environment Configuration -->
            <div class="result-card">
                <div class="card-header {{ $results['env_config']['status'] === 'success' ? 'success' : ($results['env_config']['status'] === 'fixed' ? 'fixed' : 'error') }}">
                    Environment Configuration
                </div>
                <div class="card-body bg-white dark:bg-gray-800">
                    <p class="font-medium">{{ $results['env_config']['message'] }}</p>
                    @if(count($results['env_config']['details']) > 0)
                        <ul class="details-list">
                            @foreach($results['env_config']['details'] as $detail)
                                <li>{{ $detail }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <!-- Directories -->
            <div class="result-card">
                <div class="card-header {{ $results['directories']['status'] === 'success' ? 'success' : ($results['directories']['status'] === 'fixed' ? 'fixed' : 'error') }}">
                    Directories
                </div>
                <div class="card-body bg-white dark:bg-gray-800">
                    <p class="font-medium">{{ $results['directories']['message'] }}</p>
                    @if(count($results['directories']['details']) > 0)
                        <ul class="details-list">
                            @foreach($results['directories']['details'] as $detail)
                                <li>{{ $detail }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <!-- Product Images -->
            <div class="result-card">
                <div class="card-header {{ $results['product_images']['status'] === 'success' ? 'success' : ($results['product_images']['status'] === 'fixed' ? 'fixed' : 'error') }}">
                    Product Images
                </div>
                <div class="card-body bg-white dark:bg-gray-800">
                    <p class="font-medium">{{ $results['product_images']['message'] }}</p>
                    @if(count($results['product_images']['details']) > 0)
                        <ul class="details-list">
                            @foreach($results['product_images']['details'] as $detail)
                                <li>{{ $detail }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <!-- Product Color Images -->
            <div class="result-card">
                <div class="card-header {{ $results['color_images']['status'] === 'success' ? 'success' : ($results['color_images']['status'] === 'fixed' ? 'fixed' : 'error') }}">
                    Product Color Images
                </div>
                <div class="card-body bg-white dark:bg-gray-800">
                    <p class="font-medium">{{ $results['color_images']['message'] }}</p>
                    @if(count($results['color_images']['details']) > 0)
                        <ul class="details-list">
                            @foreach($results['color_images']['details'] as $detail)
                                <li>{{ $detail }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <!-- Service Images -->
            <div class="result-card">
                <div class="card-header {{ $results['service_images']['status'] === 'success' ? 'success' : ($results['service_images']['status'] === 'fixed' ? 'fixed' : 'error') }}">
                    Service Images
                </div>
                <div class="card-body bg-white dark:bg-gray-800">
                    <p class="font-medium">{{ $results['service_images']['message'] }}</p>
                    @if(count($results['service_images']['details']) > 0)
                        <ul class="details-list">
                            @foreach($results['service_images']['details'] as $detail)
                                <li>{{ $detail }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <!-- Branch Images -->
            <div class="result-card">
                <div class="card-header {{ $results['branch_images']['status'] === 'success' ? 'success' : ($results['branch_images']['status'] === 'fixed' ? 'fixed' : 'error') }}">
                    Branch Images
                </div>
                <div class="card-body bg-white dark:bg-gray-800">
                    <p class="font-medium">{{ $results['branch_images']['message'] }}</p>
                    @if(count($results['branch_images']['details']) > 0)
                        <ul class="details-list">
                            @foreach($results['branch_images']['details'] as $detail)
                                <li>{{ $detail }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <!-- Category Images -->
            <div class="result-card">
                <div class="card-header {{ $results['category_images']['status'] === 'success' ? 'success' : ($results['category_images']['status'] === 'fixed' ? 'fixed' : 'error') }}">
                    Category Images
                </div>
                <div class="card-body bg-white dark:bg-gray-800">
                    <p class="font-medium">{{ $results['category_images']['message'] }}</p>
                    @if(count($results['category_images']['details']) > 0)
                        <ul class="details-list">
                            @foreach($results['category_images']['details'] as $detail)
                                <li>{{ $detail }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <a href="{{ route('admin.image.test') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
            Test Images
        </a>
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
            Return to Dashboard
        </a>
    </div>
</div>
@endsection
