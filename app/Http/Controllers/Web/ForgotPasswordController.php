<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\PasswordResetCodeNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function showRequestForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetCode(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = (string) $validated['email'];
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

                return back()
                    ->withErrors([
                        'email' => __('messages.password_reset_email_failed'),
                    ])
                    ->withInput();
            }
        } else {
            Log::info('Password reset requested for unknown email (web)', [
                'email' => $email,
                'request_id' => (string) Str::uuid(),
            ]);
        }

        $request->session()->put('password_reset_email', $email);

        return redirect()->route('password.reset.form', ['email' => $email])
            ->with('status', __('messages.password_reset_code_sent', [
                'minutes' => $expiresMinutes,
            ]));
    }

    public function showResetForm(Request $request): View|RedirectResponse
    {
        $email = $request->query('email') ?? $request->session()->get('password_reset_email');

        if (!$email) {
            return redirect()->route('password.request')->withErrors([
                'email' => __('messages.password_reset_missing_email'),
            ]);
        }

        return view('auth.reset-password', [
            'email' => $email,
            'status' => session('status'),
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'string', 'min:4', 'max:10'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            [
                'email' => $validated['email'],
                'token' => $validated['code'],
                'password' => $validated['password'],
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
            return back()->withErrors([
                'code' => __('messages.password_reset_invalid_code'),
            ])->withInput();
        }

        $request->session()->forget('password_reset_email');

        return redirect()
            ->route('login')
            ->with('success', __('messages.password_reset_success'));
    }
}
