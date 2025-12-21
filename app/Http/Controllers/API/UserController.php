<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Only admin can view all users
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $query = User::query();

        // Filter by role if provided
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search by name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(10);

        return response()->json([
            'success' => true,
            'users' => $users,
        ]);
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Only admin can create users
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,vendor,customer,provider,merchant,service_provider,products_manager',
            'phone' => 'nullable|string|max:20',
            'profile_image' => 'nullable|string',
            'status' => 'nullable|in:active,inactive,suspended',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'profile_image' => $request->profile_image,
            'status' => $request->status ?? 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'user' => $user,
        ], 201);
    }

    /**
     * Display the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Only admin or the user themselves can view user details
        if (Auth::user()->role !== 'admin' && Auth::id() != $id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $user = User::with(['company', 'branches'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'user' => $user,
        ]);
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Only admin or the user themselves can update user details
        if (Auth::user()->role !== 'admin' && Auth::id() != $id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'string|max:255',
            'email' => [
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($id),
            ],
            'password' => 'nullable|string|min:8',
            'role' => 'in:admin,vendor,customer,provider,merchant,service_provider,products_manager',
            'phone' => 'nullable|string|max:20',
            'profile_image' => 'nullable|string',
            'status' => 'in:active,inactive,suspended',
        ]);

        // Only admin can change role and status
        if (Auth::user()->role !== 'admin') {
            $request->request->remove('role');
            $request->request->remove('status');
        }

        $data = $request->all();

        // Hash password if provided
        if ($request->has('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'user' => $user,
        ]);
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Only admin can delete users
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }

    /**
     * Get the authenticated user's profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $user = User::with(['company', 'branches'])->findOrFail(Auth::id());

        return response()->json([
            'success' => true,
            'user' => $user,
        ]);
    }

    /**
     * Update the authenticated user's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore(Auth::id()),
            ],
            'phone' => 'sometimes|nullable|string|max:20',
            'current_password' => 'required_with:new_password|string',
            'new_password' => 'nullable|string|min:8|confirmed',
            'new_password_confirmation' => 'nullable|string',
            // Allow either an uploaded image or an existing URL/path
            'profile_image' =>
                'sometimes|nullable|required_without:profile_image_url|image|mimes:jpg,jpeg,png,webp|max:2048',
            'profile_image_url' => 'sometimes|nullable|string',
        ]);

        // Validate current password if a new password is requested
        if ($request->filled('new_password')) {
            if (!$request->filled('current_password') || !Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect.',
                ], 422);
            }

            $user->password = Hash::make($request->new_password);
        }

        // Update basic fields
        if ($request->filled('name')) {
            $user->name = $request->name;
        }

        if ($request->filled('email')) {
            $user->email = $request->email;
        }

        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }

        // Handle profile image upload or direct URL/path assignment
        if ($request->hasFile('profile_image')) {
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');

            // Remove old profile image if it exists
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $user->profile_image = $imagePath;
        } elseif ($request->filled('profile_image_url')) {
            $user->profile_image = $request->input('profile_image_url');
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => $user->fresh(),
        ]);
    }

    /**
     * Get all locations for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLocations()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            $locations = UserLocation::where('user_id', $user->id)
                ->orderBy('is_default', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'locations' => $locations,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch locations',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
