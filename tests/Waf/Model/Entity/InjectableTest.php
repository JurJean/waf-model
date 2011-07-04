<?php
/**
 * InjectableTest description
 *
 * @author     ASOclusive
 * @category   
 * @package    
 * @subpackage 
 * @version    $Id:$
 */
class Waf_Model_Entity_InjectableTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->entity = new Waf_Model_Entity_InjectableTest_TestEntity();
    }
    
    public function testSetState()
    {
        $this->entity->__setState(array(
            '_name' => 'baz'
        ));

        $this->assertEquals('baz', $this->entity->getName());
    }

    /**
     * @expectedException Waf_Model_Entity_Exception
     */
    public function testSetStateFailsIfPropertyNotDefined()
    {
        $this->entity->__setState(array(
            'notExists' => true
        ));
    }

    public function testGetState()
    {
        $this->entity->setName('foo');
        $this->assertArrayHasKey(
            '_name',
            $this->entity->__getState()
        );
    }

    public function testGetStateIsNotAffectedByGetters()
    {
        $this->entity->setBaz('test');
        $state = $this->entity->__getState();
        $this->assertEquals('test', $state['_baz']);
    }
}

class Waf_Model_Entity_InjectableTest_TestEntity extends Waf_Model_Entity_Injectable
{
    protected $_name;
    protected $_baz;
    
    public function setName($name)
    {
        $this->_name = $name;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function setBaz($baz)
    {
        $this->_baz = $baz;
    }

    public function getBaz()
    {
        return md5($this->_baz);
    }
}