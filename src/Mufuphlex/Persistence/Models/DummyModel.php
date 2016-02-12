<?php

namespace Mufuphlex\Persistence\Models;

/**
 * Class DummyModel
 * @package Mufuphlex\Persistence\Models
 */
class DummyModel implements ModelPersistentInterface, ModelPersistentDoctrineInterface
{
    use Traits\ModelDoctrineTrait;

    /**
     * @return mixed
     * @throws \Exception
     * @codeCoverageIgnore
     */
    public function save()
    {
        throw new \Exception('Redefine it');
    }
}