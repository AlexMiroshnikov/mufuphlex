<?php
namespace Mufuphlex\ReSearcher;

/**
 * Class SearcherResult
 * @package Mufuphlex\ReSearcher
 */
class SearcherResult
{
	/** @var array */
	protected $_tokens = array();

	/** @var float */
	protected $_score = 1.0;

	/** @var \Object */
	protected $_object = null;

	/** @var bool */
	protected $_exactMatch = false;

	/** @var mixed */
	protected $_id = null;

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->_id;
	}

	/**
	 * @param mixed $id
	 * @return $this
	 */
	public function setId($id)
	{
		$this->_id = $id;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getTokens()
	{
		return $this->_tokens;
	}

	/**
	 * @param array $tokens
	 * @return $this
	 */
	public function setTokens(array $tokens)
	{
		$this->_tokens = $tokens;
		return $this;
	}

	/**
	 * @return float
	 */
	public function getScore()
	{
		return $this->_score;
	}

	/**
	 * @param float $score
	 * @return $this
	 */
	public function setScore($score)
	{
		$this->_score = $score;
		return $this;
	}

	/**
	 * @return Object
	 */
	public function getObject()
	{
		return $this->_object;
	}

	/**
	 * @param Object $object
	 * @return $this
	 */
	public function setObject($object)
	{
		$this->_object = $object;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function hasExactMatch()
	{
		return $this->_exactMatch;
	}

	/**
	 * @param boolean $exactMatch
	 * @return $this
	 */
	public function setExactMatch($exactMatch)
	{
		$this->_exactMatch = $exactMatch;
		return $this;
	}
}