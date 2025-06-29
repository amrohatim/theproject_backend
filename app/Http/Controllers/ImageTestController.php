<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class ImageTestController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('image_test', compact('products'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'test_image' => 'required|image|max:2048',
        ]);

        if ($request->hasFile('test_image')) {
            // Store in public disk
            $path = $request->file('test_image')->store('test_images', 'public');

            // Get the URL
            $url = Storage::url($path);

            return redirect()->route('test.images')->with('test_image_path', $url);
        }

        return redirect()->route('test.images')->with('error', 'Failed to upload image');
    }

    public function apiTest()
    {
        $products = Product::all()->take(5);
        $appUrl = Config::get('app.url');

        return response()->json([
            'success' => true,
            'app_url' => $appUrl,
            'products' => $products,
            'storage_link_exists' => file_exists(public_path('storage')),
            'storage_path' => storage_path('app/public'),
            'public_path' => public_path('storage'),
        ]);
    }
}
