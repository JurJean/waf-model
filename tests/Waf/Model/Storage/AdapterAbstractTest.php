<?php
class Waf_Model_Storage_AdapterAbstractTest extends PHPUnit_Framework_TestCase
{
    public function testSetGetConnection()
    {
        $adapter = new Waf_Model_Storage_AdapterAbstractTest_Concrete;
        $adapter->setConnection('foo');
        $this->assertEquals('foo', $adapter->getConnection());
    }
    
    /**
     * 
     * @expectedException Waf_Model_Exception
     */
    public function testGetConnectionFail()
    {
        $adapter = new Waf_Model_Storage_AdapterAbstractTest_Concrete;
        $this->assertEquals('foo', $adapter->getConnection());
    }
    
    public function testHasConnectionTrue()
    {
        $adapter = new Waf_Model_Storage_AdapterAbstractTest_Concrete;
        $adapter->setConnection('foo');
        $this->assertTrue($adapter->hasConnection());
    }
    
    
    public function testHasConnectionFalse()
    {
        $adapter = new Waf_Model_Storage_AdapterAbstractTest_Concrete;
        $this->assertFalse($adapter->hasConnection());
    }
}

class Waf_Model_Storage_AdapterAbstractTest_Concrete extends Waf_Model_Storage_AdapterAbstract
{
    public function createQuery($name)
    {

    }

    public function beginTransaction()
    {

    }

    public function commitTransaction()
    {

    }

    public function rollbackTransaction()
    {

    }
    
    public function query($query)
    {
        
    }

    public function insert($name, $data)
    {

    }

    public function update($name, $data, $queryFilter)
    {

    }

    public function delete($name, $queryFilter)
    {

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