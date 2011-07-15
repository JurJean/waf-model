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
class Waf_Zend_Controller_Action_Helper_ParamTest
    extends PHPUnit_Framework_TestCase
{
    public $param;
    public $request;
    public $resolver;
    
    public function setUp()
    {
        $this->param = new Waf_Zend_Controller_Action_Helper_Param();
        $this->request = $this->getMockForAbstractClass(
            'Zend_Controller_Request_Abstract'
        );
//        $this->resolver = $this->getMockForAbstractClass(
//            'Waf_Zend_Controller_Action_Helper_Param_Resolver_ResolverAbstract'
//        );
        $this->resolver = $this->getMock(
            'Waf_Zend_Controller_Action_Helper_Param_Resolver_Entity'
        );
        
        Zend_Controller_Front::getInstance()->resetInstance();
        Zend_Controller_Front::getInstance()->setRequest($this->request);
    }

    public function tearDown()
    {
        Zend_Controller_Front::getInstance()->resetInstance();
    }

    /**
     * @expectedException Waf_Zend_Controller_Action_Helper_Param_Exception
     */
    public function testFailGetResolver()
    {
        $this->param->getResolver('page');
    }

    public function testSetGetResolver()
    {
        $this->param->setResolver('foo', $this->resolver);
        $this->assertSame(
            $this->resolver,
            $this->param->getResolver('foo')
        );
    }

    /**
     * @expectedException Waf_Zend_Controller_Action_Helper_Param_Exception
     */
    public function testFailParamNotExists()
    {
        $this->assertEquals(
            'bar',
            $this->param->getParam('foo')
        );
    }

    public function testResolveParamValue()
    {
        $this->resolver
            ->expects($this->once())
            ->method('resolve')
            ->will($this->returnValue('bar'));
        $this->request->setParam('foo', true);
        $this->param->setResolver('foo', $this->resolver);
        $this->param->getParam('foo');
    }

    public function testResolveParam()
    {
        $this->resolver
            ->expects($this->once())
            ->method('resolve')
            ->will($this->returnValue('bar'));
        $this->request->setParam('foo', true);
        $this->param->setResolver('foo', $this->resolver);
        $this->assertEquals(
            'bar',
            $this->param->getParam('foo')
        );
    }

    public function testSetResolverByOption()
    {
        $this->param->setOptions(array(
            'resolvers' => array(
                'test' => array(
                    'type' => 'Entity'
                )
            )
        ));
        $this->assertType(
            'Waf_Zend_Controller_Action_Helper_Param_Resolver_Entity',
            $this->param->getResolver('test')
        );
    }
}