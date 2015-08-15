<?php
namespace Mufuphlex\ReSearcher\InteractableObject;

/**
 * Class Dummy
 * @package Mufuphlex\ReSearcher\InteractableObject
 */
class Dummy extends \Mufuphlex\ReSearcher\InteractableObject
{
	protected $_id = 'dummy id';
	protected $_type = 'dummy type';
	protected $_tokens = array('dummy', 'token');
	protected $_mutable = false;

	public function __construct(array $options = array())
	{
		if (!empty($options['id']))
		{
			$this->_id = (string)$options['id'];
		}

		if (!empty($options['mutable']))
		{
			$this->_mutable = (bool)$options['mutable'];
		}
	}

	/**
	 * @param $source
	 * @return $this
	 */
	public static function create($source)
	{
		return new self();
	}

	/**
	 * @param array $resultsIds
	 * @return mixed
	 */
	public static function createResults(array $resultsIds)
	{
		$results = array();

		foreach ($resultsIds as $id)
		{
			$obj = new \stdClass();
			$obj->result = 'dummy result '.$id;
			$obj->id = (int)$id;
			$results[] = $obj;
		}

		return $results;
	}
}