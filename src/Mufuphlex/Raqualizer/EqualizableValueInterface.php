<?php

namespace Mufuphlex\Raqualizer;

/**
 * Interface EqualizableValueInterface
 * @package Mufuphlex\Raqualizer
 */
interface EqualizableValueInterface
{
    /**
     * @return int|float
     */
    public function getOriginal();

    /**
     * @return float
     */
    public function getRatio();

    /**
     * @param float $ratio
     * @return $this
     */
    public function setRatio($ratio);
}