<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DialogGroup
 *
 * @property int $id
 * @property int $group_creator
 * @property int $title
 * @property int $admin
 * @property string $avatar
 * @property string $created_at
 *  */
class DialogGroup extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'group_creator', 'title', 'avatar'];

    // Администратор группы
    public function admins(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'admin_dialog_groups');
    }

    // Участники группы
    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'dialog_group_users');
    }

    // Является ли пользователь администратором группы
    public function userIsAdmin($userId): bool
    {
        return $this->admins()->where('user_id', $userId)->exists();
    }

    // Является ли пользователь создателем группы
    public function userIsCreator($userId): bool
    {
        return $this->group_creator === $userId;
    }
}
