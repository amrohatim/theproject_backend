<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use Illuminate\Http\Request;

class FcmTokenController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'platform' => 'nullable|string',
            'device_id' => 'nullable|string',
            'user_id' => 'nullable|integer|exists:users,id',
        ]);

        $userId = $validated['user_id'] ?? $request->user()?->getKey();
        if (!$userId) {
            return response()->json(['ok' => false, 'message' => 'User is required'], 401);
        }

        FcmToken::where('token', $validated['token'])
            ->where('user_id', '!=', $userId)
            ->delete();

        FcmToken::updateOrCreate(['user_id' => $userId], [
            'token' => $validated['token'],
            'user_id' => $userId,
            'platform' => $validated['platform'] ?? null,
            'device_id' => $validated['device_id'] ?? null,
            'last_seen_at' => now(),
        ]);

        return response()->json(['ok' => true]);
    }
}
