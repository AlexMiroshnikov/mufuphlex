<?php

use Mufuphlex\Raqualizer\EqualizableValue;

class EqualizableValueTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionText $original must be numeric
     */
    public function testConstructorFailsOnNull()
    {
        new EqualizableValue(null);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionText $original must be numeric
     */
    public function testConstructorFailsOnBool()
    {
        new EqualizableValue(true);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionText $original must be numeric
     */
    public function testConstructorFailsOnString()
    {
        new EqualizableValue('xaba');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionText $original must be numeric
     */
    public function testConstructorFailsOnArray()
    {
        new EqualizableValue(array(100));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionText $original must be numeric
     */
    public function testConstructorFailsOnObject()
    {
        new EqualizableValue(new stdClass());
    }
}