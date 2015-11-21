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

    /** @var EqualizableValueSetInterface */
    private $dependsOn;

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
        if ($this->dependsOn) {
            return $this->arrangeRatiosFromDependence();
        }

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

        /*
        if (array_sum($ratios) > 1.001) {
            throw new \LogicException('Sum of ratios in the set is greater than 1');
        }
        //*/

        return $this;
    }

    /**
     * @param EqualizableValueSetInterface $set
     * @return $this
     */
    public function dependsOn(EqualizableValueSetInterface $set)
    {
        if ($this->dependsOn !== null) {
            throw new \LogicException('The set already depends on some another set');
        }

        $this->validateDependence($set);
        $this->dependsOn = $set;
        return $this;
    }

    /**
     * @param EqualizableValueSetInterface $set
     */
    private function validateDependence(EqualizableValueSetInterface $set)
    {
        $selfCount = count($this->getValues());
        $theirCount = count($set->getValues());

        if ($selfCount !== $theirCount) {
            throw new \LogicException('Self count '.$selfCount.' does not equal to dependence\'s '.$theirCount);
        }
    }

    /**
     * @return $this
     */
    private function arrangeRatiosFromDependence()
    {
        $dependenceValues = $this->dependsOn->getValues();

        /** @var EqualizableValueInterface $value */
        foreach ($dependenceValues as $i => $value) {
            $this->values[$i]->setRatio($value->getRatio());
        }

        return $this;
    }
}