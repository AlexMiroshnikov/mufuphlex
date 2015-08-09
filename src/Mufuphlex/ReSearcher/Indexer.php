<?php
namespace Mufuphlex\ReSearcher;

/**
 * Class Indexer
 * @package Mufuphlex\ReSearcher
 */
class Indexer extends Interactor
{
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
			$dataBySet[$this->_redisInteractor->makeKeyName($token, $this->_redisInteractor->getPrefixToken(), $type)][] = $id;
		}

		foreach ($dataBySet as $key => $values)
		{
			$cnt += $this->_redisInteractor->getRedisUtil()->setAddMulti($key, $values);
			unset($dataBySet[$key]);
		}

		return $cnt;
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
		$keyName = $this->_redisInteractor->makeKeyName($entry->getId(), $this->_redisInteractor->getPrefixEntry(), $entry->getType());

		if ($entry->isMutable())
		{
			$this->_removeEntryFromTokenSets($entry);
			$this->_redisInteractor->getRedisUtil()->del($keyName);
		}

		return $this->_redisInteractor->getRedisUtil()->setAddMulti($keyName, $entry->getTokens());
	}

	/**
	 * @param InteractableInterface $entry
	 * @return $this
	 */
	protected function _removeEntryFromTokenSets(InteractableInterface $entry)
	{
		$type = $entry->getType();
		$id = $entry->getId();
		$keyName = $this->_redisInteractor->makeKeyName($id, $this->_redisInteractor->getPrefixEntry(), $type);
		$currentTokens = $this->_redisInteractor->getRedisUtil()->setGet($keyName);

		foreach ($currentTokens as $token)
		{
			$tokenKeyName = $this->_redisInteractor->makeKeyName($token, $this->_redisInteractor->getPrefixToken(), $type);
			$this->_redisInteractor->getRedisUtil()->setRemoveValue($tokenKeyName, $id);
		}

		return $this;
	}
}