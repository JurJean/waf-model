<?php
class Waf_Model_Mapper_Property_StringTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->string = new Waf_Model_Mapper_Property_String('string', null);
    }

    public function tearDown()
    {
        unset($this->string);
    }

    public function testSetNotNullTrueToStorage()
    {
        $this->assertType('string', $this->string->toStorage(array('string'=>null)));
    }

    public function testSetNotNullFalseToStorage()
    {
        $this->string->setNotNull(false);
        $this->assertNull($this->string->toStorage(array('string'=>null)));
    }

    public function testIntToStorage()
    {
        $this->assertType('string', $this->string->toStorage(array('string'=>10)));
    }

    public function testSetNotNullTrueToEntity()
    {
        $this->assertType('string', $this->string->toEntity(array('string'=>null)));
    }

    public function testSetNotNullFalseToEntity()
    {
        $this->string->setNotNull(false);
        $this->assertNull($this->string->toEntity(array('string'=>null)));
    }

    public function testIntToEntity()
    {
        $this->assertType('string', $this->string->toEntity(array('string'=>22)));
    }
}