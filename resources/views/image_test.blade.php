<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Test Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .product {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .product img {
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
    </style>
</head>
<body>
    <h1>Image Test Page</h1>
    <p>This page tests the visibility of product images from the database.</p>
    
    <h2>Products</h2>
    @foreach($products as $product)
    <div class="product">
        <h3>{{ $product->name }} (ID: {{ $product->id }})</h3>
        
        <div>
            <strong>Image Path:</strong>
            <div class="image-path">{{ $product->image }}</div>
        </div>
        
        <div>
            <strong>Full URL:</strong>
            <div class="image-path">{{ url($product->image) }}</div>
        </div>
        
        <div>
            <strong>Storage URL:</strong>
            <div class="image-path">{{ Storage::url($product->image) }}</div>
        </div>
        
        <div>
            <strong>Direct Image:</strong><br>
            @if($product->image)
                <img src="{{ url($product->image) }}" alt="{{ $product->name }}" onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}'; this.nextElementSibling.style.display='block';">
                <div class="error" style="display: none;">Image failed to load from: {{ url($product->image) }}</div>
            @else
                <div class="error">No image path available</div>
            @endif
        </div>
        
        <div>
            <strong>Storage Image:</strong><br>
            @if($product->image)
                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}'; this.nextElementSibling.style.display='block';">
                <div class="error" style="display: none;">Image failed to load from: {{ Storage::url($product->image) }}</div>
            @else
                <div class="error">No image path available</div>
            @endif
        </div>
        
        <div>
            <strong>Public Path Image:</strong><br>
            @if($product->image)
                @php
                    $publicPath = str_replace('/storage/', '', $product->image);
                @endphp
                <img src="{{ asset('storage/' . $publicPath) }}" alt="{{ $product->name }}" onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}'; this.nextElementSibling.style.display='block';">
                <div class="error" style="display: none;">Image failed to load from: {{ asset('storage/' . $publicPath) }}</div>
            @else
                <div class="error">No image path available</div>
            @endif
        </div>
    </div>
    @endforeach
    
    <h2>Storage Link Status</h2>
    <div>
        <p>Storage public directory: {{ storage_path('app/public') }}</p>
        <p>Public storage link: {{ public_path('storage') }}</p>
        
        @if(file_exists(public_path('storage')))
            <p class="success">✓ Storage link exists</p>
        @else
            <p class="error">✗ Storage link does not exist. Run 'php artisan storage:link'</p>
        @endif
    </div>
    
    <h2>Test Image Upload</h2>
    <form action="{{ route('test.upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="test_image">Upload Test Image:</label>
            <input type="file" name="test_image" id="test_image">
        </div>
        <button type="submit" style="margin-top: 10px;">Upload</button>
    </form>
    
    @if(session('test_image_path'))
        <div style="margin-top: 20px;">
            <h3>Uploaded Test Image</h3>
            <div>
                <strong>Image Path:</strong>
                <div class="image-path">{{ session('test_image_path') }}</div>
            </div>
            <div>
                <strong>Image URL:</strong>
                <div class="image-path">{{ url(session('test_image_path')) }}</div>
            </div>
            <div>
                <strong>Image:</strong><br>
                <img src="{{ url(session('test_image_path')) }}" alt="Test Image" style="max-width: 300px;">
            </div>
        </div>
    @endif
</body>
</html>
