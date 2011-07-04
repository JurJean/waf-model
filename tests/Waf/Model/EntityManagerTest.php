<?php
class Waf_Model_EntityManagerTest extends PHPUnit_Framework_TestCase
{
    public $entityManager;
    public $model;
    public $entity;
    public $identityMap;
    public $unitOfWork;
    public $mapper;
    public $queryFilter;
    public $paginator;

    public function setUp()
    {
        $this->entityManager = new Waf_Model_EntityManager();
        $this->model         = $this->getMock('Waf_Model');
        $this->entity        = $this->getMock('Waf_Model_Entity');
        $this->identityMap   = $this->getMock('Waf_Model_EntityManager_IdentityMap');
        $this->unitOfWork    = $this->getMock('Waf_Model_UnitOfWork', array(), array($this->entityManager));
        $this->mapper        = $this->getMock('Waf_Model_Mapper');
        $this->queryFilter   = $this->getMock('Waf_Model_QueryFilter');
        $this->paginator     = $this->getMock('Zend_Paginator', array(), array(new Zend_Paginator_Adapter_Null));
        
        $this->model
            ->expects($this->any())
            ->method('getMapper')
            ->will($this->returnValue($this->mapper));

        $this->entityManager->setModel($this->model);
    }

    public function testGetDefaultUnitOfWork()
    {
        $this->assertType(
            'Waf_Model_UnitOfWork',
            $this->entityManager->getUnitOfWork()
        );
    }

    public function testSetGetUnitOfWork()
    {
        $this->entityManager->setUnitOfWork($this->unitOfWork);
        $this->assertSame(
            $this->unitOfWork,
            $this->entityManager->getUnitOfWork()
        );
    }

    public function testGetIdentityMapType()
    {
        $this->assertType(
            'Waf_Model_EntityManager_IdentityMap',
            $this->entityManager->getIdentityMap($this->entity)
        );
    }

    public function testGetIdentityMapSameEntity()
    {
        $this->assertSame(
            $this->entityManager->getIdentityMap($this->entity),
            $this->entityManager->getIdentityMap($this->entity)
        );
    }

    public function testSetGetIdentityMap()
    {
        $this->entityManager->setIdentityMap($this->identityMap, $this->entity);
        $this->assertSame(
            $this->identityMap,
            $this->entityManager->getIdentityMap($this->entity)
        );
    }

    public function testGetRepository()
    {
        $this->assertType(
            'Waf_Model_Repository',
            $this->entityManager->getRepository('Burp')
        );
    }

    public function testGetRepositoryByEntity()
    {
        $this->assertType(
            'Waf_Model_Repository',
            $this->entityManager->getRepository(
                $this->entity
            )
        );
    }

    /**
     * @expectedException Waf_Model_EntityManager_Exception
     */
    public function testGetRepositoryNotAnEntity()
    {
        $this->assertType(
            'Waf_Model_Repository',
            $this->entityManager->getRepository(
                $this->model
            )
        );
    }

    public function testGetMapperFromModel()
    {
        $this->model
            ->expects($this->once())
            ->method('getMapper')
            ->with($this->equalTo('Bar'));
        $this->entityManager->getMapper('Bar');
    }


    public function testGetMapperFromModelByEntity()
    {
        $this->model
            ->expects($this->once())
            ->method('getMapper')
            ->with($this->equalTo(get_class($this->entity)));
        $this->entityManager->getMapper($this->entity);
    }

    public function testPersistEntity()
    {
        $this->unitOfWork
            ->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($this->entity));
        $this->entityManager->setUnitOfWork($this->unitOfWork);
        $this->entityManager->persist($this->entity);
    }

    public function testPersistEntities()
    {
        $collection = new ArrayObject(array($this->entity));
        $this->unitOfWork
            ->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($this->entity));
        $this->entityManager->setUnitOfWork($this->unitOfWork);
        $this->entityManager->persist($collection);
    }

    public function testRemoveEntity()
    {
        $this->unitOfWork
            ->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($this->entity));
        $this->entityManager->setUnitOfWork($this->unitOfWork);
        $this->entityManager->setIdentityMap($this->identityMap, $this->entity);
        $this->entityManager->remove($this->entity);
    }

    public function testRemoveEntities()
    {
        $collection = new ArrayObject(array($this->entity));
        $this->unitOfWork
            ->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($this->entity));
        $this->entityManager->setUnitOfWork($this->unitOfWork);
        $this->entityManager->setIdentityMap($this->identityMap, $this->entity);
        $this->entityManager->remove($collection);
    }

    public function testFlush()
    {
        $this->unitOfWork
            ->expects($this->once())
            ->method('flush');
        $this->entityManager->setUnitOfWork($this->unitOfWork);
        $this->entityManager->flush();
    }

    public function testRefresh()
    {
        $this->mapper
            ->expects($this->once())
            ->method('refresh')
            ->with($this->equalTo($this->entity));
        $this->entityManager->refresh($this->entity);
    }

    public function testCount()
    {
        $this->mapper
            ->expects($this->once())
            ->method('count')
            ->with($this->equalTo($this->queryFilter));
        $this->entityManager->count(
            'Foo',
            $this->queryFilter
        );
    }

    public function testFind()
    {
        $this->mapper
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo($this->queryFilter))
            ->will($this->returnValue($this->entity));
        $this->assertSame(
            $this->entity,
            $this->entityManager->find(
                'Foo',
                $this->queryFilter
            )
        );
    }

    public function testFindNoResult()
    {
        $this->assertNull(
            $this->entityManager->find(
                'Foo',
                $this->queryFilter
            )
        );
    }

    public function testFindGetsManaged()
    {
        $this->mapper
            ->expects($this->once())
            ->method('find')
            ->will($this->returnValue($this->entity));
        $this->identityMap
            ->expects($this->once())
            ->method('manage')
            ->with($this->equalTo($this->entity));
        $this->entityManager->setIdentityMap($this->identityMap, $this->entity);
        $this->entityManager->find(
            'Foo',
            $this->queryFilter
        );
    }

    public function testFetch()
    {
        $collection = new ArrayObject(array($this->entity));
        $this->mapper
            ->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo($this->queryFilter))
            ->will($this->returnValue($collection));
        $this->assertSame(
            $collection,
            $this->entityManager->fetch(
                'Foo',
                $this->queryFilter
            )
        );
    }

    public function testFetchNoResults()
    {
        $this->assertNull(
            $this->entityManager->fetch(
                'Foo',
                $this->queryFilter
            )
        );
    }

    public function testFetchGetsManaged()
    {
        $collection = new ArrayObject(array($this->entity));
        $this->mapper
            ->expects($this->once())
            ->method('fetch')
            ->will($this->returnValue($collection));
        $this->identityMap
            ->expects($this->once())
            ->method('manage')
            ->with($this->equalTo($this->entity));
        $this->entityManager->setIdentityMap($this->identityMap, $this->entity);
        $this->entityManager->fetch(
            'Foo',
            $this->queryFilter
        );
    }

    public function testPaginate()
    {
        $this->mapper
            ->expects($this->once())
            ->method('paginate')
            ->with($this->equalTo($this->queryFilter))
            ->will($this->returnValue($this->paginator));
        $this->assertSame(
            $this->paginator,
            $this->entityManager->paginate(
                'Foo',
                $this->queryFilter
            )
        );
    }

//    public function testPersistProxiesToUnitOfWorkPersist()
//    {
//        $this->unitOfWork
//            ->expects($this->once())
//            ->method('persist')
//            ->with($this->equalTo($this->entity));
//        $this->entityManager->setUnitOfWork($this->unitOfWork);
//        $this->entityManager->persist($this->entity);
//    }
//
//    public function testPersistArrayProxiesToUnitOfWorkPersist()
//    {
//        $collection = array(
//            clone $this->entity,
//            clone $this->entity
//        );
//        $this->unitOfWork
//            ->expects($this->any())
//            ->method('persist')
//            ->with($this->entity);
//        $this->entityManager->setUnitOfWork($this->unitOfWork);
//        $this->entityManager->persist($this->entity);
//    }
//
//    public function testRemoveProxiesToUnitOfWorkRemove()
//    {
//        $this->unitOfWork
//            ->expects($this->once())
//            ->method('remove')
//            ->with($this->equalTo($this->entity));
//        $this->entityManager->setUnitOfWork($this->unitOfWork);
//        $this->entityManager->remove($this->entity);
//    }
//
//    public function testRemoveArrayProxiesToUnitOfWorkRemove()
//    {
//        $collection = array(
//            clone $this->entity,
//            clone $this->entity
//        );
//        $this->unitOfWork
//            ->expects($this->any())
//            ->method('remove')
//            ->with($this->entity);
//        $this->entityManager->setUnitOfWork($this->unitOfWork);
//        $this->entityManager->remove($this->entity);
//    }
//
//    public function testFlushProxiesToUnitOfWorkFlush()
//    {
//        $this->unitOfWork
//            ->expects($this->once())
//            ->method('flush');
//        $this->entityManager->setUnitOfWork($this->unitOfWork);
//        $this->entityManager->flush();
//    }
//
//    public function testFindReturnsMapperFindValue()
//    {
//        $this->mapper
//            ->expects($this->once())
//            ->method('find');
//        $this->entityManager->find('Baz', 1);
//    }
//
//    public function testFetchReturnsMapperFetchValue()
//    {
//        $this->mapper
//            ->expects($this->once())
//            ->method('fetch');
//        $this->entityManager->fetch('Baz', 1);
//    }
//
//    public function testEntityNotManaged()
//    {
//        $this->assertFalse($this->entityManager->isManaged($this->entity));
//    }
//
//    public function testPersistEntityIsManaged()
//    {
//        $this->entityManager->persist($this->entity);
//        $this->assertTrue($this->entityManager->isManaged($this->entity));
//    }
//
//    public function testRemoveEntityIsDetached()
//    {
//        $this->entityManager->persist($this->entity);
//        $this->entityManager->remove($this->entity);
//        $this->assertFalse($this->entityManager->isManaged($this->entity));
//    }
//
//    public function testFindEntityIsManaged()
//    {
//        $this->mapper
//            ->expects($this->once())
//            ->method('find')
//            ->will($this->returnValue($this->entity));
//        $this->assertTrue($this->entityManager->isManaged(
//            $this->entityManager->find('Foo', 'Bar'))
//        );
//    }
//
//    public function testFindTwiceByIdIsTheSame()
//    {
//        $this->mapper
//            ->expects($this->atLeastOnce())
//            ->method('find')
//            ->will($this->returnValue($this->entity));
//        $entity1 = $this->entityManager->find('Foo', 1);
//        $entity2 = $this->entityManager->find('Foo', 1);
//
//        $this->assertSame($entity1, $entity2);
//    }
//
//    public function testFindNull()
//    {
//        $this->mapper
//            ->expects($this->atLeastOnce())
//            ->method('find')
//            ->will($this->returnValue(null));
//        $this->assertNull($this->entityManager->find('Foo', null));
//    }
//
////    public function testFetchEntitiesAreManaged()
////    {
////        $entities = $this->entityManager->fetch('Entity', 'Yeah');
////        foreach ($entities as $entity) {
////            $this->assertTrue($this->entityManager->isManaged($entity));
////        }
////    }
//
//    public function testFetchNull()
//    {
//        $this->mapper
//            ->expects($this->atLeastOnce())
//            ->method('fetch')
//            ->will($this->returnValue(null));
//        $this->assertNull($this->entityManager->fetch('Foo', null));
//    }
//
//    public function testRefreshEntityGetsRefreshed()
//    {
//        $this->assertNotSame(
//            $this->entity,
//            $this->entityManager->refresh($this->entity)
//        );
//    }
//
//    public function testIsManagedById()
//    {
//        $this->entity
//            ->expects($this->atLeastOnce())
//            ->method('getId')
//            ->will($this->returnValue(1));
//        $this->entityManager->persist($this->entity);
//        $this->assertTrue(
//            $this->entityManager->isManagedById(
//                get_class($this->entity),
//                $this->entity->getId()
//            )
//        );
//    }
//
//    /**
//     * @expectedException Waf_Model_EntityManager_NotManagedException
//     */
//    public function testGetManagedByIdFailNotManaged()
//    {
//        $this->entity
//            ->expects($this->atLeastOnce())
//            ->method('getId')
//            ->will($this->returnValue(1));
//        $this->entityManager->getManagedById(
//            get_class($this->entity),
//            $this->entity->getId()
//        );
//    }
//
//    public function testGetManagedById()
//    {
//         $this->entity
//            ->expects($this->atLeastOnce())
//            ->method('getId')
//            ->will($this->returnValue(1));
//         $this->entityManager->persist($this->entity);
//         $this->assertSame(
//             $this->entity,
//             $this->entityManager->getManagedById(
//                 get_class($this->entity),
//                 $this->entity->getId()
//             )
//         );
//    }
}