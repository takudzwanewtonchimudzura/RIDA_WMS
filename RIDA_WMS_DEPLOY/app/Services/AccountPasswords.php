<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AccountPasswords
{
    public function update(User $user, #[\SensitiveParameter] string $password, string $action, bool $temporary = false): void
    {
        DB::transaction(function () use ($user, $password, $action, $temporary) {
            $locked = User::lockForUpdate()->findOrFail($user->id);
            abort_unless($locked->is_active, 403, 'Inactive accounts cannot change passwords.');
            $locked->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
                'password_version' => $locked->password_version + 1,
                'must_change_password' => $temporary,
                'temporary_password_expires_at' => $temporary ? now()->addDay() : null,
            ])->save();
            Password::broker()->deleteToken($locked);
            if (config('session.driver') === 'database') {
                DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))->where('user_id', $locked->id)->delete();
            }
            AuditLog::record($locked, $action, [
                'temporary' => $temporary,
                'temporary_password_expires_at' => $locked->temporary_password_expires_at?->toIso8601String(),
            ]);
            $user->refresh();
        });
    }
}
