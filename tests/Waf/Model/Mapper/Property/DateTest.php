<?php
class Waf_Model_Mapper_Property_DateTest extends PHPUnit_Framework_TestCase
{
    public $date;

    public function setUp()
    {
        $this->date = new Waf_Model_Mapper_Property_Date('date');
    }

    public function tearDown()
    {
        unset($this->date);
    }

    /**
     * @expectedException Waf_Model_Mapper_Exception
     */
    public function testToStorageFail()
    {
        $this->date->toStorage(array('date'=>'test'));
    }

    public function testToStorage()
    {
        $now = Zend_Date::now();
        $this->assertEquals(
            $now->get('yyyy-MM-dd'),
            $this->date->toStorage(array('date'=>$now))
        );
    }

    public function testToEntityIsZendDateInstance()
    {
        $this->assertType(
            'Zend_Date', $this->date->toEntity(array('date'=>'2010-06-11'))
        );
    }

    public function testToEntity()
    {
        $date = '2010-06-11';
        $this->assertEquals(
            '2010-06-11',
            $this->date->toEntity(array('date'=>'2010-06-11'))->get('yyyy-MM-dd')
        );
    }
}