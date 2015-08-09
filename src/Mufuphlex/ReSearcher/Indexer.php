<?php
namespace Mufuphlex\ReSearcher;

/**
 * Class Indexer
 * @package Mufuphlex\ReSearcher
 */
class Indexer extends Interactor
{
	/** @var callable */
	protected $_filter = null;

	/**
	 * @param InteractableInterface $obj
	 * @return int
	 */
	public function addObject(InteractableInterface $obj)
	{
		$cnt = 0;

		if (!$obj->getTokens())
		{
			return $cnt;
		}

		$cnt += $this->_addEntry($obj);

		$dataBySet = array();
		$id = $obj->getId();
		$type = $obj->getType();

		foreach ($obj->getTokens() as $token)
		{
			if ($this->_filter AND !call_user_func_array($this->_filter, array($token)))
			{
				continue;
			}

			$dataBySet[$this->_makeKeyNameToken($token, $type)][] = $id;
		}

		foreach ($dataBySet as $key => $values)
		{
			$cnt += $this->_addMulti($key, $values);
			unset($dataBySet[$key]);
		}

		return $cnt;
	}

	/**
	 * @param callable $filter
	 * @return $this
	 */
	public function setFilter(\Closure $filter)
	{
		$this->_filter = $filter;
		return $this;
	}

	/**
	 * @param InteractableInterface $entry
	 * @return int
	 */
	protected function _addEntry(InteractableInterface $entry)
	{
		$cnt = $this->_addKnownType($entry->getType());

		if ($entry->isMutable())
		{
			$cnt += $this->_fillTokensOfEntry($entry);
		}

		return $cnt;
	}

	/**
	 * @param mixed $type
	 * @return int
	 */
	protected function _addKnownType($type)
	{
		return $this->_redisInteractor->getRedisUtil()->setAdd($this->_redisInteractor->getSetNameKnownTypes(), $type);
	}

	/**
	 * @param InteractableInterface $entry
	 * @return int
	 */
	protected function _fillTokensOfEntry(InteractableInterface $entry)
	{
		$keyName = $this->_makeKeyNameEntry($entry);

		if ($entry->isMutable())
		{
			$this->_removeEntryFromTokenSets($entry);
			$this->_redisInteractor->getRedisUtil()->del($keyName);
		}

		return $this->_addMulti($keyName, $entry->getTokens());
	}

	/**
	 * @param InteractableInterface $entry
	 * @return $this
	 */
	protected function _removeEntryFromTokenSets(InteractableInterface $entry)
	{
		$type = $entry->getType();
		$id = $entry->getId();
		$keyName = $this->_makeKeyNameEntry($entry);
		$currentTokens = $this->_redisInteractor->getRedisUtil()->setGet($keyName);

		foreach ($currentTokens as $token)
		{
			$tokenKeyName = $this->_makeKeyNameToken($token, $type);
			$this->_redisInteractor->getRedisUtil()->setRemoveValue($tokenKeyName, $id);
		}

		return $this;
	}
}