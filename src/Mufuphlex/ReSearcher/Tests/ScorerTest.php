<?php
use Mufuphlex\ReSearcher\Scorer;
use Mufuphlex\ReSearcher\Searcher;
use Mufuphlex\ReSearcher\SearcherResult;

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
		$this->_redisInteractor = $this->getMockBuilder('\Mufuphlex\ReSearcher\RedisInteractor')->disableOriginalConstructor()->getMock();
	}

//*
	public function testProximityPenalty()
	{
		$searcher = new Searcher($this->_redisInteractor);
		$searcher->setStr('a');
		$scorer = new Scorer($searcher);

		$strs = array(
			'a' => 1,
			'a b' => 1.5,
			'a b c' => 1.667
		);

		foreach ($strs as $str => $expectedScore)
		{
			$result = new SearcherResult();
			$result->setTokens(explode(' ', $str));
			$score = $scorer->score($result);
			$this->assertEquals($expectedScore, $score, '', self::ASSERT_SCORE_DELTA);
		}

		$searcher->setStr('a b');
		$scorer = new Scorer($searcher);

		$strs = array(
			'a b' => 1,
			'a b c' => 1.333
		);

		foreach ($strs as $str => $expectedScore)
		{
			$result = new SearcherResult();
			$result->setTokens(explode(' ', $str));
			$score = $scorer->score($result);
			$this->assertEquals($expectedScore, $score, '', self::ASSERT_SCORE_DELTA);
		}
	}
//*/

//*
	public function testComplexScoring()
	{
		$strs = array(
			'vé máy bay đi singapore giá rẻ flightbooking vn truy cập ngay filght booking bớt tiền vé máy bay thêm tiền ăn chơi flightbooking vn ve may bay quoc te di singapore' => 2.719, //2.684, //1.719, //2.718,
			'vé máy bay đi singapore giá rẻ đặt vé máy bay đi singapore giá rẻ tại vebay365 liên hệ 1900 55 88 05 vebay365 com vn ve quoc te 5041 ve may bay di singapore html' => 2.722, //1.722, //2.722,
			'tour malaysia singapore giá 10 790 000 bao gồm vé máy bay n tặng buffet lẩu nướng show cá heo kay vn du lich singapore malaysia' => 7.808,
			'đại học singapore tư vấn du học singapore 2015 học bổng tới 70 thực tập hưởng lương cấp bằng anh úc tặng vé máy bay newworldedu vn tin tuc du hoc 4 singapore newsword html' => 15.829 //19.797, //19.829, //15.829
		);

		$term = 'vé máy bay singapore';
		$searcher = new Searcher($this->_redisInteractor);
		$searcher->setStr($term);
		$scorer = new Scorer($searcher);

		foreach ($strs as $str => $expectedScore)
		{
			$result = new SearcherResult();
			$result->setTokens(explode(' ', $str));
			$score = $scorer->score($result);
			$this->assertEquals($expectedScore, $score, '', self::ASSERT_SCORE_DELTA);
		}
	}
//*/

//*
	public function testSimpleScoring()
	{
		$searcher = new Searcher($this->_redisInteractor);

		$this->_testSimpleScoring($searcher, 'a b', 'a b', 1.0);
		$this->_testSimpleScoring($searcher, 'a b', 'b a', 1.55);	// must be greater than 1.0 due to not the same order
		$this->_testSimpleScoring($searcher, '"a b"', 'b a', 1.55);	// must be greater than 1.0 due to not the same order + exact match
		$this->_testSimpleScoring($searcher, 'a b', 'c a d e a b f', 1.571);	// must be greater than 1.0 due to length
		$this->_testSimpleScoring($searcher, 'a b', 'c b a d e f a', 1.729);	// must be greater than 1.0 due to not the same order
	}
//*/

	private function _testSimpleScoring(Searcher $searcher, $term, $resultStr, $score)
	{
		$searcher->setStr($term);
		$scorer = new Scorer($searcher);
		$result = new SearcherResult();
		$result->setTokens(explode(' ', $resultStr));
		$this->assertEquals($score, $scorer->score($result), '', self::ASSERT_SCORE_DELTA);
	}
}