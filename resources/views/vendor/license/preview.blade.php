<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>License Document Preview</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #1a1a1a;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        
        .preview-container {
            position: relative;
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .preview-image {
            max-width: 95%;
            max-height: 95%;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        }
        
        .preview-pdf {
            width: 95%;
            height: 95%;
            border: none;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        }
        
        .controls {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }
        
        .control-btn {
            background: rgba(0, 0, 0, 0.7);
            color: white;
            border: none;
            padding: 12px 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 16px;
        }
        
        .control-btn:hover {
            background: rgba(0, 0, 0, 0.9);
        }
        
        .loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 18px;
        }
        
        .error-message {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #ef4444;
            text-align: center;
            font-size: 18px;
        }
        
        .vendor-info {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 16px;
            border-radius: 8px;
            max-width: 300px;
        }
    </style>
</head>
<body>
    <div class="preview-container">
        <!-- Loading indicator -->
        <div id="loading" class="loading">
            <i class="fas fa-spinner fa-spin mr-2"></i>
            Loading document...
        </div>

        <!-- Error message -->
        <div id="error" class="error-message hidden">
            <div>
                <svg class="w-12 h-12 mx-auto mb-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p>Unable to load document</p>
                <p class="text-sm mt-2">The document may be corrupted or in an unsupported format.</p>
            </div>
        </div>

        @php
            $fileExtension = strtolower(pathinfo($license->license_file_name, PATHINFO_EXTENSION));
            $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']);
            $isPdf = $fileExtension === 'pdf';
            $fileUrl = route('vendor.license.view', $license->id);
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
                src="{{ $fileUrl }}" 
                class="preview-pdf hidden"
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
                    <p>Unsupported file format</p>
                    <p class="text-sm mt-2">This file type cannot be previewed in the browser.</p>
                    <a href="{{ $fileUrl }}" 
                       download="{{ $license->license_file_name }}"
                       class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                        <i class="fas fa-download mr-2"></i>
                        Download File
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Controls -->
    <div class="controls">
        <button onclick="window.close()" class="control-btn" title="Close">
            <i class="fas fa-times"></i>
        </button>
        <a href="{{ $fileUrl }}" 
           download="{{ $license->license_file_name }}" 
           class="control-btn" 
           title="Download">
            <i class="fas fa-download"></i>
        </a>
        <button onclick="window.history.back()" class="control-btn" title="Back to License Management">
            <i class="fas fa-arrow-left"></i>
        </button>
    </div>

    <!-- License Information -->
    <div class="vendor-info">
        <div class="font-medium text-lg mb-2">{{ auth()->user()->name }}</div>
        @if(auth()->user()->company)
            <div class="text-sm text-gray-300 mb-1">{{ auth()->user()->company->name }}</div>
        @endif
        <div class="text-sm text-gray-300 mb-1">Status: 
            <span class="font-medium">{{ ucfirst($license->status) }}</span>
        </div>
        <div class="text-sm text-gray-300">Submitted: {{ $license->created_at->format('M d, Y') }}</div>
    </div>

    <script>
        function hideLoading() {
            document.getElementById('loading').style.display = 'none';
            const image = document.getElementById('license-image');
            const pdf = document.getElementById('license-pdf');
            
            if (image) {
                image.classList.remove('hidden');
            }
            if (pdf) {
                pdf.classList.remove('hidden');
            }
        }

        function showError() {
            document.getElementById('loading').style.display = 'none';
            document.getElementById('error').classList.remove('hidden');
        }

        // Handle escape key to close
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.close();
            }
        });

        // Auto-hide loading after 10 seconds if document doesn't load
        setTimeout(function() {
            const loading = document.getElementById('loading');
            if (loading.style.display !== 'none') {
                showError();
            }
        }, 10000);
    </script>
</body>
</html>
