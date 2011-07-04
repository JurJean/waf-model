<?php
/**
 * MapperDriver using Zend_Config to autoload Mapping configurations from
 * configured paths.
 *
 * @category   Waf
 * @package    Waf_Model
 * @subpackage Mapper_Driver
 * @version    $Id:$
 */
class Waf_Model_Mapper_Driver_Zend_Config
    extends Waf_Model_Mapper_Driver_DriverAbstract
{
    /**
     * @var array
     */
    protected $_configPaths = array();

    /**
     * @var string
     */
    protected $_configType;

    /**
     * Set multiple configuration paths at once
     *
     * @param array $paths
     * @return Waf_Model_Mapper_Driver_Zend_Config
     */
    public function setConfigPaths(array $paths)
    {
        foreach ($paths as $path) {
            $this->addConfigPath($path);
        }

        return $this;
    }

    /**
     * Add a configuration path
     *
     * @param string $path
     * @return Waf_Model_Mapper_Driver_Zend_Config
     */
    public function addConfigPath($path)
    {
        $this->_configPaths[] = $path;

        return $this;
    }

    /**
     * Get all configuration paths
     *
     * @return array
     */
    public function getConfigPaths()
    {
        if (empty($this->_configPaths)) {
            throw new Waf_Model_Mapper_Driver_Exception('No config paths set');
        }

        return $this->_configPaths;
    }

    /**
     * Set the type of config, can be whatever Zend_Config can hanlde
     *
     * @param string $type
     * @return Waf_Model_Mapper_Driver_Zend_Config
     */
    public function setConfigType($configType)
    {
        $this->_configType = $configType;

        return $this;
    }

    /**
     * Get type of Config
     *
     * @return string
     */
    public function getConfigType()
    {
        if (null === $this->_configType) {
            throw new Waf_Model_Mapper_Driver_Exception('No config type set');
        }

        return $this->_configType;
    }

    public function getMapper($entityName)
    {
        return $this->loadMapper($entityName);
    }

    /**
     * Defined by Waf_Model_Mapper_Driver_DriverAbstract, this method will try
     * to find a configuration file in any of the set ConfigPaths
     *
     * @param string $entityName
     * @return Waf_Model_Mapper_MapperAbstract
     */
    public function loadMapper($entityName)
    {
        return new Waf_Model_Mapper(
            $this->getConfig($entityName)
        );
    }

    /**
     * Get config for $entityName from one of the loaded config paths
     *
     * @param string|Waf_Model_Entity_EntityAbstract $entityName
     * @return array
     */
    public function getConfig($entityName)
    {
        if ($entityName instanceof Waf_Model_Entity_EntityAbstract) {
            $entityName = get_class($entityName);
        }

        $key = implode('_', array(
            get_class($this),
            $entityName,
            'Config'
        ));

        if ($this->hasCache() && $this->getCache()->test($key)) {
            return $this->getCache()->load($key);
        }

        $configFile  = $entityName . '.' . $this->getConfigType();
        $configClass = 'Zend_Config_' . ucfirst($this->getConfigType());

        foreach ($this->getConfigPaths() as $path) {
            if (file_exists($path . DIRECTORY_SEPARATOR . $configFile)) {
                $config = new $configClass(
                    $path . DIRECTORY_SEPARATOR . $configFile
                );
                $config = $config->toArray();
                $config['entityName'] = $entityName;
                $config['propertyMapper'] = array(
                    'properties' => array()
                );
                foreach ($config['properties'] as $propertyName => $property) {
                    $property['propertyName'] = $propertyName;
                    $config['propertyMapper']['properties'][] = $property;
                }

                if ($this->hasCache()) {
                    $this->getCache()->save($config, $key);
                }

                return $config;
            }
        }

        throw new Waf_Model_Mapper_Driver_Exception(sprintf(
            'Could not find a configuration for %s',
            $entityName
        ));
    }
}