<?php
namespace Mufuphlex\ReHelper;

/**
 * Class RedisKey
 * @package Mufuphlex\ReHelper
 */
abstract class RedisKey
{
	/** @var string */
	protected $_name = '';

	/** @var \Mufuphlex\Util\RedisUtil */
	protected $_redisUtil = null;

	final public function __construct($name, \Mufuphlex\Util\RedisUtil $redisUtil)
	{
		$this->_validateString('name', $name);
		$this->_name = $name;
		$this->_redisUtil = $redisUtil;
	}

	/**
	 * @param string $name
	 * @param string $val
	 */
	protected function _validateString($name, $val)
	{
		if (!is_string($val))
		{
			throw new \InvalidArgumentException('$'.$name.' must be a string, '.gettype($val).' given');
		}
	}
}