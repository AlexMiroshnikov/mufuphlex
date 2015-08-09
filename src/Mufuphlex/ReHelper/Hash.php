<?php
namespace Mufuphlex\ReHelper;

/**
 * Class Hash
 * @package Mufuphlex\RePhull
 */
class Hash extends RedisKey
{
	/**
	 * @param string $key
	 * @param mixed $value
	 * @return $this
	 */
	public function setValueByKey($key, $value)
	{
		$this->_validateString('key', $key);
		$this->_redisUtil->hashSetValue($this->_name, $key, $value);
		return $this;
	}
}