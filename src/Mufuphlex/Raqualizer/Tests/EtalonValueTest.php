<?php

use Mufuphlex\Raqualizer\EtalonValue;

class EtalonValueTest extends PHPUnit_Framework_TestCase
{
    public function testDefault()
    {
        $args = array(0, 0.5, 1);

        foreach ($args as $arg) {
            $etalon = new EtalonValue($arg);
            $this->assertInstanceOf('\Mufuphlex\Raqualizer\EtalonValue', $etalon);
            $this->assertEquals($arg, $etalon->get());
        }
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionText $value must be numeric
     */
    public function testConstructorFailsOnNotNumeric()
    {
        new EtalonValue('xaba');
    }
}