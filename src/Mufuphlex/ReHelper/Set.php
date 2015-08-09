<?php
namespace Mufuphlex\ReHelper;

/**
 * Class Set
 * @package Mufuphlex\ReHelper
 */
class Set extends RedisKey
{
	/**
	 * @param $value
	 * @return $this
	 */
	public function add($value)
	{
		$this->_redisUtil->setAdd($this->_name, $value);
		return $this;
	}

	/**
	 * @return array
	 */
	public function get()
	{
		return $this->_redisUtil->setGet($this->_name);
	}
}