<?php
class Waf_Model_RepositoryTest extends PHPUnit_Framework_TestCase
{
    public $repository;
    public $collection;
    public $queryFilter;

    public function setUp()
    {
        $this->entityManager = $this->getMock('Waf_Model_EntityManager');
        $this->collection    = $this->getMock('Waf_Model_Collection');
        $this->queryFilter   = $this->getMock('Waf_Model_QueryFilter');

        $this->entityManager
            ->expects($this->any())
            ->method('fetch')
            ->will($this->returnValue($this->collection));
        $this->collection
            ->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue(new ArrayObject(array())));
        
        $this->repository = new Waf_Model_Repository(
            $this->entityManager,
            'Foo'
        );
    }

    public function testGetEntityManager()
    {
        $this->assertEquals(
            $this->entityManager,
            $this->repository->getEntityManager()
        );
    }

    public function testGetDefaultQueryFilter()
    {
        $this->assertType(
            'Waf_Model_QueryFilter',
            $this->repository->getQueryFilter()
        );
    }

    public function testSetGetQueryFilter()
    {
        $this->repository->setQueryFilter($this->queryFilter);
        $this->assertSame(
            $this->queryFilter,
            $this->repository->getQueryFilter()
        );
    }

    public function testGetQueryFilterSetsDefaultNamespace()
    {
        $this->queryFilter
            ->expects($this->once())
            ->method('setNamespace');
        $this->repository->setQueryFilter($this->queryFilter);
        $this->repository->getQueryFilter();
    }

    public function testResetQueryFilterSameClass()
    {
        $this->repository->setQueryFilter($this->queryFilter);
        $this->repository->resetQueryFilter();
        $this->assertType(
            get_class($this->queryFilter),
            $this->repository->getQueryFilter()
        );
    }

    public function testResetQueryFilter()
    {
        $this->repository->setQueryFilter($this->queryFilter);
        $this->repository->resetQueryFilter();
        $this->assertNotSame(
            $this->queryFilter,
            $this->repository->getQueryFilter()
        );
    }

    public function testMagicCallAddsFilter()
    {
        $this->queryFilter
            ->expects($this->once())
            ->method('addFilter')
            ->with($this->equalTo('TestQueryFilter'));
        $this->repository->setQueryFilter($this->queryFilter);
        $this->repository->testQueryFilter();
    }

    public function testCountProxiesToEntityManager()
    {
        $this->entityManager
            ->expects($this->once())
            ->method('count')
            ->with(
                $this->equalTo('Foo'),
                $this->equalTo($this->queryFilter)
            );
        $this->repository->setQueryFilter($this->queryFilter);
        $this->repository->count();
    }

    public function testExistsFalse()
    {
        $this->entityManager
            ->expects($this->once())
            ->method('count')
            ->will($this->returnValue(0));
        $this->assertFalse($this->repository->exists());
    }

    public function testExistsTrue()
    {
        $this->entityManager
            ->expects($this->once())
            ->method('count')
            ->will($this->returnValue(10));
        $this->assertTrue($this->repository->exists());
    }

    public function testFindProxiesToEntityManager()
    {
        $this->entityManager
            ->expects($this->once())
            ->method('find')
            ->with(
                $this->equalTo('Foo'),
                $this->equalTo($this->queryFilter)
            );
        $this->repository->setQueryFilter($this->queryFilter);
        $this->repository->find();
    }

    public function testOverrideFindQueryFilter()
    {
        $this->entityManager
            ->expects($this->once())
            ->method('find')
            ->with(
                $this->equalTo('Foo'),
                $this->equalTo($this->queryFilter)
            );
        $this->repository->find($this->queryFilter);
    }

    public function testFetchProxiesToEntityManager()
    {
        $this->entityManager
            ->expects($this->once())
            ->method('fetch')
            ->with(
                $this->equalTo('Foo'),
                $this->equalTo($this->queryFilter)
            );
        $this->repository->setQueryFilter($this->queryFilter);
        $this->repository->fetch();
    }

    public function testOverrideFetchQueryFilter()
    {
        $this->entityManager
            ->expects($this->once())
            ->method('fetch')
            ->with(
                $this->equalTo('Foo'),
                $this->equalTo($this->queryFilter)
            );
        $this->repository->fetch($this->queryFilter);
    }

    public function testPaginateProxiesToEntityManager()
    {
        $paginator = new Zend_Paginator_Adapter_Array(array());

        $this->entityManager
            ->expects($this->any())
            ->method('paginate')
            ->with(
                $this->equalTo('Foo'),
                $this->equalTo($this->queryFilter)
            )
            ->will($this->returnValue($paginator));
        $this->repository->setQueryFilter($this->queryFilter);
        $this->repository->paginate();
    }
//
//    public function testPaginateCurrentQueryFilter()
//    {
//        $paginator = new Zend_Paginator_Adapter_Array(array());
//        $entityManager = $this->getMock('Waf_Model_EntityManager');
//
//        $repository = new Waf_Model_Repository(
//            $entityManager,
//            'Sex_Php_RockNRoll'
//        );
//
//        $repository->paginate();
//    }

    public function testGetIterator()
    {
        $this->entityManager
            ->expects($this->once())
            ->method('fetch');
        foreach ($this->repository as $item) {

        }
    }
}