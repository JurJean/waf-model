<?php
/**
 * Helps with common requestParam to object resolving within the controller
 *
 * Almost every action does the same: it takes a request param, checks if an
 * entity exists which matches and if not throwing an exception if it is not
 * found and otherwise returning the entity. This abstract class provides the
 * glue by doing all these things - simply extend this class and the _params
 * property and define which properties map to which entities.
 *
 * @category   Waf
 * @package    Waf_Zend_Controller
 * @subpackage Action_Helper_Param
 * @version    $Id: $
 */
class Waf_Zend_Controller_Action_Helper_Param
    extends Zend_Controller_Action_Helper_Abstract
{
    protected $_resolvers = array();

    /**
     * Construct, optionally using $options
     * 
     * @param array $options
     * @return void
     */
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
     * Direct method proxies to getParam()
     *
     * @param string $param
     * @return mixed
     */
    public function direct($param)
    {
        return $this->getParam($param);
    }

    /**
     *
     * @param <type> $param
     * @return <type> 
     */
    public function getParam($param)
    {
        $value = $this->getRequest()->getParam($param, false);
        
        if (false === $value) {
            throw new Waf_Zend_Controller_Action_Helper_Param_Exception(
                sprintf(
                    'Param %s was not passed in the request',
                    $param
                )
            );
        }

        return $this->getResolver($param)->resolve($value);
    }

    /**
     * Set multiple resolvers at once
     * 
     * @param array $resolvers
     * @return Waf_Zend_Controller_Action_Helper_Param
     */
    public function setResolvers(array $resolvers)
    {
        foreach ($resolvers as $param => $resolver) {
            $this->setResolver($param, $resolver);
        }

        return $this;
    }

    /**
     * Set $resolver for $param
     *
     * @param string $param
     * @param Waf_Zend_Controller_Action_Helper_Param_Resolver $resolver
     * @return Waf_Zend_Controller_Action_Helper_Param
     */
    public function setResolver($param, $resolver)
    {
        if (is_array($resolver)) {
            if (!isset($resolver['type'])) {
                throw new Exception(
                    'When adding a resolver by config, the type must be specified'
                );
            }
            if (@class_exists($resolver['type'], true)) {
                $className = $resolver['type'];
            } else {
                $className = get_class($this) . '_Resolver_' . $resolver['type'];
            }
            unset($resolver['type']);
            $resolver = new $className($resolver);
        }
        $this->_resolvers[$param] = $resolver;
        return $this;
    }

    /**
     * Get Waf_Zend_Controller_Action_Helper_Param_Resolver for $param
     *
     * @todo determine behaviour if not defined
     * @param string $param
     * @return Waf_Zend_Controller_Action_Helper_Param_Resolver
     */
    public function getResolver($param)
    {
        if (!isset($this->_resolvers[$param])) {
            throw new Waf_Zend_Controller_Action_Helper_Param_Exception(
                sprintf(
                    'No resolver defined for param %s',
                    $param
                )
            );
        }

        return $this->_resolvers[$param];
    }
}