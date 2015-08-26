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
					$array[$key] = self::cutByWhitelist($value, $map[$key]);
				}
				elseif ($map[$key] instanceof \Closure)
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
							$array[$key] = self::cutByWhitelist($value, $map[$mapKey]);
						}
						elseif ($map[$mapKey] instanceof \Closure)
						{
							$array[$key] = $map[$mapKey]($value);
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

	/**
	 * @param array $array
	 * @param array $map
	 * @return array
	 */
	public static function cutByBlacklist(array &$array, array $map)
	{
		$mapKeys = array_keys($map);

		foreach ($array as $key => $value)
		{
			$unset = false;

			if (isset($map[$key]))
			{
				if (is_array($map[$key]))
				{
					$array[$key] = self::cutByBlacklist($value, $map[$key]);
				}
				elseif ($map[$key] instanceof \Closure)
				{
					$array[$key] = $map[$key]($value);
				}
				else
				{
					$unset = true;
				}
			}
			else
			{
				foreach ($mapKeys as $mapKey)
				{
					if (preg_match('@^/.+/[siu]*$@', $mapKey) AND preg_match($mapKey, $key))
					{
						if (is_array($map[$mapKey]))
						{
							$array[$key] = self::cutByBlacklist($value, $map[$mapKey]);
						}
						elseif ($map[$mapKey] instanceof \Closure)
						{
							$array[$key] = $map[$mapKey]($value);
						}
						else
						{
							$unset = true;
						}
						break;
					}
				}
			}

			if ($unset)
			{
				unset($array[$key]);
			}
		}

		return $array;
	}

	/**
	 * @param array $array
	 * @param bool $keepKeys
	 * @return array
	 */
	public static function unique(array $array, $keepKeys = false)
	{
		if ($keepKeys)
		{
			$array = array_reverse($array, true);
		}

		$flip = array_flip($array);

		if (!$keepKeys)
		{
			return array_keys($flip);
		}

		return array_flip($flip);
	}
}