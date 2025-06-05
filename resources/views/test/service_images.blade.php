<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Images Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Service Images Test</h1>
        
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Service Image Display Test</h2>
            <p class="text-gray-600 mb-4">Testing service image display with the updated ImageHelper and Service model.</p>
            
            @if($services->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($services as $service)
                        <div class="border rounded-lg p-4">
                            <div class="mb-4">
                                <h3 class="font-semibold text-lg">{{ $service->name }}</h3>
                                <p class="text-sm text-gray-600">
                                    Branch: {{ $service->branch->name ?? 'N/A' }} | 
                                    Category: {{ $service->category->name ?? 'N/A' }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    Price: ${{ number_format($service->price, 2) }} | 
                                    Duration: {{ $service->duration }} min
                                </p>
                            </div>
                            
                            <!-- Service Image Display -->
                            <div class="mb-4">
                                <h4 class="font-medium mb-2">Service Image:</h4>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                                    @if($service->image)
                                        <img src="{{ $service->image }}" 
                                             alt="{{ $service->name }}" 
                                             class="max-w-full h-48 object-cover mx-auto rounded"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                        <div style="display: none;" class="text-red-500">
                                            <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                                            <p>Image failed to load</p>
                                        </div>
                                    @else
                                        <div class="text-gray-400">
                                            <i class="fas fa-image text-2xl mb-2"></i>
                                            <p>No image available</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Raw Image Data -->
                            <div class="mb-4">
                                <h4 class="font-medium mb-2">Raw Image Data:</h4>
                                <div class="bg-gray-100 p-2 rounded text-xs">
                                    <p><strong>Raw DB Value:</strong> {{ $service->getRawOriginal('image') ?? 'NULL' }}</p>
                                    <p><strong>Processed URL:</strong> {{ $service->image ?? 'NULL' }}</p>
                                </div>
                            </div>
                            
                            <!-- Test Links -->
                            <div class="space-y-2">
                                <h4 class="font-medium">Test Links:</h4>
                                @if($service->image)
                                    @php
                                        $filename = basename($service->getRawOriginal('image'));
                                    @endphp
                                    <div class="text-xs space-y-1">
                                        <a href="{{ $service->image }}" target="_blank" class="block text-blue-600 hover:underline">
                                            <i class="fas fa-external-link-alt"></i> Model URL
                                        </a>
                                        <a href="{{ url('/storage/services/' . $filename) }}" target="_blank" class="block text-blue-600 hover:underline">
                                            <i class="fas fa-external-link-alt"></i> Direct Storage URL
                                        </a>
                                        <a href="{{ url('/api/service-image/' . $filename) }}" target="_blank" class="block text-blue-600 hover:underline">
                                            <i class="fas fa-external-link-alt"></i> API Endpoint
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-circle text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600">No services with images found in the database.</p>
                </div>
            @endif
        </div>
        
        <!-- Debug Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Debug Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <h3 class="font-medium mb-2">Environment:</h3>
                    <ul class="space-y-1">
                        <li><strong>APP_URL:</strong> {{ config('app.url') }}</li>
                        <li><strong>Storage Link Exists:</strong> {{ file_exists(public_path('storage')) ? 'Yes' : 'No' }}</li>
                        <li><strong>Services Directory Exists:</strong> {{ file_exists(public_path('storage/services')) ? 'Yes' : 'No' }}</li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-medium mb-2">Statistics:</h3>
                    <ul class="space-y-1">
                        <li><strong>Total Services:</strong> {{ \App\Models\Service::count() }}</li>
                        <li><strong>Services with Images:</strong> {{ \App\Models\Service::whereNotNull('image')->count() }}</li>
                        <li><strong>Services Displayed:</strong> {{ $services->count() }}</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="mt-8 text-center">
            <a href="{{ url('/') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                <i class="fas fa-home mr-2"></i> Back to Home
            </a>
        </div>
    </div>
</body>
</html>
