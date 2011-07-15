<?php
/**
 * Helps getting correct services from controller actions
 *
 * 
 */
class Waf_Zend_Controller_Action_Helper_Service
    extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * @var Zend_Filter_Inflector
     */
    protected $_inflector;
    
    /**
     * @var Waf_Model
     */
    protected $_model;

    /**
     * Direct method proxies to getService()
     *
     * @param string $serviceName
     * @param null|string $module
     * @return Waf_Model_Service_ServiceAbstract
     */
    public function direct($serviceName, $module = null)
    {
        return $this->getService($serviceName, $module);
    }

    /**
     * Get service by $serviceName
     *
     * If class $serviceName doesn't exist, it will try to use the inflector to
     * determine correct class name. If $module is null, the current module is
     * used from Request.
     *
     * @param string $serviceName
     * @param null|string $module
     * @return Waf_Model_Service_ServiceAbstract
     */
    public function getService($serviceName, $module = null)
    {
        if (!class_exists($serviceName)) {
            if (null === $module) {
                $module = $this->getRequest()->getModuleName();
            }
            
            $serviceName = $this->getInflector()->filter(
                array(
                    'module' => $module,
                    'name'   => $serviceName
                )
            );
        }

        $service = new $serviceName;
        $service->setModel($this->getModel());
        return $service;
    }

    /**
     * Get service as property
     *
     * @param string $serviceName
     * @return Waf_Model_Service_ServiceAbstract
     */
    public function __get($serviceName)
    {
        return $this->getService($serviceName);
    }

    /**
     * Set Inflector to use determing correct class name
     *
     * @param Zend_Filter_Inflector $inflector
     * @return Waf_Zend_Controller_Action_Helper_Service
     */
    public function setInflector(Zend_Filter_Inflector $inflector)
    {
        $this->_inflector = $inflector;

        return $this;
    }

    /**
     * Get Inflector to use determing correct class name
     *
     * @return Zend_Filter_Inflector
     */
    public function getInflector()
    {
        if (null === $this->_inflector) {
            $filters = array(
                'Word_DashToCamelCase',
                new Zend_Filter_Callback('ucfirst')
            );
            $inflector = new Zend_Filter_Inflector(':module_Model_Service_:name');
            $inflector->setRules(array(
                ':module' => $filters,
                ':name'   => $filters
            ));
            $this->_inflector = $inflector;
        }

        return $this->_inflector;
    }

    /**
     * Get registered Waf_Model instance
     *
     * @return Waf_Model
     */
    public function getModel()
    {
        if (null === $this->_model) {
            $this->_model = Waf_Model::getRegistered();
        }

        return $this->_model;
    }
}