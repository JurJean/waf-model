<?php
/**
 * ReflectTest description
 *
 * @author     ASOclusive
 * @category   
 * @package    
 * @subpackage 
 * @version    $Id:$
 */
class Waf_Model_EntityGenerator_ReflectTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->generator = new Waf_Model_EntityGenerator_Reflect(
            'Waf_Model_EntityGenerator_ReflectTest_TestEntity'
        );
    }

    public function testGeneratesEntity()
    {
        $this->assertType(
            'Waf_Model_EntityGenerator_ReflectTest_TestEntity',
            $this->generator->generateEntity(array())
        );
    }

    public function testGeneratesEntityPublicProperty()
    {
        $entity = $this->generator->generateEntity(
            array('public' => true)
        );
        $this->assertTrue($entity->getPublic());
    }

    public function testGeneratesEntityProtectedProperty()
    {
        $entity = $this->generator->generateEntity(
            array('protected' => true)
        );
        $this->assertTrue($entity->getProtected());
    }

    public function testGeneratesEntityPrivateProperty()
    {
        $entity = $this->generator->generateEntity(
            array('private' => true)
        );
        $this->assertTrue($entity->getPrivate());
    }

    public function testGenerateState()
    {
        $expected = array(
            '_id'       => 1,
            'public'    => true,
            'protected' => true,
            'private'   => true
        );
        $entity = $this->generator->generateEntity($expected);
        $this->assertEquals(
            $expected,
            $this->generator->generateState($entity)
        );
    }
}

class Waf_Model_EntityGenerator_ReflectTest_TestEntity extends Waf_Model_Entity
{
    public $public;
    protected $protected;
    private $private;
    
    /**
     * Private constructor to make sure the Generator bypasses the constructor
     */
    private function __construct()
    {
        
    }

    public function getPublic()
    {
        return $this->public;
    }

    public function getProtected()
    {
        return $this->protected;
    }

    public function getPrivate()
    {
        return $this->private;
    }
}