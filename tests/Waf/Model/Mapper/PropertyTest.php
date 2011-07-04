<?php
class Waf_Model_Mapper_PropertyTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->propertyMapper = new Waf_Model_Mapper_Property();
        $this->property = $this->getMockForAbstractClass('Waf_Model_Mapper_Property_PropertyAbstract', array('property'));
    }

    public function testGetPropertiesDefault()
    {
        $this->assertType(
            'array',
            $this->propertyMapper->getProperties()
        );
    }

    public function testAddPropertyGetProperty()
    {
        $this->propertyMapper->addProperty($this->property);
        $this->assertSame(
            $this->property,
            $this->propertyMapper->getProperty('property')
        );
    }

    public function testAddPropertyGetProperties()
    {
        $this->propertyMapper->addProperty($this->property);
        $this->assertEquals(1, count($this->propertyMapper->getProperties()));
    }

    /**
     * @expectedException Waf_Model_Mapper_Exception
     */
    public function testAddPropertyByArrayFailTypeNotDefined()
    {
        $this->propertyMapper->addProperty(array());
    }

    public function testAddPropertyByArray()
    {
        $className = get_class($this->property);
        $this->propertyMapper->addProperty(array(
            'type' => $className,
            'propertyName'=>'property'
        ));
        $this->assertType(
            $className,
            $this->propertyMapper->getProperty('property')
        );
    }

    public function testSetPropertiesGetProperties()
    {
        $this->propertyMapper->setProperties(array(
            $this->property
        ));
        $this->assertEquals(1, count($this->propertyMapper->getProperties()));
    }

    public function testToEntityPassesState()
    {
        $state = array(
            'property'=>'value'
        );
        $this->property
            ->expects($this->once())
            ->method('toEntity')
            ->with($this->equalTo($state));
        $this->propertyMapper->addProperty($this->property);
        $this->propertyMapper->toEntity($state);
    }

    public function testToEntityMapsPropertyAndValue()
    {
        $expected = array(
            'property' => 'value'
        );
        $this->property
            ->expects($this->any())
            ->method('toEntity')
            ->will($this->returnValue('value'));
        $this->property->setPropertyName('property');
        $this->propertyMapper->addProperty($this->property);
        $this->assertEquals(
            $expected,
            $this->propertyMapper->toEntity(array())
        );
    }

    public function testToEntityMapsArray()
    {
        $expected = array(
            'property' => 'value'
        );
        $this->property
            ->expects($this->any())
            ->method('toEntity')
            ->will($this->returnValue(array('property'=>'value')));
        $this->propertyMapper->addProperty($this->property);
        $this->assertEquals(
            $expected,
            $this->propertyMapper->toEntity(array())
        );
    }

    public function testToStoragePassesState()
    {
        $state = array(
            'property' => 'value'
        );
        $this->property
            ->expects($this->once())
            ->method('toStorage')
            ->with($this->equalTo($state));
        $this->propertyMapper->addProperty($this->property);
        $this->propertyMapper->toStorage($state);
    }

    public function testToStorageMapsPropertyAndValue()
    {
        $expected = array(
            'property' => 'value'
        );
        $this->property
            ->expects($this->any())
            ->method('toStorage')
            ->will($this->returnValue('value'));
        $this->property->setFieldName('property');
        $this->propertyMapper->addProperty($this->property);
        $this->assertEquals(
            $expected,
            $this->propertyMapper->toStorage(array())
        );
    }

    public function testToStorageMapsArray()
    {
        $expected = array(
            'property' => 'value'
        );
        $this->property
            ->expects($this->any())
            ->method('toStorage')
            ->will($this->returnValue(array('property'=>'value')));
        $this->propertyMapper->addProperty($this->property);
        $this->assertEquals(
            $expected,
            $this->propertyMapper->toStorage(array())
        );
    }

    public function testToStorageDoesntMapPropertiesThatAreNotStoraged()
    {
        $expected = array();
        $this->property->disableStorage(false);
        $this->propertyMapper->addProperty($this->property);
        $this->assertEquals(
            $expected,
            $this->propertyMapper->toStorage(array('property' => 'value'))
        );
    }
}