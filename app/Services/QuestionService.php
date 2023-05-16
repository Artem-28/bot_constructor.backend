<?php

namespace App\Services;

use App\Models\Question;
use Illuminate\Database\Eloquent\Model;

class QuestionService
{
    private function formattingData($data): array
    {
        $payload = [];
        if (array_key_exists('prevId', $data)) {
            $payload['prev_id'] = $data['prevId'];
        }
        if (array_key_exists('nextId', $data)) {
            $payload['next_id'] = $data['nextId'];
        }
        if (array_key_exists('text', $data)) {
            $payload['text'] = $data['text'];
        }
        if (array_key_exists('type', $data)) {
            $payload['type'] = $data['type'];
        }
        return $payload;
    }

    // Создание вопроса для теста
    public function create(int $testId, array $data)
    {
        $payload = $this->formattingData($data);

        $question =  new Question([
            'test_id' => $testId,
            ...$payload
        ]);

        // Если есть prev_id создаем следующий вопрос
        if ($question->have_prev_question) {
            $prevQuestion = $this->getQuestionById($question->prev_id);
            $prevQuestion?->nextQuestion()->associate($question);
            return $prevQuestion->save();
        }
        dd('test');
        $question->save();
        return $question;
    }

    public function getQuestionById(int $questionId): Model | null
    {
        return Question::query()->find($questionId);
    }
}
