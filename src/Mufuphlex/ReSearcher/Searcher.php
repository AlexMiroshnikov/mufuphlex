<?php
namespace Mufuphlex\ReSearcher;

/**
 * Class Searcher
 * @package Mufuphlex\ReSearcher
 */
class Searcher extends Interactor
{
	/** @var TokenizerInterface  */
	protected $_tokenizer = null;

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

		if (!$tokens = $this->_tokenizer->tokenize($str))
		{
			return null;
		}

		$searcherResultSettings = $this->_prepareSearcherResultSettings($searcherResultSettings);
		$this->_result = $this->_search($tokens, $searcherResultSettings);
		$typedResults = array();

		foreach ($this->_result as $type => $results)
		{
			$this->_count += count($results);
			$typedResults[$type] = call_user_func_array(
				array($searcherResultSettings[$type]->getResultClass(), 'createResults'),
				array($results)
			);
		}

		return $typedResults;

		/*
		$ttlCnt = 0;

		foreach ($result as $type => $results)
		{
			$cnt = count($results);
			echo "\n\t".$cnt." ".$type;

			$ttlCnt += $cnt;
			$resultTokens = array();

			if ($searchResultSettings[$type]->needsSortByProximity())
			{
				$result[$type] = $this->_sortByProximity($tokens, $results, $type, $resultTokens);
			}

			if ($limit = $searchResultSettings[$type]->getLimit())
			{
				$result[$type] = array_slice($result[$type], 0, $limit);
			}

			if ($resultTokens)
			{
				foreach ($result[$type] as $id)
				{
					echo "\n\t\t".implode(' ', $resultTokens[$id]);
				}
			}

			$createdResult = call_user_func_array(array($searchResultSettings[$type]->getResultClass(), 'createResults'), array($result[$type]));
			echo "\nCreated result:\n";var_dump($createdResult);
			//break;
		}
		echo "\nSearch result cnt:\t".$ttlCnt;
		//*/
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

		foreach ($searchResultSettings as $setting)
		{
			$type = $setting->getType();
			$keys = array();

			foreach ($tokens as $token)
			{
				$keys[] = $this->_makeKeyNameToken($token, $type);
			}

			$keys = array_unique($keys);
			//echo "\nType '".$type."': \n";var_dump($keys);echo "\n";

			if ($intersection = $this->_redisInteractor->getRedisUtil()->setIntersect($keys))
			{
				$result[$type] = $intersection;
			}
		}

		return $result;
	}
}