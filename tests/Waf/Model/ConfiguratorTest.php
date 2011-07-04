<?php
class Waf_Model_ConfiguratorTest
    extends PHPUnit_Framework_TestCase
{
    public $testObject;
    
    public function setUp()
    {
        $this->testObject = new Waf_Model_ConfiguratorTest_TestObject();
    }
    
    public function getOptions()
    {
        return array(
            'foo' => 'test'
        );
    }
    
    public function getWrongOptions()
    {
        return array(
            'bogus' => 'test'
        );
    }
    
    public function getConfig()
    {
        return new Zend_Config($this->getOptions());
    }
    
    /**
     * @expectedException Waf_Model_Configurator_Exception
     */
    public function testSetOptionsNull()
    {
        Waf_Model_Configurator::setOptions(
            $this->testObject,
            null
        );
    }
    
    /**
     * @expectedException Waf_Model_Configurator_Exception
     */
    public function testSetOptionsZendConfig()
    {
        Waf_Model_Configurator::setOptions(
            $this->testObject,
            $this->getConfig()
        );
    }
    
    public function testSetWrongOptions()
    {
        Waf_Model_Configurator::setOptions(
            $this->testObject,
            $this->getWrongOptions()
        );
    }
    
    /**
     * @expectedException Waf_Model_Configurator_Exception
     */
    public function testSetNoObject()
    {
        Waf_Model_Configurator::setOptions('foo', $this->getOptions());
    }
    
    public function testSetOptions()
    {
        Waf_Model_Configurator::setOptions(
            $this->testObject,
            $this->getOptions()
        );
        
        $this->assertEquals('test', $this->testObject->getFoo());
    }
    
    public function testSetArray()
    {
        $options = array(
            'foo' => array(
                'bar',
                'baz'
            )
        );
        
        Waf_Model_Configurator::setOptions($this->testObject, $options);
        
        $this->assertType('array', $this->testObject->getFoo());
    }
    
    public function testSetConstructorOptions()
    {
        Waf_Model_Configurator::setConstructorOptions(
            $this->testObject,
            $this->getOptions()
        );
        
        $this->assertEquals('test', $this->testObject->getFoo());
    }
    
    public function testSetConstructorOptionsZendConfig()
    {
        Waf_Model_Configurator::setConstructorOptions(
            $this->testObject,
            $this->getConfig()
        );
    }
    
    public function testSetNoConstructorOptions()
    {
        Waf_Model_Configurator::setConstructorOptions(
            $this->testObject,
            null
        );
    }

    public function testSetConstructorOptionsSetsObjectOptionsIfAvailable()
    {
        $expected = array('hell'=>'yeah');
        $this->testObject = new Waf_Model_ConfiguratorTest_TestObjectWithOptions;
        Waf_Model_Configurator::setConstructorOptions(
            $this->testObject,
            $expected
        );
        $this->assertEquals($expected, $this->testObject->options);
    }
}

class Waf_Model_ConfiguratorTest_TestObject
{
    protected $_foo;

    public function setFoo($value)
    {
        $this->_foo = $value;
    }
    
    public function getFoo()
    {
        return $this->_foo;
    }
}
class Waf_Model_ConfiguratorTest_TestObjectWithOptions
{
    public function setOptions(array $data)
    {
        $this->options = $data;
    }
}