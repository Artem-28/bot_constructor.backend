<?php

namespace App\Transformers;
use App\Models\Question;
use \League\Fractal\TransformerAbstract;

class QuestionTransformer extends TransformerAbstract
{
    public function transform(Question $question): array
    {
        return [
            'id' => $question->id,
            'groupId' => $question->group_id,
            'prevQuestionId' => $question->prev_id,
            'nextQuestionId' => $question->next_id,
            'text' => $question->text,
        ];
    }
}
