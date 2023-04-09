<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Question
 *
 * @property int $id
 * @property int $test_id
 * @property int $prev_id
 * @property int $next_id
 *  */
class GroupQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'test_id',
        'prev_id',
        'next_id',
    ];

    protected $with = [];

    public function nextGroup(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(GroupQuestion::class, 'next_id');
    }

    public function prevGroup(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(GroupQuestion::class, 'prev_id');
    }

    public function questions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Question::class, 'group_id');
    }
}
