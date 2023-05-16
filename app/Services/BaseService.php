<?php

namespace App\Services;

class BaseService
{

    private array $updated;
    private array $created;

    public function __construct()
    {
        $this->updated = [];
        $this->created = [];
    }

    protected function setUpdateResource(...$resource): void
    {
        array_push($this->updated, ...$resource);
    }

    protected function setCreateResource(...$resource): void
    {
        array_push($this->created, ...$resource);
    }

    public function getResource(): array
    {
        return [
            'created' => $this->created,
            'updated' => $this->updated,
        ];
    }
    // Преобразование параметров в массив
    protected function convertParamToArray($param): array
    {
        if (is_array($param)) {
            return $param;
        }
        return array($param);
    }
}
