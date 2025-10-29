<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\PasswordResetCodeNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * Handle incoming password reset requests.
     */
    public function request(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = (string) $request->input('email');
        $user = User::where('email', $email)->first();

        $expiresMinutes = (int) config(
            'auth.passwords.' . config('auth.defaults.passwords') . '.expire',
            60,
        );

        if ($user) {
            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            try {
                DB::table('password_reset_tokens')->updateOrInsert(
                    ['email' => $user->email],
                    [
                        'token' => Hash::make($code),
                        'created_at' => now(),
                    ],
                );

                $user->notify(new PasswordResetCodeNotification($code));
            } catch (\Throwable $e) {
                Log::error('Failed to dispatch password reset code', [
                    'email' => $email,
                    'error' => $e->getMessage(),
                ]);
            }
        } else {
            Log::info('Password reset requested for unknown email', [
                'email' => $email,
                'request_id' => (string) Str::uuid(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => __('If we found an account with that email, we have sent a verification code.'),
            'channels' => [
                'email' => true,
                'sms' => false,
            ],
            'code_expires_in_minutes' => $expiresMinutes,
        ]);
    }

    public function verifyCode(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required|string|min:4|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = (string) $request->input('email');
        $code = (string) $request->input('code');

        $repository = Password::getRepository();
        $codeIsValid = $repository->exists(['email' => $email], $code);

        if (!$codeIsValid) {
            return response()->json([
                'success' => false,
                'message' => __('The verification code is invalid or has expired.'),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => __('Code verified. You can reset your password now.'),
        ]);
    }

    public function reset(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required|string|min:4|max:10',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $status = Password::reset(
            [
                'email' => $request->input('email'),
                'token' => $request->input('code'),
                'password' => $request->input('password'),
                'password_confirmation' => $request->input('password_confirmation'),
            ],
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json([
                'success' => false,
                'message' => __('The verification code is invalid or has expired.'),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => __('Password reset successfully. You can now log in.'),
        ]);
    }
}
