<?php

namespace Mufuphlex\ReSearcher;

/**
 * Class Scorer
 * @package Mufuphlex\ReSearcher
 */
class Scorer implements ScorerInterface
{
	const SCORE_DEFAULT = 1.0;
	const WEIGHT_PROXIMITY = 1.0;
	const WEIGHT_ORDER_PENALTY = 1.1;
	const WEIGHT_ORDER_BONUS = 1;

	/** @var Searcher */
	protected $_searcher = null;

	/** @var float */
	protected $_score = self::SCORE_DEFAULT;

	/** @var array */
	protected $_tokens = array();

	/** @var int */
	protected $_tokensCnt = 0;

	/** @var array */
	protected $_searcherTokens = array();

	/** @var int */
	protected $_searcherTokensCnt = 0;

	/** @var array */
	protected $_tokenPenaltyMap = array();

	/** @var float */
	protected $_elementaryPenalty = 0.0;

	/** @var float */
	protected $_orderPenalty = 0.0;

	/** @var array */
	protected $_positions = array();

	/** @var int */
	protected $_exactMatchCnt = 0;

	/** @var bool */
	protected $_hasExactMatch = false;

	/**
	 * @param Searcher $searcher
	 */
	public function __construct(Searcher $searcher)
	{
		$this->_searcher = $searcher;
		$this->_searcherTokens = $searcher->getTokens();
		$this->_searcherTokensCnt = count($this->_searcherTokens);
	}

	/**
	 * @param SearcherResult $searcherResult
	 * @return float
	 */
	public function score(SearcherResult $searcherResult)
	{
		$this->_score = self::SCORE_DEFAULT;
		$this->_tokens = $searcherResult->getTokens();

		if (($this->_tokensCnt = count($this->_tokens)) > 1)
		{
			$this->_setScoreByTokens();
		}

		$this->_penalizeForProximity();
		$searcherResult->setScore($this->_score);
		$searcherResult->setExactMatch($this->_hasExactMatch);
		return $this->_score;
	}

	/**
	 * @param void
	 * @return void
	 */
	protected function _setScoreByTokens()
	{
		$this->_elementaryPenalty = 1/$this->_tokensCnt;
		$this->_orderPenalty = self::WEIGHT_ORDER_PENALTY*$this->_elementaryPenalty;
		$this->_exactMatchCnt = 0;
		$this->_hasExactMatch = false;
		$this->_tokenPenaltyMap = array_fill_keys($this->_searcherTokens, false);
		$this->_setPositions();
		$this->_scorePositions();
	}

	/**
	 * @param int $index
	 * @param string $token
	 * @return float
	 */
	protected function _calcPenalty($index, $token)
	{
		$penalty = (isset($this->_positions[$index - 1]) ? ($this->_positions[$index] - $this->_positions[$index - 1] - 1) : 0);
		$this->_exactMatch($penalty);

		if ((($pos = $this->_exactMatchCnt - 1) > 0) AND
			$token != $this->_searcherTokens[$pos]
		)
		{
			$penalty += $this->_orderPenalty;
		}

		return $penalty;
	}

	/**
	 * @param int $penalty
	 * @return bool
	 */
	protected function _exactMatch($penalty)
	{
		if ($penalty)
		{
			$this->_exactMatchCnt = 1;
			return false;
		}

		if (!$this->_hasExactMatch)
		{
			$this->_exactMatchCnt++;

			if ($this->_exactMatchCnt == $this->_searcherTokensCnt)
			{
				$this->_hasExactMatch = true;
			}
		}

		return true;
	}

	/**
	 * @param void
	 * @return void
	 */
	protected function _penalizeForProximity()
	{
		if ($this->_searcherTokensCnt != $this->_tokensCnt)
		{
			/*
			 * Implements rule "as closer result by length to the search - than better"
			 * Tokens: "a b c"
			 * 	Search "a"
			 * 	penalty = (1 - 1/3) = 2/3 - higher => result is worse
			 * 	Search "a c"
			 * 	penalty = (1 - 2/3) = 1/3 - lower => result is better
			 */
			$this->_score += self::WEIGHT_PROXIMITY*(1 - $this->_searcherTokensCnt / $this->_tokensCnt);
		}
	}

	/**
	 * @param void
	 * @return $this
	 */
	protected function _setPositions()
	{
		$positions = array_intersect($this->_tokens, $this->_searcherTokens);
		$this->_positions = array_keys($positions);
		return $this;
	}

	/**
	 * @param void
	 * @return void
	 */
	protected function _scorePositions()
	{
		foreach ($this->_positions as $index => $curPosition)
		{
			$token = $this->_tokens[$curPosition];

			if ($this->_tokenPenaltyMap[$token] !== false)	// small bonus for repeating the token
			{
				$this->_score -= $this->_elementaryPenalty;
			}
			else
			{
				$this->_tokenPenaltyMap[$token] = true;
			}

			$penalty = $this->_calcPenalty($index, $token);

			if ($penalty < 0)
			{
				$this->_score += $penalty;
			}
			elseif ($this->_tokenPenaltyMap[$token] === true)
			{
				$this->_tokenPenaltyMap[$token] = $penalty;
			}
			elseif ($penalty < $this->_tokenPenaltyMap[$token])
			{
				$this->_tokenPenaltyMap[$token] = $penalty;
			}
		}

		foreach ($this->_tokenPenaltyMap as $penalty)
		{
			$this->_score += $penalty;
		}
	}
}