<?php
namespace Mufuphlex\ReSearcher;

/**
 * Class SearcherResultSettings
 * @package Mufuphlex\ReSearcher
 */
class SearcherResultSettings
{
	/** @var string */
	protected $_type = self::DEFAULT_TYPE;

	/** @var int */
	protected $_weight = 1;

	/** @var string */
	//protected $_resultClass = '\Mufuphlex\RePhull\IndexableObject';
	protected $_resultClass = '';

	/** @var bool */
	protected $_sortByProximity = false;

	/** @var int */
	protected $_limit = self::DEFAULT_RESULTS_LIMIT;

	/** @var mixed */
	protected $_postprocessor = null;

	const DEFAULT_TYPE = 'dflt';
	const DEFAULT_RESULTS_LIMIT = 100;

	/**
	 * @param array $options
	 * <dl>
	 * <dt>type</dt>			<dd>Type of object to search</dd>
	 * <dt>weight</dt>			<dd>Weight of search results</dd>
	 * <dt>resultClass</dt>		<dd>Class describing end-result item</dd>
	 * <dt>sortByProximity</dt>	<dd>Should the results be sorted or not</dd>
	 * <dt>limit</dt>			<dd>How many results maximum should be returned</dd>
	 * </dl>
	 */
	public function __construct(array $options = array())
	{
		if (!empty($options['type']))
		{
			$this->_type = (string)$options['type'];
		}

		if (!empty($options['weight']))
		{
			$this->_weight = (int)$options['weight'];
		}

		if (!empty($options['resultClass']))
		{
			$this->_resultClass = (string)$options['resultClass'];
		}

		if (!empty($options['sortByProximity']))
		{
			$this->_sortByProximity = (bool)$options['sortByProximity'];
		}

		if (!empty($options['limit']))
		{
			$this->_limit = (int)$options['limit'];
		}

		if (!empty($options['postprocessor']))
		{
			$this->_postprocessor = $options['postprocessor'];
		}
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->_type;
	}

	/**
	 * @param $type
	 * @return $this
	 */
	public function setType($type)
	{
		if ($this->_type !== self::DEFAULT_TYPE)
		{
			throw new \LogicException('Property "type" can not be redefined');
		}

		$this->_type = $type;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getWeight()
	{
		return $this->_weight;
	}

	/**
	 * @return string
	 */
	public function getResultClass()
	{
		return $this->_resultClass;
	}

	/**
	 * @return boolean
	 */
	public function needsSortByProximity()
	{
		return $this->_sortByProximity;
	}

	/**
	 * @return int
	 */
	public function getLimit()
	{
		return $this->_limit;
	}
}