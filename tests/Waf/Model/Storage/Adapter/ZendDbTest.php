<?php
require_once 'PHPUnit/Extensions/Database/TestCase.php';

class Waf_Model_Storage_Adapter_ZendDBTest extends Zend_Test_PHPUnit_DatabaseTestCase
{
    private $_connectionMock;

    protected function getConnection()
    {
        if (null === $this->_connectionMock) {
            $adapter = Zend_Db::factory('pdo_sqlite', array(
                'dbname'=>':memory:'
            ));
            $adapter->query('DROP TABLE IF EXISTS test');
            $adapter->query('CREATE TABLE test (id INTEGER PRIMARY KEY, name)');
            $this->_connectionMock = $this->createZendDbConnection($adapter, 'zenddbtest');
        }

        return $this->_connectionMock;
    }

    protected function getDataSet()
    {
        return $this->createFlatXmlDataSet(dirname(__FILE__).'/_files/ZendDb.xml');
    }
    
    /**
     * 
     * @expectedException Zend_Db_Adapter_Exception
     */
    public function testConstructWithoutParams()
    {
        $adapter = new Waf_Model_Storage_Adapter_ZendDb('Pdo_Mysql');
        $this->assertType('Waf_Model_Storage_AdapterAbstract', $adapter);
    }
    
    public function testConstructWithMinimalParams()
    {
        $adapter = new Waf_Model_Storage_Adapter_ZendDb('Pdo_MySql', array(
            'host'           => '127.0.0.1',
            'username'       => 'test',
            'password'       => 'test',
            'dbname'         => 'test'));
        $this->assertType('Waf_Model_Storage_AdapterAbstract', $adapter);
    }
    
    public function testIfConnectionSet()
    {
        $adapter = new Waf_Model_Storage_Adapter_ZendDb('Pdo_MySql', array(
            'host'           => '127.0.0.1',
            'username'       => 'test',
            'password'       => 'test',
            'dbname'         => 'test'));
        $this->assertType('Zend_Db_Adapter_Abstract', $adapter->getConnection());
    }
    
    public function testConnectionTypeIfPdoMysql()
    {
        $adapter = new Waf_Model_Storage_Adapter_ZendDb('Pdo_MySql', array(
            'host'           => '127.0.0.1',
            'username'       => 'test',
            'password'       => 'test',
            'dbname'         => 'test'));
        $this->assertType('Zend_Db_Adapter_Pdo_Mysql', $adapter->getConnection());
    }
    
    public function testConstructWithConnectionInstance()
    {
        $connection = $this->getConnection()->getConnection();
        $adapter = new Waf_Model_Storage_Adapter_ZendDb($connection);
        $this->assertEquals($connection, $adapter->getConnection());
    }

    public function testCreateQueryIsDbSelect()
    {
        $adapter = new Waf_Model_Storage_Adapter_ZendDb('Pdo_MySql', array(
            'host'           => '127.0.0.1',
            'username'       => 'test',
            'password'       => 'test',
            'dbname'         => 'test'));
        $this->assertType('Zend_Db_Select', $adapter->createQuery('test'));
    }

    public function testCreateQueryIsUnique()
    {
        $adapter = new Waf_Model_Storage_Adapter_ZendDb('Pdo_MySql', array(
            'host'           => '127.0.0.1',
            'username'       => 'test',
            'password'       => 'test',
            'dbname'         => 'test'));

        $query1 = $adapter->createQuery('test');
        $query2 = $adapter->createQuery('test');

        $this->assertNotEquals(
            spl_object_hash($query1),
            spl_object_hash($query2)
        );
    }

    public function testCreateQuerySetsFrom()
    {
        $adapter = new Waf_Model_Storage_Adapter_ZendDb('Pdo_MySql', array(
            'host'           => '127.0.0.1',
            'username'       => 'test',
            'password'       => 'test',
            'dbname'         => 'test'));

        $query = $adapter->createQuery('foo');
        $part  = $query->getPart('from');

        $this->assertEquals('from', $part['foo']['joinType']);
    }

    public function testBeginTransactionRollback()
    {
        $adapter = new Waf_Model_Storage_Adapter_ZendDb(
            $this->getConnection()->getConnection()
        );
        $adapter->beginTransaction();
        $adapter->insert('test', array(
            'id'  => null,
            'name' => 'foobar'
        ));
        $adapter->rollbackTransaction();
        $this->assertEquals(
            3,
            count($adapter->fetch('test', new Waf_Model_Storage_Adapter_ZendDb_TestQueryFilter))
        );
    }

    public function testBeginTransactionCommit()
    {
        $adapter = new Waf_Model_Storage_Adapter_ZendDb(
            $this->getConnection()->getConnection()
        );
        $adapter->beginTransaction();
        $adapter->insert('test', array(
            'id'  => null,
            'name' => 'foobar'
        ));
        $adapter->commitTransaction();
        $this->assertEquals(
            4,
            count($adapter->fetch('test', new Waf_Model_Storage_Adapter_ZendDb_TestQueryFilter))
        );
    }

    /**
     * @expectedException Waf_Model_Storage_Exception
     */
    public function testFindFailInvalidQueryFilter()
    {
        $adapter = new Waf_Model_Storage_Adapter_ZendDb(
            $this->getConnection()->getConnection()
        );
        $adapter->find('test', 'invalid');
    }

    public function testInsert()
    {
        $adapter = new Waf_Model_Storage_Adapter_ZendDb(
            $this->getConnection()->getConnection()
        );
        $adapter->insert('test', array(
            'id'  => null,
            'name' => 'foobar'
        ));
        $this->assertEquals(
            4,
            count($adapter->fetch('test', new Waf_Model_Storage_Adapter_ZendDb_TestQueryFilter))
        );
    }

    public function testInsertReturnsLastInsertId()
    {
        $adapter = new Waf_Model_Storage_Adapter_ZendDb(
            $this->getConnection()->getConnection()
        );
        $this->assertEquals(4, $adapter->insert('test', array(
            'id'  => null,
            'name' => 'foobar'
        )));
    }

    public function testUpdate()
    {
        $adapter = new Waf_Model_Storage_Adapter_ZendDb(
            $this->getConnection()->getConnection()
        );
        $adapter->update('test', array(
            'name' => 'foobar'
        ), 1);
        $row = $adapter->find('test', 1);
        $this->assertEquals('foobar', $row['name']);
    }

    public function testUpdateByQueryFilter()
    {
        $queryFilter = new Waf_Model_Storage_Adapter_ZendDb_TestQueryFilterUpdate();
        $adapter = new Waf_Model_Storage_Adapter_ZendDb(
            $this->getConnection()->getConnection()
        );
        $adapter->update('test', array(
            'name' => 'foobar'
        ), $queryFilter);
        $row = $adapter->find('test', 3);
        $this->assertEquals('foobar', $row['name']);
    }

    public function testDelete()
    {
        $adapter = new Waf_Model_Storage_Adapter_ZendDb(
            $this->getConnection()->getConnection()
        );
        $adapter->delete('test', 1);
        $this->assertFalse($adapter->find('test', 1));
    }

    public function testDeleteByQueryFilter()
    {
        $queryFilter = new Waf_Model_Storage_Adapter_ZendDb_TestQueryFilterUpdate();
        $adapter = new Waf_Model_Storage_Adapter_ZendDb(
            $this->getConnection()->getConnection()
        );
        $adapter->delete('test', $queryFilter);
        $this->assertFalse($adapter->find('test', 3));
    }

    public function testPaginateReturnsPaginatorAdapter()
    {
        $adapter = new Waf_Model_Storage_Adapter_ZendDb(
            $this->getConnection()->getConnection()
        );
        $this->assertType(
            'Waf_Model_Paginator_Adapter_ZendDb',
            $adapter->paginate('test', 1)
        );
    }

    public function testCountReturnsPaginatorCountValue()
    {
        $paginatorAdapter = $this->getMock('Zend_Paginator_Adapter_Array', array('count'), array(array()));
        $paginatorAdapter
            ->expects($this->any())
            ->method('count')
            ->will($this->returnValue(50));
        $adapter = $this->getMock('Waf_Model_Storage_Adapter_ZendDb', array('paginate'));
        $adapter
            ->expects($this->any())
            ->method('paginate')
            ->will($this->returnValue($paginatorAdapter));

        $this->assertEquals(50, $adapter->count('test', 5));
    }
}

class Waf_Model_Storage_Adapter_ZendDb_TestConnection extends Zend_Test_DbAdapter
{
    public $sql;
    public $bind;

    public function query($sql, $bind = array())
    {
        $this->query = $sql;
        $this->bind = $bind;
    }
}

class Waf_Model_Storage_Adapter_ZendDb_TestQueryFilter implements Waf_Model_QueryFilter_QueryFilterInterface
{
    public function filter($query)
    {
        return $query;
    }
}

class Waf_Model_Storage_Adapter_ZendDb_TestQueryFilterUpdate implements Waf_Model_QueryFilter_QueryFilterInterface
{
    public function filter($query)
    {
        return $query->where('name = ?', 'baz');
    }
}