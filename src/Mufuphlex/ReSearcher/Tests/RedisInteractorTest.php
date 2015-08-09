<?php
class RedisInteractorTest extends PHPUnit_Framework_TestCase
{
	public function testGetRedisUtil()
	{
		$redisInteractor = new \Mufuphlex\ReSearcher\RedisInteractor(array(
			'namespace' => 'Testing:',
			'db' => 2
		));

		$this->assertInstanceOf('\Mufuphlex\Util\RedisUtil', $redisInteractor->getRedisUtil());
	}

	/**
	 * @expectedException \Mufuphlex\ReSearcher\Exception
	 * @expectedExceptionMessage Property "redisUtil" can not be redefined
	 */
	public function testSetRedisUtilThrowsException()
	{
		$redisInteractor = new \Mufuphlex\ReSearcher\RedisInteractor();
		$redisUtil = new \Mufuphlex\Util\RedisUtil(array('db' => 10));
		$redisInteractor->setRedisUtil($redisUtil);
	}

	public function testMakeKeyName()
	{
		$redisInteractor = new \Mufuphlex\ReSearcher\RedisInteractor();

		$name = 'name';

		$this->assertEquals(
			implode('_', array(\Mufuphlex\ReSearcher\RedisInteractor::KEY_PREFIX_DEFAULT, $name)),
			$redisInteractor->makeKeyName($name)
		);

		$prefix = 'customPrefix';

		$this->assertEquals(
			implode('_', array($prefix, $name)),
			$redisInteractor->makeKeyName($name, $prefix)
		);

		$postfix = 'customPostfix';

		$this->assertEquals(
			implode('_', array($prefix, $name, $postfix)),
			$redisInteractor->makeKeyName($name, $prefix, $postfix)
		);
	}
}