<?php
class Waf_Zend_Controller_Action_Helper_FormTest
    extends PHPUnit_Framework_TestCase
{
    public $formHelper;

    public function setUp()
    {
        $request =  new Zend_Controller_Request_HttpTestCase('http://www.test.com/kweenie');
        $request->setModuleName('foo-foo');
        $request->setControllerName('bar-bar');
        $request->setActionName('baz-baz');
        $request->setMethod('get');
        Zend_Controller_Front::getInstance()->setRequest($request);

        $this->formHelper = new Waf_Zend_Controller_Action_Helper_Action_FormTest_TestFormHelper();
    }

    public function tearDown()
    {
        unset($this->formHelper);
        Zend_Controller_Front::getInstance()->resetInstance();
    }

    public function testGetFormClassNameByRequest()
    {
        $this->assertEquals(
            'FooFoo_Form_BarBarBazBaz',
            $this->formHelper->getFormClassName()
        );
    }

    public function testHasForm()
    {
        $this->assertFalse(
            $this->formHelper->hasForm(
                'Waf_Zend_Controller_Action_Helper_Action_FormTest_TestForm'
            )
        );
    }

    public function testAddHasForm()
    {
        $this->formHelper->addForm(
            new Waf_Zend_Controller_Action_Helper_Action_FormTest_TestForm()
        );

        $this->assertTrue(
            $this->formHelper->hasForm(
                'Waf_Zend_Controller_Action_Helper_Action_FormTest_TestForm'
            )
        );
    }

    public function testAddHasFormByObject()
    {
        $form = new Waf_Zend_Controller_Action_Helper_Action_FormTest_TestForm();
        $this->formHelper->addForm(
            get_class($form)
        );

        $this->assertTrue(
            $this->formHelper->hasForm($form)
        );
    }

    public function testAddByString()
    {
        $this->formHelper->addForm(
            'Waf_Zend_Controller_Action_Helper_Action_FormTest_TestForm'
        );

        $this->assertTrue($this->formHelper->hasForm(
            'Waf_Zend_Controller_Action_Helper_Action_FormTest_TestForm'
        ));
    }

    /**
     * @expectedException Exception
     */
    public function testAddFailure()
    {
        $this->formHelper->addForm(
            new Waf_Zend_Controller_Action_Helper_Action_FormTest_TestFormInvalid()
        );
    }

    public function testGetFormDefault()
    {
        $helper = new Waf_Zend_Controller_Action_Helper_Action_FormTest_TestFormHelper_GetDefault();

        $this->assertType(
            'Waf_Zend_Controller_Action_Helper_Action_FormTest_TestForm',
            $helper->getForm()
        );
    }

    public function testHasNoActiveForm()
    {
        $this->assertFalse($this->formHelper->hasActiveForm());
    }

    public function testSetActiveFormHasActiveForm()
    {
        $this->formHelper->setActiveForm(
            new Waf_Zend_Controller_Action_Helper_Action_FormTest_TestForm()
        );

        $this->assertTrue($this->formHelper->hasActiveForm());
    }

    public function testSetActiveFormGetActiveFormIsTheSameForm()
    {
        $form = new Waf_Zend_Controller_Action_Helper_Action_FormTest_TestForm();

        $this->formHelper->setActiveForm($form);

        $this->assertEquals(
            spl_object_hash($form),
            spl_object_hash($this->formHelper->getActiveForm())
        );
    }

    public function testSetActiveFormAddsForm()
    {
        $this->formHelper->setActiveForm(
            'Waf_Zend_Controller_Action_Helper_Action_FormTest_TestForm'
        );

        $this->assertTrue($this->formHelper->hasForm(
            'Waf_Zend_Controller_Action_Helper_Action_FormTest_TestForm'
        ));
    }

    public function testGetActiveFormDefault()
    {
        $helper = new Waf_Zend_Controller_Action_Helper_Action_FormTest_TestFormHelper_GetDefault();

        $this->assertType(
            'Waf_Zend_Controller_Action_Helper_Action_FormTest_TestForm',
            $helper->getActiveForm()
        );
    }

    public function testIsValidGetSetsFormMethod()
    {
        $form = new Waf_Zend_Controller_Action_Helper_Action_FormTest_TestForm();
        $form->setMethod('post');

        $this->formHelper->setActiveForm($form);
        $this->formHelper->isValidGet();

        $this->assertEquals(
            'get',
            $form->getMethod()
        );
    }

    public function testIsValidPostSetsFormMethod()
    {
        $form = new Waf_Zend_Controller_Action_Helper_Action_FormTest_TestForm();
        $form->setMethod('get');

        $this->formHelper->setActiveForm($form);
        $this->formHelper->isValidPost();

        $this->assertEquals(
            'post',
            $form->getMethod()
        );
    }

    public function testIsValidGetNotValidOnPost()
    {
        Zend_Controller_Front::getInstance()->getRequest()->setMethod('POST');
        $helper = new Waf_Zend_Controller_Action_Helper_Action_FormTest_TestFormHelper_GetDefault();
        $this->assertFalse($helper->isValidGet());
    }

    public function testIsValidGetFalseEmptyQuery()
    {
        $request = new Zend_Controller_Request_HttpTestCase();
        $request->setMethod('get');
        $request->setQuery(array());
        Zend_Controller_Front::getInstance()->setRequest($request);

        $helper = new Waf_Zend_Controller_Action_Helper_Action_FormTest_TestFormHelper_GetDefault();
        $this->assertFalse($helper->isValidGet());
    }

    public function testIsValidGetTrueWithQuery()
    {
        $request = new Zend_Controller_Request_HttpTestCase();
        $request->setMethod('get');
        $request->setQuery(array('foo'=>'bar'));
        Zend_Controller_Front::getInstance()->setRequest($request);

        $helper = new Waf_Zend_Controller_Action_Helper_Action_FormTest_TestFormHelper_GetDefault();
        $this->assertTrue($helper->isValidGet());
    }

    public function testIsValidPostNotValid()
    {
        Zend_Controller_Front::getInstance()->getRequest()->setMethod('GET');
        $helper = new Waf_Zend_Controller_Action_Helper_Action_FormTest_TestFormHelper_GetDefault();

        $this->assertFalse($helper->isValidPost());
    }

    public function testProxyMethodsToActiveForm()
    {
        $form = new Waf_Zend_Controller_Action_Helper_Action_FormTest_TestForm();
        $this->formHelper->setActiveForm($form);
        $this->formHelper->proxy();
        $this->assertTrue($form->proxy);
    }

    public function testProxyPropertiesToActiveForm()
    {
        $form = new Waf_Zend_Controller_Action_Helper_Action_FormTest_TestForm();
        $form->proxy = true;
        $this->formHelper->setActiveForm($form);
        $this->assertTrue($this->formHelper->proxy);
    }
}

class Waf_Zend_Controller_Action_Helper_Action_FormTest_TestFormHelper
    extends Waf_Zend_Controller_Action_Helper_Form
{
}

class Waf_Zend_Controller_Action_Helper_Action_FormTest_TestFormHelper_GetDefault
    extends Waf_Zend_Controller_Action_Helper_Action_FormTest_TestFormHelper
{
    public function getFormClassName($form = null, $module = null)
    {
        return 'Waf_Zend_Controller_Action_Helper_Action_FormTest_TestForm';
    }
}

class Waf_Zend_Controller_Action_Helper_Action_FormTest_TestForm
    extends Zend_Form
{
    public $proxy = false;

    public function proxy()
    {
        $this->proxy = true;
    }
}

class Waf_Zend_Controller_Action_Helper_Action_FormTest_TestFormInvalid
{

}