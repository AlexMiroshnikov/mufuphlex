<?php
namespace Mufuphlex\ReSearcher;

/**
 * Class Searcher
 * @package Mufuphlex\ReSearcher
 */
class Searcher extends Interactor
{
	const PROXIMITY_WEIGHT = 1;

	/** @var TokenizerInterface  */
	protected $_tokenizer = null;

	/** @var array */
	protected $_tokens = array();

	/** @var int */
	protected $_tokensCount = 0;

	/** @var array */
	protected $_result = array();

	/** @var int */
	protected $_count = 0;

	/**
	 * @param RedisInteractor $redisInteractor
	 * @param TokenizerInterface $tokenizer
	 */
	public function __construct(RedisInteractor $redisInteractor, TokenizerInterface $tokenizer = null)
	{
		$this->_redisInteractor = $redisInteractor;
		$this->_tokenizer = ($tokenizer ? $tokenizer : new TokenizerDefault());
	}

	/**
	 * @param $str
	 * @param array $searcherResultSettings
	 * @return SearcherResult|null
	 */
	public function search($str, array $searcherResultSettings = array())
	{
		$this->_reset();

		if (!$this->_tokens = $this->_tokenizer->tokenize($str))
		{
			return null;
		}

		$this->_tokensCount = count($this->_tokens);

		$searcherResultSettings = $this->_prepareSearcherResultSettings($searcherResultSettings);
		$this->_result = $this->_search($this->_tokens, $searcherResultSettings);
		$typedResults = array();

		foreach ($this->_result as $type => $results)
		{
			$this->_count += count($results);
			$typedResults[$type] = $this->_createTypedResults($searcherResultSettings[$type], $results);

			if ($searcherResultSettings[$type]->needsSortByProximity())
			{
				usort($typedResults[$type], function($a, $b){
					if ($a->getScore() > $b->getScore()) return 1;
					if ($a->getScore() < $b->getScore()) return -1;
					return 0;
				});
			}
		}

		return $typedResults;
	}

	/**
	 * @return int
	 * @throws Exception
	 */
	public function getResultCount()
	{
		if (!$this->_result)
		{
			throw new Exception('Can not provide count because the search was not executed yet');
		}

		return $this->_count;
	}

	/**
	 * @param string $type
	 * @return int
	 * @throws Exception
	 */
	public function getResultCountByType($type)
	{
		if (!$this->_result)
		{
			throw new Exception('Can not provide count because the search was not executed yet');
		}

		return (isset($this->_result[$type]) ? count($this->_result[$type]) : 0);
	}

	/**
	 * @param array $searcherResultSettings
	 * @return array
	 */
	protected function _prepareSearcherResultSettings(array $searcherResultSettings = array())
	{
		if (!$searcherResultSettings)
		{
			foreach ($knownTypes =$this->_redisInteractor->getKnownTypes() as $type)
			{
				$searcherResultSettings[] = new SearcherResultSettings(array('type' => $type));
			}
		}

		foreach ($searcherResultSettings as $key => $setting)
		{
			if (!($setting instanceof SearcherResultSettings))
			{
				throw new \InvalidArgumentException('$setting must be instance of SearchResultSettings, '.gettype($setting).' given');
			}

			$searcherResultSettings[$setting->getType()] = $setting;
			unset($searcherResultSettings[$key]);
		}

		return $searcherResultSettings;
	}

	/**
	 * @param void
	 * @return void
	 */
	protected function _reset()
	{
		$this->_result = array();
		$this->_count = 0;
		$this->_tokens = array();
		$this->_tokensCount = 0;
	}

	/**
	 * @param array $tokens
	 * @param array $searchResultSettings
	 * @return array [string $type] => array($id)
	 */
	protected function _search(array $tokens, array $searchResultSettings)
	{
		/** @var \Mufuphlex\ReSearcher\SearcherResultSettings $setting */
		$setting = null;
		$result = array();
		$tokens = array_unique($tokens);

		foreach ($searchResultSettings as $setting)
		{
			$type = $setting->getType();
			$keys = array();

			foreach ($tokens as $token)
			{
				$keys[] = $this->_makeKeyNameToken($token, $type);
			}

			$keys = array_unique($keys);

			if ($intersection = $this->_redisInteractor->getRedisUtil()->setIntersect($keys))
			{
				$result[$type] = $intersection;
			}
		}

		return $result;
	}

	/**
	 * @param SearcherResultSettings $searcherResultSettings
	 * @param array $results
	 * @return array
	 */
	protected function _createTypedResults(SearcherResultSettings $searcherResultSettings, array $results)
	{
		$typedResults = array();

		$objects = call_user_func_array(
			array($searcherResultSettings->getResultClass(), 'createResults'),
			array($results)
		);

		$type = $searcherResultSettings->getType();
		$needSort = $searcherResultSettings->needsSortByProximity();

		foreach ($objects as $object)
		{
			$typedResults[] = $this->_createTypedResult($object, $type, $needSort);
		}

		return $typedResults;
	}

	/**
	 * @param \Object $object
	 * @param string $type
	 * @param bool $needSort
	 * @return SearcherResult
	 */
	protected function _createTypedResult($object, $type, $needSort)
	{
		$typedResult = new SearcherResult();
		$typedResult->setObject($object);
		$keyName = $this->_redisInteractor->makeKeyName($object->id, $this->_redisInteractor->getPrefixEntry(), $type);
		$tokens = $this->_redisInteractor->getRedisUtil()->hashGet($keyName);
		$typedResult->setTokens($tokens);

		if ($needSort)
		{
			$score = $this->_calcScore($tokens);
			$typedResult->setScore($score);
		}

		return $typedResult;
	}

	/**
	 * @param array $tokens
	 * @return float
	 */
	protected function _calcScore(array $tokens)
	{
		$score = 1.0;

		if (($tokensCnt = count($tokens)) > 1)
		{
			$positions = array_intersect($tokens, $this->_tokens);
			$positions = array_keys($positions);

			foreach ($positions as $i => $val)
			{
				if (!isset($positions[$i+1]))
				{
					break;
				}

				if ($tokens[$positions[$i+1]] == $tokens[$val])
				{
					$score -= 1/$tokensCnt;
				}
				else
				{
					$score += ($positions[$i + 1] - $val - 1);
				}
			}
		}

		if ($this->_tokensCount != $tokensCnt)
		{

			$score += self::PROXIMITY_WEIGHT*(1 - $this->_tokensCount / $tokensCnt);
		}

		return $score;
	}
}