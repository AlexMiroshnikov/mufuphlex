<?php
namespace Mufuphlex\ReSearcher;

/**
 * Class Interactor
 * @package Mufuphlex\ReSearcher
 */
abstract class Interactor
{
	/** @var RedisInteractor */
	protected $_redisInteractor = null;

	/**
	 * @param RedisInteractor $redisInteractor
	 */
	public function __construct(RedisInteractor $redisInteractor)
	{
		$this->_redisInteractor = $redisInteractor;
	}

	/**
	 * @param string $token
	 * @param string $type
	 * @return string
	 */
	protected function _makeKeyNameToken($token, $type)
	{
		return $this->_redisInteractor->makeKeyName($token, $this->_redisInteractor->getPrefixToken(), $type);
	}
}