<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flutter Image Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .image-test {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .image-test img {
            max-width: 300px;
            max-height: 300px;
            display: block;
            margin-bottom: 10px;
        }
        .image-path {
            font-family: monospace;
            background-color: #f5f5f5;
            padding: 5px;
            margin: 5px 0;
            word-break: break-all;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
        h2 {
            margin-top: 30px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1>Flutter Image Test Page</h1>
    <p>This page tests various image loading methods to help debug Flutter image loading issues.</p>
    
    <h2>Configuration</h2>
    <div class="image-test">
        <div><strong>APP_URL:</strong> <span class="image-path">{{ config('app.url') }}</span></div>
        <div><strong>Storage Link Status:</strong> 
            @if(file_exists(public_path('storage')))
                <span class="success">✓ Storage link exists</span>
            @else
                <span class="error">✗ Storage link does not exist. Run 'php artisan storage:link'</span>
            @endif
        </div>
        <div><strong>Storage Path:</strong> <span class="image-path">{{ storage_path('app/public') }}</span></div>
        <div><strong>Public Storage Path:</strong> <span class="image-path">{{ public_path('storage') }}</span></div>
    </div>
    
    <h2>Direct Image Loading</h2>
    <div class="image-test">
        <h3>Public Image (from /public/images/products)</h3>
        <div><strong>Path:</strong> <span class="image-path">/images/products/smartphone-x.jpg</span></div>
        <div><strong>URL:</strong> <span class="image-path">{{ url('/images/products/smartphone-x.jpg') }}</span></div>
        <div>
            <strong>Image:</strong><br>
            <img src="{{ url('/images/products/smartphone-x.jpg') }}" alt="Smartphone X" onerror="this.onerror=null; this.src='{{ asset('images/placeholder.jpg') }}'; this.nextElementSibling.style.display='block';">
            <div class="error" style="display: none;">Image failed to load</div>
        </div>
    </div>
    
    <div class="image-test">
        <h3>Storage Image (from /storage/products)</h3>
        @php
            $storageImage = \App\Models\Product::first()->image ?? null;
        @endphp
        
        @if($storageImage)
            <div><strong>Path:</strong> <span class="image-path">{{ $storageImage }}</span></div>
            <div><strong>URL:</strong> <span class="image-path">{{ url($storageImage) }}</span></div>
            <div>
                <strong>Image:</strong><br>
                <img src="{{ url($storageImage) }}" alt="Storage Image" onerror="this.onerror=null; this.src='{{ asset('images/placeholder.jpg') }}'; this.nextElementSibling.style.display='block';">
                <div class="error" style="display: none;">Image failed to load</div>
            </div>
        @else
            <div class="error">No product images found in database</div>
        @endif
    </div>
    
    <h2>API Response Test</h2>
    <div class="image-test">
        <p>Check the API response at: <a href="{{ url('/api/test/images') }}" target="_blank">{{ url('/api/test/images') }}</a></p>
        <p>This endpoint returns JSON with image URLs that can be tested in your Flutter app.</p>
    </div>
    
    <h2>Flutter Integration Tips</h2>
    <div class="image-test">
        <h3>Network Image Loading in Flutter</h3>
        <pre class="image-path">
// Basic network image
Image.network(
  'http://10.0.2.2:8000/images/products/smartphone-x.jpg',
  errorBuilder: (context, error, stackTrace) {
    return Text('Error loading image');
  },
)

// With CachedNetworkImage
CachedNetworkImage(
  imageUrl: 'http://10.0.2.2:8000/images/products/smartphone-x.jpg',
  placeholder: (context, url) => CircularProgressIndicator(),
  errorWidget: (context, url, error) => Icon(Icons.error),
)
        </pre>
        
        <h3>Common Issues</h3>
        <ul>
            <li>For Android emulators, use <code>10.0.2.2</code> to access your computer's localhost</li>
            <li>For physical devices, use your computer's actual IP address</li>
            <li>Make sure to add internet permission in <code>AndroidManifest.xml</code></li>
            <li>For iOS, configure App Transport Security settings</li>
            <li>Check that your Laravel server is accessible from your device/emulator</li>
        </ul>
    </div>
</body>
</html>
