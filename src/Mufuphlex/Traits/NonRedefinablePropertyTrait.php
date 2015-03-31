<?php
namespace Mufuphlex\Traits;

trait NonRedefinablePropertyTrait
{
	/**
	 * @param string $property
	 * @param string $exceptionName
	 * @throws \Exception
	 */
	protected function _checkNonRedefinable($property, $exceptionName = '\\Mufuphlex\\Exception\\NonRedefinablePropertyException')
	{
		if ($this->$property !== null)
		{
			throw new $exceptionName('Property "' . $property . '" can not be redefined');
		}
	}
}