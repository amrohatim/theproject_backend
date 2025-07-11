<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImageController extends Controller
{
    /**
     * Serve product images
     *
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function serveProductImage($filename)
    {
        try {
            // Validate filename to prevent directory traversal
            if (!$this->isValidFilename($filename)) {
                Log::warning("Invalid product image filename requested: {$filename}");
                return $this->returnPlaceholderImage();
            }

            // Check multiple possible paths for product images
            $possiblePaths = [
                "products/{$filename}",           // Main product images
                "products/colors/{$filename}",   // Product color images
            ];

            $foundPath = null;
            $fullPath = null;

            // Try each possible path
            foreach ($possiblePaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    $foundPath = $path;
                    $fullPath = Storage::disk('public')->path($path);

                    // Verify file exists on filesystem
                    if (file_exists($fullPath)) {
                        Log::info("Product image found at: {$path}");
                        break;
                    } else {
                        Log::warning("Product image exists in storage but missing from filesystem: {$fullPath}");
                        $foundPath = null;
                        $fullPath = null;
                    }
                }
            }

            // If no image found in any location
            if (!$foundPath || !$fullPath) {
                Log::info("Product image not found in any location: {$filename}");
                return $this->returnPlaceholderImage();
            }

            // Get file info
            $mimeType = $this->getMimeType($fullPath);
            $fileSize = filesize($fullPath);

            // Return the file with appropriate headers
            return Response::file($fullPath, [
                'Content-Type' => $mimeType,
                'Content-Length' => $fileSize,
                'Cache-Control' => 'public, max-age=31536000', // Cache for 1 year
                'Expires' => gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT',
            ]);

        } catch (\Exception $e) {
            Log::error("Error serving product image {$filename}: " . $e->getMessage());
            return $this->returnPlaceholderImage();
        }
    }

    /**
     * Serve product color images specifically
     *
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function serveProductColorImage($filename)
    {
        try {
            // Validate filename to prevent directory traversal
            if (!$this->isValidFilename($filename)) {
                Log::warning("Invalid product color image filename requested: {$filename}");
                return $this->returnPlaceholderImage();
            }

            // Check if file exists in products/colors directory
            $path = "products/colors/{$filename}";
            if (!Storage::disk('public')->exists($path)) {
                Log::info("Product color image not found: {$path}");
                return $this->returnPlaceholderImage();
            }

            // Get the full file path
            $fullPath = Storage::disk('public')->path($path);

            // Verify file exists on filesystem
            if (!file_exists($fullPath)) {
                Log::warning("Product color image file missing from filesystem: {$fullPath}");
                return $this->returnPlaceholderImage();
            }

            // Get file info
            $mimeType = $this->getMimeType($fullPath);
            $fileSize = filesize($fullPath);

            // Return the file with appropriate headers
            return Response::file($fullPath, [
                'Content-Type' => $mimeType,
                'Content-Length' => $fileSize,
                'Cache-Control' => 'public, max-age=31536000', // Cache for 1 year
                'Expires' => gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT',
            ]);

        } catch (\Exception $e) {
            Log::error("Error serving product color image {$filename}: " . $e->getMessage());
            return $this->returnPlaceholderImage();
        }
    }

    /**
     * Serve service images
     *
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function serveServiceImage($filename)
    {
        try {
            // Validate filename to prevent directory traversal
            if (!$this->isValidFilename($filename)) {
                Log::warning("Invalid service image filename requested: {$filename}");
                return $this->returnPlaceholderImage();
            }

            // Check if file exists in storage
            $path = "services/{$filename}";
            if (!Storage::disk('public')->exists($path)) {
                Log::info("Service image not found: {$path}");
                return $this->returnPlaceholderImage();
            }

            // Get the full file path
            $fullPath = Storage::disk('public')->path($path);
            
            // Verify file exists on filesystem
            if (!file_exists($fullPath)) {
                Log::warning("Service image file missing from filesystem: {$fullPath}");
                return $this->returnPlaceholderImage();
            }

            // Get file info
            $mimeType = $this->getMimeType($fullPath);
            $fileSize = filesize($fullPath);

            // Return the file with appropriate headers
            return Response::file($fullPath, [
                'Content-Type' => $mimeType,
                'Content-Length' => $fileSize,
                'Cache-Control' => 'public, max-age=31536000', // Cache for 1 year
                'Expires' => gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT',
            ]);

        } catch (\Exception $e) {
            Log::error("Error serving service image {$filename}: " . $e->getMessage());
            return $this->returnPlaceholderImage();
        }
    }

    /**
     * Serve general storage images (for backward compatibility)
     *
     * @param string $folder
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function serveStorageImage($folder, $filename)
    {
        try {
            // Validate inputs
            if (!$this->isValidFolder($folder) || !$this->isValidFilename($filename)) {
                Log::warning("Invalid storage image request: {$folder}/{$filename}");
                return $this->returnPlaceholderImage();
            }

            // Check if file exists in storage
            $path = "{$folder}/{$filename}";
            if (!Storage::disk('public')->exists($path)) {
                Log::info("Storage image not found: {$path}");
                return $this->returnPlaceholderImage();
            }

            // Get the full file path
            $fullPath = Storage::disk('public')->path($path);
            
            // Verify file exists on filesystem
            if (!file_exists($fullPath)) {
                Log::warning("Storage image file missing from filesystem: {$fullPath}");
                return $this->returnPlaceholderImage();
            }

            // Get file info
            $mimeType = $this->getMimeType($fullPath);
            $fileSize = filesize($fullPath);

            // Return the file with appropriate headers
            return Response::file($fullPath, [
                'Content-Type' => $mimeType,
                'Content-Length' => $fileSize,
                'Cache-Control' => 'public, max-age=31536000', // Cache for 1 year
                'Expires' => gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT',
            ]);

        } catch (\Exception $e) {
            Log::error("Error serving storage image {$folder}/{$filename}: " . $e->getMessage());
            return $this->returnPlaceholderImage();
        }
    }

    /**
     * Validate filename to prevent directory traversal attacks
     *
     * @param string $filename
     * @return bool
     */
    private function isValidFilename($filename)
    {
        // Check for directory traversal attempts
        if (strpos($filename, '..') !== false || strpos($filename, '/') !== false || strpos($filename, '\\') !== false) {
            return false;
        }

        // Check for valid image extensions
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        return in_array($extension, $allowedExtensions);
    }

    /**
     * Validate folder name
     *
     * @param string $folder
     * @return bool
     */
    private function isValidFolder($folder)
    {
        $allowedFolders = ['products', 'services', 'categories', 'users', 'merchants', 'branches', 'companies', 'merchant-logos'];
        return in_array($folder, $allowedFolders);
    }

    /**
     * Get MIME type for file
     *
     * @param string $filePath
     * @return string
     */
    private function getMimeType($filePath)
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
        ];

        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }

    /**
     * Return a placeholder image when the requested image is not found
     *
     * @return \Illuminate\Http\Response
     */
    private function returnPlaceholderImage()
    {
        // Check if placeholder exists
        $placeholderPath = public_path('images/placeholder.png');
        
        if (file_exists($placeholderPath)) {
            return Response::file($placeholderPath, [
                'Content-Type' => 'image/png',
                'Cache-Control' => 'public, max-age=86400', // Cache for 1 day
            ]);
        }

        // If no placeholder exists, return a simple 1x1 transparent PNG
        $transparentPng = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
        
        return Response::make($transparentPng, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
