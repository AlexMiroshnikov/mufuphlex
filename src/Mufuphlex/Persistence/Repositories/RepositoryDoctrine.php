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

    /** @var string */
    protected $modelClassName = '';

    const OPTION_SETUP_PATHS = 'opt_setup_paths';
    const OPTION_CONNECTION_CONFIG = 'opt_conn_conf';
    const OPTION_MODEL_CLASS_NAME = 'opt_class_name';

    /**
     * @param array $options
     * @return RepositoryDoctrine
     * @throws \Doctrine\ORM\ORMException
     */
    public static function factory(array $options)
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
        $this->validateModelInterface($model);

        $entityManager = $this->entityManager();
        $entityManager->persist($model);
        $entityManager->flush();
    }

    /**
     * @param $id
     * @return ModelPersistentInterface|null
     */
    public function findById($id)
    {
        return $this->entityManager()->find($this->getModelClassName(), $id);
    }

    /**
     * @return string
     */
    public function getModelClassName()
    {
        if ($this->modelClassName === '') {
            throw new \LogicException('modelClassName is not set yet');
        }

        return $this->modelClassName;
    }

    /**
     * @param string $className
     * @return $this
     */
    public function setModelClassName($className)
    {
        if ($this->modelClassName !== '') {
            throw new \LogicException('modelClassName can not be reassigned');
        }

        if (!is_string($className)) {
            throw new \InvalidArgumentException('$className must be a string, '.gettype($className).' given');
        }

        if (!class_exists($className)) {
            throw new \InvalidArgumentException('Class "'.$className.'" does not exist');
        }

        $this->modelClassName = $className;
        return $this;
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
    protected function init(array $options)
    {
        $this->initModelClassName($options);
        $this->initEntityManager($options);
    }

    /**
     * @param array $options
     * @return void
     */
    protected function initModelClassName(array $options)
    {
        if (!isset($options[static::OPTION_MODEL_CLASS_NAME])) {
            throw new \InvalidArgumentException('Option OPTION_MODEL_CLASS_NAME must be specified');
        }

        $this->setModelClassName($options[static::OPTION_MODEL_CLASS_NAME]);
    }

    /**
     * @param ModelPersistentInterface $model
     * @throws \InvalidArgumentException
     */
    protected function validateModelInterface(ModelPersistentInterface $model)
    {
        if (!($model instanceof ModelPersistentDoctrineInterface)) {
            throw new \InvalidArgumentException('$model must implement ModelPersistentDoctrineInterface');
        }
    }

    /**
     * @param array $options
     * @throws \Doctrine\ORM\ORMException
     */
    protected function initEntityManager(array $options)
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