<?php
class Waf_Model_UnitOfWorkTest extends PHPUnit_Framework_TestCase
{
    public $mapper;
    public $unitOfWork;
    public $queryCache;
    public $entity;
    public $storage;

    public function setUp()
    {
        $this->model           = $this->getMock('Waf_Model');
        $this->entityManager   = $this->getMock('Waf_Model_EntityManager');
        $this->unitOfWork      = new Waf_Model_UnitOfWork($this->entityManager);
        $this->entity          = new Waf_Model_UnitOfWorkTest_TestEntity;
        $this->identity        = $this->getMock('Waf_Model_Mapper_Identity');
        $this->mapper          = $this->getMock('Waf_Model_Mapper');
        $this->storage         = $this->getMock('Waf_Model_Storage_Adapter_ZendDb');
        $this->entityGenerator = $this->getMock('Waf_Model_EntityGenerator_Reflect', array(), array('Waf_Model_UnitOfWorkTest_TestEntity'));
        
        $this->entityGenerator
            ->expects($this->any())
            ->method('generateState')
            ->will($this->returnValue(
                array('_id' => 1)
            ));
        $this->entityGenerator
            ->expects($this->any())
            ->method('generateEntity')
            ->will($this->returnValue($this->entity));
        $this->mapper
            ->expects($this->any())
            ->method('getStorageAdapter')
            ->will($this->returnValue($this->storage));
        $this->mapper
            ->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($this->identity));
        $this->mapper
            ->expects($this->any())
            ->method('getEntityGenerator')
            ->will($this->returnValue($this->entityGenerator));
        $this->entityManager
            ->expects($this->any())
            ->method('getMapper')
            ->will($this->returnValue($this->mapper));
    }

    public function testGetEntityManager()
    {
        $this->assertType(
            get_class($this->entityManager),
            $this->unitOfWork->getEntityManager()
        );
    }

    public function testGetMapper()
    {
        $this->assertSame(
            $this->mapper,
            $this->unitOfWork->getMapper('Test')
        );
    }

    public function testIsNotScheduledQuery()
    {
        $this->assertFalse(
            $this->unitOfWork->isScheduledQuery('foo')
        );
    }

    public function testScheduleQueryIsScheduled()
    {
        $this->unitOfWork->scheduleQuery('foo');
        $this->assertTrue(
            $this->unitOfWork->isScheduledQuery('foo')
        );
    }

    public function testIsNotScheduledForInsert()
    {
        $this->assertFalse(
            $this->unitOfWork->isScheduledForInsert($this->entity)
        );
    }

    public function testScheduleForInsertIsScheduled()
    {
        $this->unitOfWork->scheduleForInsert($this->entity);
        $this->assertTrue(
            $this->unitOfWork->isScheduledForInsert($this->entity)
        );
    }

    public function testIsNotScheduledForUpdate()
    {
        $this->assertFalse(
            $this->unitOfWork->isScheduledForUpdate($this->entity)
        );
    }

    public function testScheduleForUpdateIsScheduled()
    {
        $this->unitOfWork->scheduleForUpdate($this->entity);
        $this->assertTrue(
            $this->unitOfWork->isScheduledForUpdate($this->entity)
        );
    }

    public function testIsNotScheduledForDelete()
    {
        $this->assertFalse(
            $this->unitOfWork->isScheduledForDelete($this->entity)
        );
    }

    public function testScheduleForDeleteIsScheduled()
    {
        $this->unitOfWork->scheduleForDelete($this->entity);
        $this->assertTrue(
            $this->unitOfWork->isScheduledForDelete($this->entity)
        );
    }

    public function testFlushHandlesQueries()
    {
        $this->model
            ->expects($this->once())
            ->method('getStorageAdapter')
            ->will($this->returnValue($this->storage));
        $this->entityManager
            ->expects($this->once())
            ->method('getModel')
            ->will($this->returnValue($this->model));
        $this->storage
            ->expects($this->once())
            ->method('query');
        $this->unitOfWork->scheduleQuery('foo');
        $this->unitOfWork->flush();
        $this->assertFalse($this->unitOfWork->isScheduledQuery('foo'));
    }

    public function testFlushHandlesInserts()
    {
        $this->storage
            ->expects($this->once())
            ->method('insert');
        $this->unitOfWork->scheduleForInsert($this->entity);
        $this->unitOfWork->flush();
        $this->assertFalse($this->unitOfWork->isScheduledForInsert($this->entity));
    }

    public function testFlushHandlesUpdates()
    {
        $this->storage
            ->expects($this->once())
            ->method('update');
        $this->unitOfWork->scheduleForUpdate($this->entity);
        $this->unitOfWork->flush();
        $this->assertFalse($this->unitOfWork->isScheduledForUpdate($this->entity));
    }

    public function testFlushHandlesDeletes()
    {
        $this->storage
            ->expects($this->once())
            ->method('delete');
        $this->unitOfWork->scheduleForDelete($this->entity);
        $this->unitOfWork->flush();
        $this->assertFalse($this->unitOfWork->isScheduledForDelete($this->entity));
    }

    public function testRemoveIsScheduledForDelete()
    {
        $this->unitOfWork->remove($this->entity);
        $this->assertTrue($this->unitOfWork->isScheduledForDelete($this->entity));
    }

    public function testPersistNewEntityIsScheduledForInsert()
    {
        $this->unitOfWork->persist($this->entity);
        $this->assertTrue($this->unitOfWork->isScheduledForInsert($this->entity));
    }

    public function testPersistNewEntityIsNotScheduledForUpdate()
    {
        $this->unitOfWork->persist($this->entity);
        $this->assertFalse($this->unitOfWork->isScheduledForUpdate($this->entity));
    }

    public function testPersistExistingEntityIsScheduledForUpdate()
    {
        $entity = $this->getMock('Waf_Model_Entity');
        $entity
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(5));

        $this->unitOfWork->persist($entity);
        $this->assertTrue($this->unitOfWork->isScheduledForUpdate($entity));
    }

    public function testPersistExistingEntityIsNotScheduledForInsert()
    {
        $entity = $this->getMock('Waf_Model_Entity');
        $entity
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(5));

        $this->unitOfWork->persist($entity);
        $this->assertFalse($this->unitOfWork->isScheduledForInsert($entity));
    }


    public function testFlushBeginsTransaction()
    {
        $this->storage
            ->expects($this->once())
            ->method('beginTransaction');
        $this->unitOfWork->scheduleForInsert($this->entity);
        $this->unitOfWork->flush();
    }

    public function testFlushCommitsTransactions()
    {
        $this->storage
            ->expects($this->once())
            ->method('commitTransaction');
        $this->unitOfWork->persist($this->entity);
        $this->unitOfWork->flush();
    }

    public function testHasScheduledInsertsTrue()
    {
        $this->unitOfWork->scheduleForInsert($this->entity);
        $this->assertTrue($this->unitOfWork->hasScheduledInserts());
    }
    public function testHasScheduledInsertsFalse()
    {
        $this->assertFalse($this->unitOfWork->hasScheduledInserts());
    }
    public function testHasScheduledUpdatesTrue()
    {
        $this->unitOfWork->scheduleForUpdate($this->entity);
        $this->assertTrue($this->unitOfWork->hasScheduledUpdates());
    }
    public function testHasScheduledUpdateFalse()
    {
        $this->assertFalse($this->unitOfWork->hasScheduledUpdates());
    }
    public function testHasScheduledDeletesTrue()
    {
        $this->unitOfWork->scheduleForDelete($this->entity);
        $this->assertTrue($this->unitOfWork->hasScheduledDeletes());
    }
    public function testHasScheduledDeletesFalse()
    {
        $this->assertFalse($this->unitOfWork->hasScheduledDeletes());
    }
//    public function testPreInsert()
//    {
//        $entity = new Waf_Model_UnitOfWorkTest_TestEntity();
//        $this->unitOfWork->scheduleForInsert($entity);
//        $this->unitOfWork->flush();
//        $this->assertTrue($entity->preInsert);
//    }

//    public function testPostInsert()
//    {
//        $entity = new Waf_Model_UnitOfWorkTest_TestEntity();
//        $this->unitOfWork->scheduleForInsert($entity);
//        $this->unitOfWork->flush();
//        $this->assertTrue($entity->postInsert);
//    }
//
//    public function testExecuteInsertsNotifiesCache()
//    {
//        $this->unitOfWork->getMapper('test')->setQueryCache($this->queryCache);
//        $this->unitOfWork->scheduleForInsert($this->entity);
//        $this->unitOfWork->flush();
//        $this->assertTrue($this->queryCache->notified);
//    }
}

class Waf_Model_UnitOfWorkTest_TestEntityManager extends Waf_Model_EntityManager
{
    protected $_mapper;

    public function getMapper($entityName)
    {
        if (null === $this->_mapper) {
            if (is_object($entityName)) {
                $entityName = get_class($entityName);
            }

            $this->_mapper = new Waf_Model_UnitOfWorkTest_TestMapper();
            $this->_mapper->setModel(new Waf_Model);
            $this->_mapper->setEntityName($entityName);
            $this->_mapper->setIdentity(new Waf_Model_UnitOfWorkTest_TestIdentity());
        }
        
        return $this->_mapper;
    }
}

class Waf_Model_UnitOfWorkTest_TestMapper extends Waf_Model_Mapper
{
    protected $_storageAdapter;

    public function getStorageAdapter()
    {
        if (null === $this->_storageAdapter) {
            $this->_storageAdapter = new Waf_Model_UnitOfWorkTest_TestStorageAdapter();
        }

        return $this->_storageAdapter;
    }

    public function toEntity($state)
    {
        return array();
    }

    public function toStorage($state)
    {
        return array();
    }
}

class Waf_Model_UnitOfWorkTest_TestEntity extends Waf_Model_Entity
    implements Waf_Model_Hookable_PreInsert,
               Waf_Model_Hookable_PostInsert,
               Waf_Model_Hookable_PreUpdate,
               Waf_Model_Hookable_PostUpdate,
               Waf_Model_Hookable_PreDelete,
               Waf_Model_Hookable_PostDelete
{
    protected $_id;
    public $preInsert;
    public $postInsert;
    public $preUpdate;
    public $postUpdate;
    public $preDelete;
    public $postDelete;
    public $prePersist;
    public $postPersist;

    public function __sleep()
    {
        return array('_id');
    }

    public function setId($id = null)
    {
        $this->_id = $id;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function preInsert(Waf_Model_EntityManager $entityManager)
    {
        $this->preInsert = true;
    }

    public function postInsert(Waf_Model_EntityManager $entityManager)
    {
        $this->postInsert = true;
    }

    public function preUpdate(Waf_Model_EntityManager $entityManager)
    {
        $this->preUpdate = true;
    }

    public function postUpdate(Waf_Model_EntityManager $entityManager)
    {
        $this->postUpdate = true;
    }

    public function preDelete(Waf_Model_EntityManager $entityManager)
    {
        $this->preDelete = true;
    }

    public function postDelete(Waf_Model_EntityManager $entityManager)
    {
        $this->postDelete = true;
    }
}

class Waf_Model_UnitOfWorkTest_TestIdentity extends Waf_Model_Mapper_Identity
{
    public $storage;

    public function toStorage($value)
    {
        return $this->storage;
    }
}

class Waf_Model_UnitOfWorkTest_TestStorageAdapter extends Waf_Model_Storage_AdapterAbstract
{
    public $transactionBegin    = false;
    public $transactionCommit   = false;
    public $transactionRollback = false;
    public $insert = false;
    public $update = false;
    public $delete = false;

    public function createQuery($name)
    {

    }

    public function beginTransaction()
    {
        $this->transactionBegin = true;
    }

    public function commitTransaction()
    {
        $this->transactionCommit = true;
    }

    public function rollbackTransaction()
    {
        $this->transactionRollback = true;
    }
    
    public function query($query)
    {
        
    }

    public function insert($name, $data)
    {
        $this->insert = true;
        return 1;
    }

    public function update($name, $data, $queryFilter)
    {
        $this->update = true;
    }

    public function delete($name, $queryFilter)
    {
        $this->delete = true;
    }

    public function count($name, $queryFilter)
    {

    }

    public function find($name, $queryFilter)
    {

    }

    public function fetch($name, $queryFilter)
    {

    }

    public function paginate($name, $queryFilter)
    {
        
    }
}
