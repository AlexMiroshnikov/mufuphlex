<?php
class IndexerTest extends PHPUnit_Framework_TestCase
{
	/** @var \Mufuphlex\ReSearcher\Indexer */
	private $_indexer = null;

	public function setUp()
	{
		$redisInteractor = new \Mufuphlex\ReSearcher\RedisInteractor(array(
			'db' => 2,
			'namespace' => 'Testing:'
		));

		$redisInteractor->getRedisUtil()->flushDb();

		$this->_indexer = new \Mufuphlex\ReSearcher\Indexer($redisInteractor);
	}

	public function testAddObjectNotEmpty()
	{
		$object = new \Mufuphlex\ReSearcher\InteractableObject\Dummy(array('id' => 'Not Empty'));
		$this->assertEquals(3, $this->_indexer->addObject($object));
	}

	public function testAddObjectEmpty()
	{
		$object = new \Mufuphlex\ReSearcher\InteractableObject\Dummy(array('id' => 'Empty'));
		$object->setTokens(array());
		$this->assertEquals(0, $this->_indexer->addObject($object));
	}
}