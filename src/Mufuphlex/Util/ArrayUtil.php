<?php

namespace Mufuphlex\Util;

/**
 * Class ArrayUtil
 * @package Mufuphlex\Util
 */
class ArrayUtil
{
	/**
	 * @param array $array
	 * @param array $map
	 * @return array
	 */
	public static function cutByWhitelist(array &$array, array $map)
	{
		foreach ($array as $key => $value)
		{
			if (isset($map[$key]))
			{
				if (is_array($map[$key]) AND is_array($value))
				{
					$array[$key] = self::_cutByWhitelist($value, $map[$key]);
				}
				elseif ($map[$key] instanceof Closure)
				{
					$array[$key] = $map[$key]($value);
				}
			}
			else
			{
				$unset = true;
				$mapKeys = array_keys($map);

				foreach ($mapKeys as $mapKey)
				{
					if (preg_match('@^/.+/[siu]*$@', $mapKey) AND preg_match($mapKey, $key))
					{
						if (is_array($map[$mapKey]))
						{
							$array[$key] = self::_cutByWhitelist($value, $map[$mapKey]);
						}
						$unset = false;
						break;
					}
				}

				if ($unset)
				{
					unset($array[$key]);
				}
			}
		}

		return $array;
	}
}