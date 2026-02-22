<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    /**
     * Display vendor security settings and active login sessions.
     *
     * @return \Illuminate\View\View
     */
    public function security()
    {
        $currentSessionId = session()->getId();
        $sessionDriver = config('session.driver');
        $activeSessions = collect();

        if ($sessionDriver === 'database') {
            $activeSessions = DB::table('sessions')
                ->select('id', 'ip_address', 'user_agent', 'last_activity')
                ->where('user_id', Auth::id())
                ->orderByDesc('last_activity')
                ->get()
                ->map(function ($session) use ($currentSessionId) {
                    $device = $this->parseDeviceLabel((string) ($session->user_agent ?? ''));

                    return (object) [
                        'id' => $session->id,
                        'ip_address' => $session->ip_address ?: 'N/A',
                        'last_activity' => (int) $session->last_activity,
                        'last_activity_human' => Carbon::createFromTimestamp((int) $session->last_activity)->diffForHumans(),
                        'is_current' => $session->id === $currentSessionId,
                        'device_label' => $device['label'],
                        'device_icon' => $device['icon'],
                    ];
                });
        }

        return view('vendor.settings.security', compact('activeSessions', 'currentSessionId', 'sessionDriver'));
    }

    /**
     * Update the vendor's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('vendor.settings.security')
            ->with('success', 'Password updated successfully.');
    }

    /**
     * Log out all other sessions except the current one.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logoutOtherSessions(Request $request)
    {
        if (config('session.driver') !== 'database') {
            return redirect()->route('vendor.settings.security')
                ->with('error', 'Session revocation requires database session driver.');
        }

        $deletedCount = DB::table('sessions')
            ->where('user_id', Auth::id())
            ->where('id', '!=', $request->session()->getId())
            ->delete();

        if ($deletedCount > 0) {
            return redirect()->route('vendor.settings.security')
                ->with('success', "Logged out {$deletedCount} other session(s).");
        }

        return redirect()->route('vendor.settings.security')
            ->with('success', 'No other active sessions found.');
    }

    /**
     * Update the vendor's profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($request->only(['name', 'email', 'phone']));

        return redirect()->route('vendor.settings.profile')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Parse user agent into device label and icon.
     *
     * @param string $userAgent
     * @return array{label: string, icon: string}
     */
    private function parseDeviceLabel(string $userAgent): array
    {
        $agent = strtolower($userAgent);

        if (str_contains($agent, 'android')) {
            $os = 'Android';
        } elseif (str_contains($agent, 'iphone') || str_contains($agent, 'ipad') || str_contains($agent, 'ios')) {
            $os = 'iOS';
        } elseif (str_contains($agent, 'mac os') || str_contains($agent, 'macintosh')) {
            $os = 'macOS';
        } elseif (str_contains($agent, 'windows')) {
            $os = 'Windows';
        } elseif (str_contains($agent, 'linux')) {
            $os = 'Linux';
        } else {
            $os = 'Unknown OS';
        }

        if (str_contains($agent, 'edg/')) {
            $browser = 'Edge';
        } elseif (str_contains($agent, 'opr/') || str_contains($agent, 'opera')) {
            $browser = 'Opera';
        } elseif (str_contains($agent, 'firefox/')) {
            $browser = 'Firefox';
        } elseif (str_contains($agent, 'chrome/') && !str_contains($agent, 'edg/')) {
            $browser = 'Chrome';
        } elseif (str_contains($agent, 'safari/') && !str_contains($agent, 'chrome/')) {
            $browser = 'Safari';
        } else {
            $browser = 'Unknown Browser';
        }

        $mobileOs = in_array($os, ['Android', 'iOS'], true);
        $icon = $mobileOs ? 'fa-mobile-alt' : 'fa-desktop';

        return [
            'label' => "{$os} - {$browser}",
            'icon' => $icon,
        ];
    }
}
