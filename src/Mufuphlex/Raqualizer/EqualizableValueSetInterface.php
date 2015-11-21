<?php
namespace Mufuphlex\Raqualizer;

/**
 * Interface EqualizableValueSetInterface
 * @package Mufuphlex\Raqualizer
 */
interface EqualizableValueSetInterface
{
    /**
     * @param EqualizableValueInterface $value
     * @return $this
     */
    public function addValue(EqualizableValueInterface $value);

    /**
     * @param array $values
     * @return $this
     */
    public function addValues(array $values);

    /**
     * @return array
     */
    public function getValues();

    /**
     * @param void
     * @return $this
     */
    public function arrangeRatios();

    /**
     * @param EqualizableValueSetInterface $set
     * @return $this
     */
    public function dependsOn(EqualizableValueSetInterface $set);
}