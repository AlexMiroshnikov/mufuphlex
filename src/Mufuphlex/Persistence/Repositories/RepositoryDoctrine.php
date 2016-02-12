<?php

namespace Mufuphlex\Persistence\Repositories;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Setup;
use Mufuphlex\Persistence\Models\ModelPersistentDoctrineInterface;
use Mufuphlex\Persistence\Models\ModelPersistentInterface;

/**
 * Class RepositoryDoctrine
 * @package Mufuphlex\Persistence\Repositories
 */
class RepositoryDoctrine implements RepositoryInterface
{
    /** @var  EntityManagerInterface */
    protected $entityManager;

    const OPTION_SETUP_PATHS = 'opt_setup_paths';
    const OPTION_CONNECTION_CONFIG = 'opt_conn_conf';

    /**
     * @param array $options
     * @return RepositoryDoctrine
     * @throws \Doctrine\ORM\ORMException
     */
    public static function factory(array $options = array())
    {
        $self = new self;
        $self->init($options);
        return $self;
    }

    /**
     * @param ModelPersistentInterface $model
     * @return mixed
     */
    public function save(ModelPersistentInterface $model)
    {
        if (!($model instanceof ModelPersistentDoctrineInterface)) {
            throw new \InvalidArgumentException('$model must implement ModelPersistentDoctrineInterface');
        }

        $entityManager = $this->entityManager();
        $entityManager->persist($model);
        $entityManager->flush();
    }


    /**
     * @param EntityManagerInterface $entityManager
     * @return $this
     * @throws \LogicException
     */
    public function setEntityManager(EntityManagerInterface $entityManager)
    {
        if ($this->entityManager !== null) {
            throw new \LogicException('entityManager can not be reassigned');
        }

        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * @return EntityManagerInterface
     */
    protected function entityManager()
    {
        if ($this->entityManager === null) {
            throw new \LogicException('entityManager is not set yet');
        }

        return $this->entityManager;
    }

    /**
     * @param array $options
     * @throws \Doctrine\ORM\ORMException
     */
    protected function init(array $options = array())
    {
        if (isset($options[static::OPTION_SETUP_PATHS]) &&
            isset($options[static::OPTION_CONNECTION_CONFIG])
        ) {
            $isDevMode = true;
            $annotationConfig = Setup::createAnnotationMetadataConfiguration($options[static::OPTION_SETUP_PATHS], $isDevMode);
            $entityManager = EntityManager::create($options[static::OPTION_CONNECTION_CONFIG], $annotationConfig);
            $this->setEntityManager($entityManager);
        }
    }
}