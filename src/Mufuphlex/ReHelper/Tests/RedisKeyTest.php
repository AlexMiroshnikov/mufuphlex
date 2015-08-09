<?php
class RedisKeyTest extends PHPUnit_Framework_TestCase
{
	/** @var \Mufuphlex\Util\RedisUtil  */
	private $_redisUtil = null;

	public function setUp()
	{
		$this->_redisUtil = new \Mufuphlex\Util\RedisUtil(array('db' => 2));
		$this->_redisUtil->flushDb();
	}

	public function tearDown()
	{
		$this->_redisUtil->flushDb();
	}

	public function testRedisKey()
	{
		$set = new \Mufuphlex\ReHelper\Set('nameSet', $this->_redisUtil);
		$value = 'value';
		$set->add($value);
		$this->assertEquals(array($value), $set->get());

		//$hash = new \Mufuphlex\ReHelper\Hash('nameHash', $this->_redisUtil);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testThrowsException()
	{
		$name = array();
		$set = new \Mufuphlex\ReHelper\Set($name, $this->_redisUtil);
	}
}