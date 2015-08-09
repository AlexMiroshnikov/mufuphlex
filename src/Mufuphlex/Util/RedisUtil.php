<?php
namespace Mufuphlex\Util;

/**
 * Class RedisUtil
 * @package Mufuphlex\Util
 */
class RedisUtil
{
	/** @var \Redis */
	private $_redis;

	/**
	 * @param array $connectConfig
	 */
	public function __construct(array $connectConfig = array())
	{
		$this->_redis = new \Redis();

		$this->_redis->connect(
			(!empty($connectConfig['host']) ? $connectConfig['host'] : 'localhost'),
			(!empty($connectConfig['port']) ? $connectConfig['port'] : 6379),
			(!empty($connectConfig['timeout']) ? $connectConfig['timeout'] : 0.0)
		);

		$this->_redis->select($connectConfig['db']);

		if (!empty($connectConfig['namespace']))
		{
			$this->_redis->setOption(\Redis::OPT_PREFIX, $connectConfig['namespace']);
		}
	}

/**
	 * @param string $hash
	 * @param string $key
	 * @param mixed $value
	 * @return $this
	 * /
	public function hashSetValue($hash, $key, $value)
	{
		if (!$data = $this->_redis->hGetAll($hash))
		{
			$data = array();
		}
		$data[$key] = $value;
		$this->_redis->hMset($hash, $data);
		return $this;
	}
//*/

	/**
	 * @param string $key
	 * @param mixed $value
	 * @return int
	 */
	public function setAdd($key, $value)
	{
		return $this->_redis->sAdd($key, $value);
	}

	/**
	 * @param string $key
	 * @return array
	 */
	public function setGet($key)
	{
		return $this->_redis->sMembers($key);
	}

	/**
	 * @param string $key
	 * @param array $values
	 * @return int
	 */
	public function setAddMulti($key, array $values)
	{
		array_unshift($values, $key);
		return call_user_func_array(array($this->_redis, 'sAdd'), $values);
	}

	/**
	 * @param array $keys
	 * @return array|null
	 */
	public function setIntersect(array $keys)
	{
		if (!$keys)
		{
			return null;
		}

		if (count($keys) < 2)
		{
			$key = current($keys);
			return $this->_redis->sMembers($key);
		}

		$intersection = call_user_func_array(array($this->_redis, 'sInter'), $keys);
		return $intersection;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @return $this
	 */
	public function setRemoveValue($key, $value)
	{
		$this->_redis->sRem($key, $value);
		return $this;
	}

	/**
	 * @param string $pattern
	 * @return array
	 */
	public function keys($pattern)
	{
		return $this->_redis->keys($pattern);
	}

	/**
	 * @param string $listName
	 * @return array
	 */
	public function listGet($listName)
	{
		return $this->_redis->lGetRange($listName, 0, $this->_redis->lLen($listName)-1);
	}

	/**
	 * @param string $listName
	 * @return $this
	 */
	public function listAdd($listName, $value)
	{
		$this->_redis->lPush($listName, $value);
		return $this;
	}

	/**
	 * @param string $key
	 * @return $this
	 */
	public function del($key)
	{
		$this->_redis->del($key);
		return $this;
	}

	/**
	 * @param void
	 * @return $this
	 */
	public function flushDb()
	{
		$this->_redis->flushDB();
		return $this;
	}
}