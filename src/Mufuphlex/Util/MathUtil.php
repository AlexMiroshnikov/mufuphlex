<?php
namespace Mufuphlex\Util;

/**
 * Class MathUtil
 * @package Mufuphlex\Util
 */
class MathUtil
{
    /**
     * @param int|string $a
     * @param int|string $b
     * @return string
     */
    public static function sum($a, $b)
    {
        $a = static::_convertToArray($a);
        $b = static::_convertToArray($b);
        $cnt = max(count($a), count($b));
        $result = array();
        $mind = 0;

        for ($i = 0; $i < $cnt; $i++)
        {
            $sum = (isset($a[$i]) ? $a[$i] : 0);

            if (isset($b[$i]))
            {
                $sum += $b[$i];
            }

            if ($mind)
            {
                $sum += $mind;
                $mind = 0;
            }

            if ($sum >= 10)
            {
                $mind = 1;
                $sum = $sum - 10;
            }

            $result[$i] = $sum;
        }

        if ($mind)
        {
            $result[] = $mind;
        }

        return implode('', array_reverse($result));
    }

    /**
     * @param mixed $a
     * @param midex $b
     * @return string
     */
    public static function mult($a, $b)
    {
        $vals = array(static::_convertToArray($a), static::_convertToArray($b));
        usort($vals, function($a, $b){
            if (count($a) > count($b)) return -1;
            return 0;
        });
        $cnt = count($vals[0]);
        $result = 0;
        $mind = 0;

        for ($i = 0; $i < $cnt; $i++)
        {
            $line = array();
            $j = 0;

            while (isset($vals[1][$j]))
            {
                $mul = $vals[0][$i] * $vals[1][$j];

                if ($mind > 0)
                {
                    $mul += $mind;
                    $mind = 0;
                }

                if ($mul >= 10)
                {
                    $mind = floor($mul / 10);
                    $mul = $mul % 10;
                }

                $line[] = $mul;
                $j++;
            }

            if ($mind > 0)
            {
                $line[] = $mind;
                $mind = 0;
            }

            $line = array_reverse($line);

            for ($k=0; $k < $i; $k++)
            {
                $line[] = 0;
            }

            $result = static::sum($result, implode('', $line));
        }

        return $result;
    }

    /**
     * @param mixed $val
     * @return array
     */
    private static function _convertToArray($val)
    {
        $val = (string)$val;
        $i = 0;
        $arr = array_fill(0, strlen($val), 0);

        while (isset($val[$i]))
        {
            $arr[$i] = $val[$i];
            $i++;
        }

        return array_reverse($arr);
    }
}