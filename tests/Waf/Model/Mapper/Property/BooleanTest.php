<?php
class Waf_Model_Mapper_Property_BooleanTest extends PHPUnit_Framework_TestCase
{
    public $boolean;

    public function setUp()
    {
        $this->boolean = new Waf_Model_Mapper_Property_Boolean('boolean');
    }

    public function tearDown()
    {
        unset($this->boolean);
    }

    public function testTrueToStorage()
    {
        $this->assertEquals(1, $this->boolean->toStorage(array('boolean'=>true)));
    }

    public function testFalseToStorage()
    {
        $this->assertEquals(0, $this->boolean->toStorage(array('boolean'=>false)));
    }

    public function testTrueToEntity()
    {
        $this->assertTrue($this->boolean->toEntity(array('boolean'=>'1')));
    }

    public function testFalseToEntity()
    {
        $this->assertFalse($this->boolean->toEntity(array('boolean'=>'0')));
    }
}