<?php

use Mufuphlex\Persistence\Repositories\RepositoryDoctrine;

class RepositoryDoctrineTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        parent::tearDown();
        Mockery::close();
    }

    public function testFactorySuccess()
    {
        $repo = static::getRepo();
        $this->assertInstanceOf('Mufuphlex\Persistence\Repositories\RepositoryDoctrine', $repo);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage entityManager can not be reassigned
     */
    public function testSetEntityManagerFailsOnRedefine()
    {
        $repo = static::getRepo();
        /** @var $entityManagerMock Doctrine\ORM\EntityManagerInterface */
        $entityManagerMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');
        $repo->setEntityManager($entityManagerMock);
    }

    public function testSaveSuccess()
    {
        $model = new \Mufuphlex\Persistence\Models\DummyModel();
        $repo = static::getRepoWithMock($model);
        $repo->save($model);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testSaveFailsOnNotInterfacePassed()
    {
        $model = new stdClass();
        /** @var $entityManagerMock Doctrine\ORM\EntityManagerInterface */
        $entityManagerMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');
        $entityManagerMock->shouldReceive('persist')->never();
        $entityManagerMock->shouldReceive('flush')->never();
        $repo = new RepositoryDoctrine();
        $repo->setEntityManager($entityManagerMock);
        $repo->save($model);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $model must implement ModelPersistentDoctrineInterface
     */
    public function testSaveFailsOnNotDoctrineInterfacePassed()
    {
        $model = new \Mufuphlex\Persistence\Models\DummyPersistentModel();
        /** @var $entityManagerMock Doctrine\ORM\EntityManagerInterface */
        $entityManagerMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');
        $entityManagerMock->shouldReceive('persist')->never();
        $entityManagerMock->shouldReceive('flush')->never();
        $repo = new RepositoryDoctrine();
        $repo->setEntityManager($entityManagerMock);
        $repo->save($model);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage entityManager is not set yet
     */
    public function testSaveFailsOnNotSetEntityManager()
    {
        $model = new \Mufuphlex\Persistence\Models\DummyModel();
        $repo = new RepositoryDoctrine();
        $repo->save($model);
    }

    /**
     * @return RepositoryDoctrine
     */
    private static function getRepo()
    {
        $options = array(
            RepositoryDoctrine::OPTION_SETUP_PATHS => array(),
            RepositoryDoctrine::OPTION_CONNECTION_CONFIG => array(
                'driver' => 'pdo_sqlite',
                'path' => __DIR__ . '/db.sqlite'
            ),
        );
        return RepositoryDoctrine::factory($options);
    }

    /**
     * @param $model
     * @return RepositoryDoctrine
     */
    private static function getRepoWithMock($model)
    {
        $entityManagerMock = static::getEntityManagerMock($model);
        $repo = new RepositoryDoctrine();
        $repo->setEntityManager($entityManagerMock);
        return $repo;
    }

    /**
     * @param $model
     * @return Doctrine\ORM\EntityManagerInterface
     */
    private static function getEntityManagerMock($model)
    {
        $entityManagerMock = Mockery::mock('Doctrine\ORM\EntityManagerInterface');
        $entityManagerMock->shouldReceive('persist')->withArgs(array($model))->once();
        $entityManagerMock->shouldReceive('flush')->withNoArgs()->once();
        return $entityManagerMock;
    }
}