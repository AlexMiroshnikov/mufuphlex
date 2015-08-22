<?php
namespace Mufuphlex\ReSearcher;

/**
 * Interface ScorerInterface
 * @package Mufuphlex\ReSearcher
 */
interface ScorerInterface
{
	/**
	 * @param SearcherResult $searcherResult
	 * @return float
	 */
	public function score(SearcherResult $searcherResult);
}