<?php

namespace Mufuphlex\Persistence\Models\Traits;

/**
 * Class ModelDoctrineTrait
 * @package Mufuphlex\Persistence\Models\Traits
 */
trait ModelDoctrineTrait
{
    /**
     * @var string
     */
    protected $DORMentityDefinition = '';

    /**
     * @var array
     */
    protected $DORMentityProperties = array();

    /**
     * ModelDoctrineTrait constructor.
     */
    public function __construct()
    {
        $this->makeDORMEntityDefinition();
    }

    /**
     * @return string
     */
    public function getDORMentityDefinition()
    {
        return $this->DORMentityDefinition;
    }

    /**
     * @return array
     */
    public function getDORMentityProperties()
    {
        return $this->DORMentityProperties;
    }

    /**
     * @param void
     * @return void
     */
    protected function makeDORMEntityDefinition()
    {
        $className = get_class($this);
        $modelName = preg_replace('/^(?:.+\\\\)?([^\\\\]+)Model$/', '$1', $className);
        $this->DORMentityDefinition = '@Entity @Table(name="'.$modelName.'")';
    }
}