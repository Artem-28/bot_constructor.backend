<?php

namespace App\Services;

class BaseService
{
    // Преобразование параметров в массив
    protected function convertParamToArray($param): array
    {
        if (is_array($param)) {
            return $param;
        }
        return array($param);
    }
}
