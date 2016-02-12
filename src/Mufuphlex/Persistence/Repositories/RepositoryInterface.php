<?php

namespace Mufuphlex\Persistence\Repositories;
use Mufuphlex\Persistence\Models\ModelPersistentInterface;

/**
 * Interface RepositoryInterface
 * @package Mufuphlex\Persistence\Repositories
 */
interface RepositoryInterface
{
    /**
     * @param ModelPersistentInterface $model
     * @return mixed
     */
    public function save(ModelPersistentInterface $model);
}