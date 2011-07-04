<?php
/**
 * Mapper Abstract for the Waf_Model component
 *
 * A mapper handles the translation of the Entities in the application to the
 * Storage. A mapper should exist for every Entity, and can be autoloaded using
 * one of the MapperDrivers or created in-memory using PHP.
 * 
 * @category   Waf
 * @package    Waf_Model
 * @subpackage Mapper
 * @version    $Id: MapperAbstract.php 407 2010-08-19 15:03:07Z jur $
 */
abstract class Waf_Model_Mapper_MapperAbstract extends Waf_Model_ElementAbstract
{
    public function __construct($options = null)
    {
        Waf_Model_Configurator::setConstructorOptions($this, $options);
    }

    abstract public function toEntity($state);

    abstract public function toStorage($state);
}