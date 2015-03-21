<?php

require_once __DIR__.'/../ArrayUtil.php';

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

		$array = array(
			'one another' => array(
				1 => array(
					'low level' => 'unexpected due to regex'
				),
				'not numeric' => array(
					'low level' => 'unexpected due to not numeric',
				),
				2 => array(
					'low level' => 'unexpected due to regex as well',
				),
			),
			'one' => array(
				3 => array(
					'the first low level' => 'unexpected due to key',
					'low level' => 'low level in one - 1'
				),
				'not numeric' => array(
					'low level' => 'unexpected due to not numeric',
					'another low level' => 'unexpected in one'
				),
				4 => array(
					'low level' => 'low level in one - 2',
					'the second low level' => 'unexpected due to key',
				)
			),
			'another' => array(
				5 => array(
					'the first low level' => 'unexpected due to key',
					'low level' => 'low level in another - 1'
				),
				'not numeric' => array(
					'low level' => 'unexpected due to not numeric',
					'another low level' => 'unexpected in another'
				),
				6 => array(
					'low level' => 'low level in another - 2',
					'the second low level' => 'unexpected due to key',
				)
			)
		);

		$cutByWhitelist = \Mufuphlex\Util\ArrayUtil::cutByWhitelist($array, $map);

		$this->assertEquals(
			array(
				'one' => array(
					3 => array(
						'low level' => 'low level in one - 1'
					),
					4 => array(
						'low level' => 'low level in one - 2'
					)
				),
				'another' => array(
					5 => array(
						'low level' => 'low level in another - 1'
					),
					6 => array(
						'low level' => 'low level in another - 2'
					)
				)
			),
			$cutByWhitelist
		);
	}

	public function testWhitelistClosureCase()
	{
		$map = array(
			0 => array(
				'/\d+/' => function($arg){return $arg*2+1;}
			)
		);

		$array = array(
			array(
				0,
				1,
				2,
				'internal string key' => 'unexpected internal content'
			),
			'string key' => 'unexpected content'
		);

		$cutByWhitelist = \Mufuphlex\Util\ArrayUtil::cutByWhitelist($array, $map);

		$this->assertEquals(
			array(
				array(
					1,
					3,
					5
				)
			),
			$cutByWhitelist
		);
	}
}