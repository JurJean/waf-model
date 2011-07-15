<?php
/**
 * Handle forms
 *
 * Provides a naming convention for your forms, and allows to check if a form is
 * valid using a single line.
 *
 * @todo multi-page forms
 * @category   Waf
 * @package    Waf_Zend_Controller
 * @subpackage Action_Helper
 * @version    $Id: $
 */
class Waf_Zend_Controller_Action_Helper_Form
    extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * @var array
     */
    private $_forms = array();

    /**
     * @var string
     */
    private $_activeForm;

    /**
     * Direct method proxies to getActiveForm()
     *
     * @return Zend_Form
     */
    public function direct()
    {
        return $this->getActiveForm();
    }

    /**
     * Set active form
     *
     * - If $form is an instance of Zend_Form, it will be added by addForm()
     * - Otherwise the form will be loaded based on several conditions
     *
     * @param mixed $form
     * @param string $module
     * @return Zend_Form
     */
    public function setActiveForm($form = null, $module = null)
    {
        $this->_activeForm = get_class($this->getForm($form, $module));

        return $this;
    }

    /**
     * Is an active form specified?
     *
     * @return boolean
     */
    public function hasActiveForm()
    {
        return null !== $this->_activeForm;
    }

    /**
     * Get the active form, defaults to the form as defined in getFormClassName()
     *
     * @return Zend_Form
     */
    public function getActiveForm()
    {
        if (!$this->hasActiveForm()) {
            $this->setActiveForm();
        }

        return $this->getForm($this->_activeForm);
    }

    /**
     * Add a form to the stack
     *
     * @param mixed $form
     * @return self Fluent interface
     */
    public function addForm($form)
    {
        if (is_string($form)) {
            $form = new $form;
        }

        if (!$form instanceof Zend_Form) {
            throw new Zend_Controller_Exception(
                '$form must be a string or an array'
            );
        }

        if ($this->hasForm($form)) {
            throw new Zend_Controller_Exception(sprintf(
                'Form %s was already added',
                get_class($form)
            ));
        }

        $this->_forms[get_class($form)] = $form;

        return $this;
    }

    /**
     * Is $form in the stack?
     *
     * @param mixed $form
     * @return boolean
     */
    public function hasForm($form)
    {
        if (is_object($form)) {
            $form = get_class($form);
        }

        return isset($this->_forms[$form]);
    }

    /**
     * Get form. Uses getFormClassName() to get the corrct form.
     *
     * @see getFormClassName()
     * @param mixed $form
     * @param null|string $module
     * @return Zend_Form
     */
    public function getForm($form = null, $module = null)
    {
        $formName = $this->getFormClassName($form, $module);

        if (!$this->hasForm($formName)) {
            if ($form instanceof Zend_Form) {
                $this->addForm($form);
            } else {
                $this->addForm($formName);
            }
        }

        return $this->_forms[$formName];
    }

    /**
     * Get form class name
     *
     * - if both $form and $module are null, it creates the className based on
     *   the current Request
     * - if $form is an instance of Zend_Form, it returns the class name
     * - if $form is a string and $module is null, it is assumed that $form is
     *   the full className of the form
     * - if both $form and $module are a string, it creates the className using
     *   $module_Form_$form convention
     * 
     * @param mixed $form
     * @param null|string $module
     * @return string
     */
    public function getFormClassName($form = null, $module = null)
    {
        if (null === $form) {
            return $this->getFormClassNameByRequest();
        }

        if (is_string($form) && is_string($module)) {
            return ucfirst($module) . '_Form_' . $form;
        }

        if (is_string($form)) {
            return $form;
        }

        if (!$form instanceof Zend_Form) {
            throw new Exception(
                '$form is an object but not an instance of Zend_Form'
            );
        }

        return get_class($form);
    }

    /**
     * Get the form className based on the current Request
     *
     * @return string
     */
    public function getFormClassNameByRequest()
    {
        $filter   = new Zend_Filter_Word_DashToCamelCase();
        $request  = $this->getRequest();
        $formName = ucfirst($filter->filter($request->getModuleName()))
                  . '_Form_'
                  . ucfirst($filter->filter($request->getControllerName()))
                  . ucfirst($filter->filter($request->getActionName()));

        return $formName;
    }

    /**
     * Returns only true if the current request is a GET request, a GET query
     * exists, and all validators are passed
     *
     * @return boolean
     */
    public function isValidGet()
    {
        $this->getActiveForm()->setMethod('get');

        if (!$this->getRequest()->isGet()) {
            return false;
        }
        
        $query = $this->getRequest()->getQuery();
        if (empty($query)) {
            return false;
        }

        return $this->getActiveForm()->isValid($query);
    }

    /**
     * Returns only true if the current request is a POST request and all
     * validators are passed
     *
     * @return boolean
     */
    public function isValidPost()
    {
        $this->getActiveForm()->setMethod('post');

        if (!$this->getRequest()->isPost()) {
            return false;
        }

        return $this->getActiveForm()->isValid(
            $this->getRequest()->getPost()
        );
    }

    /**
     * Proxy calls to undefined methods to getActiveForm()
     *
     * @param string $method
     * @param array $params
     * @return mixed
     */
    public function __call($method, $params)
    {
        return call_user_func_array(
            array($this->getActiveForm(), $method),
            $params
        );
    }

    /**
     * Proxy calls to undefined properties to getActiveForm()
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getActiveForm()->$key;
    }
}