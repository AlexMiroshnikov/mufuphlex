<?php
class SearcherTest extends PHPUnit_Framework_TestCase
{
	/** @var \Mufuphlex\ReSearcher\RedisInteractor */
	private $_redisInteractor = null;

	/** @var \Mufuphlex\ReSearcher\Indexer */
	private $_indexer = null;

	/** @var \Mufuphlex\ReSearcher\Searcher */
	private $_searcher = null;

	private $_proximitySets = array(
		//*
		'tặng trị' => array(
			'long võ sự kiện tặng thuốc tăng lực quà tặng trị giá 10tr vnđ lên cấp 9x chỉ trong 2 ngày tham gia ngay longvo vn',
			'webgame cao cấp tu tiên chi lộ cực hot 2015 game tặng code trị giá lên tới 1 triệu vnd vào chơi ngay nhận hàng nóng tutienchilo com landingpage ba ntk2',
			'3g wifi thiết bị phát wifi từ sim 3g 3g wifi bộ phát wifi 3g tốc độ 21 6 43 2 lte tặng sim 3g trị giá 300k vnwifi net 3g phat wifi wifi 3g usb 3g phat wifi aspx'
		)/**/,
		'"hot girl"' => array(
			'thử độ hot girl cùng đo bạn hot tới đâu click ngay cpvm vn',
			'chơi game hot cùng hot girl chơi game vơ i hot girl trang anna qua thi ch mê tham gia ngay thitien vn landing',
			'chơi game hot cùng hot girl ngay cùng hot girl trang anna hóa thân tiên nữ nơi tiên giới kì ảo xem ngay thitien vn landing'
		),
		//*/
		'game online moi' => array(
			'3 ' => 'game online moi',
			'2 ' => 'game online moi',
			'1 ' => 'game moi online'
		)
		//*/
	);

	public function setUp()
	{
		$this->_redisInteractor = new \Mufuphlex\ReSearcher\RedisInteractor(array(
			'db' => 2,
			'namespace' => 'Testing:'
		));

		$this->_redisInteractor->getRedisUtil()->flushDb();

		$this->_indexer = new \Mufuphlex\ReSearcher\Indexer($this->_redisInteractor);
		$this->_searcher = new \Mufuphlex\ReSearcher\Searcher($this->_redisInteractor);
		//$this->_searcher->setVerbose(true);
	}

	//*
	public function testProximitySorting()
	{
		$i = 0;
		echo "\n";

		foreach ($this->_proximitySets as $str => $set)
		{
			echo "Proximity set ".++$i."\n";
			$this->_testProximitySorting($str, $set);
		}
	}
	//*/

	public function _testProximitySorting($str, $set)
	{
		$this->_redisInteractor->getRedisUtil()->flushDb();
		$idsPredefined = false;
		$idsMap = array();

		foreach ($set as $i => $item)
		{
			if (!$idsPredefined && is_string($i))
			{
				$idsPredefined = true;
			}

			if ($idsPredefined)
			{
				$id = (int)trim($i);
				$idsMap[$i] = $id;
			}
			else
			{
				$id = $i + 1;
			}

			$dummy = new \Mufuphlex\ReSearcher\InteractableObject\Dummy(array('id' => $id));
			$dummy->setTokens(explode(' ', $item));
			$this->_indexer->addObject($dummy);
		}

		$type = 'dummy type';

		$result = $this->_searcher->search($str, array(
			new \Mufuphlex\ReSearcher\SearcherResultSettings(array(
				'type' => $type,
				'resultClass' => '\Mufuphlex\ReSearcher\InteractableObject\Dummy',
				'sortByProximity' => true
			))
		));

		$result = $result[$type];

		$setCnt = count($set);
		$this->assertCount($setCnt, $result);

		$idsMapFlip = ($idsMap ? array_flip($idsMap) : null);
		$idsMap = array_values($idsMap);

		foreach ($result as $resultNum => $searchResult)
		{
			$expectedId = ($idsMap ? $idsMap[$resultNum] : ($resultNum + 1));
			$this->assertEquals($expectedId, $searchResult->getId());
			$expectedStr = ($idsMap ? $set[$idsMapFlip[$expectedId]] : $set[$resultNum]);
			$this->assertEquals($expectedStr, implode(' ', $searchResult->getTokens()));
		}
	}

	//*
	public function testExactMatch()
	{
		$func = __FUNCTION__;
		$baseStr1 = 'big red '.$func;
		$baseStr2 = 'big '.$func;

		$items = array(
			$baseStr1,
			$baseStr2
		);

		foreach ($items as $i => $item)
		{
			$dummy = new \Mufuphlex\ReSearcher\InteractableObject\Dummy(array('id' => ($i+1)));
			$dummy->setTokens(explode(' ', $item));
			$this->_indexer->addObject($dummy);
		}

		$type = 'dummy type';

		$settings = new \Mufuphlex\ReSearcher\SearcherResultSettings(array(
			'type' => $type,
			'resultClass' => '\Mufuphlex\ReSearcher\InteractableObject\Dummy'
		));

		//*
		$result = $this->_searcher->search($baseStr2, array($settings));
		$result = $result[$type];
		$this->assertCount(2, $result);
		//*/

		$result = $this->_searcher->search('"'.$baseStr2.'"', array($settings));
		$result = $result[$type];
		$this->assertCount(1, $result);
		$this->assertEquals(2, $result[0]->getId());
	}
	//*/

	//*
	public function testExactMatchFalseNegative()
	{
		$str1 = 'cây hương thảo rosemary bonsai cực thơm đuổi muỗi giảm stress tốt trí nhớ và là gia vị cực ngon https www facebook com cayhuongthaorosemarygiare';
		$str2 = 'quà 8 3 cây hương thảo rosemary quà tặng bất ngờ cho 8 3 cây bonsai vừa đẹp vừa thơm tốt sức khoẻ https www facebook com notes c c3 a2y h c6 b0 c6 a1ng th e1 ba a3o rosemary c c3 a2y h c6 b0 c6 a1ng th e1 ba a3o rosemary bonsai c e1 bb b1c th c6 a1m c4 91u e1 bb 95i mu e1 bb 97i gi e1 ba a3m stress l c3 a0m gia v e1 bb 8b 487909521317155';

		$items = array(
			$str1,
			$str2
		);

		foreach ($items as $i => $item)
		{
			$dummy = new \Mufuphlex\ReSearcher\InteractableObject\Dummy(array('id' => ($i+1)));
			$dummy->setTokens(explode(' ', $item));
			$this->_indexer->addObject($dummy);
		}

		$type = 'dummy type';

		$result = $this->_searcher->search('"rosemary bonsai"', array(
			new \Mufuphlex\ReSearcher\SearcherResultSettings(array(
				'type' => $type,
				'resultClass' => '\Mufuphlex\ReSearcher\InteractableObject\Dummy',
				'sortByProximity' => true
			))
		));

		$result = $result[$type];

		$this->assertCount(2, $result);

		for ($i=1; $i<=2; $i++)
		{
			$str = ${'str'.$i};
			$this->assertEquals($i, $result[($i-1)]->getId());
			$this->assertEquals($str, implode(' ', $result[($i-1)]->getTokens()));
		}
	}
	//*/
}