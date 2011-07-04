<?php
require_once 'Waf/Registry/RegisterableAbstract.php';

/**
 * 
 * @category   Waf
 * @package    Waf_Registry
 * @subpackage UnitTests
 * @version    $Id: RegisterableAbstractTest.php 33 2010-01-27 11:44:48Z rick $
 */
class Waf_Registry_RegisterableAbstractTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_obj = new Waf_Registry_RegisterableAbstractTest_Temp;
    }

    public function tearDown()
    {
        Zend_Registry::_unsetInstance();
        unset($this->_obj);
    }
    
    public function testRegister()
    {
        $this->_obj->register();
        $myObj = Waf_Registry_RegisterableAbstractTest_Temp::getRegistered();
        $this->assertEquals($this->_obj, $myObj);
    }
    
    /**
     * 
     * @expectedException Waf_Exception
     */
    public function testGetRegisteredFail()
    {
        $myObj = Waf_Registry_RegisterableAbstractTest_Temp::getRegistered();
        $this->assertEquals($this->_obj, $myObj);
    }
    
    public function testIsRegisteredTrue()
    {
        $this->_obj->register();
        $this->assertTrue(Waf_Registry_RegisterableAbstractTest_Temp::isRegistered());
    }
    
    public function testIsRegisteredFalse()
    {
        $this->assertFalse(Waf_Registry_RegisterableAbstractTest_Temp::isRegistered());
    }

}

class Waf_Registry_RegisterableAbstractTest_Temp extends Waf_Registry_RegisterableAbstract
{

}

