<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CategoryCourse extends Pivot
{
    protected $table = 'category_course';

    protected $fillable = [
        'course_id',
        'category_code',
    ];
}
