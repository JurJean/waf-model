<?php
/**
 * Abstract MapperDriver
 *
 * The MapperDriver allows Mappers to be loaded on demand, using for example
 * configuration files or via reflection.
 *
 * @category   Waf
 * @package    Waf_Model
 * @subpackage Mapper_Driver
 * @version    $Id:$
 */
abstract class Waf_Model_Mapper_Driver_DriverAbstract
{
    private $_cache;

    /**
     * Constructor accepts $options which are set using the
     * Waf_Model_Configurator
     *
     * @param array $options
     * @return void
     */
    public function __construct($options = null)
    {
        Waf_Model_Configurator::setConstructorOptions($this, $options);
    }

    /**
     * Get the (cached) mapper
     * 
     * @param string $entityName
     * @return Waf_Model_Mapper_MapperAbstract
     */
    public function getMapper($entityName)
    {
        $key = $this->_generateCacheKey($entityName);
        if ($this->hasCache() && $mapper = $this->getCache()->load($key)) {
            return $mapper;
        }

        $mapper = $this->loadMapper($entityName);

        if ($this->hasCache()) {
            $this->getCache()->save($mapper, $key);
        }

        return $mapper;
    }

    /**
     * Abstract method to actually load the Mapper
     *
     * @return Waf_Model_Mapper_MapperAbstract
     */
    abstract public function loadMapper($entityName);

    public function setCache(Zend_Cache_Core $cache)
    {
        $this->_cache = $cache;
        return $this;
    }

    public function hasCache()
    {
        return null !== $this->_cache;
    }

    public function getCache()
    {
        if (!$this->hasCache()) {
            throw new Waf_Model_Mapper_Driver_Exception(
                'No MapperDriver cache set'
            );
        }

        return $this->_cache;
    }

    protected function _generateCacheKey($entityName)
    {
        return get_class($this) . '_' . $entityName;
    }
}