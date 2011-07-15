<?php
class Waf_Zend_Controller_ActionTest
    extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // Make sure only our fake Helper is available
        Zend_Controller_Action_HelperBroker::resetHelpers();
        Zend_Controller_Action_HelperBroker::addHelper(new Waf_Zend_Controller_ActionTest_TestHelper);
        
        $this->controller = new Waf_Zend_Controller_Action(
            $this->getMock('Zend_Controller_Request_Abstract'),
            $this->getMock('Zend_Controller_Response_Abstract')
        );
        
    }
    
    public function tearDown()
    {
        unset($this->controller);
    }
    
    /**
     * @expectedException Zend_Controller_Action_Exception
     */
    public function testMagicGetterFailOnNoHelper()
    {
        $this->controller->test;
    }
    
    /**
     * @expectedException Zend_Controller_Action_Exception
     */
    public function testMagicMethodFailOnNoHelperAndNothingElseEither()
    {
        $this->controller->test('foo');
    }
    
    public function testUndefinedPropertyProxiesToHelper()
    {
        $this->assertType(
            'Waf_Zend_Controller_ActionTest_TestHelper',
            $this->controller->TestHelper
        );
        
    }

    public function testUndefinedMethodProxiesToHelpersDirect()
    {
        $this->assertTrue($this->controller->TestHelper());
    }

    /**
     * @expectedException Zend_Controller_Action_Exception
     */
    public function testCallToUndefinedActionThrowsDispatcherException()
    {
        $this->controller->undefinedAction();
    }
}

class Waf_Zend_Controller_ActionTest_TestHelper
    extends Zend_Controller_Action_Helper_Abstract
{
    public function direct()
    {
        return true;
    }
}