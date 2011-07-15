<?php
/**
 * ParamTest description
 *
 * @author     ASOclusive
 * @category   
 * @package    
 * @subpackage 
 * @version    $Id:$
 */
class Waf_Zend_Application_Resource_ParamTest
    extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Zend_Controller_Front::getInstance()->resetInstance();
        $this->resource = new Waf_Zend_Application_Resource_Param();
    }

    public function tearDown()
    {
        Zend_Controller_Action_HelperBroker::resetHelpers();
    }

    public function testInitAddsHelperToBroker()
    {
        $this->resource->init();
        $this->assertTrue(
            Zend_Controller_Action_HelperBroker::hasHelper('Param')
        );
    }
}