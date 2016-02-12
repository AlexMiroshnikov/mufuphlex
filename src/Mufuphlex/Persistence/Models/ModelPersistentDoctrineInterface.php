<?php

namespace Mufuphlex\Persistence\Models;

/**
 * Interface ModelPersistentDoctrineInterface
 * @package Mufuphlex\Persistence\Models
 */
interface ModelPersistentDoctrineInterface
{
    /**
     * @return mixed
     */
    public function getDORMEntityDefinition();

    /**
     * @return mixed
     */
    public function getDORMEntityProperties();
}