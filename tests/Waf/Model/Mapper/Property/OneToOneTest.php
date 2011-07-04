<?php
class Waf_Model_Mapper_Property_OneToOneTest extends PHPUnit_Framework_TestCase
{
    public $property;
    public $entity;
    public $model;
    public $entityManager;
    public $repository;
    
    public function setUp()
    {
        $this->property      = new Waf_Model_Mapper_Property_OneToOne('entity');
        $this->entity        = $this->getMock('Waf_Model_Entity');
        $this->model         = $this->getMock('Waf_Model');
        $this->entityManager = $this->getMock('Waf_Model_EntityManager');
        $this->repository    = $this->getMock('Waf_Model_Repository', array(), array($this->entityManager, 'Entity'));

        $this->model
            ->expects($this->any())
            ->method('getEntityManager')
            ->will($this->returnValue($this->entityManager));

        $this->entityManager
            ->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($this->repository));
    }

    /**
     * @expectedException Waf_Model_Mapper_Exception
     */
    public function testGetRelatedEntityFailureNotSet()
    {
        $this->property->getRelatedEntity();
    }

    public function testSetGetRelatedEntityByString()
    {
        $class = get_class($this->entity);
        $this->property->setRelatedEntity($class);
        $this->assertEquals($class, $this->property->getRelatedEntity());
    }

    public function testSetRelatedEntityByInstance()
    {
        $this->property->setRelatedEntity($this->entity);
        $this->assertEquals(
            get_class($this->entity),
            $this->property->getRelatedEntity()
        );
    }

    public function testGetRepositoryFromEntityManager()
    {
        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('Entity'));

        $this->property->setModel($this->model);
        $this->property->setRelatedEntity('Entity');
        $this->property->getRepository();
    }

    public function testToStorage()
    {
        $this->entity
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));

        $this->assertEquals(
            1,
            $this->property->toStorage(array('entity'=>$this->entity))
        );
    }
    
//    public function testToStorageNull()
//    {
//        $this->entity
//            ->expects($this->once())
//            ->method('getId')
//            ->will($this->returnValue(null));
//
//        $this->assertEquals(
//            null,
//            $this->property->toStorage($this->entity)
//        );
//    }
    
    public function testToEntity()
    {
        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(1));

        $this->property->setModel($this->model);
        $this->property->setRelatedEntity('Entity');
        $this->property->toEntity(array('entity'=>1));
    }

//    public function testToEntityNull()
//    {
//        $this->property->setModel($this->model);
//        $this->property->setRelatedEntity('Entity');
//        $this->property->setNotNull(0);
//        $this->assertNull($this->property->toEntity(null));
//    }
}