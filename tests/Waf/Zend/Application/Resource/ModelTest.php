<?php
/**
 * 
 *
 * @category
 * @package    
 * @subpackage 
 * @version    $Id:$
 */
class Waf_Zend_Application_Resource_ModelTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Zend_Registry::_unsetInstance();
        $this->resource = new Waf_Zend_Application_Resource_Model();
        $this->resource->setOptions(array(
            'basepath'=>dirname(__FILE__)
        ));
    }

    public function tearDown()
    {
        Zend_Registry::_unsetInstance();
    }

    public function testGetModelDefault()
    {
        $this->assertType(
            'Waf_Model',
            $this->resource->getModel()
        );
    }

    public function testGetModelGetsRegistered()
    {
        $this->resource->getModel();
        $this->assertTrue(Waf_Model::isRegistered());
    }

    public function testGetMapperDriverDefault()
    {
        $this->assertType(
            'Waf_Model_Mapper_Driver_Zend_Config',
            $this->resource->initMapperDriver()
        );
    }

    public function testGetMapperDriverDefaultType()
    {
        $mapperDriver = $this->resource->initMapperDriver();
        $this->assertEquals(
            'ini',
            $mapperDriver->getConfigType()
        );
    }

    public function testGetMapperDriverDefaultBasePath()
    {
        $mapperDriver = $this->resource->initMapperDriver();
        $paths        = $mapperDriver->getConfigPaths();
        $this->assertEquals(
            dirname(__FILE__) . '/models/mappers/configs',
            $paths[0]
        );
    }
}