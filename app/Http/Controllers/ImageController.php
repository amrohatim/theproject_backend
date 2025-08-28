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

            // Check multiple possible paths for product color images
            $possiblePaths = [
                "products/colors/{$filename}",   // Path for /images/products/colors/ URLs
                "product-colors/{$filename}",    // Path for /storage/product-colors/ URLs
            ];

            $foundPath = null;
            foreach ($possiblePaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    $foundPath = $path;
                    break;
                }
            }

            if (!$foundPath) {
                Log::info("Product color image not found in any location: {$filename}");
                return $this->returnPlaceholderImage();
            }

            // Get the full file path
            $fullPath = Storage::disk('public')->path($foundPath);

            // Verify file exists on filesystem
            if (!file_exists($fullPath)) {
                Log::warning("Product color image file missing from filesystem: {$fullPath}");
                return $this->returnPlaceholderImage();
            }

            Log::info("Product color image found: {$foundPath}");

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
     * Serve merchant logo images
     *
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function serveMerchantImage($filename)
    {
        try {
            // Validate filename to prevent directory traversal
            if (!$this->isValidFilename($filename)) {
                Log::warning("Invalid merchant image filename requested: {$filename}");
                return $this->returnPlaceholderImage();
            }

            // Check if file exists in storage
            $path = "images/merchants/{$filename}";
            if (!Storage::disk('public')->exists($path)) {
                Log::info("Merchant image not found: {$path}");
                return $this->returnPlaceholderImage();
            }

            // Get the full file path
            $fullPath = Storage::disk('public')->path($path);

            // Verify file exists on filesystem
            if (!file_exists($fullPath)) {
                Log::warning("Merchant image file missing from filesystem: {$fullPath}");
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
            Log::error("Error serving merchant image {$filename}: " . $e->getMessage());
            return $this->returnPlaceholderImage();
        }
    }

    /**
     * Serve company logo images
     *
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function serveCompanyLogo($filename)
    {
        try {
            // Validate filename to prevent directory traversal
            if (!$this->isValidFilename($filename)) {
                Log::warning("Invalid company logo filename requested: {$filename}");
                return $this->returnPlaceholderImage();
            }

            // First check if file exists in public/images/companies/ (where files are actually copied)
            $publicPath = public_path("images/companies/{$filename}");
            if (file_exists($publicPath)) {
                Log::info("Company logo found in public directory: {$publicPath}");

                // Get file info
                $mimeType = $this->getMimeType($publicPath);
                $fileSize = filesize($publicPath);

                // Return the file with appropriate headers
                return Response::file($publicPath, [
                    'Content-Type' => $mimeType,
                    'Content-Length' => $fileSize,
                    'Cache-Control' => 'public, max-age=31536000', // Cache for 1 year
                    'Expires' => gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT',
                ]);
            }

            // Fallback: Check if file exists in storage/app/public/companies/
            $path = "companies/{$filename}";
            if (!Storage::disk('public')->exists($path)) {
                Log::info("Company logo not found in storage or public directory: {$filename}");
                return $this->returnPlaceholderImage();
            }

            // Get the full file path from storage
            $fullPath = Storage::disk('public')->path($path);

            // Verify file exists on filesystem
            if (!file_exists($fullPath)) {
                Log::warning("Company logo file missing from filesystem: {$fullPath}");
                return $this->returnPlaceholderImage();
            }

            Log::info("Company logo found in storage directory: {$fullPath}");

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
            Log::error("Error serving company logo {$filename}: " . $e->getMessage());
            return $this->returnPlaceholderImage();
        }
    }

    /**
     * Serve branch images
     *
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function serveBranchImage($filename)
    {
        try {
            // Validate filename to prevent directory traversal
            if (!$this->isValidFilename($filename)) {
                Log::warning("Invalid branch image filename requested: {$filename}");
                return $this->returnPlaceholderImage();
            }

            // Check if file exists in storage
            $path = "branches/{$filename}";
            if (!Storage::disk('public')->exists($path)) {
                Log::info("Branch image not found in storage: {$path}");
                return $this->returnPlaceholderImage();
            }

            // Get the full file path from storage
            $fullPath = Storage::disk('public')->path($path);

            // Verify file exists on filesystem
            if (!file_exists($fullPath)) {
                Log::warning("Branch image file missing from filesystem: {$fullPath}");
                return $this->returnPlaceholderImage();
            }

            Log::info("Branch image found in storage directory: {$fullPath}");

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
            Log::error("Error serving branch image {$filename}: " . $e->getMessage());
            return $this->returnPlaceholderImage();
        }
    }

    /**
     * Serve category images
     *
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function serveCategoryImage($filename)
    {
        try {
            // Validate filename to prevent directory traversal
            if (!$this->isValidFilename($filename)) {
                Log::warning("Invalid category image filename requested: {$filename}");
                return $this->returnPlaceholderImage();
            }

            // Check if file exists in storage
            $path = "categories/{$filename}";
            if (!Storage::disk('public')->exists($path)) {
                Log::info("Category image not found in storage: {$path}");
                return $this->returnPlaceholderImage();
            }

            // Get the full file path from storage
            $fullPath = Storage::disk('public')->path($path);

            // Verify file exists on filesystem
            if (!file_exists($fullPath)) {
                Log::warning("Category image file missing from filesystem: {$fullPath}");
                return $this->returnPlaceholderImage();
            }

            Log::info("Category image found in storage directory: {$fullPath}");

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
            Log::error("Error serving category image {$filename}: " . $e->getMessage());
            return $this->returnPlaceholderImage();
        }
    }

    /**
     * Serve provider product images
     *
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function serveProviderProductImage($filename)
    {
        try {
            // Validate filename to prevent directory traversal
            if (!$this->isValidFilename($filename)) {
                Log::warning("Invalid provider product image filename requested: {$filename}");
                return $this->returnPlaceholderImage();
            }

            // Check if file exists in storage
            $path = "provider_products/{$filename}";
            if (!Storage::disk('public')->exists($path)) {
                Log::info("Provider product image not found in storage: {$path}");
                return $this->returnPlaceholderImage();
            }

            // Get the full file path from storage
            $fullPath = Storage::disk('public')->path($path);

            // Verify file exists on filesystem
            if (!file_exists($fullPath)) {
                Log::warning("Provider product image file missing from filesystem: {$fullPath}");
                return $this->returnPlaceholderImage();
            }

            Log::info("Provider product image found in storage directory: {$fullPath}");

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
            Log::error("Error serving provider product image {$filename}: " . $e->getMessage());
            return $this->returnPlaceholderImage();
        }
    }

    /**
     * Serve deal images
     *
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function serveDealImage($filename)
    {
        try {
            // Validate filename to prevent directory traversal
            if (!$this->isValidFilename($filename)) {
                Log::warning("Invalid deal image filename requested: {$filename}");
                return $this->returnPlaceholderImage();
            }

            // Check if file exists in storage
            $path = "deals/{$filename}";
            if (!Storage::disk('public')->exists($path)) {
                Log::info("Deal image not found in storage: {$path}");
                return $this->returnPlaceholderImage();
            }

            // Get the full file path from storage
            $fullPath = Storage::disk('public')->path($path);

            // Verify file exists on filesystem
            if (!file_exists($fullPath)) {
                Log::warning("Deal image file missing from filesystem: {$fullPath}");
                return $this->returnPlaceholderImage();
            }

            Log::info("Deal image found in storage directory: {$fullPath}");

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
            Log::error("Error serving deal image {$filename}: " . $e->getMessage());
            return $this->returnPlaceholderImage();
        }
    }

    /**
     * Serve UAE ID images
     *
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function serveUaeIdImage($filename)
    {
        try {
            // Validate filename to prevent directory traversal
            if (!$this->isValidFilename($filename)) {
                Log::warning("Invalid UAE ID image filename requested: {$filename}");
                return $this->returnPlaceholderImage();
            }

            // Check if file exists in storage
            $path = "images/uae_ids/{$filename}";
            if (!Storage::disk('public')->exists($path)) {
                Log::info("UAE ID image not found: {$path}");
                return $this->returnPlaceholderImage();
            }

            // Get the full file path
            $fullPath = Storage::disk('public')->path($path);

            // Verify file exists on filesystem
            if (!file_exists($fullPath)) {
                Log::warning("UAE ID image file missing from filesystem: {$fullPath}");
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
            Log::error("Error serving UAE ID image {$filename}: " . $e->getMessage());
            return $this->returnPlaceholderImage();
        }
    }

    /**
     * Serve business type images
     *
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function serveBusinessTypeImage($filename)
    {
        try {
            // Validate filename to prevent directory traversal
            if (!$this->isValidFilename($filename)) {
                Log::warning("Invalid business type image filename requested: {$filename}");
                return $this->returnPlaceholderImage();
            }

            // Check if file exists in storage
            $path = "business-types/{$filename}";
            if (!Storage::disk('public')->exists($path)) {
                Log::info("Business type image not found: {$path}");
                return $this->returnPlaceholderImage();
            }

            // Get the full file path
            $fullPath = Storage::disk('public')->path($path);

            // Verify file exists on filesystem
            if (!file_exists($fullPath)) {
                Log::warning("Business type image file missing from filesystem: {$fullPath}");
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
            Log::error("Error serving business type image {$filename}: " . $e->getMessage());
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
        $allowedFolders = ['products', 'services', 'categories', 'users', 'merchants', 'branches', 'companies', 'merchant-logos', 'images/uae_ids', 'avatars', 'business-types'];
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

    /**
     * Serve avatar images
     *
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function serveAvatarImage($filename)
    {
        try {
            // Validate filename to prevent directory traversal
            if (!$this->isValidFilename($filename)) {
                Log::warning("Invalid avatar image filename requested: {$filename}");
                return $this->returnPlaceholderImage();
            }

            // Check if file exists in storage
            $path = "avatars/{$filename}";
            if (!Storage::disk('public')->exists($path)) {
                Log::info("Avatar image not found in storage: {$path}");
                return $this->returnPlaceholderImage();
            }

            // Get the full path to the file
            $fullPath = Storage::disk('public')->path($path);

            // Return the file response with appropriate headers
            return response()->file($fullPath, [
                'Cache-Control' => 'public, max-age=31536000', // Cache for 1 year
                'Expires' => gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT',
            ]);

        } catch (\Exception $e) {
            Log::error("Error serving avatar image {$filename}: " . $e->getMessage());
            return $this->returnPlaceholderImage();
        }
    }
}
