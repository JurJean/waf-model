<?php
class Waf_Model_MapperTest extends PHPUnit_Framework_TestCase
{
    public $mapper;
    public $storageAdapter;
    public $storageHandler;
    public $entity;
    public $entityManager;
    public $identityMap;
    public $identity;
    public $propertyMapper;
    public $model;

    public function setUp()
    {
        $this->mapper           = new Waf_Model_Mapper();
        $this->storageHandler   = $this->getMock('Waf_Model_Storage');
        $this->storageAdapter   = $this->getMock('Waf_Model_Storage_Adapter_ZendDb');
        $this->entity           = $this->getMock('Waf_Model_Entity');
        $this->entityManager    = $this->getMock('Waf_Model_EntityManager');
        $this->identityMap      = $this->getMock('Waf_Model_EntityManager_IdentityMap');
        $this->identity         = $this->getMock('Waf_Model_Mapper_Identity');
        $this->propertyMapper   = $this->getMock('Waf_Model_Mapper_Property');
        $this->model            = $this->getMock('Waf_Model');
        $this->model
            ->expects($this->any())
            ->method('getStorageHandler')
            ->will($this->returnValue($this->storageHandler));

        $this->mapper->setModel($this->model);
        $this->entityManager
            ->expects($this->any())
            ->method('getIdentityMap')
            ->will($this->returnValue($this->identityMap));
    }

    /**
     * @expectedException Waf_Model_Mapper_Exception
     */
    public function testGetEntityNameFailureNotSet()
    {
        $this->mapper->getEntityName();
    }

    public function testSetGetEntityName()
    {
        $this->mapper->setEntityName('Entity');
        $this->assertEquals(
            'Entity',
            $this->mapper->getEntityName()
        );
    }
    
    public function testSetStorageAdapterByString()
    {
        $this->storageHandler
            ->expects($this->once())
            ->method('getAdapter')
            ->with($this->equalTo('test'))
            ->will($this->returnValue($this->storageAdapter));
        $this->mapper->setStorageAdapter('test');
        $this->assertSame(
            $this->storageAdapter,
            $this->mapper->getStorageAdapter()
        );
    }

    public function testGetStorageReferenceDefaultsToFilteredEntityName()
    {
        $this->mapper->setEntityName('FilteredEntity');
        $this->assertEquals(
            'filtered_entity',
            $this->mapper->getStorageReference()
        );
    }

    public function testSetGetStorageReference()
    {
        $this->mapper->setStorageReference('entity');
        $this->assertEquals(
            'entity',
            $this->mapper->getStorageReference()
        );
    }

    public function testGetIdentityDefault()
    {
        $this->assertType(
            'Waf_Model_Mapper_Identity',
            $this->mapper->getIdentity()
        );
    }

    public function testSetGetIdentity()
    {
        $this->mapper->setIdentity($this->identity);
        $this->assertSame(
            $this->identity,
            $this->mapper->getIdentity()
        );
    }

    public function testSetIdentityByArrayType()
    {
        $this->mapper->setIdentity(array());
        $this->assertType(
            'Waf_Model_Mapper_Identity',
            $this->mapper->getIdentity()
        );
    }

    public function testSetModelPassesToIdentity()
    {
        $this->identity
            ->expects($this->once())
            ->method('setModel')
            ->with($this->equalTo($this->model));
        $this->mapper->setIdentity($this->identity);
        $this->mapper->setModel($this->model);
    }

    public function testGetPropertyMapperDefault()
    {
        $this->assertType(
            'Waf_Model_Mapper_Property',
            $this->mapper->getPropertyMapper()
        );
    }

    public function testSetGetPropertyMapper()
    {
        $this->mapper->setPropertyMapper($this->propertyMapper);
        $this->assertSame(
            $this->propertyMapper,
            $this->mapper->getPropertyMapper()
        );
    }

    public function testSetPropertyMapperByArrayType()
    {
        $this->mapper->setPropertyMapper(array());
        $this->assertType(
            'Waf_Model_Mapper_Property',
            $this->mapper->getPropertyMapper()
        );
    }

    public function testSetModelPassesToPropertyMapper()
    {
        $this->propertyMapper
            ->expects($this->any())
            ->method('setModel')
            ->with($this->equalTo($this->model));
        $this->mapper->setPropertyMapper($this->propertyMapper);
        $this->mapper->setModel($this->model);
    }

    /**
     * @expectedException Waf_Model_Mapper_Exception
     */
    public function testToStorageFailsWrongEntity()
    {
        $this->mapper->setEntityName('Entity');
        $this->mapper->toStorage($this->entity);
    }

    public function testToStorage()
    {
        $this->identity
            ->expects($this->any())
            ->method('toStorage')
            ->will($this->returnValue(array('id' => 1)));
        $this->propertyMapper
            ->expects($this->any())
            ->method('toStorage')
            ->will($this->returnValue(array('property' => 'value')));

        $this->mapper->setModel($this->model);
        $this->mapper->setEntityName(get_class($this->entity));
        $this->mapper->setIdentity($this->identity);
        $this->mapper->setPropertyMapper($this->propertyMapper);
        
        $expected = array(
            'id'       => 1,
            'property' => 'value'
        );
        $this->assertEquals(
            $expected,
            $this->mapper->toStorage($this->entity)
        );
    }

    public function testFindChecksEntityId()
    {
        $this->identityMap
            ->expects($this->once())
            ->method('contains')
            ->with($this->equalTo(1))
            ->will($this->returnValue(true));
        $this->identityMap
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo(1))
            ->will($this->returnValue($this->entity));
        $this->entityManager
            ->expects($this->atLeastOnce())
            ->method('getIdentityMap')
            ->with($this->equalTo(get_class($this->entity)));
        $this->storageAdapter
            ->expects($this->once())
            ->method('find')
            ->will($this->returnValue(
                array(
                    'id' => 1
                )
            ));
        $this->mapper->setEntityManager($this->entityManager);
        $this->mapper->setStorageAdapter($this->storageAdapter);
        $this->mapper->setEntityName(get_class($this->entity));
        $this->assertSame(
            $this->entity,
            $this->mapper->find(1)
        );
    }
}