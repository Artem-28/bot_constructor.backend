<?php

namespace App\Serializer;

use League\Fractal\Serializer\ArraySerializer;

class CollectArraySerializer extends ArraySerializer
{
    public function collection(?string $resourceKey, array $data): array
    {
        return $data;
    }
}
