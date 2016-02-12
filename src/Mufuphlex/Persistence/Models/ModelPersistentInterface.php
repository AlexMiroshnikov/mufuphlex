<?php

namespace Mufuphlex\Persistence\Models;

/**
 * Interface ModelPersistentInterface
 * @package Mufuphlex\Persistence\Models
 */
interface ModelPersistentInterface
{
    /**
     * @return mixed
     */
    public function save();
}