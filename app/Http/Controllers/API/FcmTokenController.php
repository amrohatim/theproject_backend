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
            'user_id'=>'nullable'
            
        ]);

        FcmToken::updateOrCreate(
            ['token' => $validated['token']],
            [
                
                'platform' => $validated['platform'] ?? null,
                'device_id' => $validated['device_id'] ?? null,
                'user_d'=>$validated['user_id']??null,
                'last_seen_at' => now(),
            ]
        );

        return response()->json(['ok' => true]);
    }
}
