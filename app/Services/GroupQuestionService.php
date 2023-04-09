<?php

namespace App\Services;

use App\Models\GroupQuestion;
use Illuminate\Support\Facades\DB;

class GroupQuestionService
{
    public function create(int $testId, int | null $prevGroupId = null)
    {
        return DB::transaction(function () use ($testId, $prevGroupId) {

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
        });
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

    // Соеденяет две группы
    private function connectGroup(GroupQuestion | null $prevGroup, GroupQuestion | null $nextGroup): void
    {
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

    public function getGroupById(int $groupId, ...$relations)
    {
        return GroupQuestion::query()->with($relations)->find($groupId);
    }

    // Получит первую группы в тесте
    public function getStartGroupByTestId(int $testId, ...$relations)
    {
        return GroupQuestion::query()
            ->with($relations)
            ->where('test_id', $testId)
            ->where('prev_id', null)
            ->first();
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
            $this->connectGroup($prevGroup, $nextGroup);
            // Удаляем группу
            $group->delete();
        });
    }
}
