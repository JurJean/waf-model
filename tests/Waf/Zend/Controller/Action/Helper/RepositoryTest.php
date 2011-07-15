<?php
class Waf_Zend_Controller_Action_Helper_RepositoryTest
    extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->repositoryHelper = new Waf_Zend_Controller_Action_Helper_Repository();
    }

    public function tearDown()
    {
        unset($this->repositoryHelper);
        Zend_Registry::_unsetInstance();
        Zend_Controller_Front::getInstance()->resetInstance();
    }

    public function testGetEntityManagerDefault()
    {
        $model = new Waf_Model();
        $model->register();
        $model->setEntityManager(
            new Waf_Zend_Controller_Action_Helper_RepositoryTest_TestEntityManager
        );

        $this->assertType(
            'Waf_Zend_Controller_Action_Helper_RepositoryTest_TestEntityManager',
            $this->repositoryHelper->getEntityManager()
        );
    }

//    public function testGetRepository()
//    {
//        $model = new Waf_Model();
//        $model->register();
//        $model->setEntityManager(
//            new Waf_Zend_Controller_Action_Helper_RepositoryTest_TestEntityManager
//        );
//
//        $this->assertType(
//            'Waf_Zend_Controller_Action_Helper_RepositoryTest_TestRepository',
//            $this->repositoryHelper->getRepository('Foo')
//        );
//    }

    public function testGetEntityName()
    {
        $this->assertEquals(
            'FooBar', $this->repositoryHelper->getEntityName('FooBar')
        );
    }

    public function testGetEntityNameFromModule()
    {
        $this->assertEquals(
            'Default_Model_Entity_FooBar',
            $this->repositoryHelper->getEntityName('FooBar', 'default')
        );
    }

    public function testGetEntityNameFromController()
    {
        $request = new Zend_Controller_Request_Simple();
        $request->setControllerName('test');
        $request->setModuleName('default');

        Zend_Controller_Front::getInstance()->setRequest($request);
        
        $this->assertEquals(
            'Default_Model_Entity_Test',
            $this->repositoryHelper->getEntityName()
        );
    }

    public function testGetRepositorySetsQueryFilterNamespace()
    {
        $model = new Waf_Model();
        $model->register();

        $repository = $this->repositoryHelper->getRepository('QueryTest', 'test');
        $this->assertEquals(
            'Test_Model_QueryFilter_QueryTest',
            $repository->getQueryFilter()->getNamespace()
        );
    }

    public function testDirectGetsRepository()
    {
        $model = new Waf_Model();
        $model->register();

        $this->assertType(
            'Waf_Model_Repository',
            $this->repositoryHelper->direct('DirectTest', 'test')
        );
    }

    public function testMagicCallProxiesToRepositoryMethod()
    {
        $model = new Waf_Model();
        $model->register();
        $model->setEntityManager(
            new Waf_Zend_Controller_Action_Helper_RepositoryTest_TestEntityManager
        );
        
        $request = new Zend_Controller_Request_Simple();
        $request->setControllerName('test');
        $request->setModuleName('default');

        Zend_Controller_Front::getInstance()->setRequest($request);

        $this->assertType(
            'Waf_Zend_Controller_Action_Helper_RepositoryTest_TestRepository',
            $this->repositoryHelper->testMagicCall()
        );
    }
}

class Waf_Zend_Controller_Action_Helper_RepositoryTest_TestEntityManager
    extends Waf_Model_EntityManager
{
    public function getRepository($entityName)
    {
        return new Waf_Zend_Controller_Action_Helper_RepositoryTest_TestRepository($this, $entityName);
    }
}

class Waf_Zend_Controller_Action_Helper_RepositoryTest_TestRepository
    extends Waf_Model_Repository
{
    public function testMagicCall()
    {
        return $this;
    }
}