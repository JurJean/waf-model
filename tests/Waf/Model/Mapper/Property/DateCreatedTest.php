<?php
class Waf_Model_Mapper_Property_DateCreatedTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->date = new Waf_Model_Mapper_Property_DateCreated('created', null);
    }

    public function tearDown()
    {
        unset($this->date);
    }

    public function testToStorage()
    {
        $date = Zend_Date::now()->get('yyyy-MM-dd HH:mm:ss');
        $this->assertEquals($date, $this->date->toStorage(array('created'=>null)));
    }

    public function testToStorageExists()
    {
        $date = Zend_Date::now();
        $this->assertEquals($date->get('yyyy-MM-dd HH:mm:ss'), $this->date->toStorage(array('created'=>$date)));
    }
}