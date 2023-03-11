<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $email
 * @property string|null $phone
 * @property string $email_verified_at
 * @property string $phone_verified_at
 * @property array $permissions
 *  */

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at'
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
    ];

    protected $appends = [
        'permissions'
    ];

    protected $with = [
        'account',
        'profile'
    ];

    public function account(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Account::class);
    }

    // Аккаунты в которых пользователь является учителем
    public function teacherAccounts(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Account::class, 'teachers');
    }

    public function adminDialogGroups(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(DialogGroup::class, 'admin_dialog_groups');
    }

    public function profile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_users', 'user_id', 'role', 'id', 'slug');
    }

    public function roleSlugs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RoleUser::class);
    }

    public function getPermissionsAttribute()
    {
        return $this->roleSlugs->pluck('role')->toArray();
    }

    public function hasPermission(string $role): bool
    {
        return in_array($role, $this->permissions);
    }
}
