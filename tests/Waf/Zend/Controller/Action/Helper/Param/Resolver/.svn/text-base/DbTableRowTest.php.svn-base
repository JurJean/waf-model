<?php
/**
 * DbTableRowTest description
 *
 * @author     ASOclusive
 * @category   
 * @package    
 * @subpackage 
 * @version    $Id:$
 */
class Waf_Zend_Controller_Action_Helper_Param_Resolver_DbTableRowTest
    extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->rowResolver = new Waf_Zend_Controller_Action_Helper_Param_Resolver_DbTableRow();
        $this->dbAdapter = new Zend_Test_DbAdapter();
        Zend_Db_Table::setDefaultAdapter($this->dbAdapter); // I know this sucks
        $this->dbTable = $this->getMock('Zend_Db_Table', array(), array('db'=>$this->dbAdapter));
    }

    public function testDefaultResolveMethod()
    {
        $this->assertEquals(
            'fetchRow',
            $this->rowResolver->getMethod()
        );
    }

    /**
     * @expectedException Waf_Zend_Controller_Action_Helper_Param_Resolver_Exception
     */
    public function testFailGetDbTableClass()
    {
        $this->rowResolver->getDbTableClass();
    }

    public function testSetGetDbTableClass()
    {
        $this->rowResolver->setDbTableClass('Test');
        $this->assertEquals(
            'Test',
            $this->rowResolver->getDbTableClass()
        );
    }

    public function testGetDbTable()
    {
        $class = get_class($this->dbTable);
        $this->rowResolver->setDbTableClass($class);
        $this->assertType(
            $class,
            $this->rowResolver->getDbTable()
        );
    }

    public function testSetGetDbTable()
    {
        $this->rowResolver->setDbTable($this->dbTable);
        $this->assertSame(
            $this->dbTable,
            $this->rowResolver->getDbTable()
        );
    }

    /**
     * @expectedException Waf_Zend_Controller_Action_Helper_Param_Resolver_Exception
     */
    public function testFailResolve()
    {
        $this->dbTable
            ->expects($this->once())
            ->method('fetchRow')
            ->will($this->returnValue(null));
        $this->rowResolver->setDbTable($this->dbTable);
        $this->rowResolver->resolve(1);
    }

    public function testResolve()
    {
        $this->dbTable
            ->expects($this->once())
            ->method('fetchRow')
            ->will($this->returnValue(true));
        $this->rowResolver->setDbTable($this->dbTable);
        $this->assertTrue($this->rowResolver->resolve(1));
    }
}