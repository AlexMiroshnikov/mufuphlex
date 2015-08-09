<?php
namespace Mufuphlex\ReSearcher;

/**
 * Class TokenizerDefault
 * @package Mufuphlex\ReSearcher
 */
class TokenizerDefault implements TokenizerInterface
{
	/**
	 * @param string $str
	 * @return array
	 */
	public function tokenize($str)
	{
		return explode(' ', $str);
	}
}