<?php
namespace Mufuphlex\ReSearcher;

/**
 * Interface TokenizerInterface
 * @package Mufuphlex\ReSearcher
 */
interface TokenizerInterface
{
	/**
	 * @param string $str
	 * @return array
	 */
	public function tokenize($str);
}