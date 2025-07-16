<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>License Preview - {{ $license->user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #1a1a1a;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        .preview-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #1a1a1a;
        }
        .preview-image {
            max-width: 95vw;
            max-height: 95vh;
            object-fit: contain;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
        .preview-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }
        .control-btn {
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .control-btn:hover {
            background-color: rgba(0, 0, 0, 0.9);
        }
        .vendor-info {
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 15px;
            border-radius: 5px;
            font-size: 14px;
            z-index: 1000;
        }
        .loading {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 18px;
            z-index: 1000;
        }
        .error-message {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #ef4444;
            font-size: 18px;
            text-align: center;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <div class="preview-container">
        <!-- Loading indicator -->
        <div id="loading" class="loading">
            <div class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Loading license document...
            </div>
        </div>

        <!-- Error message -->
        <div id="error" class="error-message hidden">
            <div>
                <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <h3 class="text-lg font-medium mb-2">Unable to load license document</h3>
                <p class="text-sm text-gray-400">The license file could not be displayed. It may be corrupted or in an unsupported format.</p>
                <div class="mt-4">
                    <a href="{{ route('admin.vendor-licenses.download', $license->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download File
                    </a>
                </div>
            </div>
        </div>

        <!-- License document display -->
        @if($license->license_file_path)
            @php
                $fileExtension = strtolower(pathinfo($license->license_file_path, PATHINFO_EXTENSION));
                $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                $isPdf = $fileExtension === 'pdf';
                $fileUrl = asset('storage/' . $license->license_file_path);
            @endphp

            @if($isImage)
                <img 
                    id="license-image" 
                    src="{{ $fileUrl }}" 
                    alt="License Document" 
                    class="preview-image hidden"
                    onload="hideLoading()"
                    onerror="showError()"
                >
            @elseif($isPdf)
                <iframe 
                    id="license-pdf"
                    src="{{ route('admin.vendor-licenses.view', $license->id) }}" 
                    class="preview-image hidden"
                    frameborder="0"
                    onload="hideLoading()"
                    onerror="showError()">
                </iframe>
            @else
                <div class="error-message">
                    <div>
                        <svg class="w-12 h-12 mx-auto mb-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-lg font-medium mb-2 text-white">Unsupported File Format</h3>
                        <p class="text-sm text-gray-400">This file format cannot be previewed in the browser.</p>
                        <div class="mt-4">
                            <a href="{{ route('admin.vendor-licenses.download', $license->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download File
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="error-message">
                <div>
                    <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-lg font-medium mb-2">No License File</h3>
                    <p class="text-sm text-gray-400">No license document has been uploaded for this vendor.</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Vendor Information -->
    <div class="vendor-info">
        <div class="font-medium text-lg mb-2">{{ $license->user->name }}</div>
        @if($license->user->company)
            <div class="text-sm text-gray-300 mb-1">{{ $license->user->company->name }}</div>
        @endif
        <div class="text-sm text-gray-300 mb-1">Status: 
            <span class="font-medium">{{ ucfirst($license->status) }}</span>
        </div>
        <div class="text-sm text-gray-300">Submitted: {{ $license->created_at->format('M d, Y') }}</div>
    </div>

    <!-- Controls -->
    <div class="preview-controls">
        <button onclick="window.close()" class="control-btn">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            Close
        </button>
        
        <a href="{{ route('admin.vendor-licenses.download', $license->id) }}" class="control-btn">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Download
        </a>
        
        <a href="{{ route('admin.vendor-licenses.show', $license->id) }}" class="control-btn">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Details
        </a>
    </div>

    <script>
        function hideLoading() {
            const loading = document.getElementById('loading');
            const licenseImage = document.getElementById('license-image');
            const licensePdf = document.getElementById('license-pdf');
            
            if (loading) {
                loading.style.display = 'none';
            }
            
            if (licenseImage) {
                licenseImage.classList.remove('hidden');
            }
            
            if (licensePdf) {
                licensePdf.classList.remove('hidden');
            }
        }

        function showError() {
            const loading = document.getElementById('loading');
            const error = document.getElementById('error');
            
            if (loading) {
                loading.style.display = 'none';
            }
            
            if (error) {
                error.classList.remove('hidden');
            }
        }

        // Handle keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.close();
            }
        });

        // Auto-hide loading after 10 seconds as fallback
        setTimeout(function() {
            const loading = document.getElementById('loading');
            if (loading && loading.style.display !== 'none') {
                showError();
            }
        }, 10000);
    </script>
</body>
</html>
