<?php
/**
 * Base class for custom Zend_Controller_Action
 * 
 * Provides some magic __call and __get methods for easy access to helpers.
 * Please note: try to avoid using these in context where they might cause confusion
 * with local methods and/or properties.
 *
 * @category   Waf
 * @package    Waf_Zend_Controller
 * @subpackage Action
 * @version    $Id: Action.php 379 2010-08-06 13:46:21Z rick $
 */
class Waf_Zend_Controller_Action
    extends Zend_Controller_Action
{
    /**
     * Proxy calls to undefined properties to getHelper()
     * 
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
//        if ($this->_helper->hasHelper($property)) {
            return $this->getHelper($property);
//        }
        
        throw new Waf_Exception(
            'Tried to proxy property $this->' . $property . ' to a Helper with '
            . 'this name, but it does not seem to exist.'
        );
    }
    
    /**
     * Proxy calls to undefined methods to getHelper()->direct()
     * 
     * 
     * @param string $method
     * @param array $params
     * @return mixed
     */
    public function __call($method, $params)
    {
        if (substr($method, -6) !== 'Action') {
            $helper = $this->_helper->getHelper($method);
            return call_user_func_array(
                array($helper, 'direct'),
                $params
            );
        }
        
        return parent::__call($method, $params);
    }
}