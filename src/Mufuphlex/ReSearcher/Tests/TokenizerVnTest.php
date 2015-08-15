<?php
class TokenizerVnTest extends PHPUnit_Framework_TestCase
{
	/** @var \Mufuphlex\ReSearcher\TokenizerVn */
	private $_tokenizer = null;

	public function setUp()
	{
		$this->_tokenizer = new \Mufuphlex\ReSearcher\TokenizerVn();
	}

	public function testTokenize()
	{
		$str = 'a b c';
		$this->assertEquals(array('a', 'b', 'c'), $this->_tokenizer->tokenize($str));

		$str = 'This is a string 123';
		$this->assertEquals(array('this', 'is', 'a', 'string', '123'), $this->_tokenizer->tokenize($str));

		$str = 'Siêu thị Intimex Bờ Hồ - Hà Nội';
		$this->assertEquals(array('siêu', 'thị', 'intimex', 'bờ', 'hồ', 'hà', 'nội'), $this->_tokenizer->tokenize($str));

		$str = 'xaba xaba';
		$this->assertEquals(array('xaba', 'xaba'), $this->_tokenizer->tokenize($str));
	}
}