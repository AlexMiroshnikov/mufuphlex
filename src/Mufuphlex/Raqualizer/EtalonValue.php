<?php

namespace Mufuphlex\Raqualizer;

/**
 * Class EtalonValue
 * @package Mufuphlex\Raqualizer
 */
class EtalonValue implements EtalonValueInterface
{
    /** @var int|float */
    private $value;

    /**
     * EtalonValue constructor.
     * @param $value
     */
    public function __construct($value)
    {
        if (!is_numeric($value)) {
            throw new \InvalidArgumentException('$value must be numeric');
        }

        $this->value = $value;
    }

    /**
     * @return int
     */
    public function get()
    {
        return $this->value;
    }
}