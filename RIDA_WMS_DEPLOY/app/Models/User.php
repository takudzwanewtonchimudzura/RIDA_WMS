<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, \Illuminate\Database\Eloquent\SoftDeletes, Concerns\AuditsDeletes;

    public const ROLES = [
        'admin' => 'Administrator',
        'supervisor' => 'Supervisor / Provincial Engineer',
        'operator' => 'Field Officer',
        'finance' => 'Finance',
        'stores' => 'Stores',
        'viewer' => 'Viewer',
    ];

    public function canApprove(): bool
    {
        return in_array($this->role, ['admin', 'supervisor'], true);
    }

    public function canManageFinance(): bool
    {
        return in_array($this->role, ['admin', 'finance'], true);
    }

    public function canManageStores(): bool
    {
        return in_array($this->role, ['admin', 'stores'], true);
    }

    public function canCapture(): bool
    {
        return in_array($this->role, ['admin', 'operator', 'supervisor'], true);
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'department',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'password_version',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'is_active' => 'boolean',
        'password_version' => 'integer',
        'must_change_password' => 'boolean',
        'temporary_password_expires_at' => 'datetime',
    ];

    // Role checking methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isOperator()
    {
        return $this->role === 'operator';
    }

    public function isViewer()
    {
        return $this->role === 'viewer';
    }

    // Relationship with boreholes
    public function boreholes()
    {
        return $this->hasMany(Borehole::class, 'created_by');
    }
}