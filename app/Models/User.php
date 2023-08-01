<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;
use App\Enums\ActiveStatus;
use App\Enums\UserChangeInfo;
use App\Enums\UserChangePassword;
use App\Enums\Role;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'role',
        'is_changed_password',
        'is_changed_info',
        'token',
        'token_expired_at',
        'remember_token',
        'last_login_at',
        'userable_id',
        'userable_type',
        'language'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'role' => Role::class,
        'is_changed_password' => UserChangePassword::class,
        'is_changed_info' => UserChangeInfo::class,
        'status' => ActiveStatus::class
    ];

    /**
     * Get the parent userable model (customer).
     */
    public function userable(): MorphTo
    {
        return $this->morphTo();
    }

    public function isAdmin(): bool
    {
        return $this->role == Role::ADMIN;
    }

    public function isUser(): bool
    {
        return $this->role == Role::USER;
    }

    public function hasRole($role): bool
    {
        return $this->role->value == $role;
    }

    public function isFirstLogin(): bool
    {
        return $this->is_changed_password == UserChangePassword::NO_CHANGE;
    }

    public function isChangedInfo(): bool
    {
        return $this->is_changed_info == UserChangeInfo::CHANGED;
    }

    public function isActive(): bool
    {
        return $this->status == ActiveStatus::ACTIVE;
    }

    public function isInActive(): bool
    {
        return $this->status == ActiveStatus::INACTIVE;
    }

    public function scopeRoleUser($q)
    {
        return $q->where('users.role', Role::USER);
    }

    public function scopeRoleAdmin($q)
    {
        return $q->where('users.role', Role::ADMIN);
    }

    public function scopeJoinCustomer($q, $join = 'join')
    {
        return $q->{$join}('customers', function ($q) {
            $q->on('customers.id', 'users.userable_id')
                ->where('users.userable_type', 'App\Models\Customer');
        });
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
