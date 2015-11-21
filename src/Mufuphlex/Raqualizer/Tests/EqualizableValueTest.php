<?php

use Mufuphlex\Raqualizer\EqualizableValue;

class EqualizableValueTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultFunctionality()
    {
        $args = array(1, 0.5);

        foreach ($args as $arg) {
            $value = new EqualizableValue($arg);
            $this->assertInstanceOf('\Mufuphlex\Raqualizer\EqualizableValue', $value);
            $this->assertEquals($arg, $value->getOriginal());
            $this->assertEquals(1, $value->getRatio());
            $value->setRatio($arg);
            $this->assertEquals($arg, $value->getRatio());
        }
    }

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

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionText $ratio must be numeric
     */
    public function testSetRatioFailsOnNotNumeric()
    {
        $value = new EqualizableValue(1);
        $value->setRatio('xaba');
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionText $ratio is expected to be between 0 and 1
     */
    public function testSetRatioFailsOnLeserThanZero()
    {
        $value = new EqualizableValue(1);
        $value->setRatio(-1);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionText $ratio is expected to be between 0 and 1
     */
    public function testSetRatioFailsOnGreaterThanOne()
    {
        $value = new EqualizableValue(1);
        $value->setRatio(2);
    }
}