<?php
/**
 * 
 *
 * @category   
 * @package    
 * @subpackage 
 * @version    $Id:$
 */
class Waf_Zend_Controller_Action_Helper_Param_Resolver_EntityTest
    extends PHPUnit_Framework_TestCase
{
    public $entityResolver;
    public $entityManager;
    public $repository;

    public function setUp()
    {
        $this->tearDown();
        $this->entityResolver = new Waf_Zend_Controller_Action_Helper_Param_Resolver_Entity();
        $this->entityManager  = $this->getMock('Waf_Model_EntityManager');
        $this->repository = $this->getMock('Waf_Model_Repository', array(), array($this->entityManager, 'Foo'));
    }

    public function tearDown()
    {
        Zend_Registry::_unsetInstance();
    }

    /**
     * @expectedException Waf_Zend_Controller_Action_Helper_Param_Resolver_Exception
     */
    public function testFailGetEntityName()
    {
        $this->entityResolver->getEntityName();
    }

    public function testSetGetEntityName()
    {
        $this->entityResolver->setEntityName('Model_Entity_Foo');
        $this->assertEquals(
            'Model_Entity_Foo',
            $this->entityResolver->getEntityName()
        );
    }

    /**
     * @expectedException Waf_Zend_Controller_Action_Helper_Param_Resolver_Exception
     */
    public function testFailGetResolveMethod()
    {
        $this->entityResolver->getMethod();
    }

    public function testSetGetResolveMethod()
    {
        $this->entityResolver->setMethod('WithId');
        $this->assertEquals(
            'WithId',
            $this->entityResolver->getMethod()
        );
    }

    public function testGetRepository()
    {
        $model = new Waf_Model();
        $model->register();
        $this->entityResolver->setEntityName('Bla');
        $this->assertType(
            'Waf_Model_Repository',
            $this->entityResolver->getRepository()
        );
    }

    public function testSetGetRepository()
    {
        $this->entityResolver->setRepository($this->repository);
        $this->assertSame(
            $this->repository,
            $this->entityResolver->getRepository()
        );
    }
}