<?php

namespace Mufuphlex\Raqualizer;

/**
 * Class EqualizableValueSet
 * @package Mufuphlex\Raqualizer
 */
class EqualizableValueSet implements EqualizableValueSetInterface
{
    /** @var array of EqualizableValueInterface */
    private $values = array();

    /**
     * @param EqualizableValueInterface $value
     * @return $this
     */
    public function addValue(EqualizableValueInterface $value)
    {
        if (!in_array($value, $this->values, true)) {
            $this->values[] = $value;
        }

        return $this;
    }

    /**
     * @param array $values
     * @return $this
     */
    public function addValues(array $values)
    {
        foreach ($values as $value) {
            $this->addValue($value);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @return $this
     */
    public function arrangeRatios()
    {
        $originals = array();

        /** @var EqualizableValueInterface $value */
        foreach ($this->values as $value) {
            $originals[] = $value->getOriginal();
        }

        $total = array_sum($originals);
        $ratios = array();

        foreach ($originals as $i => $original) {
            $ratio = $original / $total;
            $this->values[$i]->setRatio($ratio);
            $ratios[] = $ratio;
        }

        if (array_sum($ratios) > 1.001) {
            throw new \LogicException('Sum of ratios in the set is greater than 1');
        }

        return $this;
    }
}