<?php
/**
 *
 *
 * @category
 * @package
 * @subpackage
 * @version    $Id:$
 */
class Waf_Model_Paginator_Adapter_ZendDbTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->mapper           = $this->getMock('Waf_Model_Mapper');
        $this->dbAdapter        = $this->getMock('Zend_Test_DbAdapter');
        $this->select           = $this->getMock('Zend_Db_Select', array(), array($this->dbAdapter));
        $this->paginatorAdapter = new Waf_Model_Paginator_Adapter_ZendDb($this->select);
    }

    /**
     * @expectedException Waf_Model_Paginator_Exception
     */
    public function testGetMapperFailureNotSet()
    {
        $this->paginatorAdapter->getMapper();
    }

    public function testSetGetMapper()
    {
        $this->paginatorAdapter->setMapper($this->mapper);
        $this->assertSame(
            $this->mapper,
            $this->paginatorAdapter->getMapper()
        );
    }
}