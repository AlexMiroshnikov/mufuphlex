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
}