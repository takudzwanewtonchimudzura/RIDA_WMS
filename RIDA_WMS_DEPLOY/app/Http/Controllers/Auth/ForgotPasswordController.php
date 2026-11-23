<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class ForgotPasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware('throttle:password-email')->only('sendResetLinkEmail');
    }

    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email|max:255']);
        if (in_array(config('mail.default'), ['log', 'array'], true)) {
            return back()->withErrors(['email' => 'Email delivery is not configured. Contact your administrator for a temporary password.'])->withInput($request->only('email'));
        }
        try {
            $status = Password::sendResetLink(['email' => $request->input('email'), 'is_active' => true]);
        } catch (TransportExceptionInterface $exception) {
            report($exception);
            return back()->withErrors(['email' => 'Email delivery failed. Contact your administrator or try again later.'])->withInput($request->only('email'));
        }
        if (!in_array($status, [Password::RESET_LINK_SENT, Password::INVALID_USER, Password::RESET_THROTTLED], true)) {
            throw new \RuntimeException('Unexpected password-reset delivery result: ' . $status);
        }
        return back()->with('status', 'If this is an active account, a reset link has been requested. Check your email; repeated requests may be throttled.');
    }
}
