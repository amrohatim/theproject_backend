<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Helpers\ProviderDashboardHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display the provider's profile.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        // Ensure the provider record exists
        $provider = $user->providerRecord;
        if (!$provider) {
            $provider = Provider::create([
                'user_id' => $user->id,
                'business_name' => "{$user->name}'s Business",
                'company_name' => "{$user->name}'s Company",
                'status' => 'active',
                'is_verified' => false
            ]);
        }

        // If no view exists yet, return to dashboard with a message
        if (!view()->exists('provider.profile.index')) {
            return ProviderDashboardHelper::getDashboardData('Profile management is under development');
        }

        return view('provider.profile.index', compact('user'));
    }

    /**
     * Update the provider's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:20',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'phone']);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            try {
                // Use the public images directory for direct access
                $destinationPath = public_path('images/users');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                
                // Delete old image if exists
                if ($user->profile_image) {
                    $oldImagePath = public_path($user->profile_image);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                
                $file = $request->file('profile_image');
                $fileName = time() . '_' . $file->getClientOriginalName();
                
                // Move the file directly to the public directory
                $file->move($destinationPath, $fileName);
                
                // Store the direct public URL path
                $data['profile_image'] = 'images/users/' . $fileName;
            } catch (\Exception $e) {
                // Handle any exceptions that may occur during the image upload process
            }
        }

        $user->update($data);

        return redirect()->route('provider.profile.index')
            ->with('success', 'Profile updated successfully');
    }

    /**
     * Show the form for changing password.
     *
     * @return \Illuminate\View\View
     */
    public function showChangePasswordForm()
    {
        return view('provider.profile.change-password');
    }

    /**
     * Update the provider's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('provider.profile.index')
            ->with('success', 'Password changed successfully');
    }
}
