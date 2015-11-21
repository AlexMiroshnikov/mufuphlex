<?php

namespace Mufuphlex\Raqualizer;

/**
 * Class EqualizableValue
 * @package Mufuphlex\Raqualizer
 */
class EqualizableValue implements EqualizableValueInterface
{
    /** @var int|float */
    private $original;

    /** @var float */
    private $ratio = 1;

    /**
     * EqualizableValue constructor.
     * @param $original
     */
    public function __construct($original)
    {
        if (!is_numeric($original)) {
            throw new \InvalidArgumentException('$original must be numeric');
        }

        $this->original = $original;
    }

    /**
     * @return int|float
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * @return float
     */
    public function getRatio()
    {
        return $this->ratio;
    }

    /**
     * @param float $ratio
     * @return $this
     */
    public function setRatio($ratio)
    {
        if (!is_numeric($ratio)) {
            throw new \InvalidArgumentException('$ratio must be numeric');
        }

        if ($ratio > 1 OR $ratio < 0) {
            throw new \LogicException('$ratio is expected to be between 0 and 1');
        }

        $this->ratio = $ratio;
        return $this;
    }
}