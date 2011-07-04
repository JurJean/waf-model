<?php
class Waf_Model_ElementAbstractTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_model = $this->getMock('Waf_Model_ModelAbstract');
        $this->_element = new Waf_Model_ElementAbstractTest_Concrete();
    }

    public function tearDown()
    {
        unset($this->_element);
    }
    
    
    public function testGetModel()
    {
        $this->_element->setModel($this->_model);
        $this->assertEquals($this->_model, $this->_element->getModel());
    }
    
    /**
     * 
     * @expectedException Waf_Model_Exception
     */
    public function testGetModelFail()
    {
        $this->assertEquals($this->_model, $this->_element->getModel());
    }

    public function testConstructorOptions()
    {
        $element = new Waf_Model_ElementAbstractTest_Concrete(array(
            'model' => $this->_model
        ));
        $this->assertEquals($this->_model, $element->getModel());
    }
}

class Waf_Model_ElementAbstractTest_Concrete extends Waf_Model_ElementAbstract
{
}