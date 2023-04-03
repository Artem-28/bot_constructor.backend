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
 * @property array $category_codes
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
    protected $with = [];
    protected $appends = [
        'category_codes'
    ];

    public function subCourses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Course::class, 'parent_course_id');
    }

    public function categories(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_course', 'course_id', 'category_code', 'id', 'code');
    }

    public function categoryCourse(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CategoryCourse::class);
    }

    public function getCategoryCodesAttribute()
    {
        return $this->categoryCourse->pluck('category_code')->toArray();
    }
}
