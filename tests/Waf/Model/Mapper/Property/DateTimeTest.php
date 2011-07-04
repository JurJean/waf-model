<?php
class Waf_Model_Mapper_Property_DateTimeTest extends PHPUnit_Framework_TestCase
{
    public $dateTime;

    public function setUp()
    {
        $this->dateTime = new Waf_Model_Mapper_Property_DateTime('dateTime');
    }

    public function tearDown()
    {
        unset($this->dateTime);
    }

    /**
     * @expectedException Waf_Model_Mapper_Exception
     */
    public function testToStorageFail()
    {
        $this->dateTime->toStorage(array('dateTime'=>'test'));
    }

    public function testToStorage()
    {
        $now = Zend_Date::now();
        $this->assertEquals(
            $now->get('yyyy-MM-dd HH:mm:ss'),
            $this->dateTime->toStorage(array('dateTime'=>$now))
        );
    }

    public function testToEntityIsZendDateInstance()
    {
        $this->assertType(
            'Zend_Date', $this->dateTime->toEntity(array('date_time'=>'2010-06-11 11:28:34'))
        );
    }

    public function testToEntity()
    {
        $date = '2010-06-11 11:28:34';
        $this->assertEquals(
            $date,
            $this->dateTime->toEntity(array('date_time'=>$date))->get('yyyy-MM-dd HH:mm:ss')
        );
    }
}