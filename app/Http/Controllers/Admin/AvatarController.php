<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAvatarRequest;
use App\Http\Requests\UpdateAvatarRequest;
use App\Models\Avatar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class AvatarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $avatars = Avatar::orderBy('created_at', 'desc')->paginate(12);
        return view('admin.avatars.index', compact('avatars'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.avatars.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAvatarRequest $request)
    {
        try {
            $file = $request->file('avatar_image');
            $originalName = $file->getClientOriginalName();
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            
            // Store the file
            $path = $file->storeAs('avatars', $fileName, 'public');
            
            // Get image dimensions
            $imagePath = storage_path('app/public/' . $path);
            $imageSize = getimagesize($imagePath);
            
            Avatar::create([
                'name' => $request->name ?: pathinfo($originalName, PATHINFO_FILENAME),
                'description' => $request->description,
                'avatar_image' => $path,
                'original_filename' => $originalName,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'width' => $imageSize[0] ?? null,
                'height' => $imageSize[1] ?? null
            ]);
            
            return redirect()->route('admin.avatars.index')
                ->with('success', 'Avatar uploaded successfully!');
                
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to upload avatar: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Avatar $avatar)
    {
        return view('admin.avatars.show', compact('avatar'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Avatar $avatar)
    {
        return view('admin.avatars.edit', compact('avatar'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAvatarRequest $request, Avatar $avatar)
    {
        try {
            $data = [
                'name' => $request->name ?: $avatar->name,
                'description' => $request->description
            ];
            
            // Handle new image upload
            if ($request->hasFile('avatar_image')) {
                // Delete old image
                if ($avatar->avatar_image && Storage::disk('public')->exists($avatar->avatar_image)) {
                    Storage::disk('public')->delete($avatar->avatar_image);
                }
                
                $file = $request->file('avatar_image');
                $originalName = $file->getClientOriginalName();
                $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                
                // Store the new file
                $path = $file->storeAs('avatars', $fileName, 'public');
                
                // Get image dimensions
                $imagePath = storage_path('app/public/' . $path);
                $imageSize = getimagesize($imagePath);
                
                $data = array_merge($data, [
                    'avatar_image' => $path,
                    'original_filename' => $originalName,
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'width' => $imageSize[0] ?? null,
                    'height' => $imageSize[1] ?? null
                ]);
            }
            
            $avatar->update($data);
            
            return redirect()->route('admin.avatars.index')
                ->with('success', 'Avatar updated successfully!');
                
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update avatar: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Avatar $avatar)
    {
        try {
            // Delete the image file
            if ($avatar->avatar_image && Storage::disk('public')->exists($avatar->avatar_image)) {
                Storage::disk('public')->delete($avatar->avatar_image);
            }
            
            $avatar->delete();
            
            return redirect()->route('admin.avatars.index')
                ->with('success', 'Avatar deleted successfully!');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete avatar: ' . $e->getMessage());
        }
    }
}
