@extends('layouts.dashboard')

@section('title', 'Image Test')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Image Test Dashboard</h1>
                <div class="flex space-x-2">
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                        <i class="fas fa-check-circle mr-1"></i>
                        System Active
                    </span>
                </div>
            </div>

            <!-- System Information -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-server text-blue-500 text-xl mr-3"></i>
                        <div>
                            <h3 class="font-semibold text-gray-800">App URL</h3>
                            <p class="text-sm text-gray-600">{{ config('app.url') }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-link text-green-500 text-xl mr-3"></i>
                        <div>
                            <h3 class="font-semibold text-gray-800">Storage Link</h3>
                            <p class="text-sm text-gray-600">
                                @if(file_exists(public_path('storage')))
                                    <span class="text-green-600">✓ Active</span>
                                @else
                                    <span class="text-red-600">✗ Missing</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-purple-50 p-4 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-database text-purple-500 text-xl mr-3"></i>
                        <div>
                            <h3 class="font-semibold text-gray-800">Products</h3>
                            <p class="text-sm text-gray-600">{{ $products->count() }} items</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Image Test Results -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <i class="fas fa-image mr-2"></i>
                    Image Test Results
                </h2>
                
                @if($products->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($products->take(6) as $product)
                            <div class="border rounded-lg p-4 bg-gray-50">
                                <h4 class="font-medium text-gray-800 mb-2">{{ $product->name }}</h4>
                                
                                @if($product->image)
                                    <div class="mb-3">
                                        <img src="{{ $product->image }}" 
                                             alt="{{ $product->name }}" 
                                             class="w-full h-32 object-cover rounded"
                                             onerror="this.parentElement.innerHTML='<div class=\'w-full h-32 bg-red-100 rounded flex items-center justify-center\'><span class=\'text-red-500 text-sm\'>Image not found</span></div>'">
                                    </div>
                                    <p class="text-xs text-gray-500 break-all">{{ $product->image }}</p>
                                @else
                                    <div class="w-full h-32 bg-gray-200 rounded flex items-center justify-center mb-3">
                                        <span class="text-gray-500 text-sm">No image</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-box-open text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-600">No products found in the database.</p>
                    </div>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                    
                    <button onclick="window.location.reload()" 
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Refresh Test
                    </button>
                    
                    <a href="{{ url('/storage') }}" 
                       target="_blank"
                       class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-external-link-alt mr-2"></i>
                        Check Storage
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.container {
    min-height: calc(100vh - 200px);
}
</style>
@endsection
