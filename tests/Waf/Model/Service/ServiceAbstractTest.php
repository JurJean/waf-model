<?php
class Waf_Model_ServiceAbstractTest extends PHPUnit_Framework_TestCase
{
    public $abstractService;

    public $model;

    public $entityManager;

    public function setUp()
    {
        $this->abstractService = new Tests_Waf_Model_ServiceTestClass;
        $this->model = $this->getMock("Waf_Model");

        $this->abstractService->setModel(
            $this->model
        );

        $this->entityManager = $this->getMock("Waf_Model_EntityManager");
    }

    public function testGetEntityManager()
    {
        $this->model
             ->expects($this->once())
             ->method('getEntityManager')
             ->will($this->returnValue($this->entityManager));

        $this->assertSame(
            $this->abstractService->getEntityManager(),
            $this->entityManager
        );
    }


    // @todo : create test
    public function testGetUnitOfWork()
    {
    }

    // @todo : create test
    public function scheduleQuery($query)
    {
    }

    // @todo : create test
    public function testGetRepository()
    {
    }
}


class Tests_Waf_Model_ServiceTestClass extends Waf_Model_Service_ServiceAbstract
{

}