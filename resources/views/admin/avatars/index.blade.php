@extends('layouts.dashboard')

@section('title', 'Avatar Management')
@section('page-title', 'Avatar Management')

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Avatar Management</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">Manage all avatars in the system</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('admin.avatars.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i> Add Avatar
            </a>
        </div>
    </div>

    <!-- Search and filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 p-4 border border-gray-200 dark:border-gray-700">
        <form action="{{ route('admin.avatars.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md" placeholder="Search avatars...">
                    </div>
                </div>
                
                <div>
                    <label for="sort" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sort By</label>
                    <select id="sort" name="sort" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Newest First</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name A-Z</option>
                        <option value="file_size" {{ request('sort') == 'file_size' ? 'selected' : '' }}>File Size</option>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Avatars grid -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 border border-gray-200 dark:border-gray-700">
        @if($avatars->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 p-6">
                @foreach($avatars as $avatar)
                <div class="bg-white dark:bg-gray-700 rounded-lg shadow border border-gray-200 dark:border-gray-600 overflow-hidden hover:shadow-lg transition-shadow duration-200">
                    <div class="aspect-square bg-gray-100 dark:bg-gray-600 flex items-center justify-center">
                        @if($avatar->avatar_image)
                            <img src="{{ $avatar->image_url }}" alt="{{ $avatar->name ?? 'Avatar' }}" class="w-full h-full object-cover">
                        @else
                            <i class="fas fa-user-circle text-gray-400 text-6xl"></i>
                        @endif
                    </div>
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ $avatar->name ?? 'Unnamed Avatar' }}
                            </h3>
                            <div class="flex space-x-1">
                                <a href="{{ route('admin.avatars.show', $avatar->id) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300" title="View">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('admin.avatars.edit', $avatar->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300" title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                                <form action="{{ route('admin.avatars.destroy', $avatar->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300" title="Delete" onclick="return confirm('Are you sure you want to delete this avatar?')">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @if($avatar->description)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2 line-clamp-2">{{ $avatar->description }}</p>
                        @endif
                        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                            <span>{{ $avatar->formatted_file_size }}</span>
                            @if($avatar->width && $avatar->height)
                                <span>{{ $avatar->width }}x{{ $avatar->height }}</span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            {{ $avatar->created_at->format('M d, Y') }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-12">
                <i class="fas fa-user-circle text-gray-300 dark:text-gray-600 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No avatars found</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">Get started by uploading your first avatar.</p>
                <a href="{{ route('admin.avatars.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-plus mr-2"></i> Add Avatar
                </a>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($avatars->hasPages())
        <div class="mt-4">
            {{ $avatars->links() }}
        </div>
    @endif
</div>
@endsection