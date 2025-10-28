<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SMSalaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class PasswordResetController extends Controller
{
    public function __construct(private SMSalaService $smsalaService)
    {
    }

    /**
     * Handle incoming password reset requests.
     */
    public function request(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'channel' => 'sometimes|in:email,sms,both',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = (string) $request->input('email');
        $channel = (string) $request->input('channel', 'email');

        $user = User::where('email', $email)->first();

        $status = Password::sendResetLink(['email' => $email]);
        if ($status !== Password::RESET_LINK_SENT) {
            Log::warning('Password reset link dispatch did not return success status', [
                'status' => $status,
                'email' => $email,
            ]);
        }

        $otpData = null;
        if ($user && in_array($channel, ['sms', 'both'], true) && $this->canSendSmsOtp($user)) {
            $otpResult = $this->smsalaService->sendOTP($user->phone, 'password_reset');
            if (($otpResult['success'] ?? false) === true) {
                $otpData = [
                    'request_id' => $otpResult['request_id'] ?? null,
                    'expires_in' => $otpResult['expires_in'] ?? 600,
                    'method' => $otpResult['method'] ?? 'smsala',
                ];
            } else {
                Log::warning('Password reset OTP dispatch failed', [
                    'email' => $email,
                    'phone' => $user->phone,
                    'error' => $otpResult['message'] ?? 'unknown',
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => __('If we found an account with that email, we have sent password reset instructions.'),
            'channels' => [
                'email' => true,
                'sms' => in_array($channel, ['sms', 'both'], true),
            ],
            'otp' => $otpData,
        ]);
    }

    private function canSendSmsOtp(User $user): bool
    {
        return !empty($user->phone) && (bool) ($user->phone_verified ?? false);
    }
}
