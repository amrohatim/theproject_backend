@extends('layouts.dashboard')

@section('title', 'Avatar Details')
@section('page-title', 'Avatar Details')

@section('content')
<div class="container mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Avatar Details</h2>
        <p class="mt-1 text-gray-600 dark:text-gray-400">View avatar information and image</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <!-- Avatar Image Section -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex flex-col lg:flex-row lg:items-start lg:space-x-8">
                <!-- Avatar Image -->
                <div class="flex-shrink-0 mb-6 lg:mb-0">
                    <div class="relative">
                        <img src="{{ $avatar->image_url }}" alt="{{ $avatar->name }}" class="w-64 h-64 object-cover rounded-lg border border-gray-300 dark:border-gray-600 shadow-sm">
                        <div class="absolute top-2 right-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                {{ strtoupper(pathinfo($avatar->file_path, PATHINFO_EXTENSION)) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Avatar Information -->
                <div class="flex-1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Avatar Name</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white font-medium">{{ $avatar->name ?: 'No name provided' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">File Size</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $avatar->file_size_formatted }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dimensions</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $avatar->width }} × {{ $avatar->height }} pixels</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">File Type</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $avatar->mime_type }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Created</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $avatar->created_at->format('M d, Y \\a\\t g:i A') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last Updated</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $avatar->updated_at->format('M d, Y \\a\\t g:i A') }}</p>
                        </div>

                        @if($avatar->description)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $avatar->description }}</p>
                        </div>
                        @endif

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">File Path</label>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 font-mono bg-gray-50 dark:bg-gray-700 p-2 rounded border">{{ $avatar->file_path }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.avatars.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Avatars
                </a>
                
                <a href="{{ $avatar->image_url }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                    View Full Size
                </a>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.avatars.edit', $avatar->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Avatar
                </a>

                <form action="{{ route('admin.avatars.destroy', $avatar->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this avatar? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete Avatar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Usage Information -->
    <div class="mt-6 bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Avatar Usage</h3>
                <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                    <p>To use this avatar in your application, reference it by its ID: <code class="bg-blue-100 dark:bg-blue-800 px-1 py-0.5 rounded text-xs font-mono">{{ $avatar->id }}</code></p>
                    <p class="mt-1">Direct image URL: <code class="bg-blue-100 dark:bg-blue-800 px-1 py-0.5 rounded text-xs font-mono break-all">{{ $avatar->image_url }}</code></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection