<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProviderImageHelper
{
    /**
     * Fix image path for display in provider dashboard templates
     *
     * @param string|null $imagePath
     * @return string|null
     */
    public static function fixPath($imagePath)
    {
        if (empty($imagePath)) {
            return null;
        }

        // If image starts with http or https, it's already a full URL
        if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
            return $imagePath;
        }

        // Remove any leading slash
        $imagePath = ltrim($imagePath, '/');

        // If the path points to public storage, trust the disk check (symlink may be restricted)
        if (str_starts_with($imagePath, 'storage/')) {
            $storageRelativePath = substr($imagePath, strlen('storage/'));
            if (Storage::disk('public')->exists($storageRelativePath)) {
                return '/' . $imagePath;
            }
        }

        // Check if file exists in public directory
        if (file_exists(public_path($imagePath))) {
            return '/' . $imagePath;
        }

        // Try to find the image in common locations
        $filename = basename($imagePath);
        $possiblePaths = [
            'images/products/' . $filename,
            'images/provider_products/' . $filename,
            'images/users/' . $filename,
            'storage/products/' . $filename,
            'storage/provider_products/' . $filename,
            'storage/users/' . $filename,
        ];

        foreach ($possiblePaths as $path) {
            if (str_starts_with($path, 'storage/')) {
                $storageRelativePath = substr($path, strlen('storage/'));
                if (Storage::disk('public')->exists($storageRelativePath)) {
                    Log::info("Found image at alternative path: {$path} for original: {$imagePath}");
                    return '/' . $path;
                }
                continue;
            }

            if (file_exists(public_path($path))) {
                Log::info("Found image at alternative path: {$path} for original: {$imagePath}");
                return '/' . $path;
            }
        }

        // If we can't find the image, return a placeholder
        Log::warning("Image not found: {$imagePath}, using placeholder");
        return '/images/placeholder.png';
    }

    /**
     * Create a placeholder image if it doesn't exist
     *
     * @param string $path Full path to create the placeholder
     * @return bool
     */
    public static function createPlaceholderImage($path)
    {
        // Ensure directory exists
        $directory = dirname($path);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Check if we have a placeholder to copy
        $placeholderPath = public_path('images/placeholder.png');
        if (file_exists($placeholderPath)) {
            return copy($placeholderPath, $path);
        }

        // Create a simple placeholder image
        $img = imagecreatetruecolor(100, 100);
        $bgColor = imagecolorallocate($img, 200, 200, 200);
        $textColor = imagecolorallocate($img, 50, 50, 50);
        imagefill($img, 0, 0, $bgColor);
        imagestring($img, 5, 10, 40, 'No Image', $textColor);
        $result = imagepng($img, $path);
        imagedestroy($img);

        return $result;
    }

    /**
     * Check if an image exists and is accessible
     *
     * @param string $path
     * @return bool
     */
    public static function imageExists($path)
    {
        if (empty($path)) {
            return false;
        }

        // Remove any leading slash
        $path = ltrim($path, '/');

        if (str_starts_with($path, 'storage/')) {
            $storageRelativePath = substr($path, strlen('storage/'));
            return Storage::disk('public')->exists($storageRelativePath);
        }

        // Check if file exists in public directory
        return file_exists(public_path($path));
    }

    /**
     * Get the correct image URL for a provider product
     *
     * @param string|null $imagePath
     * @return string
     */
    public static function getProviderProductImageUrl($imagePath)
    {
        $fixedPath = self::fixPath($imagePath);
        
        if ($fixedPath) {
            return $fixedPath;
        }
        
        return '/images/placeholder.png';
    }

    /**
     * Get the correct image URL for a user profile
     *
     * @param string|null $imagePath
     * @return string
     */
    public static function getUserProfileImageUrl($imagePath)
    {
        $fixedPath = self::fixPath($imagePath);
        
        if ($fixedPath) {
            return $fixedPath;
        }
        
        return '/images/placeholder.png';
    }
}
