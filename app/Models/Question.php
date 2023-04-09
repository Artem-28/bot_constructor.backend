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
 * @property string $text
 * @property string $type
 * @property bool $have_prev_question
 *  */
class Question extends Model
{
    use HasFactory;

    const SINGLE_CHOICE_TYPE = 'single_choice';
    const MULTIPLE_CHOSE_TYPE = 'multiple_choice';

    protected $fillable = [
        'id',
        'test_id',
        'prev_id',
        'next_id',
        'type',
        'text',
    ];

    protected $appends = [
        'have_prev_question'
    ];

    public function getHavePrevQuestionAttribute(): bool
    {
        return (bool) $this->prev_id;
    }

    // Предыдущий вопрос
    public function prevQuestion(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Question::class, 'prev_id');
    }

    // Следующий вопрос
    public function nextQuestion(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Question::class, 'next_id', 'id' );
    }
}
