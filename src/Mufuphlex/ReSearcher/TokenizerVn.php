<?php
namespace Mufuphlex\ReSearcher;

/**
 * Class TokenizerVn
 * @package Mufuphlex\ReSearcher
 */
class TokenizerVn implements TokenizerInterface
{
	const VN_LETTERS = 'aáàảạãăắằẳặẵâấầẩậẫbcdđeéèẻẹẽêếềểệễfghiíìỉĩịjklmnoóòỏọõôốồổộỗơớờởợỡpqrstuúùủụũưứừửựữvxyýỳỷỵỹzwĐ';

	/**
	 * @param string $str
	 * @return array
	 */
	public function tokenize($str)
	{
		$tokens = array();
		$str = preg_replace('/[^'.self::VN_LETTERS.'0-9]/siu', ' ', $str);
		$parts = explode(' ', $str);

		foreach ($parts as $part)
		{
			if ($part = trim($part, " \t\r\n"))
			{
				$tokens[] = mb_strtolower($part);
			}
		}

		$tokens = array_unique($tokens);
		return $tokens;
	}
}