<?php
/**
 * IdentityMapTest description
 *
 * @author     ASOclusive
 * @category   
 * @package    
 * @subpackage 
 * @version    $Id:$
 */
class Waf_Model_EntityManager_IdentityMapTest extends PHPUnit_Framework_TestCase
{
    public $map;
    
    public function setUp()
    {
        $this->map = new Waf_Model_EntityManager_IdentityMap();
    }

    public function getEntity1()
    {
        $entity = $this->getMock('Waf_Model_Entity');
        $entity
            ->expects($this->atLeastOnce())
            ->method('getId')
            ->will($this->returnValue(1));
        return $entity;

    }

    public function getEntity2()
    {
        $entity = $this->getMock('Waf_Model_Entity');
        $entity
            ->expects($this->atLeastOnce())
            ->method('getId')
            ->will($this->returnValue(2));
        return $entity;
    }

    public function testContainsFalse()
    {
        $this->assertFalse(
            $this->map->contains($this->getEntity1())
        );
    }

    public function testContainsTrue()
    {
        $entity = $this->getEntity1();
        $this->map->manage($entity);
        $this->assertTrue(
            $this->map->contains($entity)
        );
    }

    public function testContainsId()
    {
        $entity = $this->getEntity1();
        $this->map->manage($entity);
        $this->assertTrue(
            $this->map->contains(1)
        );
    }

    /**
     * @expectedException Waf_Model_EntityManager_NotManagedException
     */
    public function testGetNotManagedFailure()
    {
        $this->map->get($this->getEntity1());
    }

    public function testGetManaged()
    {
        $entity = $this->getEntity1();
        $cloned = clone $entity;
        $this->map->manage($entity);
        $this->assertSame(
            $entity,
            $this->map->get($cloned)
        );
    }

    public function testGetManagedId()
    {
        $entity = $this->getEntity1();
        $this->map->manage($entity);
        $this->assertSame(
            $entity,
            $this->map->get(1)
        );
    }

    /**
     * @expectedException Waf_Model_EntityManager_NotManagedException
     */
    public function testDetachFailNotManaged()
    {
        $this->map->detach($this->getEntity1());
    }

    public function testDetach()
    {
        $entity = $this->getEntity1();
        $this->map->manage($entity);
        $this->map->detach($entity);
        $this->assertFalse($this->map->contains($entity));
    }
}