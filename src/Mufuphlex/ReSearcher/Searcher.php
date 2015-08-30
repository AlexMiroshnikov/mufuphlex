<?php
namespace Mufuphlex\ReSearcher;

/**
 * Class Searcher
 * @package Mufuphlex\ReSearcher
 */
class Searcher extends Interactor
{
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

	/** @var ScorerInterface */
	protected $_scorer = null;

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

		if (!$this->setStr($str))
		{
			return null;
		}

		$searcherResultSettings = $this->_prepareSearcherResultSettings($searcherResultSettings);
		$this->_result = $this->_search($this->_tokens, $searcherResultSettings);
		$typedResults = array();
		$this->_scorer = new Scorer($this);

		foreach ($this->_result as $type => $results)
		{
			$typedResults[$type] = $this->_createTypedResults($searcherResultSettings[$type], $results);

			if ($searcherResultSettings[$type]->needsSortByProximity())
			{
				usort($typedResults[$type], function($a, $b){
					// 1 score up => position down
					if ($a->getScore() > $b->getScore()) return 1;
					if ($a->getScore() < $b->getScore()) return -1;
					// 2 id up => position up
					if ($a->getId() > $b->getId()) return -1;
					if ($a->getId() < $b->getId()) return 1;
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
	 * @return array
	 */
	public function getTokens()
	{
		return $this->_tokens;
	}

	/**
	 * @return bool
	 */
	public function setStr($str)
	{
		if (!is_string($str))
		{
			throw new \InvalidArgumentException('$str must be a string, '.gettype($str).' given');
		}

		$this->_setStr($str);

		if (!$this->_tokens = $this->_tokenizer->tokenize($this->_str))
		{
			return false;
		}

		$this->_tokensCount = count($this->_tokens);
		return true;
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
		$type = $searcherResultSettings->getType();
		$needSort = $searcherResultSettings->needsSortByProximity() || $this->_isExact;
		$typedResults = array();
		$prefixEntry = $this->_redisInteractor->getPrefixEntry();
		$redisUtil = $this->_redisInteractor->getRedisUtil()->multiStart();

		foreach ($results as $resultId)
		{
			$keyName = $this->_redisInteractor->makeKeyName($resultId, $prefixEntry, $type);
			$redisUtil->multiSeq(array('hGetAll' => array($keyName)));
		}

		$tokens = $redisUtil->multiExec();

		foreach ($tokens as $num => $curTokens)
		{
			if (!$typedResult = $this->_createTypedResult($results[$num], $curTokens, $needSort))
			{
				continue;
			}

			$typedResults[] = $typedResult;
		}

		return $typedResults;
	}

	/**
	 * @param mixed $resultId
	 * @param array $tokens
	 * @param bool $needSort
	 * @return SearcherResult
	 */
	protected function _createTypedResult($resultId, array $tokens, $needSort)
	{
		$typedResult = new SearcherResult();
		$typedResult->setId($resultId);
		$typedResult->setTokens($tokens);

		if ($needSort)
		{
			$this->_scorer->score($typedResult);

			if ($this->_isExact AND !$typedResult->hasExactMatch())
			{
				return null;
			}
		}

		return $typedResult;
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