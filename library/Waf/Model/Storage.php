<?php
/**
 * Storage management component of Waf_Model
 * 
 * Allows for the use of different types of storage and/or multiple
 * connections inside one Model.
 * 
 * @category   Waf
 * @package    Waf_Model
 * @subpackage Storage
 * @version    $Id: Storage.php 379 2010-08-06 13:46:21Z rick $
 */
class Waf_Model_Storage
{
    private $_adapters = array();
    private $_defaultAdapter = null;
    
    /**
     * Add an Adapter to the Storage manager
     * 
     * @param Waf_Model_Storage_AdapterAbstract $adapter
     * @param string $name (optional, defaults to 'default')
     */
    public function addAdapter(Waf_Model_Storage_AdapterAbstract $adapter, $name = 'default')
    {
        if (count($this->_adapters) === 0) {
            $this->setDefaultAdapter($adapter);
        }
        $this->_adapters[$name] = $adapter;
    }
    
    /**
     * Returns the requested adapter
     * 
     * @param $name|null
     * @return Waf_Model_Storage_AdapterAbstract
     */
    public function getAdapter($name = null)
    {
        if (null === $name) {
            return $this->getDefaultAdapter();
        } else {
            return $this->_adapters[$name];
        }
    }
    
    /**
     * Checks if the Adapter by the given name is exists
     * 
     * @param string $name
     * @return bool
     */
    public function hasAdapter($name)
    {
        return isset($this->_adapters[$name]);
    }
    
    /**
     * Sets the default Adapter 
     * 
     * @param $nameOrObject string|Waf_Model_Storage_AdapterAbstract
     * @throws Waf_Model_Exception if a name is given while no Adapter by that name is registered
     */
    public function setDefaultAdapter($nameOrObject)
    {
        if ($nameOrObject instanceof Waf_Model_Storage_AdapterAbstract) {
            $this->_defaultAdapter = $nameOrObject;
        } else {
            if ($this->hasAdapter($nameOrObject)) {
                $this->_defaultAdapter = $this->getAdapter($nameOrObject);
            } else {
                throw new Waf_Model_Exception('No such adapter');
            }
        }
    }
    
    /**
     * Returns the default adapter
     * 
     * @return Waf_Model_Storage_AdapterAbstract
     * @throws Waf_Model_Exception if no default adapter is known
     */
    public function getDefaultAdapter()
    {
        if (null === $this->_defaultAdapter) {
            throw new Waf_Model_Exception('No default adapter available');
        }
        return $this->_defaultAdapter;
    }
    
    public function hasDefaultAdapter()
    {
        return null !== $this->_defaultAdapter;
    }
}