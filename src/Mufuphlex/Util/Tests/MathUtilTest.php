<?php

class MathUtilTest extends PHPUnit_Framework_TestCase
{
    public function testSum()
    {
        $sums = array(
            array(0,0,0),
            array(1,0,1),
            array(0,1,1),
            array(1,2,3),
            array(15,2,17),
            array(3,19,22),
            array(235,899,1134),
            array(
                implode('', array_fill(0, 400, 1)),
                implode('', array_fill(0, 400, 2)),
                implode('', array_fill(0, 400, 3))
            )
        );

        foreach ($sums as $data)
        {
            $this->assertEquals((string)$data[2], \Mufuphlex\Util\MathUtil::sum($data[0], $data[1]));
        }
    }

    public function testMult()
    {
        $mults = array(
            array(0,0,0),
            array(1,0,0),
            array(0,1,0),
            array(1,2,2),
            array(2,1,2),
            array(15,2,30),
            array(3,16,48),
            array(
                53,
                '80658175170943878571660636856403766975289505440883277824000000000000',
                '4274883284060025564298013753389399649690343788366813724672000000000000'
            ),
            array(
                '230843697339241380472092742683027581083278564571807941132288000000000000',
                55,
                '12696403353658275925965100847566516959580321051449436762275840000000000000'
            )
        );

        foreach ($mults as $data)
        {
            $this->assertEquals((string)$data[2], \Mufuphlex\Util\MathUtil::mult($data[0], $data[1]));
        }
    }
}