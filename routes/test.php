<?php

use Illuminate\Support\Facades\Route;
use App\Models\Category;

Route::get("/test/category-images", function() {
    $categories = Category::whereNotNull("image")->take(5)->get();
    
    $result = [
        "status" => "success",
        "message" => "Category images test",
        "timestamp" => now()->toISOString(),
        "base_url" => config("app.url"),
        "categories" => $categories->map(function($category) {
            $parsedUrl = parse_url($category->image);
            $path = ltrim($parsedUrl["path"] ?? "", "/");
            $fullPath = public_path($path);
            
            return [
                "id" => $category->id,
                "name" => $category->name,
                "image_url" => $category->image,
                "file_exists" => file_exists($fullPath),
                "file_size" => file_exists($fullPath) ? filesize($fullPath) : 0,
                "raw_path" => $category->getAttributes()["image"]
            ];
        })
    ];
    
    return response()->json($result);
});