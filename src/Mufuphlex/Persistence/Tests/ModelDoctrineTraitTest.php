<?php

class ModelDoctrineTraitTest extends PHPUnit_Framework_TestCase
{
    public function testGetDefaultDORM()
    {
        $model = new \Mufuphlex\Persistence\Models\DummyModel();
        $this->assertEquals('@Entity @Table(name="Dummy")', $model->getDORMentityDefinition());
        $entityProperties = $model->getDORMentityProperties();
        $this->assertInternalType('array', $entityProperties);
        $this->assertEquals(array(), $entityProperties);
    }
}