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
 * @property array $sortedQuestions
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

    protected $appends = [
        'sortedQuestions'
    ];

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

    public function getSortedQuestionsAttribute(): array
    {
        return $this->sortedByRelatedKeys($this->questions, 'prev_id');
    }

    // TODO вынести в helpers
    private function sortedByRelatedKeys($data, string $relatedKey, $startValue = null): array
    {
        $result = [];
        foreach ($data as $item) {
            if ($item[$relatedKey] === $startValue) {
                // Добавляем текущий элемент в результирующий массив
                $result[] = $item;
                $result = array_merge($result, $this->sortedByRelatedKeys($data, $relatedKey, $item['id']));
            }
        }

        return $result;
    }
}
