<?php
namespace Mufuphlex\ReSearcher;

/**
 * Class InteractableObject
 * @package Mufuphlex\ReSearcher
 */
abstract class InteractableObject implements InteractableInterface
{
	/** @var string */
	protected $_id = null;

	/** @var array */
	protected $_tokens = array();

	/** @var string */
	protected $_type = self::TYPE_DEFAULT;

	/** @var bool	Tells if the indexed item can be edit in the future */
	protected $_mutable = true;

	const TYPE_DEFAULT = 'dflt';

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->_id;
	}

	/**
	 * @param mixed $id
	 */
	public function setId($id)
	{
		if ($this->_id !== null)
		{
			throw new \LogicException('Can not redefine id');
		}

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
	 * @return array
	 */
	public function getTokensUnique()
	{
		return array_unique($this->_tokens);
	}

	/**
	 * @param array $tokens
	 */
	public function setTokens($tokens)
	{
		$this->_tokens = $tokens;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getType()
	{
		return $this->_type;
	}

	/**
	 * @param string $type
	 * @return $this
	 */
	public function setType($type)
	{
		if ($this->_type !== self::TYPE_DEFAULT)
		{
			throw new \LogicException('Can not redefine type');
		}

		$this->_type = (string)$type;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function isMutable()
	{
		return $this->_mutable;
	}
}