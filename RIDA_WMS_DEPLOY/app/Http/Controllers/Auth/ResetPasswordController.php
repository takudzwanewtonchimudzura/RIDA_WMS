<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AccountPasswords;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password as PasswordRule;

class ResetPasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware('throttle:password-reset')->only('reset');
    }

    public function showResetForm(Request $request)
    {
        return view('auth.passwords.reset', ['token' => $request->route('token'), 'email' => $request->input('email')]);
    }

    public function reset(Request $request, AccountPasswords $passwords)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'email' => 'required|email|max:255',
            'password' => ['required', 'confirmed', PasswordRule::min(12)->mixedCase()->numbers()],
        ]);
        $status = DB::transaction(function () use ($validated, $passwords) {
            User::where('email', $validated['email'])->lockForUpdate()->first();
            return Password::reset($validated + ['is_active' => true], function ($user, $password) use ($passwords) {
                $passwords->update($user, $password, 'password_reset');
                event(new PasswordReset($user));
            });
        });
        if ($status !== Password::PASSWORD_RESET) {
            return back()->withErrors(['email' => 'This reset link is invalid or expired. Request a new link or contact your administrator.'])->withInput($request->only('email'));
        }
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('status', 'Password reset successfully. Sign in with your new password.');
    }
}
