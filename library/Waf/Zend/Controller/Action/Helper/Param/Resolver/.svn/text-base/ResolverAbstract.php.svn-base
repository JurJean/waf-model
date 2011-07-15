<?php
/**
 * ResolverAbstract description
 *
 * @author     ASOclusive
 * @category   
 * @package    
 * @subpackage 
 * @version    $Id:$
 */
abstract class Waf_Zend_Controller_Action_Helper_Param_Resolver_ResolverAbstract
    implements Waf_Zend_Controller_Action_Helper_Param_Resolver
{
    protected $_resolveMethod;

    public function __construct(array $options = null)
    {
        if (null !== $options) {
            $this->setOptions($options);
        }
    }
    
    /**
     * Set options
     *
     * @param array $options
     * @return Waf_Zend_Controller_Action_Helper_Param_Resolver_Repository
     */
    public function setOptions(array $options)
    {
        foreach ($options as $method => $param) {
            $method = 'set' . ucfirst($method);
            $this->$method($param);
        }

        return $this;
    }
    
    /**
     * Set method to use resolving the param
     *
     * @param string $resolveMethod
     * @return Waf_Zend_Controller_Action_Helper_Param_Resolver_Repository
     */
    public function setMethod($resolveMethod)
    {
        $this->_resolveMethod = $resolveMethod;
        return $this;
    }

    /**
     * Get method to use resolving the param
     *
     * @return string
     */
    public function getMethod()
    {
        if (null === $this->_resolveMethod) {
            throw new Waf_Zend_Controller_Action_Helper_Param_Resolver_Exception(
                'No method defined to use resolving param'
            );
        }

        return $this->_resolveMethod;
    }
}