<?php
class Waf_Zend_Controller_Action_Helper_ServiceTest
    extends PHPUnit_Framework_TestCase
{
    public $serviceHelper;
    public $request;
    public $service;
    public $inflector;
    public $model;

    public function setUp()
    {
        $this->serviceHelper = new Waf_Zend_Controller_Action_Helper_Service();

        $this->request = $this->getMock('Zend_Controller_Request_Http');
        $this->request
            ->expects($this->any())
            ->method('getModuleName')
            ->will($this->returnValue('test'));

        $this->service = $this->getMock('Waf_Model_Service');

        $this->inflector = $this->getMock('Zend_Filter_Inflector');
        $this->inflector
            ->expects($this->any())
            ->method('filter')
            ->will($this->returnValue(get_class($this->service)));

        Zend_Registry::_unsetInstance();
        $this->model = new Waf_Model();
        $this->model->register();
        
        $front = Zend_Controller_Front::getInstance();
        $front->resetInstance();
        $front->setRequest($this->request);
    }

    public function testDefaultInflector()
    {
        $this->assertEquals(
            'RenStimpy_Model_Service_HappyJoy',
            $this->serviceHelper->getInflector()->filter(
                array(
                    'module' => 'ren-stimpy',
                    'name'   => 'happy-joy'
                )
            )
        );
    }

    public function testGetServiceByExistingClassName()
    {
        $this->inflector
            ->expects($this->never())
            ->method('filter');
        $this->serviceHelper->setInflector($this->inflector);
        $this->serviceHelper->getService(get_class($this->service));
    }

    public function testGetServiceInflectorArguments()
    {
        $this->inflector
            ->expects($this->once())
            ->method('filter')
            ->with(
                $this->equalTo(
                    array(
                        'module' => 'test',
                        'name'   => 'foo'
                    )
                )
            );
        $this->serviceHelper->setInflector($this->inflector);
        $this->serviceHelper->getService('foo', 'test');
    }

    public function testGetServiceGuessModule()
    {
        $this->request
            ->expects($this->once())
            ->method('getModuleName');
        $this->serviceHelper->setInflector($this->inflector);
        $this->serviceHelper->getService('foo');
    }

    public function testGetService()
    {
        $this->serviceHelper->setInflector($this->inflector);
        $this->assertEquals(
            get_class($this->service),
            get_class($this->serviceHelper->getService('foo'))
        );
    }

    public function testDirectGetsService()
    {
        $this->serviceHelper->setInflector($this->inflector);
        $this->assertEquals(
            get_class($this->service),
            get_class($this->serviceHelper->direct('foo'))
        );
    }

    public function testMagicGetService()
    {
        $this->serviceHelper->setInflector($this->inflector);
        $this->assertEquals(
            get_class($this->service),
            get_class($this->serviceHelper->foo)
        );
    }
}