<?php
class Waf_Model_Mapper_Property_OneToManyTest
    extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->property      = new Waf_Model_Mapper_Property_OneToMany('repository');
        $this->entity        = $this->getMock('Waf_Model_Entity');
        $this->model         = $this->getMock('Waf_Model');
        $this->entityManager = $this->getMock('Waf_Model_EntityManager');
        $this->repository    = $this->getMock('Waf_Model_Repository', array(), array($this->entityManager, 'Entity'));

        $this->model
            ->expects($this->any())
            ->method('getEntityManager')
            ->will($this->returnValue($this->entityManager));
        $this->entityManager
            ->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($this->repository));
        $this->property->setModel($this->model);
    }

    /**
     * @expectedException Waf_Model_Mapper_Exception
     */
    public function testGetRelatedFieldFailureNotSet()
    {
        $this->property->getRelatedFieldName();
    }

    public function testSetGetRelatedField()
    {
        $this->property->setRelatedFieldName('bladi');
        $this->assertEquals('bladi', $this->property->getRelatedFieldName());
    }

    public function testToStorageNotSupported()
    {
        $this->assertNull($this->property->toStorage(array('repository'=>'whatever')));
    }

    public function testToEntityType()
    {
        $this->property->setRelatedEntity('Entity');
        $this->property->setRelatedFieldName('related');
        $this->assertType(
            'Waf_Model_Repository',
            $this->property->toEntity(array())
        );
    }

    public function testToEntityRepositoryHasRelationFilter()
    {
        $this->property->setRelatedEntity('Entity');
        $this->property->setRelatedFieldName('related');
        $this->queryFilter   = $this->getMock(
            'Waf_Model_Mapper_Property_OneToMany_QueryFilter',
            array(),
            array(
                $this->property,
                array()
            )
        );
        $this->repository
            ->expects($this->once())
            ->method('setQueryFilter')
            ->with($this->equalTo(new Waf_Model_Mapper_Property_OneToMany_QueryFilter($this->property, array())));
        $repository = $this->property->toEntity(array());
    }
}