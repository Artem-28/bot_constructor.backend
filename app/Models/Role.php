<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Role
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $description
 *  */
class Role extends Model
{
    use HasFactory;

    const STUDENT = 'student';
    const TEACHER = 'teacher';
    const BUSINESS = 'business';

    protected $fillable = [
        'title',
        'description',
        'slug',
    ];

    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
