<?php

namespace App\Services;

use App\Models\Test;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class TestService
{
    private function formattingData($data): array
    {
        $payload = [];
        if (array_key_exists('title', $data)) {
            $payload['title'] = $data['title'];
        }
        if (array_key_exists('description', $data)) {
            $payload['description'] = $data['description'];
        }
        if (array_key_exists('accountId', $data)) {
            $payload['account_id'] = $data['accountId'];
        }
        return $payload;
    }
    // Создание нового теста
    public function create(int $accountId, $data): Test
    {
        $payload = $this->formattingData($data);
        $test = new Test(['account_id' => $accountId, ...$payload ]);
        $test->save();
        return $test;
    }

    // Обновления теста
    public function update(int $testId, $data): bool
    {
        $payload = $this->formattingData($data);

        $result = Test::query()
            ->where('id',$testId)
            ->limit(1)
            ->update($payload);

        return (bool) $result;
    }

    // Удаление теста
    public function delete(int $testId): bool
    {
        $result = Test::query()->find($testId)->delete();

        return (bool) $result;
    }

    public function getTestById(int $testId): Model | null
    {
        return Test::query()->find($testId);
    }
}
