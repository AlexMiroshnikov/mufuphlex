<?php
/**
 * Class ScorerTest
 */
class ScorerTest extends PHPUnit_Framework_TestCase
{
	const ASSERT_SCORE_DELTA = 0.002;

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

//*
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
			$this->assertEquals($expectedScore, $score, '', self::ASSERT_SCORE_DELTA);
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
			$this->assertEquals($expectedScore, $score, '', self::ASSERT_SCORE_DELTA);
		}
	}
//*/

	public function testComplexScoring()
	{
		$strs = array(
			'vé máy bay đi singapore giá rẻ flightbooking vn truy cập ngay filght booking bớt tiền vé máy bay thêm tiền ăn chơi flightbooking vn ve may bay quoc te di singapore' => 2.718,
			'vé máy bay đi singapore giá rẻ đặt vé máy bay đi singapore giá rẻ tại vebay365 liên hệ 1900 55 88 05 vebay365 com vn ve quoc te 5041 ve may bay di singapore html' => 2.722,
			'tour malaysia singapore giá 10 790 000 bao gồm vé máy bay n tặng buffet lẩu nướng show cá heo kay vn du lich singapore malaysia' => 7.808,
			'đại học singapore tư vấn du học singapore 2015 học bổng tới 70 thực tập hưởng lương cấp bằng anh úc tặng vé máy bay newworldedu vn tin tuc du hoc 4 singapore newsword html' => 15.829
		);

		$term = 'vé máy bay singapore';
		$searcher = new \Mufuphlex\ReSearcher\Searcher($this->_redisInteractor);
		$searcher->setStr($term);
		$scorer = new \Mufuphlex\ReSearcher\Scorer($searcher);

		foreach ($strs as $str => $expectedScore)
		{
			$result = new \Mufuphlex\ReSearcher\SearcherResult();
			$result->setTokens(explode(' ', $str));
			$score = $scorer->score($result);
			//echo "\n\t".$score."\n";
			$this->assertEquals($expectedScore, $score, '', self::ASSERT_SCORE_DELTA);
		}
	}
}