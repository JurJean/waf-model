<?php
class Waf_Model_Mapper_IdentityTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->identity = new Waf_Model_Mapper_Identity();
        $this->entity   = $this->getMock('Waf_Model_Entity');
    }

    public function testGetPropertyNameDefault()
    {
        $this->assertEquals(
            '_id',
            $this->identity->getPropertyName()
        );
    }

    public function testSetGetPropertyName()
    {
        $this->identity->setPropertyName('test');
        $this->assertEquals(
            'test',
            $this->identity->getPropertyName()
        );
    }

    public function testGetFieldNameDefault()
    {
        $this->assertEquals(
            'id',
            $this->identity->getFieldName()
        );
    }

    public function testSetGetFieldName()
    {
        $this->identity->setFieldName('test');
        $this->assertEquals(
            'test',
            $this->identity->getFieldName()
        );
    }

    public function testToEntity()
    {
        $expected = array(
            '_id' => 1
        );
        $this->assertEquals(
            $expected,
            $this->identity->toEntity(array(
                'id' => 1,
                'property' => 'value'
            ))
        );
    }

    public function testToStorage()
    {
        $expected = array(
            'id' => 1
        );
        $this->assertEquals(
            $expected,
            $this->identity->toStorage(array(
                '_id' => 1,
                'property' => 'value'
            ))
        );
    }
}