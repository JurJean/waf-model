<?php
/**
 * Static registry exclusively for Waf based applications.
 * 
 * Uses Zend_Registry, but ensures all keys are namespaced, so there's 
 * no conflict with existing uses of Zend_Registry.
 * Besides that, it ensures we can break the dependency on Zend in necessary, 
 * albeit unlikely.
 * 
 * The interface is similar to that of Zend_Registry, but it can only be used
 * as a singleton or via the static interfaces.
 * 
 * For now, there is no way to reset or create e new instance of Waf_Registry.
 * 
 * @category   Waf
 * @package    Waf_Registry
 * @version    $Id: Registry.php 50 2010-02-10 14:18:33Z jur $
 */
class Waf_Registry
{
    private static $_instance;
    private $_namespace;
    
    const REGISTRY_NAMESPACE = 'Waf';

    /**
     * private constructor to ensure singleton behaviour
     *
     */
    private function __construct()
    {
        // blank to ensure singleton-ess
    }

    /**
     * Protects singleton against cloning
     * 
     * @throws Waf_Exception
     */
    public function __clone()
    {
        throw new Waf_Exception('Singleton can not be cloned');
    }
    
    
    /**
     * Singleton instantiator
     *
     * @return Waf_Registry
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new Waf_Registry();
        }

        return self::$_instance;
    }
    
    /**
     * Adds the prefix to the index, to keep Waf indexes unique in 
     * the Zend_Registry.
     * 
     * @param string $index
     * @return string - prefixed index
     */
    private static function _addPrefix($index)
    {
        return self::REGISTRY_NAMESPACE . '_' . $index;
    }
    
    /**
     * Static set interface, same as Zend_Registry::set()
     * 
     * @param string $index
     * @param mixed $value
     */
    public static function set($index, $value)
    {
        Zend_Registry::set(self::_addPrefix($index), $value);
    }
    
    /**
     * Static get interface, same as Zend_Registry::get()
     * 
     * @param string $index
     */
    public static function get($index)
    {
        return Zend_Registry::get(self::_addPrefix($index));
    }
    
    /**
     * Static interface to check if a value is already in the registry
     * 
     * @param string $index
     * @return bool
     */
    public static function isRegistered($index)
    {
        return Zend_Registry::isRegistered(self::_addPrefix($index));
    }
    
    /**
     * Object interface for setting a registry value
     * 
     * @param string $index
     * @param mixed $value
     */
    public function __set($index, $value)
    {
        self::set($index, $value);
    }
    
    /**
     * Object interface for getting a registry value
     * 
     * @param string $index
     * @return mixed
     */
    public function __get($index)
    {
        return self::get($index);
    }
    
    /**
     * Object interface to check if a value is already in the registry
     * 
     * @param string $index
     * @return bool
     */
    public function __isset($index)
    {
        return self::isRegistered($index);
    }
}