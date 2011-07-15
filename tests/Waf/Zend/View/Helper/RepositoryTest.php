<?php
class Waf_Zend_View_Helper_RepositoryTest extends PHPUnit_Framework_TestCase
{
    public $repositoryHelper;

    public function setUp()
    {
        $this->repositoryHelper = new Waf_Zend_View_Helper_Repository();
    }

    public function tearDown()
    {
        unset($this->repositoryHelper);

        Zend_Registry::_unsetInstance();
    }

    public function testIsControllerRepositoryHelper()
    {
        $this->assertType(
            'Waf_Zend_Controller_Action_Helper_Repository',
            $this->repositoryHelper
        );
    }

    public function testSetView()
    {
        $view = new Waf_Zend_View_Helper_RepositoryTest_TestView();
        $this->repositoryHelper->setView($view);
        $this->assertEquals($view, $this->repositoryHelper->view);
    }

    public function testGetRepository()
    {
        $model = new Waf_Model();
        $model->register();
        $this->assertType(
            'Waf_Model_Repository',
            $this->repositoryHelper->repository('test')
        );
    }
}

class Waf_Zend_View_Helper_RepositoryTest_TestView
    extends Zend_View
{

}