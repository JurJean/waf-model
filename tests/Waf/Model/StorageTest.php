<?php
class Waf_Model_StorageTest extends PHPUnit_Framework_TestCase
{    
    
    protected function _getMockAdapter($returnValue = 'foo')
    {
        $adapter = $this->getMock('Waf_Model_Storage_AdapterAbstract');
        $adapter->expects($this->any())
                 ->method('getConnection')
                 ->will($this->returnValue($returnValue));
        return $adapter;
    }
    
    public function testAddAdapterNoName()
    {
        $storage = new Waf_Model_Storage;
        $mock = $this->_getMockAdapter();
        $storage->addAdapter($mock);
        $this->assertEquals($mock, $storage->getAdapter());
    }
    
    public function testAddAdapterWithName()
    {
        $storage = new Waf_Model_Storage;
        $mock = $this->_getMockAdapter();
        $storage->addAdapter($mock, 'bar');
        $this->assertEquals($mock, $storage->getAdapter('bar'));
    }
    
    public function testHasAdapterFail()
    {
        $storage = new Waf_Model_Storage;
        $this->assertFalse($storage->hasAdapter('foo'));
    }
    
    public function testHasAdapterSuccess()
    {
        $storage = new Waf_Model_Storage;
        $mock = $this->_getMockAdapter();
        $storage->addAdapter($mock, 'foo');
        $this->assertTrue($storage->hasAdapter('foo'));
    }
    
    
    public function testSetDefaultAdapterByName()
    {
        $storage = new Waf_Model_Storage;
        $mock1 = $this->_getMockAdapter(1);
        $mock2 = $this->_getMockAdapter(2);
        $mock3 = $this->_getMockAdapter(3);
        $storage->addAdapter($mock1, 'foo');
        $storage->addAdapter($mock2, 'bar');
        $storage->addAdapter($mock3, 'baz');
        $storage->setDefaultAdapter('bar');
        $this->assertEquals(2, $storage->getDefaultAdapter()->getConnection());
    }
    
    public function testSetDefaultAdapterByObject()
    {
        $storage = new Waf_Model_Storage;
        $mock1 = $this->_getMockAdapter(1);
        $mock2 = $this->_getMockAdapter(2);
        $mock3 = $this->_getMockAdapter(3);
        $storage->addAdapter($mock1, 'foo');
        $storage->addAdapter($mock3, 'baz');
        $storage->setDefaultAdapter($mock2);
        $this->assertEquals(2, $storage->getDefaultAdapter()->getConnection());
    }
    
    public function testDefaultAdapterFirst()
    {
        $storage = new Waf_Model_Storage;
        $mock1 = $this->_getMockAdapter(1);
        $mock2 = $this->_getMockAdapter(2);
        $mock3 = $this->_getMockAdapter(3);
        $storage->addAdapter($mock1, 'foo');
        $storage->addAdapter($mock3, 'baz');
        $storage->addAdapter($mock3, 'baz');
        $this->assertEquals(1, $storage->getDefaultAdapter()->getConnection());
    }
    
    public function testMultipleAdaptersPickOne()
    {
        $storage = new Waf_Model_Storage;
        $mock1 = $this->_getMockAdapter(1);
        $mock2 = $this->_getMockAdapter(2);
        $mock3 = $this->_getMockAdapter(3);
        $storage->addAdapter($mock1, 'foo');
        $storage->addAdapter($mock2, 'bar');
        $storage->addAdapter($mock3, 'baz');
        $this->assertEquals(2, $storage->getAdapter('bar')->getConnection());
    }
}
