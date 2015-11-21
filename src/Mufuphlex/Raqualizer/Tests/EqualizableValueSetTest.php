<?php

use Mufuphlex\Raqualizer\EqualizableValue;
use Mufuphlex\Raqualizer\EqualizableValueSet;

class EqualizableValueSetTest extends PHPUnit_Framework_TestCase
{
    public function testAddGetValues()
    {
        $values = array(
            new EqualizableValue(1),
            new EqualizableValue(2),
            new EqualizableValue(3)
        );

        $set = new EqualizableValueSet();

        $set->addValue($values[0]);
        $this->assertEquals(1, count($set->getValues()));

        // ensure un uniqueness of objects inside
        $set->addValue($values[0]);
        $this->assertEquals(1, count($set->getValues()));

        $set->addValues(array($values[1], $values[2]));
        $this->assertEquals(3, count($set->getValues()));

        foreach ($set->getValues() as $i => $value) {
            $this->assertSame($values[$i], $value);
        }
    }

    public function testArrangeRatios()
    {
        $args = array(
            2,
            5,
            10
        );
        $sum = array_sum($args);

        $set = new EqualizableValueSet();

        foreach ($args as $arg) {
            $set->addValue(new EqualizableValue($arg));
        }

        $set->arrangeRatios();

        foreach ($set->getValues() as $i => $value) {
            $this->assertEquals($args[$i] / $sum, $value->getRatio());
        }
    }

    public function testArrangeRatiosWithDependsOn()
    {
        $value1 = new EqualizableValue(1);
        $ratio1 = 0.789;
        $value1->setRatio($ratio1);
        $value2 = new EqualizableValue(2);
        $ratio2 = 0.123;
        $value2->setRatio($ratio2);

        $dependence = new EqualizableValueSet();
        $dependence->addValue($value1);

        $set = new EqualizableValueSet();
        $set->addValue($value2);
        $set->dependsOn($dependence);
        $set->arrangeRatios();

        $valueSet = current($set->getValues());
        $valueDependence = current($dependence->getValues());

        $this->assertEquals($valueDependence->getRatio(), $valueSet->getRatio());
        $this->assertEquals($ratio1, $valueSet->getRatio());
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionTest Self count 1 does not equal to dependence's 2
     */
    public function testDependsOnFailsOnMismatch()
    {
        $value1 = new EqualizableValue(1);
        $value2 = new EqualizableValue(2);

        $dependence = new EqualizableValueSet();
        $dependence->addValues(array($value1, $value2));

        $set = new EqualizableValueSet();
        $set->addValue($value2);

        $set->dependsOn($dependence);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionTest The set already depends on some another set
     */
    public function testDependsOnFailsOnRedefine()
    {
        $value = new EqualizableValue(1);
        $dependence = new EqualizableValueSet();
        $dependence->addValue($value);
        $dependence2 = new EqualizableValueSet();
        $dependence2->addValue($value);

        $set = new EqualizableValueSet();
        $set->addValue($value);

        $set->dependsOn($dependence);
        $set->dependsOn($dependence2);
    }
}