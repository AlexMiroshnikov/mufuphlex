<?php

class NonRedefinablePropertyTraitTest extends PHPUnit_Framework_TestCase
{
	/** @var \Mufuphlex\Tests\NonRedefinablePropertyClass */
	private $_obj = null;

	public function setUp()
	{
		parent::setUp();
		$this->_obj = new \Mufuphlex\Tests\NonRedefinablePropertyClass();
	}

	public function testNotThrowsException()
	{
		$value = rand(1,100);
		$this->_obj->setRedefinable($value);
		$this->assertEquals($value, $this->_obj->getRedefinable());
	}

	/**
	 * @expectedException \Mufuphlex\Exception\NonRedefinablePropertyException
	 * @expectedExceptionMessage Property "_nonRedefinable" can not be redefined
	 */
	public function testThrowsException()
	{
		$this->assertNull($this->_obj->getNonRedefinable());
		$value = rand(1,100);
		$this->_obj->setNonRedefinable($value);
		$this->assertEquals($value, $this->_obj->getNonRedefinable());
		$value = rand(101,999);
		$this->_obj->setNonRedefinable($value);
	}
}