<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AccountPasswords;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class AccountPasswordController extends Controller
{
    public function edit()
    {
        return response()->view('auth.passwords.change')->header('Cache-Control', 'no-store, private');
    }

    public function update(Request $request, AccountPasswords $passwords)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'different:current_password', Password::min(12)->mixedCase()->numbers()],
        ]);
        $passwords->update($request->user(), $data['password'], 'password_changed');
        $request->session()->regenerate();
        $request->session()->regenerateToken();
        $request->session()->put('password_version', $request->user()->password_version);
        return redirect()->route('account.password.edit')->with('success', 'Password changed. Other sessions and reset links have been revoked.');
    }

    public function issueTemporary(Request $request, User $user, AccountPasswords $passwords)
    {
        abort_unless($request->user()->isAdmin(), 403);
        abort_if($request->user()->is($user), 403, 'Use Change Password for your own account.');
        abort_unless($user->is_active, 422, 'Activate this account before issuing a temporary password.');
        $request->validate(['current_password' => ['required', 'current_password']]);
        $temporaryPassword = 'Rida!' . bin2hex(random_bytes(12));
        $passwords->update($user, $temporaryPassword, 'temporary_password_issued', true);
        return response()->view('users.temporary-password', compact('user', 'temporaryPassword'))
            ->header('Cache-Control', 'no-store, private')
            ->header('Pragma', 'no-cache');
    }
}
