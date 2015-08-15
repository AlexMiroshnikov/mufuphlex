<?php
class SearcherTest extends PHPUnit_Framework_TestCase
{
	/** @var \Mufuphlex\ReSearcher\Indexer */
	private $_indexer = null;

	/** @var \Mufuphlex\ReSearcher\Searcher */
	private $_searcher = null;

	public function setUp()
	{
		$redisInteractor = new \Mufuphlex\ReSearcher\RedisInteractor(array(
			'db' => 2,
			'namespace' => 'Testing:'
		));

		$redisInteractor->getRedisUtil()->flushDb();

		$this->_indexer = new \Mufuphlex\ReSearcher\Indexer($redisInteractor);
		$this->_searcher = new \Mufuphlex\ReSearcher\Searcher($redisInteractor);
	}

	public function testProximitySorting()
	{
		$str1 = 'long võ sự kiện tặng thuốc tăng lực quà tặng trị giá 10tr vnđ lên cấp 9x chỉ trong 2 ngày tham gia ngay longvo vn';
		$str2 = 'webgame cao cấp tu tiên chi lộ cực hot 2015 game tặng code trị giá lên tới 1 triệu vnd vào chơi ngay nhận hàng nóng tutienchilo com landingpage ba ntk2';
		$str3 = '3g wifi thiết bị phát wifi từ sim 3g 3g wifi bộ phát wifi 3g tốc độ 21 6 43 2 lte tặng sim 3g trị giá 300k vnwifi net 3g phat wifi wifi 3g usb 3g phat wifi aspx';

		$items = array(
			$str1,
			$str2,
			$str3
		);

		foreach ($items as $i => $item)
		{
			$dummy = new \Mufuphlex\ReSearcher\InteractableObject\Dummy(array('id' => ($i+1)));
			$dummy->setTokens(explode(' ', $item));
			$this->_indexer->addObject($dummy);
		}

		$type = 'dummy type';

		$result = $this->_searcher->search('tặng trị', array(
			new \Mufuphlex\ReSearcher\SearcherResultSettings(array(
				'type' => $type,
				'resultClass' => '\Mufuphlex\ReSearcher\InteractableObject\Dummy',
				'sortByProximity' => true
			))
		));

		$result = $result[$type];

		$this->assertCount(3, $result);

		for ($i=1; $i<=3; $i++)
		{
			$str = ${'str'.$i};
			$this->assertEquals($i, $result[($i-1)]->getObject()->id);
			$this->assertEquals($str, implode(' ', $result[($i-1)]->getTokens()));
		}
	}
}