<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Exception;

class WebPImageService
{
    /**
     * Default WebP quality (0-100)
     */
    const DEFAULT_QUALITY = 75;

    /**
     * Allowed image MIME types for conversion
     */
    const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/jpg',
        'image/gif',
        'image/svg+xml',
        'image/webp'
    ];

    /**
     * Convert uploaded image to WebP format and store it
     *
     * @param UploadedFile $file
     * @param string $directory Storage directory (e.g., 'products', 'services')
     * @param int $quality WebP quality (0-100)
     * @param string|null $filename Custom filename (without extension)
     * @return string|null Returns the storage path or null on failure
     */
    public function convertAndStore(UploadedFile $file, string $directory, int $quality = self::DEFAULT_QUALITY, ?string $filename = null): ?string
    {
        try {
            // Validate the uploaded file
            if (!$this->validateFile($file)) {
                Log::error('WebP conversion failed: Invalid file', [
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize()
                ]);
                return null;
            }

            // Generate unique filename if not provided
            if (!$filename) {
                $filename = uniqid() . '_' . time();
            }

            $webpFilename = $filename . '.webp';
            $storagePath = $directory . '/' . $webpFilename;

            // Increase memory limit temporarily for image processing
            $originalMemoryLimit = ini_get('memory_limit');
            $fileSize = $file->getSize();

            // Increase memory based on file size - image processing requires much more memory than file size
            if ($fileSize > 5 * 1024 * 1024) { // Files larger than 5MB
                ini_set('memory_limit', '1024M'); // 1GB for very large files
            } elseif ($fileSize > 2 * 1024 * 1024) { // Files larger than 2MB
                ini_set('memory_limit', '512M'); // 512MB for large files
            } elseif ($fileSize > 1 * 1024 * 1024) { // Files larger than 1MB
                ini_set('memory_limit', '256M'); // 256MB for medium files
            } else {
                ini_set('memory_limit', '256M'); // Default increase for all image processing
            }

            // Log memory usage before processing
            Log::info('WebP conversion starting', [
                'file_size' => $fileSize,
                'memory_limit_before' => $originalMemoryLimit,
                'memory_limit_current' => ini_get('memory_limit'),
                'memory_usage_before' => memory_get_usage(true),
                'memory_peak_before' => memory_get_peak_usage(true)
            ]);

            // Convert image to WebP
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file);

            // Log memory usage after loading image
            Log::info('Image loaded into memory', [
                'memory_usage_after_load' => memory_get_usage(true),
                'memory_peak_after_load' => memory_get_peak_usage(true)
            ]);

            // Encode to WebP with specified quality
            $webpData = $image->toWebp($quality);

            // Log memory usage after conversion
            Log::info('WebP conversion completed', [
                'memory_usage_after_convert' => memory_get_usage(true),
                'memory_peak_after_convert' => memory_get_peak_usage(true)
            ]);

            // Restore original memory limit
            ini_set('memory_limit', $originalMemoryLimit);

            // Store the WebP image
            $stored = Storage::disk('public')->put($storagePath, $webpData);

            if (!$stored) {
                Log::error('WebP conversion failed: Could not store file', [
                    'storage_path' => $storagePath
                ]);
                return null;
            }

            // Sync to public directory for direct access
            $this->syncToPublicDirectory($storagePath);

            Log::info('WebP conversion successful', [
                'original_file' => $file->getClientOriginalName(),
                'storage_path' => $storagePath,
                'quality' => $quality,
                'original_size' => $file->getSize(),
                'compressed_size' => strlen($webpData)
            ]);

            return $storagePath;

        } catch (Exception $e) {
            // Restore memory limit in case of exception
            if (isset($originalMemoryLimit)) {
                ini_set('memory_limit', $originalMemoryLimit);
            }

            Log::error('WebP conversion failed with exception', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
                'directory' => $directory,
                'file_size' => $file->getSize(),
                'memory_limit' => ini_get('memory_limit'),
                'memory_usage' => memory_get_usage(true),
                'memory_peak' => memory_get_peak_usage(true),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Convert and store image, returning the full URL path
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param int $quality
     * @param string|null $filename
     * @return string|null Returns the full URL path or null on failure
     */
    public function convertAndStoreWithUrl(UploadedFile $file, string $directory, int $quality = self::DEFAULT_QUALITY, ?string $filename = null): ?string
    {
        $storagePath = $this->convertAndStore($file, $directory, $quality, $filename);
        
        if (!$storagePath) {
            return null;
        }

        // Return the URL path in the format expected by the application
        return '/storage/' . $storagePath;
    }

    /**
     * Validate uploaded file
     *
     * @param UploadedFile $file
     * @return bool
     */
    private function validateFile(UploadedFile $file): bool
    {
        // Check if file is valid
        if (!$file->isValid()) {
            return false;
        }

        // Check MIME type
        if (!in_array($file->getMimeType(), self::ALLOWED_MIME_TYPES)) {
            return false;
        }

        // Check file size (max 20MB)
        if ($file->getSize() > 20 * 1024 * 1024) {
            return false;
        }

        return true;
    }

    /**
     * Sync uploaded image to public directory for direct access
     *
     * @param string $storagePath
     * @return void
     */
    private function syncToPublicDirectory(string $storagePath): void
    {
        try {
            $sourceFile = storage_path('app/public/' . $storagePath);
            $publicFile = public_path('storage/' . $storagePath);

            // Make sure the directory exists
            $publicDir = dirname($publicFile);
            if (!file_exists($publicDir)) {
                mkdir($publicDir, 0755, true);
            }

            // Copy file to public directory if it doesn't exist
            if (file_exists($sourceFile) && !file_exists($publicFile)) {
                copy($sourceFile, $publicFile);
                Log::info('Synced WebP image to public directory', [
                    'source' => $sourceFile,
                    'destination' => $publicFile
                ]);
            }
        } catch (Exception $e) {
            Log::error('Failed to sync WebP image to public directory', [
                'error' => $e->getMessage(),
                'storage_path' => $storagePath
            ]);
        }
    }

    /**
     * Delete image from storage (works with symbolic links)
     *
     * @param string $imagePath The image path in various formats:
     *                         - '/storage/products/image.webp'
     *                         - 'products/image.webp'
     *                         - 'https://localhost/storage/products/image.webp'
     *                         - 'http://127.0.0.1:8000/storage/products/image.webp'
     * @return bool
     */
    public function deleteImage(string $imagePath): bool
    {
        try {
            // Normalize the path to get the relative storage path
            $normalizedPath = $this->normalizeImagePath($imagePath);

            if (empty($normalizedPath)) {
                Log::warning('Empty normalized path for image deletion', [
                    'original_path' => $imagePath
                ]);
                return false;
            }

            // Delete from storage disk (this handles the symbolic link correctly)
            $deleted = false;
            if (Storage::disk('public')->exists($normalizedPath)) {
                Storage::disk('public')->delete($normalizedPath);
                $deleted = true;
                Log::info('Image deleted from storage disk', [
                    'normalized_path' => $normalizedPath,
                    'original_path' => $imagePath
                ]);
            } else {
                Log::warning('Image file not found in storage', [
                    'normalized_path' => $normalizedPath,
                    'original_path' => $imagePath
                ]);
            }

            // Verify deletion by checking if file still exists
            if (Storage::disk('public')->exists($normalizedPath)) {
                Log::error('Image still exists after deletion attempt', [
                    'normalized_path' => $normalizedPath,
                    'original_path' => $imagePath
                ]);
                return false;
            }

            if ($deleted) {
                Log::info('Image deleted successfully', [
                    'image_path' => $imagePath,
                    'normalized_path' => $normalizedPath
                ]);
            }

            return $deleted;
        } catch (Exception $e) {
            Log::error('Failed to delete image', [
                'error' => $e->getMessage(),
                'image_path' => $imagePath,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Normalize image path to get the relative storage path
     *
     * @param string $imagePath
     * @return string
     */
    private function normalizeImagePath(string $imagePath): string
    {
        if (empty($imagePath)) {
            return '';
        }

        // Remove any URL prefixes (http://, https://, domain names)
        $path = preg_replace('#^https?://[^/]+#', '', $imagePath);

        // Handle /images/ routes that map to storage paths
        if (preg_match('#^/?images/(.+)$#', $path, $matches)) {
            $path = $matches[1]; // Extract the part after /images/
        }

        // Remove /storage/ prefix if present
        $path = preg_replace('#^/?storage/#', '', $path);

        // Remove any leading slashes
        $path = ltrim($path, '/');

        return $path;
    }

    /**
     * Check if WebP format is supported by the current system
     *
     * @return bool
     */
    public function isWebPSupported(): bool
    {
        try {
            // Try to create a simple WebP image to test support
            $manager = new ImageManager(new Driver());
            $testImage = $manager->create(1, 1);
            $testImage->toWebp(75);
            return true;
        } catch (Exception $e) {
            Log::warning('WebP format not supported on this system', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
