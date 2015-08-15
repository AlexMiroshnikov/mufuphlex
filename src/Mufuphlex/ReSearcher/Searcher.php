<?php
namespace Mufuphlex\ReSearcher;

/**
 * Class Searcher
 * @package Mufuphlex\ReSearcher
 */
class Searcher extends Interactor
{
	const DEFAULT_SCORE = 1.0;
	const PROXIMITY_WEIGHT = 1;

	/** @var string */
	protected $_str = '';

	/** @var bool */
	protected $_isExact = false;

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

	/** @var bool */
	protected $_verbose = false;

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
		if ($this->_verbose) { echo "\nSearch for |".$str."|"; }

		$this->_reset();
		$this->_setStr($str);

		if (!$this->_tokens = $this->_tokenizer->tokenize($this->_str))
		{
			return null;
		}

		$this->_tokensCount = count($this->_tokens);

		$searcherResultSettings = $this->_prepareSearcherResultSettings($searcherResultSettings);
		$this->_result = $this->_search($this->_tokens, $searcherResultSettings);
		$typedResults = array();

		foreach ($this->_result as $type => $results)
		{
			$typedResults[$type] = $this->_createTypedResults($searcherResultSettings[$type], $results);

			if ($searcherResultSettings[$type]->needsSortByProximity())
			{
				usort($typedResults[$type], function($a, $b){
					if ($a->getScore() > $b->getScore()) return 1;
					if ($a->getScore() < $b->getScore()) return -1;
					return 0;
				});
			}

			$this->_count += count($typedResults[$type]);
		}

		$this->_result = $typedResults;
		return $typedResults;
	}

	/**
	 * @return int
	 * @throws Exception
	 */
	public function getResultCount()
	{
		return ($this->_result ? $this->_count : 0);
	}

	/**
	 * @param string $type
	 * @return int
	 * @throws Exception
	 */
	public function getResultCountByType($type)
	{
		return (($this->_result AND isset($this->_result[$type])) ? count($this->_result[$type]) : 0);
	}

	/**
	 * @param bool $val
	 * @return $this
	 */
	public function setVerbose($val = false)
	{
		$this->_verbose = (bool)$val;
		return $this;
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
		$this->_str = '';
		$this->_isExact = false;
		$this->_result = null;
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
		$needSort = $searcherResultSettings->needsSortByProximity() || $this->_isExact;

		foreach ($objects as $object)
		{
			$typedResult = $this->_createTypedResult($object, $type, $needSort);

			if (!$typedResult)
			{
				continue;
			}

			$typedResults[] = $typedResult;
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
			$this->_setScore($typedResult, $tokens);

			if ($this->_isExact AND !$typedResult->hasExactMatch())
			{
				if ($this->_verbose) { echo "\n\trelegate ".$object->id.' ('.$typedResult->getScore().')'; }
				return null;
			}
		}

		return $typedResult;
	}

	/**
	 * @param SearcherResult $searcherResult
	 * @param array $tokens
	 * @return float
	 */
	protected function _setScore(SearcherResult $searcherResult, array $tokens)
	{
		$score = self::DEFAULT_SCORE;
		$exactCounter = 1;
		$curScore = $score;

		if ($this->_verbose) { echo "\n\tsearch tokens cnt ".$this->_tokensCount; }

		if (($tokensCnt = count($tokens)) > 1)
		{
			$positions = array_intersect($tokens, $this->_tokens);
			if ($this->_verbose) { echo "\npositions: ";var_dump($positions); echo "\n"; }
			$positions = array_keys($positions);

			foreach ($positions as $i => $val)
			{
				if (!isset($positions[$i+1]))
				{
					break;
				}

				if (($tokens[$positions[$i + 1]] == $tokens[$val]) AND ($exactCounter != $this->_tokensCount))
				{
					$score -= 1/$tokensCnt;
				}
				else
				{
					$curScore = $positions[$i + 1] - $val;
					$score += ($curScore - 1);
				}

				if ($exactCounter < $this->_tokensCount)
				{
					if ($curScore <= self::DEFAULT_SCORE)
					{
						$exactCounter++;
					}
					else
					{
						$exactCounter = 1;
					}
					if ($this->_verbose) { echo "\n\t\tset EC ".$exactCounter; }
				}
			}
		}

		if ($this->_verbose) { echo "\n\tclean score: ".$score; }

		if ($exactCounter == $this->_tokensCount)
		{
			$searcherResult->setExactMatch(true);

			if ($score > self::DEFAULT_SCORE)
			{
				$score = self::DEFAULT_SCORE;
			}
		}

		if ($this->_tokensCount != $tokensCnt)
		{

			$score += self::PROXIMITY_WEIGHT*(1 - $this->_tokensCount / $tokensCnt);
		}

		$searcherResult->setScore($score);
		if ($this->_verbose) { echo "\n\tfinal score: ".$score; }
		return $score;
	}

	/**
	 * @param string $str
	 * @return void
	 */
	protected function _setStr($str)
	{
		if (!is_string($str))
		{
			throw new \InvalidArgumentException('$str must be a string, '.gettype($str).' given');
		}

		$this->_str = trim($str, " \t\r\n");

		if (preg_match('/^"[^"]+"$/su', $this->_str))
		{
			$this->_isExact = true;
		}

		$this->_str = trim($this->_str, '"');
	}
}