<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Course
 *
 * @property int $id
 * @property int $account_id
 * @property int $parent_course_id
 * @property string $title
 * @property string $description
 * @property string $order
 *  */
class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'account_id',
        'parent_course_id',
        'description',
        'order',
    ];

    protected $with = [
    ];

    public function subCourses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Course::class, 'parent_course_id');
    }
}
