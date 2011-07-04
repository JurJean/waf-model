<?php
class Waf_Model_Entity_EntityAbstractTest
    extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->entity = new Waf_Model_Entity_EntityAbstractTest_TestEntity();
    }
    
    public function testMagicGetProxiesToGetter()
    {
        $this->entity->setName('bar');
        $this->assertEquals('bar', $this->entity->name);
    }
    
    /**
     * @expectedException Waf_Model_Entity_Exception
     */
    public function testMagicGetNoGetter()
    {
        $test = $this->entity->bogus;
    }

    public function testToArrayIsAffectedByGetters()
    {
        $this->entity->setBar('testing');
        $data = $this->entity->toArray();
        $this->assertEquals(md5('testing'), $data['bar']);
    }

    public function testToArrayChecksHasMethods()
    {
        $expected = array(
            'id' => '',
            'bar' => '',
            'baz' => 'd41d8cd98f00b204e9800998ecf8427e',
            'modified' => ''
        );
        $this->assertEquals($expected, $this->entity->toArray());
    }
}

class Waf_Model_Entity_EntityAbstractTest_TestEntity
    extends Waf_Model_Entity_EntityAbstract
{
    protected $_id;
    protected $_name;
    private $_foo = 'bar';
    protected $_bar;
    protected $_baz;
    protected $_modified;

    public function getId()
    {
        return $this->_id;
    }
    
    public function setName($name)
    {
        $this->_name = $name;
        
        return $this;
    }

    public function hasName()
    {
        return null !== $this->_name;
    }
    
    public function getName()
    {
        if (null === $this->_name) {
            throw new Waf_Exception('Name not set');
        }
        return $this->_name;
    }
    
    public function getFoo()
    {
        return $this->_foo;
    }
    
    public function setBar($bar)
    {
        $this->_bar = md5($bar);
        
        return $this;
    }
    
    public function getBar()
    {
        return $this->_bar;
    }
    
    public function setBaz($baz)
    {
        $this->_baz = $baz;
    }
    
    public function getBaz()
    {
        return md5($this->_baz);
    }

    public function set_NotExists()
    {
        
    }

    public function setModified($modified)
    {
        $this->_modified = md5($modified);
    }

    public function getModified()
    {
        return $this->_modified;
    }
}