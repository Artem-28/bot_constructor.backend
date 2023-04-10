<?php

namespace App\Services;

use App\Models\GroupQuestion;
use App\Models\Question;
use Illuminate\Support\Facades\DB;

class GroupQuestionService
{
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

    public function createTestQuestion(int $testId, array $groupData, array $questionData)
    {
        return DB::transaction(function () use ($testId, $groupData, $questionData) {

            $groupId = array_key_exists('groupId', $groupData) ? $groupData['groupId'] : null;
            $prevGroupId = array_key_exists('prevGroupId', $groupData) ? $groupData['prevGroupId'] : null;

            if (!$groupId) {
                $group = $this->createGroup($testId, $prevGroupId);
                return $this->createQuestion($group, $questionData);
            }

            $group = $this->getGroupById($groupId);

            if (!$group) {
                $message = 'Группа с id ' . $groupId . ' не найдена';
                return throw new \Exception($message, 404);
            }

            $question = $this->createQuestion($group, $questionData);
            return $question;
        });
    }

    /**
     * @throws \Exception
     */
    private function createGroup(int $testId, int | null $prevGroupId = null): GroupQuestion
    {
        $group = new GroupQuestion([
            'test_id' => $testId,
            'prev_id' => $prevGroupId,
        ]);

        // Если не передавали предыдущую группу
        if (!$prevGroupId) {
            // устанавливаем новую группу как первую
            return $this->insertGroupAtStart($group);
        };
        // Устанавливаем следующую группу или вставляем между групп
        return $this->insertBetweenGroups($group, $group->prev_id);
    }

    // Создание вопроса
    private function createQuestion(GroupQuestion $group, array $questionData): Question
    {
        // Предыдущий вариант вопроса
        $prevQuestionId = array_key_exists('prevQuestionId', $questionData) ? $questionData['prevQuestionId'] : null;
        $question = new Question([
            'type' => $questionData['type'],
            'text' => $questionData['text']
        ]);
        // Присоеденяем группу к вопросу
        $question->group()->associate($group);

        // Если не передавали предыдущий вопрос
        if (!$prevQuestionId) {
            // Устанавливаем новый вопрос как первый
            return $this->insertQuestionAtStart($question);
        }
        // Устанавливает следующий вопрос или вставляет между вопросами
        return $this->insertBetweenQuestions($question, $prevQuestionId);
    }

    // Вставляет группу между групп либо в конец (не вставляет в начало т.е как стартовую)
    private function insertBetweenGroups(GroupQuestion $group, int $prevGroupId): GroupQuestion
    {
        $prevGroup = $this->getGroupById($prevGroupId, 'nextGroup');
        // Если предыдущей группы не найдено выкидываем исключение
        if (!$prevGroup) {
            $message = 'Группа с id ' . $prevGroupId . ' не найдена';
            return throw new \Exception($message, 404);
        }

        $nextGroup = $prevGroup->nextGroup;

        $group->prevGroup()->associate($prevGroup);
        $group->nextGroup()->associate($nextGroup);
        $group->save();
        $prevGroup->nextGroup()->associate($group);

        if ($nextGroup) {
            $nextGroup->prevGroup()->associate($group);
            $nextGroup->save();
        }
        $prevGroup->save();
        return $group;
    }

    // Вставляет группу в начало
    private function insertGroupAtStart(GroupQuestion $group): GroupQuestion
    {
        // Получаем первую группу в тесте
        $startGroup = $this->getStartGroupByTestId($group->test_id, 'prevGroup');
        // Удаляем все связи группы
        $group->prevGroup()->dissociate();
        $group->nextGroup()->dissociate();
        // Если первой группы у теста нет
        if (!$startGroup) {
            // Сохраняем группу как первую
            $group->save();
            return $group;
        }

        // Если первая группа уже была создана
        // Новой группе устанавливаем первую как следующую
        $group->nextGroup()->associate($startGroup);
        $group->save();
        // Первой группе устанавливаем предыдущую
        $startGroup->prevGroup()->associate($group);
        $startGroup->save();
        return $group;
    }

    // Устанавливает вопрос как стартовый
    private function insertQuestionAtStart(Question $question): Question
    {
        // Получаем первый вопрос в группе
        $startQuestion = $this->getStartQuestionByGroupId($question->group_id);

        // Удаляем связи у вопроса если они есть
        $question->prevQuestion()->dissociate();
        $question->nextQuestion()->dissociate();

        // Если нет первого вопроса
        if (!$startQuestion) {
            // Новый вопрос устанавливаем как первый
            $question->save();
            return $question;
        }

        // Устанавливаем новому вопросу следующий вариант
        $question->nextQuestion()->associate($startQuestion);
        $question->save();

        // У Первому вопросу устанавливаем предыдущий вариант
        $startQuestion->prevQuestion()->associate($question);
        $startQuestion->save();
        return $question;
    }

    // Вставляет вопрос между вопросов либо в конец (не вставляет в начало т.е как стартовый)
    private function insertBetweenQuestions(Question $question, int $prevQuestionId): Question
    {
        $prevQuestion = $this->getQuestionById($prevQuestionId, 'nextQuestion');
        // Если предыдущей группы не найдено выкидываем исключение
        if (!$prevQuestion) {
            $message = 'Вопрос с id ' . $prevQuestionId . ' не найдена';
            return throw new \Exception($message, 404);
        }

        if ($prevQuestion->group_id !== $question->group_id) {
            $message = 'Вопрос с id ' . $prevQuestionId . ' не относится к группе с id ' . $question->group_id;
            return throw new \Exception($message, 404);
        }

        $nextQuestion = $prevQuestion->nextQuestion;

        $question->prevQuestion()->associate($prevQuestion);
        $question->nextQuestion()->associate($nextQuestion);
        $question->save();
        $prevQuestion->nextQuestion()->associate($question);

        if ($nextQuestion) {
            $nextQuestion->prevQuestion()->associate($question);
            $nextQuestion->save();
        }
        $prevQuestion->save();
        return $question;
    }

    // Соеденяет две группы
    private function connectGroup(GroupQuestion | null $prevGroup, GroupQuestion | null $nextGroup): void
    {
        if (!$nextGroup && !$prevGroup) return;
        // Если нет следующей группы
        if (!$nextGroup) {
            // У предыдущей удаляем связь со следующей
            $prevGroup->nextGroup()->dissociate();
            $prevGroup->save();
            return;
        }

        // Если нет предыдущей группы
        if (!$prevGroup) {
            // У следуюзей удаляем связь с предыдущей
            $nextGroup->prevGroup()->dissociate();
            $nextGroup->save();
            return;
        }
        // Если есть обе группы соеденяем их вместе
        $prevGroup->nextGroup()->associate($nextGroup);
        $nextGroup->prevGroup()->associate($prevGroup);
        $prevGroup->save();
        $nextGroup->save();
    }

    // Соединяет два вопроса
    private function connectQuestions(Question | null $prevQuestion, Question | null $nextQuestion): void
    {
        if (!$nextQuestion && !$prevQuestion) return;

        if (!$nextQuestion) {
            $prevQuestion->nextQuestion()->dissociate();
            $prevQuestion->save();
            return;
        }

        if (!$prevQuestion) {
            $nextQuestion->prevQuestion()->dissociate();
            $nextQuestion->save();
            return;
        }

        $prevQuestion->nextQuestion()->associate($nextQuestion);
        $nextQuestion->prevQuestion()->associate($prevQuestion);
        $prevQuestion->save();
        $nextQuestion->save();
    }

    public function getGroupById(int $groupId, string ...$relations)
    {
        return GroupQuestion::query()->with($relations)->find($groupId);
    }

    public function getQuestionById(int $questionId, string ...$relations)
    {
        return Question::query()->with($relations)->find($questionId);
    }

    // Получит первую группы в тесте
    public function getStartGroupByTestId(int $testId, string ...$relations)
    {
        return GroupQuestion::query()
            ->with($relations)
            ->where('test_id', $testId)
            ->where('prev_id', null)
            ->first();
    }

    // Получает первый вопрос в группе
    public function getStartQuestionByGroupId(int $groupId, string ...$relations)
    {
        return Question::query()
            ->with($relations)
            ->where('group_id', $groupId)
            ->where('prev_id', null)
            ->first();
    }

    // Обновление вопроса
    public function updateQuestion(int $questionId, array $questionData)
    {
        $question = $this->getQuestionById($questionId);

        if (!$question) {
            $message = 'Вопрос с id ' . $questionId . ' не найдена';
            return throw new \Exception($message, 404);
        }

        if (array_key_exists('text', $questionData)) {
            $question->text = $questionData['text'];
        }

        $question->save();
        return $question;
    }

    // Обновление позиции вопроса изменение варианта или изменение группы
    public function updateQuestionPosition(int $questionId, int $groupId, int | null $prevQuestionId)
    {
        $question = $this->getQuestionById($questionId, 'prevQuestion', 'nextQuestion');

        if (!$question) {
            $message = 'Вопрос с id ' . $questionId . ' не найдена';
            return throw new \Exception($message, 404);
        }

        // Если нет ни предыдущего ни следующего вопроса то текущая группа остается пустой
        $isEmptyGroup = !$question->prevQuestion && !$question->nextQuestion;
        $prevGroupId = $question->group_id;

        $question = DB::transaction(function () use ($question, $groupId, $prevQuestionId) {
            $prevQuestion = $question->prevQuestion;
            $nextQuestion = $question->nextQuestion;
            $question->group_id = $groupId;
            // Соединяем два вопроса
            $this->connectQuestions($prevQuestion, $nextQuestion);

            // Если нет предыдущего вопроса
            if (!$prevQuestionId) {
                return $this->insertQuestionAtStart($question);
            }

            return $this->insertBetweenQuestions($question, $prevQuestionId);
        });

        if ($isEmptyGroup) {
            // Удаляем пустую группу
            $this->deleteQuestionGroup($prevGroupId);
        }

        return $question;
    }

    public function updateGroupPosition(int $groupId, int | null $prevGroupId): GroupQuestion
    {
        return DB::transaction(function () use ($groupId, $prevGroupId) {
            $group = $this->getGroupById($groupId, 'prevGroup', 'nextGroup');
            // Если группы не найдено выкидываем исключение
            if (!$group) {
                $message = 'Группа с id ' . $group . ' не найдена';
                return throw new \Exception($message, 404);
            }

            $prevGroup = $group->prevGroup;
            $nextGroup = $group->nextGroup;
            // Соеденяем группы между собой
            $this->connectGroup($prevGroup, $nextGroup);
            // Если нет prev_group_id значит текущую группу ставим в начало
            if (!$prevGroupId) {
                return $this->insertGroupAtStart($group);
            }
            // Вставляем между групп или в конец
            return $this->insertBetweenGroups($group, $prevGroupId);
        });
    }

    public function deleteQuestionGroup(int $groupId): void
    {
        DB::transaction(function () use ($groupId) {
            $group = $this->getGroupById($groupId, 'prevGroup', 'nextGroup');
            $prevGroup = $group->prevGroup;
            $nextGroup = $group->nextGroup;
            // Соеденяем группы между собой
            if ($prevGroup || $nextGroup) {
                $this->connectGroup($prevGroup, $nextGroup);
            }
            // Удаляем группу
            $group->delete();
        });
    }

    // Удаление вопроса
    public function deleteQuestion(int $questionId): void
    {
        $question = $this->getQuestionById($questionId, 'nextQuestion', 'prevQuestion');
        $nextQuestion = $question->nextQuestion;
        $prevQuestion = $question->prevQuestion;
        // Если нет ни следующего вопроса ни предыдушего значит группа пустая
        if (!$nextQuestion && !$prevQuestion) {
            // Удаляем группу целиком
            $this->deleteQuestionGroup($question->group_id);
            return;
        }
        // Удаляем вопрос
        DB::transaction(function () use ($question, $nextQuestion, $prevQuestion) {
            $this->connectQuestions($prevQuestion, $nextQuestion);
            $question->delete();
        });
    }

    public function getTestQuestions(int $testId): array
    {
        $questions = GroupQuestion::query()
            ->with('questions')
            ->where('test_id', $testId)
            ->orderBy('prev_id')
            ->get();

        return $this->sortedByRelatedKeys($questions, 'prev_id');
    }
}
