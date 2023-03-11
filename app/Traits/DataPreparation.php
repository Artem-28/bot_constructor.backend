<?php
namespace App\Traits;

use App\Serializer\CollectArraySerializer;
use League\Fractal\Manager;


trait DataPreparation
{
    protected function createData($resource): ?array
    {
        $manager = new Manager();
        $manager->setSerializer(new CollectArraySerializer());
        return $manager->createData($resource)->toArray();
    }
}
