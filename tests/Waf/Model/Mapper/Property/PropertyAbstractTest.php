<?php
class Waf_Model_Mapper_Property_PropertyAbstractTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Zend_Registry::_unsetInstance();
    }

    public function testConstructorSetGetPropertyName()
    {
        $property = new Waf_Model_Mapper_Property_PropertyAbstractTest_Construct('Foo');
        $this->assertEquals('Foo', $property->getPropertyName());
    }

    public function testGetFieldNameDefault()
    {
        $property = new Waf_Model_Mapper_Property_PropertyAbstractTest_Construct('FooBar');
        $this->assertEquals(
            'foo_bar',
            $property->getFieldName()
        );
    }

    public function testConstructorSetOptions()
    {
        $property = new Waf_Model_Mapper_Property_PropertyAbstractTest_Construct(array(
            'propertyName'=>'halelujah',
            'fieldName' => 'BarBaz'
        ));
        $this->assertEquals(
            'BarBaz',
            $property->getFieldName()
        );
    }

    public function testGetModelDefaultsToRegistered()
    {
        $property = $this->getMockForAbstractClass(
            'Waf_Model_Mapper_Property_PropertyAbstract',
            array('Foo', null)
        );
        $model = new Waf_Model();
        $model->register();

        $this->assertSame($model, $property->getModel());
    }

    public function testSetGetModel()
    {
        $property = $this->getMockForAbstractClass(
            'Waf_Model_Mapper_Property_PropertyAbstract',
            array('Foo', null)
        );
        $model = new Waf_Model();
        $property->setModel($model);
        $this->assertSame($model, $property->getModel());
    }
}

class Waf_Model_Mapper_Property_PropertyAbstractTest_Construct
    extends Waf_Model_Mapper_Property_PropertyAbstract
{
    public function toEntity($state)
    {

    }

    public function toStorage($state)
    {
        
    }
}