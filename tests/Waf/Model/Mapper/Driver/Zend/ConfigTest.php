<?php
/**
 * 
 *
 * @category
 * @package    
 * @subpackage 
 * @version    $Id:$
 */
class Waf_Model_Mapper_Driver_Zend_ConfigTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->driver = new Waf_Model_Mapper_Driver_Zend_Config;
    }

    public function testIsDriverAbstract()
    {
        $this->assertTrue(
            $this->driver instanceof Waf_Model_Mapper_Driver_DriverAbstract
        );
    }

    /**
     * @expectedException Waf_Model_Mapper_Driver_Exception
     */
    public function testGetConfigPathsFailNoneSet()
    {
        $this->driver->getConfigPaths();
    }

    public function testAddGetConfigPath()
    {
        $this->driver->addConfigPath('test');
        $paths = $this->driver->getConfigPaths();
        $this->assertEquals(
            'test',
            $paths[0]
        );
    }

    public function testSetGetConfigPaths()
    {
        $this->driver->setConfigPaths(array(
            'test1', 'test2'
        ));
        $this->assertEquals(2, count($this->driver->getConfigPaths()));
    }

    /**
     * @expectedException Waf_Model_Mapper_Driver_Exception
     */
    public function testGetConfigTypeFailNotSet()
    {
        $this->driver->getConfigType();
    }

    public function testSetGetConfigType()
    {
        $this->driver->setConfigType('ini');
        $this->assertEquals(
            'ini',
            $this->driver->getConfigType()
        );
    }

    /**
     * @expectedException Waf_Model_Mapper_Driver_Exception
     */
    public function testGetConfigFailNotFound()
    {
        $this->driver->addConfigPath(dirname(__FILE__) . '/_files');
        $this->driver->setConfigType('ini');
        $this->driver->getConfig('NotFound');
    }

    public function testGetConfigByString()
    {
        $this->driver->addConfigPath(dirname(__FILE__) . '/_files');
        $this->driver->setConfigType('ini');
        $this->assertType(
            'array',
            $this->driver->getConfig('Waf_Model_Mapper_Driver_Zend_ConfigTest_TestEntity')
        );
    }

    public function testGetConfigByEntity()
    {
        $this->driver->addConfigPath(dirname(__FILE__) . '/_files');
        $this->driver->setConfigType('ini');
        $this->assertType(
            'array',
            $this->driver->getConfig(
                new Waf_Model_Mapper_Driver_Zend_ConfigTest_TestEntity
            )
        );
    }

    public function testGetMapper()
    {
        $this->driver->addConfigPath(dirname(__FILE__) . '/_files');
        $this->driver->setConfigType('ini');
        $this->assertType(
            'Waf_Model_Mapper',
            $this->driver->getMapper('Waf_Model_Mapper_Driver_Zend_ConfigTest_TestEntity')
        );
    }

    public function testMappedGetEntityName()
    {
        $this->driver->addConfigPath(dirname(__FILE__) . '/_files');
        $this->driver->setConfigType('ini');
        $mapped = $this->driver->getMapper('Waf_Model_Mapper_Driver_Zend_ConfigTest_TestEntity');
        $this->assertEquals(
            'Waf_Model_Mapper_Driver_Zend_ConfigTest_TestEntity',
            $mapped->getEntityName()
        );
    }

    public function testMappedGetProperty()
    {
        $this->driver->addConfigPath(dirname(__FILE__) . '/_files');
        $this->driver->setConfigType('ini');
        $mapped = $this->driver->getMapper('Waf_Model_Mapper_Driver_Zend_ConfigTest_TestEntity');
        $this->assertType(
            'Waf_Model_Mapper_Property_String',
            $mapped->getPropertyMapper()->getProperty('test')
        );
    }
}

class Waf_Model_Mapper_Driver_Zend_ConfigTest_TestEntity
    extends Waf_Model_Entity
{
    
}