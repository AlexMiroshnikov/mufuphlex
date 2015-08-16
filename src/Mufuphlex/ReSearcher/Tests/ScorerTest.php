<?php
/**
 * Class ScorerTest
 */
class ScorerTest extends PHPUnit_Framework_TestCase
{
	/** @var \Mufuphlex\ReSearcher\RedisInteractor */
	private $_redisInteractor = null;

	public function setUp()
	{
		$this->_redisInteractor = new \Mufuphlex\ReSearcher\RedisInteractor(array(
			'db' => 2,
			'namespace' => 'Testing:'
		));

		$this->_redisInteractor->getRedisUtil()->flushDb();
	}

	public function testProximityPenalty()
	{
		$searcher = new \Mufuphlex\ReSearcher\Searcher($this->_redisInteractor);
		$searcher->setStr('a');
		$scorer = new \Mufuphlex\ReSearcher\Scorer($searcher);

		$strs = array(
			'a' => 1,
			'a b' => 1.5,
			'a b c' => 1.667
		);

		foreach ($strs as $str => $expectedScore)
		{
			$result = new \Mufuphlex\ReSearcher\SearcherResult();
			$result->setTokens(explode(' ', $str));
			$score = $scorer->score($result);
			//echo "\n\t".$score."\n";
			$this->assertEquals($expectedScore, $score, '', 0.002);
		}

		$searcher->setStr('a b');
		$scorer = new \Mufuphlex\ReSearcher\Scorer($searcher);

		$strs = array(
			'a b' => 1,
			'a b c' => 1.333
		);

		foreach ($strs as $str => $expectedScore)
		{
			$result = new \Mufuphlex\ReSearcher\SearcherResult();
			$result->setTokens(explode(' ', $str));
			$score = $scorer->score($result);
			//echo "\n\t".$score."\n";
			$this->assertEquals($expectedScore, $score, '', 0.002);
		}
	}
}