<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    protected function credentials(Request $request): array
    {
        return array_merge(
            $request->only($this->username(), 'password'),
            ['is_active' => true]
        );
    }

    protected function authenticated(Request $request, $user)
    {
        if ($user->temporary_password_expires_at && $user->temporary_password_expires_at->isPast()) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->withErrors(['email' => 'The temporary password has expired. Request a new password reset.']);
        }
        $request->session()->put('password_version', $user->password_version);
        $user->forceFill(['last_login_at' => now(), 'last_seen_at' => now()])->save();
        if ($user->must_change_password) {
            return redirect()->route('account.password.edit');
        }
    }
}
