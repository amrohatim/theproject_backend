<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Service;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ImageTestController extends Controller
{
    /**
     * Test image URLs for Flutter app
     *
     * @return \Illuminate\Http\Response
     */
    public function testImages()
    {
        $products = Product::with('branch')->take(5)->get();
        $services = Service::with('branch')->take(5)->get();
        $categories = Category::take(5)->get();
        $users = User::take(5)->get();
        
        // Get all image directories
        $imageDirectories = [
            'products' => $this->getFilesInDirectory('public/images/products'),
            'services' => $this->getFilesInDirectory('public/images/services'),
            'categories' => $this->getFilesInDirectory('public/images/categories'),
            'users' => $this->getFilesInDirectory('public/images/users'),
            'storage_products' => $this->getFilesInDirectory('storage/app/public/products'),
        ];
        
        return response()->json([
            'success' => true,
            'app_url' => Config::get('app.url'),
            'storage_link_exists' => file_exists(public_path('storage')),
            'products' => $products,
            'services' => $services,
            'categories' => $categories,
            'users' => $users,
            'image_directories' => $imageDirectories,
            'image_paths' => [
                'example_product_image' => $products->first() ? $products->first()->image : null,
                'example_service_image' => $services->first() ? $services->first()->image : null,
                'example_category_image' => $categories->first() ? $categories->first()->image : null,
                'example_user_image' => $users->first() ? $users->first()->profile_image : null,
                'public_images_path' => url('/images'),
                'storage_path' => url('/storage'),
            ],
            'test_urls' => [
                'direct_image_url' => url('/images/products/smartphone-x.jpg'),
                'storage_image_url' => url('/storage/products/pisHTvjmajAKcCn0DW4k8GCWUfVgEzrHdB7JkKKr.png'),
            ]
        ]);
    }
    
    /**
     * Get all files in a directory
     *
     * @param string $directory
     * @return array
     */
    private function getFilesInDirectory($directory)
    {
        $result = [];
        
        if (File::exists($directory)) {
            $files = File::files($directory);
            foreach ($files as $file) {
                $result[] = [
                    'name' => $file->getFilename(),
                    'path' => $file->getPathname(),
                    'url' => str_starts_with($directory, 'public/') 
                        ? url(str_replace('public/', '', $directory) . '/' . $file->getFilename())
                        : url('storage/' . str_replace('storage/app/public/', '', $directory) . '/' . $file->getFilename()),
                ];
            }
        }
        
        return $result;
    }
}
