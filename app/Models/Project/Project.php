<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Project
 *
 * @property int $id
 * @property int $tariff_project_id
 * @property int $user_id
 * @property string $title
 *  */
class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tariff_project_id',
        'user_id',
        'title'
    ];
}
