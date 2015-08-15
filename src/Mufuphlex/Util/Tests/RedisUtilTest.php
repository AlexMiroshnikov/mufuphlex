<?php
class RedisUtilTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var \Mufuphlex\Util\RedisUtil
	 */
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

	public function testSetAddAndGet()
	{
		$key = __FUNCTION__;
		$value = 'testValue';

		$this->_redisUtil->setAdd($key, $value);
		$this->assertEquals(array($value), $this->_redisUtil->setGet($key));
	}

	public function testSetAddMultiAndGet()
	{
		$key = __FUNCTION__;
		$values = array(
			'testValue 1',
			'testValue 2',
		);

		$this->_redisUtil->setAddMulti($key, $values);
		$this->assertEquals($values, $this->_redisUtil->setGet($key));
	}

	public function testSetIntersect()
	{
		$sets = array(
			array(
				'key' => 'intersect1',
				'values' => array(
					1,2,3
				)
			),
			array(
				'key' => 'intersect2',
				'values' => array(
					2,3,4
				)
			)
		);

		foreach ($sets as $set)
		{
			$this->_redisUtil->setAddMulti($set['key'], $set['values']);
		}

		foreach ($sets as $set)
		{
			$this->assertEquals($set['values'], $this->_redisUtil->setIntersect(array($set['key'])));
		}

		$this->assertEquals(array(2,3), $this->_redisUtil->setIntersect(array(
			$sets[0]['key'],
			$sets[1]['key']
		)));
	}

	public function testSetRemoveValue()
	{
		$key = __FUNCTION__;
		$valueExisting = 'testValue 1';
		$valueToRemove = 'testValue 2';
		$values = array(
			$valueExisting,
			$valueToRemove
		);
		$this->_redisUtil->setAddMulti($key, $values);
		$this->_redisUtil->setRemoveValue($key, $valueToRemove);
		$this->assertEquals(array($valueExisting), $this->_redisUtil->setGet($key));
	}

	public function testListAddAndGet()
	{
		$key = __FUNCTION__;
		$values = array();
		$value = 'value 1';
		array_unshift($values, $value);

		$this->_redisUtil->listAdd($key, $value);
		$this->assertEquals($values, $this->_redisUtil->listGet($key));

		$value = 'value 2';
		array_unshift($values, $value);

		$this->_redisUtil->listAdd($key, $value);
		$this->assertEquals($values, $this->_redisUtil->listGet($key));
	}

	public function testDel()
	{
		$key = __FUNCTION__;
		$value = 'value';
		$this->_redisUtil->setAdd($key, $value);
		$this->assertEquals(array($value), $this->_redisUtil->setGet($key));
		$this->_redisUtil->del($key);
		$this->assertEquals(array(), $this->_redisUtil->setGet($key));
	}

	public function testHashSetAndGet()
	{
		$key = __FUNCTION__;
		$data = array(
			'key 1' => 'val 1',
			'key 2' => 'val 2'
		);
		$this->_redisUtil->hashSet($key, $data);
		$this->assertEquals($data, $this->_redisUtil->hashGet($key));
	}
}