<?php
class Waf_Zend_Controller_Action_Helper_EntityManagerTest
    extends PHPUnit_Framework_TestCase
{
    public $entityManagerHelper;

    public function setUp()
    {
        $this->entityManagerHelper = new Waf_Zend_Controller_Action_Helper_EntityManager();
    }

    public function tearDown()
    {
        Zend_Registry::_unsetInstance();
    }

    public function testGetEntityManagerDefault()
    {
        $model = new Waf_Model();
        $model->register();
        $model->setEntityManager(
            new Waf_Zend_Controller_Action_Helper_EntityManagerTest_TestEntityManager
        );

        $this->assertType(
            'Waf_Zend_Controller_Action_Helper_EntityManagerTest_TestEntityManager',
            $this->entityManagerHelper->getEntityManager()
        );
    }

    public function testMagicCallProxiesToEntityManager()
    {
        $entityManager = new Waf_Zend_Controller_Action_Helper_EntityManagerTest_TestEntityManager;
        $model = new Waf_Model();
        $model->register();
        $model->setEntityManager($entityManager);

        $this->assertEquals('sapperdedosiofoo', $this->entityManagerHelper->test('foo'));
    }
}

class Waf_Zend_Controller_Action_Helper_EntityManagerTest_TestEntityManager extends Waf_Model_EntityManager
{
    public $data;

    public function test($data)
    {
        return 'sapperdedosio' . $data;
    }
}