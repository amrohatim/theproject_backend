<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\Service;
use App\Models\Branch;
use App\Models\Category;
use App\Helpers\ImageHelper;

class ImageFixController extends Controller
{
    /**
     * Fix image display issues in the admin and vendor dashboards
     *
     * @return \Illuminate\Http\Response
     */
    public function fixImages()
    {
        $results = [
            'storage_link' => $this->checkAndFixStorageLink(),
            'env_config' => $this->checkAndFixEnvConfig(),
            'directories' => $this->createNecessaryDirectories(),
            'product_images' => $this->fixProductImages(),
            'color_images' => $this->fixProductColorImages(),
            'service_images' => $this->fixServiceImages(),
            'branch_images' => $this->fixBranchImages(),
            'category_images' => $this->fixCategoryImages(),
        ];

        // Clear configuration cache
        $this->clearConfigCache();

        return view('admin.image_fix_results', compact('results'));
    }

    /**
     * Check and fix the storage link
     *
     * @return array
     */
    private function checkAndFixStorageLink()
    {
        $result = [
            'status' => 'success',
            'message' => 'Storage link is correctly configured.',
            'details' => []
        ];

        $storageLink = public_path('storage');
        $storagePath = storage_path('app/public');

        if (!file_exists($storageLink)) {
            $result['status'] = 'warning';
            $result['message'] = 'Storage link is missing. Manual action required.';
            $result['details'][] = 'The storage link from ' . $storagePath . ' to ' . $storageLink . ' needs to be created.';
            $result['details'][] = 'Please run the following command as administrator:';
            $result['details'][] = 'php artisan storage:link';

            // Try using artisan command (might work in some environments)
            try {
                exec('php artisan storage:link 2>&1', $output, $returnCode);
                if ($returnCode === 0) {
                    $result['status'] = 'fixed';
                    $result['message'] = 'Storage link was missing and has been created using artisan command.';
                    $result['details'] = ['Successfully created storage link using artisan command.'];
                } else {
                    // Command failed, but we already set the warning status
                    $result['details'][] = 'Automatic fix attempt failed. Output: ' . implode("\n", $output);

                    // Create a manual workaround by copying files instead of symlinking
                    $result['details'][] = 'Attempting alternative solution: copying files instead of creating symbolic link...';

                    if (!is_dir($storageLink)) {
                        mkdir($storageLink, 0755, true);
                    }

                    // Copy the most important directories
                    $this->copyDirectory($storagePath . '/products', $storageLink . '/products');
                    $this->copyDirectory($storagePath . '/product-colors', $storageLink . '/product-colors');
                    $this->copyDirectory($storagePath . '/services', $storageLink . '/services');
                    $this->copyDirectory($storagePath . '/categories', $storageLink . '/categories');
                    $this->copyDirectory($storagePath . '/branches', $storageLink . '/branches');

                    $result['status'] = 'fixed';
                    $result['message'] = 'Created a copy of storage files as a workaround (not a symbolic link).';
                    $result['details'][] = 'Note: This is a temporary solution. For best performance, please run "php artisan storage:link" as administrator.';
                }
            } catch (\Exception $e) {
                $result['details'][] = 'Error running artisan command: ' . $e->getMessage();

                // Try the copy workaround here too
                try {
                    if (!is_dir($storageLink)) {
                        mkdir($storageLink, 0755, true);
                    }

                    // Copy the most important directories
                    $this->copyDirectory($storagePath . '/products', $storageLink . '/products');
                    $this->copyDirectory($storagePath . '/product-colors', $storageLink . '/product-colors');
                    $this->copyDirectory($storagePath . '/services', $storageLink . '/services');
                    $this->copyDirectory($storagePath . '/categories', $storageLink . '/categories');
                    $this->copyDirectory($storagePath . '/branches', $storageLink . '/branches');

                    $result['status'] = 'fixed';
                    $result['message'] = 'Created a copy of storage files as a workaround (not a symbolic link).';
                    $result['details'][] = 'Note: This is a temporary solution. For best performance, please run "php artisan storage:link" as administrator.';
                } catch (\Exception $copyEx) {
                    $result['details'][] = 'Failed to create copy workaround: ' . $copyEx->getMessage();
                }
            }
        } else {
            if (is_link($storageLink)) {
                $target = readlink($storageLink);
                $result['details'][] = 'Storage link exists and points to: ' . $target;

                if (realpath($target) !== realpath($storagePath)) {
                    $result['status'] = 'warning';
                    $result['message'] = 'Storage link is pointing to the wrong location. Manual action required.';
                    $result['details'][] = 'Current target: ' . $target;
                    $result['details'][] = 'Expected target: ' . $storagePath;
                    $result['details'][] = 'Please run the following commands as administrator:';
                    $result['details'][] = 'rmdir ' . $storageLink . ' (or del ' . $storageLink . ')';
                    $result['details'][] = 'php artisan storage:link';
                }
            } else {
                $result['status'] = 'warning';
                $result['message'] = 'Storage path exists but is not a symbolic link. Manual action required.';
                $result['details'][] = 'Please run the following commands as administrator:';
                $result['details'][] = 'rmdir ' . $storageLink . ' (or del ' . $storageLink . ')';
                $result['details'][] = 'php artisan storage:link';

                // Try the copy workaround here too
                try {
                    // Copy the most important directories
                    $this->copyDirectory($storagePath . '/products', $storageLink . '/products');
                    $this->copyDirectory($storagePath . '/product-colors', $storageLink . '/product-colors');
                    $this->copyDirectory($storagePath . '/services', $storageLink . '/services');
                    $this->copyDirectory($storagePath . '/categories', $storageLink . '/categories');
                    $this->copyDirectory($storagePath . '/branches', $storageLink . '/branches');

                    $result['status'] = 'fixed';
                    $result['message'] = 'Updated copy of storage files as a workaround (not a symbolic link).';
                    $result['details'][] = 'Note: This is a temporary solution. For best performance, please run "php artisan storage:link" as administrator.';
                } catch (\Exception $copyEx) {
                    $result['details'][] = 'Failed to update copy workaround: ' . $copyEx->getMessage();
                }
            }
        }

        return $result;
    }

    /**
     * Helper method to copy a directory recursively
     *
     * @param string $source
     * @param string $destination
     * @return void
     */
    private function copyDirectory($source, $destination)
    {
        if (!is_dir($source)) {
            return;
        }

        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $dir = opendir($source);
        while (($file = readdir($dir)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $srcFile = $source . '/' . $file;
            $destFile = $destination . '/' . $file;

            if (is_dir($srcFile)) {
                $this->copyDirectory($srcFile, $destFile);
            } else {
                copy($srcFile, $destFile);
            }
        }
        closedir($dir);
    }

    /**
     * Check and fix .env configuration
     *
     * @return array
     */
    private function checkAndFixEnvConfig()
    {
        $result = [
            'status' => 'success',
            'message' => 'Environment configuration is correct.',
            'details' => []
        ];

        $envPath = base_path('.env');
        if (file_exists($envPath)) {
            $envContent = file_get_contents($envPath);
            $changes = false;

            // Check APP_URL
            $appUrl = Config::get('app.url');
            $result['details'][] = 'Current APP_URL: ' . $appUrl;

            // Determine the correct APP_URL based on the server
            $serverName = $_SERVER['SERVER_NAME'] ?? 'localhost';
            $serverPort = $_SERVER['SERVER_PORT'] ?? '8000';
            $correctAppUrl = "http://{$serverName}:{$serverPort}";

            if ($appUrl !== $correctAppUrl) {
                $result['status'] = 'fixed';
                $result['message'] = 'Environment configuration has been updated.';
                $result['details'][] = 'Updated APP_URL from ' . $appUrl . ' to ' . $correctAppUrl;
                $envContent = preg_replace('/APP_URL=.*/', "APP_URL={$correctAppUrl}", $envContent);
                $changes = true;
            }

            // Check FILESYSTEM_DISK
            if (strpos($envContent, 'FILESYSTEM_DISK=public') === false) {
                $result['status'] = 'fixed';
                if (!isset($result['message'])) {
                    $result['message'] = 'Environment configuration has been updated.';
                }
                $result['details'][] = 'Updated FILESYSTEM_DISK to public';

                if (strpos($envContent, 'FILESYSTEM_DISK=') !== false) {
                    $envContent = preg_replace('/FILESYSTEM_DISK=.*/', 'FILESYSTEM_DISK=public', $envContent);
                } else {
                    $envContent .= "\nFILESYSTEM_DISK=public\n";
                }
                $changes = true;
            } else {
                $result['details'][] = 'FILESYSTEM_DISK is already set to public';
            }

            if ($changes) {
                file_put_contents($envPath, $envContent);
            }
        } else {
            $result['status'] = 'error';
            $result['message'] = '.env file not found.';
        }

        return $result;
    }

    /**
     * Create necessary directories for image storage
     *
     * @return array
     */
    private function createNecessaryDirectories()
    {
        $result = [
            'status' => 'success',
            'message' => 'All necessary directories exist.',
            'details' => []
        ];

        $directories = [
            public_path('storage/products'),
            public_path('storage/product-colors'),
            public_path('storage/services'),
            public_path('storage/categories'),
            public_path('storage/branches'),
            public_path('storage/users'),
            public_path('storage/deals'),
            public_path('images/products'),
            public_path('images/services'),
            public_path('images/categories'),
        ];

        $created = false;
        foreach ($directories as $directory) {
            if (!file_exists($directory)) {
                try {
                    mkdir($directory, 0755, true);
                    $result['details'][] = 'Created directory: ' . $directory;
                    $created = true;
                } catch (\Exception $e) {
                    $result['status'] = 'error';
                    $result['message'] = 'Failed to create some directories.';
                    $result['details'][] = 'Failed to create directory ' . $directory . ': ' . $e->getMessage();
                }
            }
        }

        if ($created) {
            $result['status'] = 'fixed';
            $result['message'] = 'Created missing directories.';
        }

        return $result;
    }

    /**
     * Fix product image paths in the database
     *
     * @return array
     */
    private function fixProductImages()
    {
        $result = [
            'status' => 'success',
            'message' => 'All product images are correctly configured.',
            'details' => []
        ];

        $products = Product::all();
        $result['details'][] = 'Found ' . $products->count() . ' products.';

        $fixedCount = 0;
        foreach ($products as $product) {
            $originalImage = $product->getRawOriginal('image');

            if ($originalImage) {
                // Normalize the path
                $normalizedPath = ltrim($originalImage, '/');

                // Check if the image exists
                $imagePath = public_path($normalizedPath);
                $storageImagePath = storage_path('app/public/' . basename($normalizedPath));

                if (!file_exists($imagePath) && file_exists($storageImagePath)) {
                    // Fix the path to ensure it starts with /storage/
                    if (!str_starts_with($normalizedPath, 'storage/')) {
                        $newPath = 'storage/products/' . basename($normalizedPath);

                        // Update the product
                        $product->image = $newPath;
                        $product->save();

                        $result['details'][] = 'Fixed product image path: ' . $originalImage . ' -> ' . $newPath;
                        $fixedCount++;
                    }
                }
            }
        }

        if ($fixedCount > 0) {
            $result['status'] = 'fixed';
            $result['message'] = 'Fixed ' . $fixedCount . ' product image paths.';
        }

        return $result;
    }

    /**
     * Fix product color image paths in the database
     *
     * @return array
     */
    private function fixProductColorImages()
    {
        // Similar implementation to fixProductImages but for ProductColor model
        $result = [
            'status' => 'success',
            'message' => 'All product color images are correctly configured.',
            'details' => []
        ];

        $productColors = ProductColor::all();
        $result['details'][] = 'Found ' . $productColors->count() . ' product colors.';

        $fixedCount = 0;
        foreach ($productColors as $color) {
            $originalImage = $color->getRawOriginal('image');

            if ($originalImage) {
                // Normalize the path
                $normalizedPath = ltrim($originalImage, '/');

                // Check if the image exists
                $imagePath = public_path($normalizedPath);
                $storageImagePath = storage_path('app/public/' . basename($normalizedPath));

                if (!file_exists($imagePath) && file_exists($storageImagePath)) {
                    // Fix the path to ensure it starts with /storage/
                    if (!str_starts_with($normalizedPath, 'storage/')) {
                        $newPath = 'storage/product-colors/' . basename($normalizedPath);

                        // Update the color
                        $color->image = $newPath;
                        $color->save();

                        $result['details'][] = 'Fixed product color image path: ' . $originalImage . ' -> ' . $newPath;
                        $fixedCount++;
                    }
                }
            }
        }

        if ($fixedCount > 0) {
            $result['status'] = 'fixed';
            $result['message'] = 'Fixed ' . $fixedCount . ' product color image paths.';
        }

        return $result;
    }

    /**
     * Fix service image paths in the database
     *
     * @return array
     */
    private function fixServiceImages()
    {
        // Similar implementation to fixProductImages but for Service model
        $result = [
            'status' => 'success',
            'message' => 'All service images are correctly configured.',
            'details' => []
        ];

        $services = Service::all();
        $result['details'][] = 'Found ' . $services->count() . ' services.';

        $fixedCount = 0;
        foreach ($services as $service) {
            $originalImage = $service->getRawOriginal('image');

            if ($originalImage) {
                // Normalize the path
                $normalizedPath = ltrim($originalImage, '/');

                // Check if the image exists
                $imagePath = public_path($normalizedPath);
                $storageImagePath = storage_path('app/public/' . basename($normalizedPath));

                if (!file_exists($imagePath) && file_exists($storageImagePath)) {
                    // Fix the path to ensure it starts with /storage/
                    if (!str_starts_with($normalizedPath, 'storage/')) {
                        $newPath = 'storage/services/' . basename($normalizedPath);

                        // Update the service
                        $service->image = $newPath;
                        $service->save();

                        $result['details'][] = 'Fixed service image path: ' . $originalImage . ' -> ' . $newPath;
                        $fixedCount++;
                    }
                }
            }
        }

        if ($fixedCount > 0) {
            $result['status'] = 'fixed';
            $result['message'] = 'Fixed ' . $fixedCount . ' service image paths.';
        }

        return $result;
    }

    /**
     * Fix branch image paths in the database
     *
     * @return array
     */
    private function fixBranchImages()
    {
        // Similar implementation to fixProductImages but for Branch model
        $result = [
            'status' => 'success',
            'message' => 'All branch images are correctly configured.',
            'details' => []
        ];

        $branches = Branch::all();
        $result['details'][] = 'Found ' . $branches->count() . ' branches.';

        $fixedCount = 0;
        foreach ($branches as $branch) {
            $originalImage = $branch->getRawOriginal('image');

            if ($originalImage) {
                // Normalize the path
                $normalizedPath = ltrim($originalImage, '/');

                // Check if the image exists
                $imagePath = public_path($normalizedPath);
                $storageImagePath = storage_path('app/public/' . basename($normalizedPath));

                if (!file_exists($imagePath) && file_exists($storageImagePath)) {
                    // Fix the path to ensure it starts with /storage/
                    if (!str_starts_with($normalizedPath, 'storage/')) {
                        $newPath = 'storage/branches/' . basename($normalizedPath);

                        // Update the branch
                        $branch->image = $newPath;
                        $branch->save();

                        $result['details'][] = 'Fixed branch image path: ' . $originalImage . ' -> ' . $newPath;
                        $fixedCount++;
                    }
                }
            }
        }

        if ($fixedCount > 0) {
            $result['status'] = 'fixed';
            $result['message'] = 'Fixed ' . $fixedCount . ' branch image paths.';
        }

        return $result;
    }

    /**
     * Fix category image paths in the database
     *
     * @return array
     */
    private function fixCategoryImages()
    {
        // Similar implementation to fixProductImages but for Category model
        $result = [
            'status' => 'success',
            'message' => 'All category images are correctly configured.',
            'details' => []
        ];

        $categories = Category::all();
        $result['details'][] = 'Found ' . $categories->count() . ' categories.';

        $fixedCount = 0;
        foreach ($categories as $category) {
            $originalImage = $category->getRawOriginal('image');

            if ($originalImage) {
                // Normalize the path
                $normalizedPath = ltrim($originalImage, '/');

                // Check if the image exists
                $imagePath = public_path($normalizedPath);
                $storageImagePath = storage_path('app/public/' . basename($normalizedPath));

                if (!file_exists($imagePath) && file_exists($storageImagePath)) {
                    // Fix the path to ensure it starts with /storage/
                    if (!str_starts_with($normalizedPath, 'storage/')) {
                        $newPath = 'storage/categories/' . basename($normalizedPath);

                        // Update the category
                        $category->image = $newPath;
                        $category->save();

                        $result['details'][] = 'Fixed category image path: ' . $originalImage . ' -> ' . $newPath;
                        $fixedCount++;
                    }
                }
            }
        }

        if ($fixedCount > 0) {
            $result['status'] = 'fixed';
            $result['message'] = 'Fixed ' . $fixedCount . ' category image paths.';
        }

        return $result;
    }

    /**
     * Clear the configuration cache
     *
     * @return void
     */
    private function clearConfigCache()
    {
        exec('php artisan config:clear', $output, $returnCode);
        return $returnCode === 0;
    }
}
