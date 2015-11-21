<?php

use Mufuphlex\Raqualizer\EqualizableValue;
use Mufuphlex\Raqualizer\EqualizableValueSet;
use Mufuphlex\Raqualizer\EtalonValue;
use Mufuphlex\Raqualizer\Raqualizer;

class RaqualizerTest extends PHPUnit_Framework_TestCase
{
    public function testProcessValue()
    {
        $raqualizer = new Raqualizer();

        $etalonPriceValue = 68898;
        $etalonClicksValue = 55;
        $etalonPrice = new EtalonValue($etalonPriceValue);
        $etalonClicks = new EtalonValue($etalonClicksValue);

        $valuePrice = new EqualizableValue(78551.105463111);
        $valueClicks = new EqualizableValue(55);

        $this->assertEquals($etalonPriceValue, $raqualizer->processValue($valuePrice, $etalonPrice));
        $this->assertEquals($etalonClicksValue, $raqualizer->processValue($valueClicks, $etalonClicks));
    }

    public function testProcessSet()
    {
        $etalonPriceValue = 3967621;
        $etalonClicksValue = 2147;
        $etalonPrice = new EtalonValue($etalonPriceValue);
        $etalonClicks = new EtalonValue($etalonClicksValue);

        $valueT1Price = new EqualizableValue(3390930.562969);
        $valueT1Clicks = new EqualizableValue(1394);

        $valueT2Price = new EqualizableValue(1132603.481724);
        $valueT2Clicks = new EqualizableValue(753);

        $setPrice = new EqualizableValueSet();
        $setPrice
            ->addValue($valueT1Price)
            ->addValue($valueT2Price);

        $raqualizer = new Raqualizer();
        $result = $raqualizer->processSet($setPrice, $etalonPrice);

        $expected = array(
            2974207.15,
            993413.85
        );

        foreach ($expected as $i => $expectation) {
            $this->assertEquals($expectation, $result[$i], '', 0.02);
        }

        $setClicks = new EqualizableValueSet();
        $setClicks
            ->addValue($valueT1Clicks)
            ->addValue($valueT2Clicks);

        $setClicks->dependsOn($setPrice);

        $result = $raqualizer->processSet($setClicks, $etalonClicks);

        $expected = array(
            1609,
            538
        );

        foreach ($expected as $i => $expectation) {
            $this->assertEquals($expectation, $result[$i], '', 0.49);
        }
    }
}