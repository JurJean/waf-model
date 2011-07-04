<?php
class Waf_Model_Mapper_Property_IntegerTest extends PHPUnit_Framework_TestCase
{
    public $integer;

    public function setUp()
    {
        $this->integer = new Waf_Model_Mapper_Property_Integer('integer');
    }

    public function tearDown()
    {
        unset($this->integer);
    }

    public function testIntegerToStorage()
    {
        $this->assertEquals('1', $this->integer->toStorage(array('integer'=>1)));
    }

    public function testStringToEntity()
    {
        $this->assertEquals(1, $this->integer->toStorage(array('integer'=>'1')));
    }
}