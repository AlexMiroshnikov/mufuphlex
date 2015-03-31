<?php
namespace Mufuphlex\Tests;

class NonRedefinablePropertyClass
{
	use \Mufuphlex\Traits\NonRedefinablePropertyTrait;

	protected $_redefinable = null;
	protected $_nonRedefinable = null;

	public function setRedefinable($value)
	{
		$this->_checkNonRedefinable('_redefinable');
		$this->_redefinable = $value;
	}

	public function getRedefinable()
	{
		return $this->_redefinable;
	}

	public function setNonRedefinable($value)
	{
		$this->_checkNonRedefinable('_nonRedefinable');
		$this->_nonRedefinable = $value;
		return $this;
	}

	public function getNonRedefinable()
	{
		return $this->_nonRedefinable;
	}
}