<?php

namespace Mufuphlex\Persistence\Models;

/**
 * Class DummyPersistentModel
 * @package Mufuphlex\Persistence\Models
 */
class DummyPersistentModel implements ModelPersistentInterface
{
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