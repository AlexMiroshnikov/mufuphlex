<?php

class ArrayUtilTest extends PHPUnit_Framework_TestCase
{
	public function testWhitelistSimpleCase()
	{
		$map = array(
			'key' => array(
				'value' => array(
					'low level' => true
				)
			)
		);

		$array = array(
			'key' => array(
				'value' => array(
					'low level' => 123,
					'another low level' => 456
				),
				'another value' => array(
					'low level' => 789,
					'another low level' => 'string'
				)
			),
			'another key' => array(
				'value' => array(
					'low level' => 1234,
					'another low level' => 4567
				),
				'another value' => array(
					'low level' => 7890,
					'another low level' => 'another string'
				)
			)
		);

		$cutByWhitelist = \Mufuphlex\Util\ArrayUtil::cutByWhitelist($array, $map);

		$this->assertEquals(
			array(
				'key' => array(
					'value' => array(
						'low level' => 123
					)
				)
			),
			$cutByWhitelist
		);
	}

	public function testWhitelistRegexCase()
	{
		$map = array(
			'/^(?:one|another)$/' => array(
				'/\d+/' => array(
					'low level' => true
				)
			)
		);
	}

	public function testWhitelistClosureCase()
	{
		$map = array(
			0 => array(
				'/\d+/' => function($arg){return $arg*2+1;}
			)
		);
	}
}