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

	/**
	 * @param InteractableInterface $entry
	 * @param string $type
	 * @return string
	 */
	protected function _makeKeyNameEntry(InteractableInterface $entry)
	{
		return $this->_redisInteractor->makeKeyName($entry->getId(), $this->_redisInteractor->getPrefixEntry(), $entry->getType());
	}

	/**
	 * setAddMulti() wrapper
	 * @param string $keyName
	 * @param array $values
	 * @return int
	 */
	protected function _addMulti($keyName, $values)
	{
		return $this->_redisInteractor->getRedisUtil()->setAddMulti($keyName, $values);
	}
}