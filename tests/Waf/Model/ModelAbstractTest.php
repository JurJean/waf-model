<?php
class Waf_Model_ModelAbstractTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_model = new Waf_Model_ModelAbstractTest_Concrete;
    }

    public function tearDown()
    {
        unset($this->_model);
    }
    
    public function testConstructWithoutOptions()
    {
        $model = new Waf_Model_ModelAbstractTest_Concrete;
        $this->assertType('Waf_Model_ModelAbstract', $model);
    }
    
    public function testConstructWithOptions()
    {
        $model = new Waf_Model_ModelAbstractTest_Concrete(array('foo' => 1, 'bar' => 2));
        $this->assertEquals(2, $model->getOption('bar'));
    }

   /**
     * 
     * @expectedException Waf_Model_Exception
     */
    public function testConstructFail()
    {
        $model = new Waf_Model_ModelAbstractTest_Concrete('foo');
    }
    
    public function testSetGetOption()
    {
        $this->_model->setOption('foo', 'bar');
        $this->assertEquals('bar', $this->_model->getOption('foo'));
    }
    
    public function testGetOptionIfNotSet()
    {
        $this->assertEquals(null, $this->_model->getOption('foo'));
    }
    
    public function testHasOptionIfTrue()
    {
        $this->_model->setOption('foo', 'bar');
        $this->assertTrue($this->_model->hasOption('foo'));
    }
    
    public function testHasOptionIfFalse()
    {
        $this->assertFalse($this->_model->hasOption('foo'));
    }
    
    public function testSetGetOptionMixedCase()
    {
        $this->_model->setOption('FOO', 'bar');
        $this->assertEquals('bar', $this->_model->getOption('foo'));
    }
    
    public function testSetGetOptionMixedCase2()
    {
        $this->_model->setOption('foo', 'bar');
        $this->assertEquals('bar', $this->_model->getOption('FOO'));
    }
    
    public function testRegister()
    {
        $this->_model->register();
        $this->assertEquals($this->_model, Waf_Model_ModelAbstractTest_Concrete::getRegistered());
    }
    
    public function testNotRegistered()
    {
        Zend_Registry::_unsetInstance();
        $this->assertFalse(Waf_Model_ModelAbstractTest_Concrete::isRegistered());
    }
}

class Waf_Model_ModelAbstractTest_Concrete extends Waf_Model_ModelAbstract
{
}
