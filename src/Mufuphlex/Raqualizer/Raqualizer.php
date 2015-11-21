<?php

namespace Mufuphlex\Raqualizer;

/**
 * Class Raqualizer
 * @package Mufuphlex
 */
class Raqualizer
{
    /**
     * @param EqualizableValueInterface $value
     * @param EtalonValueInterface $etalon
     * @return float
     */
    public function processValue(EqualizableValueInterface $value, EtalonValueInterface $etalon)
    {
        return $value->getRatio() * $etalon->get();
    }

    /**
     * @param EqualizableValueSetInterface $set
     * @param EtalonValueInterface $etalon
     * @return array
     */
    public function processSet(EqualizableValueSetInterface $set, EtalonValueInterface $etalon)
    {
        $set->arrangeRatios();
        $result = array();

        foreach ($set->getValues() as $value) {
            $result[] = $this->processValue($value, $etalon);
        }

        return $result;
    }
}