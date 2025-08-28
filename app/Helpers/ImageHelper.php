<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class ImageHelper
{
    /**
     * Fix image path for display in templates
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

        // Check if file exists in public directory
        if (file_exists(public_path($imagePath))) {
            return $imagePath;
        }

        // Special case for storage paths with potentially missing 'storage/' prefix
        if (str_starts_with($imagePath, 'products/') || str_starts_with($imagePath, 'users/')) {
            $storagePath = 'storage/' . $imagePath;
            if (file_exists(public_path($storagePath))) {
                return $storagePath;
            } else {
                // Attempt to fix path by ensuring storage/ prefix
                return 'storage/' . $imagePath;
            }
        }

        // If path starts with storage/ already
        if (str_starts_with($imagePath, 'storage/')) {
            return $imagePath;
        }

        // Last resort: make an educated guess and add storage/ prefix if missing
        return 'storage/' . $imagePath;
    }

    /**
     * Get the relative path for an image (for use with asset() helper)
     *
     * @param string|null $imagePath
     * @return string|null
     */
    public static function getImagePath($imagePath)
    {
        if (empty($imagePath)) {
            // Return a default placeholder image path
            $placeholderPath = "images/placeholder.png";

            // Check if placeholder exists, if not create a simple one
            if (!file_exists(public_path($placeholderPath))) {
                self::createPlaceholderImage(public_path($placeholderPath));
            }

            return $placeholderPath;
        }

        // If the image path already starts with http, return it as is (external URL)
        if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
            return $imagePath;
        }

        // Remove any double slashes that might be in the path
        $imagePath = str_replace('//', '/', $imagePath);

        // Normalize the path by removing leading slash
        $normalizedPath = ltrim($imagePath, '/');

        // Extract the filename from the path
        $filename = basename($normalizedPath);

        // Log the image path we're trying to resolve
        Log::debug("Resolving image path: {$imagePath}, filename: {$filename}");

        // Create an array of possible paths to check
        $possiblePaths = [
            // Check in public/storage/products (most common for products)
            "storage/products/{$filename}",

            // Check in public/images/products
            "images/products/{$filename}",

            // Check in public/storage/categories
            "storage/categories/{$filename}",

            // Check in public/images/categories
            "images/categories/{$filename}",

            // Check in public/storage/deals
            "storage/deals/{$filename}",

            // Check in public/images/deals
            "images/deals/{$filename}",

            // Check in public/storage/branches
            "storage/branches/{$filename}",

            // Check in public/images/branches
            "images/branches/{$filename}",

            // Check in public/storage/merchant-logos
            "storage/merchant-logos/{$filename}",

            // Check in public/images/merchant-logos
            "images/merchant-logos/{$filename}",

            // Check for product-colors (for color variants)
            "storage/product-colors/{$filename}",

            // Check in public/images/product-colors
            "images/product-colors/{$filename}",

            // Check in storage with the original path
            $normalizedPath,

            // Check in storage with just the filename
            "storage/{$filename}",

            // Check directly in public with just the filename
            $filename,
        ];

        // Try each path
        foreach ($possiblePaths as $path) {
            if (file_exists(public_path($path))) {
                Log::debug("Found image at public_path({$path})");
                return $path;
            }
        }

        // If we couldn't find the file, return the original normalized path
        // This allows the system to handle missing files gracefully
        Log::warning("Image not found: {$imagePath}, returning normalized path: {$normalizedPath}");

        // For products, ensure we return a storage path
        if (str_contains($normalizedPath, 'products/')) {
            return "storage/{$normalizedPath}";
        }

        return $normalizedPath;
    }

    /**
     * Get the full URL for an image path
     *
     * @param string|null $imagePath
     * @return string|null
     */
    public static function getFullImageUrl($imagePath)
    {
        if (empty($imagePath)) {
            // Return a default placeholder image
            $appUrl = Config::get('app.url');
            $placeholderPath = "images/placeholder.png";

            // Check if placeholder exists, if not create a simple one
            if (!file_exists(public_path($placeholderPath))) {
                self::createPlaceholderImage(public_path($placeholderPath));
            }

            return "{$appUrl}/{$placeholderPath}";
        }

        // If the image path already starts with http, return it as is
        if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
            return $imagePath;
        }

        // Get the app URL from config
        $appUrl = Config::get('app.url');

        // Remove any double slashes that might be in the path
        $imagePath = str_replace('//', '/', $imagePath);

        // Normalize the path by removing leading slash
        $normalizedPath = ltrim($imagePath, '/');

        // Extract the filename from the path
        $filename = basename($normalizedPath);

        // Log the image path we're trying to resolve
        Log::debug("Resolving image path: {$imagePath}, filename: {$filename}");

        // Check for specific folder types first to use Laravel routes (to avoid 403 errors)
        if (str_contains($normalizedPath, 'products/colors/')) {
            // Use specific Laravel route for serving product color images
            Log::info("Using Laravel route for product color image: {$imagePath}");
            return route('images.products.colors', ['filename' => $filename]);
        }

        if (str_contains($normalizedPath, 'products/')) {
            // Use Laravel route for serving product images to avoid 403 errors
            Log::info("Using Laravel route for product image: {$imagePath}");
            return route('images.products', ['filename' => $filename]);
        }

        if (str_contains($normalizedPath, 'services/')) {
            // Use Laravel route for serving service images to avoid 403 errors
            Log::info("Using Laravel route for service image: {$imagePath}");
            return route('images.services', ['filename' => $filename]);
        }

        if (str_contains($normalizedPath, 'images/merchants/')) {
            // Use Laravel route for serving merchant logo images to avoid 403 errors
            Log::info("Using Laravel route for merchant logo image: {$imagePath}");
            return route('images.merchants', ['filename' => $filename]);
        }

        if (str_contains($normalizedPath, 'merchant-logos/')) {
            // Use Laravel route for serving merchant logo images to avoid 403 errors
            Log::info("Using Laravel route for merchant logo image: {$imagePath}");
            return route('images.storage', ['folder' => 'merchant-logos', 'filename' => $filename]);
        }

        if (str_contains($normalizedPath, 'uae_ids/')) {
            // Use Laravel route for serving UAE ID images to avoid 403 errors
            Log::info("Using Laravel route for UAE ID image: {$imagePath}");
            return route('images.uae_ids', ['filename' => $filename]);
        }

        // Create an array of possible paths to check
        $possiblePaths = [
            // Check in public/images/products
            "images/products/{$filename}",

            // Check in public/storage/products
            "storage/products/{$filename}",

            // Check in public/images/categories
            "images/categories/{$filename}",

            // Check in public/storage/categories
            "storage/categories/{$filename}",

            // Check in public/storage/deals
            "storage/deals/{$filename}",

            // Check in public/images/deals
            "images/deals/{$filename}",

            // Check in public/storage/branches
            "storage/branches/{$filename}",

            // Check in public/images/branches
            "images/branches/{$filename}",

            // Check in public/storage/merchant-logos
            "storage/merchant-logos/{$filename}",

            // Check in public/images/merchant-logos
            "images/merchant-logos/{$filename}",

            // Check in storage with the original path
            $normalizedPath,

            // Check in storage with just the filename
            "storage/{$filename}",

            // Check directly in public with just the filename
            $filename,

            // Check for product-colors (for color variants)
            "storage/product-colors/{$filename}",

            // Check in public/images/product-colors
            "images/product-colors/{$filename}",
        ];

        // Try each path
        foreach ($possiblePaths as $path) {
            if (file_exists(public_path($path))) {
                Log::debug("Found image at public_path({$path})");
                return "{$appUrl}/{$path}";
            }
        }

        // If we couldn't find the file, check if it exists in storage/app/public
        // but isn't accessible through the symbolic link
        $storageAppPublicPath = storage_path("app/public/{$filename}");
        if (file_exists($storageAppPublicPath)) {
            Log::warning("Image exists at {$storageAppPublicPath} but is not accessible via public URL. Check symbolic link.");

            // Try to copy the file to the public directory as a fallback
            try {
                $publicDestination = public_path("storage/{$filename}");
                if (!file_exists(dirname($publicDestination))) {
                    mkdir(dirname($publicDestination), 0755, true);
                }
                copy($storageAppPublicPath, $publicDestination);
                Log::info("Copied image from {$storageAppPublicPath} to {$publicDestination}");
                return "{$appUrl}/storage/{$filename}";
            } catch (\Exception $e) {
                Log::error("Failed to copy image: " . $e->getMessage());
            }
        }

        // Check if the path is a storage URL (starts with /storage/)
        if (str_starts_with($imagePath, '/storage/') || str_starts_with($imagePath, 'storage/')) {
            // Ensure the storage directory exists
            $storagePath = str_replace('/storage/', 'storage/', $imagePath);
            $storagePath = ltrim($storagePath, '/');

            // Try to create the directory if it doesn't exist
            $dirPath = dirname(public_path($storagePath));
            if (!file_exists($dirPath)) {
                try {
                    mkdir($dirPath, 0755, true);
                    Log::info("Created directory: {$dirPath}");
                } catch (\Exception $e) {
                    Log::error("Failed to create directory: " . $e->getMessage());
                }
            }

            // Check if we need to create a placeholder image at this location
            $fullPath = public_path($storagePath);
            if (!file_exists($fullPath)) {
                try {
                    // Create a placeholder image at this location
                    self::createPlaceholderImage($fullPath);
                    Log::info("Created placeholder image at: {$fullPath}");
                } catch (\Exception $e) {
                    Log::error("Failed to create placeholder image: " . $e->getMessage());
                }
            }

            return "{$appUrl}/{$storagePath}";
        }



        if (str_contains($normalizedPath, 'product-colors/')) {
            // Try to ensure the directory exists
            $dirPath = public_path('storage/product-colors');
            if (!file_exists($dirPath)) {
                try {
                    mkdir($dirPath, 0755, true);
                    Log::info("Created directory: {$dirPath}");
                } catch (\Exception $e) {
                    Log::error("Failed to create directory: " . $e->getMessage());
                }
            }

            $fullPath = public_path("storage/product-colors/{$filename}");
            if (!file_exists($fullPath)) {
                try {
                    // Create a placeholder image at this location
                    self::createPlaceholderImage($fullPath);
                    Log::info("Created placeholder image at: {$fullPath}");
                } catch (\Exception $e) {
                    Log::error("Failed to create placeholder image: " . $e->getMessage());
                }
            }

            Log::info("Using product-colors path for: {$imagePath}");
            return "{$appUrl}/storage/product-colors/{$filename}";
        }

        if (str_contains($normalizedPath, 'categories/')) {
            // Try to ensure the directory exists
            $dirPath = public_path('storage/categories');
            if (!file_exists($dirPath)) {
                try {
                    mkdir($dirPath, 0755, true);
                    Log::info("Created directory: {$dirPath}");
                } catch (\Exception $e) {
                    Log::error("Failed to create directory: " . $e->getMessage());
                }
            }

            // First check if the file exists directly with the provided path
            $directPath = public_path(ltrim($normalizedPath, '/'));
            if (file_exists($directPath)) {
                Log::info("Found category image at direct path: {$directPath}");
                return "{$appUrl}/{$normalizedPath}";
            }

            // Then check if it exists in the storage/categories directory
            $fullPath = public_path("storage/categories/{$filename}");
            if (file_exists($fullPath)) {
                Log::info("Found category image at: {$fullPath}");
                return "{$appUrl}/storage/categories/{$filename}";
            }

            // If not found, try to copy from storage/app/public/categories
            try {
                $storageAppPublicPath = storage_path("app/public/categories/{$filename}");
                if (file_exists($storageAppPublicPath)) {
                    // Copy the file to the public directory
                    copy($storageAppPublicPath, $fullPath);
                    Log::info("Copied image from {$storageAppPublicPath} to {$fullPath}");
                    return "{$appUrl}/storage/categories/{$filename}";
                } else {
                    // Create a placeholder image at this location
                    self::createPlaceholderImage($fullPath);
                    Log::info("Created placeholder image at: {$fullPath}");
                    return "{$appUrl}/storage/categories/{$filename}";
                }
            } catch (\Exception $e) {
                Log::error("Failed to handle category image: " . $e->getMessage());
                // Still try to return a valid path
                return "{$appUrl}/storage/categories/{$filename}";
            }
        }

        // Handle deal images
        if (str_contains($normalizedPath, 'deals/')) {
            // Try to ensure the directory exists
            $dirPath = public_path('storage/deals');
            if (!file_exists($dirPath)) {
                try {
                    mkdir($dirPath, 0755, true);
                    Log::info("Created directory: {$dirPath}");
                } catch (\Exception $e) {
                    Log::error("Failed to create directory: " . $e->getMessage());
                }
            }

            // First check if the file exists directly with the provided path
            $directPath = public_path(ltrim($normalizedPath, '/'));
            if (file_exists($directPath)) {
                Log::info("Found deal image at direct path: {$directPath}");
                return "{$appUrl}/{$normalizedPath}";
            }

            // Then check if it exists in the storage/deals directory
            $fullPath = public_path("storage/deals/{$filename}");
            if (file_exists($fullPath)) {
                Log::info("Found deal image at: {$fullPath}");
                return "{$appUrl}/storage/deals/{$filename}";
            }

            // If not found, try to copy from storage/app/public/deals
            try {
                $storageAppPublicPath = storage_path("app/public/deals/{$filename}");
                if (file_exists($storageAppPublicPath)) {
                    // Copy the file to the public directory
                    copy($storageAppPublicPath, $fullPath);
                    Log::info("Copied deal image from {$storageAppPublicPath} to {$fullPath}");
                    return "{$appUrl}/storage/deals/{$filename}";
                } else {
                    // Create a placeholder image at this location
                    self::createPlaceholderImage($fullPath);
                    Log::info("Created placeholder image at: {$fullPath}");
                    return "{$appUrl}/storage/deals/{$filename}";
                }
            } catch (\Exception $e) {
                Log::error("Failed to handle deal image: " . $e->getMessage());
                // Still try to return a valid path
                return "{$appUrl}/storage/deals/{$filename}";
            }
        }

        // Handle merchant logo images
        if (str_contains($normalizedPath, 'merchant-logos/')) {
            // Try to ensure the directory exists
            $dirPath = public_path('storage/merchant-logos');
            if (!file_exists($dirPath)) {
                try {
                    mkdir($dirPath, 0755, true);
                    Log::info("Created directory: {$dirPath}");
                } catch (\Exception $e) {
                    Log::error("Failed to create directory: " . $e->getMessage());
                }
            }

            // First check if the file exists directly with the provided path
            $directPath = public_path(ltrim($normalizedPath, '/'));
            if (file_exists($directPath)) {
                Log::info("Found merchant logo at direct path: {$directPath}");
                return "{$appUrl}/{$normalizedPath}";
            }

            // Then check if it exists in the storage/merchant-logos directory
            $fullPath = public_path("storage/merchant-logos/{$filename}");
            if (file_exists($fullPath)) {
                Log::info("Found merchant logo at: {$fullPath}");
                return "{$appUrl}/storage/merchant-logos/{$filename}";
            }

            // If not found, try to copy from storage/app/public/merchant-logos
            try {
                $storageAppPublicPath = storage_path("app/public/merchant-logos/{$filename}");
                if (file_exists($storageAppPublicPath)) {
                    // Copy the file to the public directory
                    copy($storageAppPublicPath, $fullPath);
                    Log::info("Copied merchant logo from {$storageAppPublicPath} to {$fullPath}");
                    return "{$appUrl}/storage/merchant-logos/{$filename}";
                } else {
                    // Create a placeholder image at this location
                    self::createPlaceholderImage($fullPath);
                    Log::info("Created placeholder image at: {$fullPath}");
                    return "{$appUrl}/storage/merchant-logos/{$filename}";
                }
            } catch (\Exception $e) {
                Log::error("Failed to handle merchant logo: " . $e->getMessage());
                // Still try to return a valid path
                return "{$appUrl}/storage/merchant-logos/{$filename}";
            }
        }

        // Handle company logo images
        if (str_contains($normalizedPath, 'companies/')) {
            // Try to ensure the directory exists
            $dirPath = public_path('storage/companies');
            if (!file_exists($dirPath)) {
                try {
                    mkdir($dirPath, 0755, true);
                    Log::info("Created directory: {$dirPath}");
                } catch (\Exception $e) {
                    Log::error("Failed to create directory: " . $e->getMessage());
                }
            }

            // First check if the file exists directly with the provided path
            $directPath = public_path(ltrim($normalizedPath, '/'));
            if (file_exists($directPath)) {
                Log::info("Found company logo at direct path: {$directPath}");
                return "{$appUrl}/{$normalizedPath}";
            }

            // Then check if it exists in the storage/companies directory
            $fullPath = public_path("storage/companies/{$filename}");
            if (file_exists($fullPath)) {
                Log::info("Found company logo at: {$fullPath}");
                return "{$appUrl}/storage/companies/{$filename}";
            }

            // If not found, try to copy from storage/app/public/companies
            try {
                $storageAppPublicPath = storage_path("app/public/companies/{$filename}");
                if (file_exists($storageAppPublicPath)) {
                    // Copy the file to the public directory
                    copy($storageAppPublicPath, $fullPath);
                    Log::info("Copied company logo from {$storageAppPublicPath} to {$fullPath}");
                    return "{$appUrl}/storage/companies/{$filename}";
                } else {
                    // Create a placeholder image at this location
                    self::createPlaceholderImage($fullPath);
                    Log::info("Created placeholder image at: {$fullPath}");
                    return "{$appUrl}/storage/companies/{$filename}";
                }
            } catch (\Exception $e) {
                Log::error("Failed to handle company logo: " . $e->getMessage());
                // Still try to return a valid path
                return "{$appUrl}/storage/companies/{$filename}";
            }
        }

        if (str_contains($normalizedPath, 'users/')) {
            // Try to ensure the directory exists
            $dirPath = public_path('storage/users');
            if (!file_exists($dirPath)) {
                try {
                    mkdir($dirPath, 0755, true);
                    Log::info("Created directory: {$dirPath}");
                } catch (\Exception $e) {
                    Log::error("Failed to create directory: " . $e->getMessage());
                }
            }

            $fullPath = public_path("storage/users/{$filename}");
            if (!file_exists($fullPath)) {
                try {
                    // Create a placeholder image at this location
                    self::createPlaceholderImage($fullPath);
                    Log::info("Created placeholder image at: {$fullPath}");
                } catch (\Exception $e) {
                    Log::error("Failed to create placeholder image: " . $e->getMessage());
                }
            }

            Log::info("Using users path for: {$imagePath}");
            return "{$appUrl}/storage/users/{$filename}";
        }



        // Handle branch images
        if (str_contains($normalizedPath, 'branches/')) {
            // Try to ensure the directory exists
            $dirPath = public_path('storage/branches');
            if (!file_exists($dirPath)) {
                try {
                    mkdir($dirPath, 0755, true);
                    Log::info("Created directory: {$dirPath}");
                } catch (\Exception $e) {
                    Log::error("Failed to create directory: " . $e->getMessage());
                }
            }

            // First check if the file exists directly with the provided path
            $directPath = public_path(ltrim($normalizedPath, '/'));
            if (file_exists($directPath)) {
                Log::info("Found branch image at direct path: {$directPath}");
                return "{$appUrl}/{$normalizedPath}";
            }

            // Then check if it exists in the storage/branches directory
            $fullPath = public_path("storage/branches/{$filename}");
            if (file_exists($fullPath)) {
                Log::info("Found branch image at: {$fullPath}");
                return "{$appUrl}/storage/branches/{$filename}";
            }

            // If not found, try to copy from storage/app/public/branches
            try {
                $storageAppPublicPath = storage_path("app/public/branches/{$filename}");
                if (file_exists($storageAppPublicPath)) {
                    // Copy the file to the public directory
                    copy($storageAppPublicPath, $fullPath);
                    Log::info("Copied branch image from {$storageAppPublicPath} to {$fullPath}");
                    return "{$appUrl}/storage/branches/{$filename}";
                } else {
                    // Create a placeholder image at this location
                    self::createPlaceholderImage($fullPath);
                    Log::info("Created placeholder image at: {$fullPath}");
                    return "{$appUrl}/storage/branches/{$filename}";
                }
            } catch (\Exception $e) {
                Log::error("Failed to handle branch image: " . $e->getMessage());
                // Still try to return a valid path
                return "{$appUrl}/storage/branches/{$filename}";
            }
        }

        // Log problematic paths for debugging
        Log::warning("Image path could not be resolved: {$imagePath}");

        // Last resort: create a placeholder in the storage directory
        $placeholderPath = "storage/{$filename}";
        $fullPath = public_path($placeholderPath);

        // Ensure the directory exists
        if (!file_exists(dirname($fullPath))) {
            try {
                mkdir(dirname($fullPath), 0755, true);
                Log::info("Created directory: " . dirname($fullPath));
            } catch (\Exception $e) {
                Log::error("Failed to create directory: " . $e->getMessage());
            }
        }

        // Create a placeholder image
        if (!file_exists($fullPath)) {
            try {
                self::createPlaceholderImage($fullPath);
                Log::info("Created placeholder image at: {$fullPath}");
            } catch (\Exception $e) {
                Log::error("Failed to create placeholder image: " . $e->getMessage());
            }
        }

        return "{$appUrl}/{$placeholderPath}";
    }

    /**
     * Create a simple placeholder image
     *
     * @param string $path
     * @return bool
     */
    private static function createPlaceholderImage($path)
    {
        // Create a simple image
        $width = 400;
        $height = 300;
        $image = \imagecreatetruecolor($width, $height);

        // Colors
        $bgColor = \imagecolorallocate($image, 240, 240, 240);
        $textColor = \imagecolorallocate($image, 50, 50, 50);
        $borderColor = \imagecolorallocate($image, 200, 200, 200);

        // Fill background
        \imagefill($image, 0, 0, $bgColor);

        // Add border
        \imagerectangle($image, 0, 0, $width-1, $height-1, $borderColor);

        // Add text
        $text = "Product Image";
        $font = 5; // Built-in font
        $textWidth = \imagefontwidth($font) * strlen($text);
        $textHeight = \imagefontheight($font);
        $x = ($width - $textWidth) / 2;
        $y = ($height - $textHeight) / 2;
        \imagestring($image, $font, $x, $y, $text, $textColor);

        // Ensure the directory exists
        $directory = dirname($path);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Save the image
        // Save as PNG since imagejpeg might not be available
        // PNG compression: 0 (no compression) to 9 (max compression). null for default.
        $result = \imagepng($image, $path); // Using default compression
        \imagedestroy($image);

        return $result;
    }

    /**
     * Check if an image exists in any of the possible locations
     *
     * @param string|null $imagePath
     * @return bool
     */
    public static function imageExists($imagePath)
    {
        if (empty($imagePath)) {
            return false;
        }

        // If it's a URL, we can't check if it exists
        if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
            return true; // Assume it exists
        }

        // Normalize the path
        $normalizedPath = ltrim(str_replace('//', '/', $imagePath), '/');
        $filename = basename($normalizedPath);

        // Check various possible locations
        $possiblePaths = [
            public_path("images/products/{$filename}"),
            public_path("storage/products/{$filename}"),
            public_path("images/categories/{$filename}"),
            public_path("storage/categories/{$filename}"),
            public_path("images/deals/{$filename}"),
            public_path("storage/deals/{$filename}"),
            public_path("images/branches/{$filename}"),
            public_path("storage/branches/{$filename}"),
            public_path("images/services/{$filename}"),
            public_path("storage/services/{$filename}"),
            public_path("images/companies/{$filename}"),
            public_path("storage/companies/{$filename}"),
            public_path($normalizedPath),
            public_path("storage/{$filename}"),
            public_path($filename),
            public_path("storage/product-colors/{$filename}"),
            public_path("images/product-colors/{$filename}"),
            storage_path("app/public/{$filename}"),
            storage_path("app/public/products/{$filename}"),
            storage_path("app/public/categories/{$filename}"),
            storage_path("app/public/services/{$filename}"),
            storage_path("app/public/deals/{$filename}"),
            storage_path("app/public/branches/{$filename}"),
            storage_path("app/public/companies/{$filename}"),
            storage_path("app/public/product-colors/{$filename}"),
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sync a specific file from storage/app/public to public/storage
     *
     * @param string $relativePath The relative path from storage/app/public (e.g., 'products/image.jpg')
     * @return bool True if sync was successful, false otherwise
     */
    public static function syncFile($relativePath)
    {
        try {
            $sourcePath = storage_path('app/public/' . $relativePath);
            $destPath = public_path('storage/' . $relativePath);

            // Check if source file exists
            if (!file_exists($sourcePath)) {
                Log::warning("Source file does not exist for sync: {$sourcePath}");
                return false;
            }

            // Ensure destination directory exists
            $destDir = dirname($destPath);
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
                Log::info("Created directory for sync: {$destDir}");
            }

            // Copy the file
            if (copy($sourcePath, $destPath)) {
                chmod($destPath, 0644);
                Log::info("Successfully synced file: {$relativePath}");
                return true;
            } else {
                Log::error("Failed to copy file during sync: {$relativePath}");
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Exception during file sync: " . $e->getMessage() . " for file: {$relativePath}");
            return false;
        }
    }

    /**
     * Sync a specific directory from storage/app/public to public/storage
     *
     * @param string $relativePath The relative directory path from storage/app/public (e.g., 'products')
     * @return bool True if sync was successful, false otherwise
     */
    public static function syncDirectory($relativePath)
    {
        try {
            $sourceDir = storage_path('app/public/' . $relativePath);
            $destDir = public_path('storage/' . $relativePath);

            // Check if source directory exists
            if (!is_dir($sourceDir)) {
                Log::warning("Source directory does not exist for sync: {$sourceDir}");
                return false;
            }

            // Ensure destination directory exists
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
                Log::info("Created destination directory for sync: {$destDir}");
            }

            $syncedCount = 0;
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($sourceDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $item) {
                $subPath = $iterator->getSubPathName();
                $destPath = $destDir . DIRECTORY_SEPARATOR . $subPath;

                if ($item->isDir()) {
                    if (!is_dir($destPath)) {
                        mkdir($destPath, 0755, true);
                    }
                } else {
                    // Only copy if file doesn't exist or source is newer
                    if (!file_exists($destPath) || filemtime($item) > filemtime($destPath)) {
                        copy($item, $destPath);
                        chmod($destPath, 0644);
                        $syncedCount++;
                    }
                }
            }

            Log::info("Successfully synced directory: {$relativePath} ({$syncedCount} files)");
            return true;
        } catch (\Exception $e) {
            Log::error("Exception during directory sync: " . $e->getMessage() . " for directory: {$relativePath}");
            return false;
        }
    }

    /**
     * Automatically sync an uploaded image file
     * This method should be called immediately after a successful image upload
     *
     * @param string $imagePath The image path returned by Laravel's store() method
     * @return bool True if sync was successful, false otherwise
     */
    public static function syncUploadedImage($imagePath)
    {
        if (empty($imagePath)) {
            return false;
        }

        // Remove any leading slash or storage/ prefix to get the relative path
        $relativePath = ltrim($imagePath, '/');
        $relativePath = str_replace('storage/', '', $relativePath);

        return self::syncFile($relativePath);
    }

    /**
     * Compress and convert image to WebP format
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @param int $quality
     * @return string|null
     */
    public static function compressToWebP($file, $directory, $quality = 75)
    {
        try {
            // Create directory if it doesn't exist
            $fullDirectory = storage_path("app/public/{$directory}");
            if (!file_exists($fullDirectory)) {
                mkdir($fullDirectory, 0755, true);
            }

            // Generate unique filename with WebP extension
            $filename = uniqid() . '_' . time() . '.webp';
            $filePath = "{$directory}/{$filename}";
            $fullPath = storage_path("app/public/{$filePath}");

            // Get image info and create image resource
            $imageInfo = getimagesize($file->getPathname());
            $mimeType = $imageInfo['mime'];

            switch ($mimeType) {
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($file->getPathname());
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($file->getPathname());
                    break;
                case 'image/gif':
                    $image = imagecreatefromgif($file->getPathname());
                    break;
                case 'image/webp':
                    $image = imagecreatefromwebp($file->getPathname());
                    break;
                default:
                    // For unsupported formats, just store the original file
                    $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $filePath = "{$directory}/{$filename}";
                    $file->storeAs($directory, $filename, 'public');
                    return $filePath;
            }

            if (!$image) {
                return null;
            }

            // Convert to WebP with specified quality
            $success = imagewebp($image, $fullPath, $quality);
            imagedestroy($image);

            if ($success) {
                return $filePath;
            }

            return null;
        } catch (\Exception $e) {
            Log::error("Image compression failed: " . $e->getMessage());
            return null;
        }
    }
}
