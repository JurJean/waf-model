<?php
/**
 * Map properties.. Property may not be the best name as Identities and
 * Relations also are properties from the view of an Entity
 */
class Waf_Model_Mapper_Property extends Waf_Model_Mapper_MapperAbstract
{
    /**
     * @var array
     */
    private $_properties = array();
    private $pluginLoader;

    /**
     * Set multiple properties at once
     *
     * @param array $properties
     * @return Waf_Model_Mapper_Property
     */
    public function setProperties(array $properties)
    {
        foreach ($properties as $property) {
            $this->addProperty($property);
        }

        return $this;
    }

    /**
     * Add a Property
     * If $property is an array, the key 'type' must exist.
     *
     * @param mixed $property
     * @return Waf_Model_Mapper_Property
     */
    public function addProperty($property, $name = null)
    {
        if (is_array($property)) {
            if (!isset($property['type'])) {
                throw new Waf_Model_Mapper_Exception(
                    'When adding a property by array, the key type must exist'
                );
            }
            $type = $property['type'];
            if (!@class_exists($type)) {
                $type = $this->getPluginLoader()->load($type);
            }
            unset($property['type']);
            $property = new $type($property);
        }

        $this->_properties[$property->getPropertyName()] = $property;

        return $this;
    }

    public function getProperty($name)
    {
        if (!isset($this->_properties[$name])) {
            throw new Waf_Model_Mapper_Exception(sprintf(
                'Property with name %s not set',
                $name
            ));
        }

        return $this->_properties[$name];
    }

    /**
     * Get all defined properties
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->_properties;
    }

    /**
     * Process all Properties to Entity values
     *
     * @param array $state
     * @return array
     */
    public function toEntity($state)
    {
        $result = array();

        foreach ($this->getProperties() as $property) {
            $value = $property->toEntity($state);

            if (!is_array($value)) {
                $result[$property->getPropertyName()] = $value;
            } else {
                $result += $value;
            }
        }


        return $result;
    }

    /**
     * Process all Properties to Storage values
     *
     * @param array $state
     * @return array
     */
    public function toStorage($state)
    {
        $result = array();

        foreach ($this->getProperties() as $property) {
            if (!$property->isStoraged()) {
                continue;
            }
            
            $value = $property->toStorage($state);
            if (!is_array($value)) {
                $result[$property->getFieldName()] = $value;
            } else {
                $result += $value;
            }
        }

        return $result;
    }

    /**
     * Set plugin loader
     *
     * @param Zend_Loader_PluginLoader $pluginLoader
     * @return Waf_Model_Mapper_Property
     */
    public function setPluginLoader(Zend_Loader_PluginLoader $pluginLoader)
    {
        $this->pluginLoader = $pluginLoader;
        return $this;
    }

    /**
     * Get pluginLoader
     *
     * @return Zend_Loader_PluginLoader
     */
    protected function getPluginLoader()
    {
        if (null === $this->pluginLoader) {
            $this->setPluginLoader(
                new Zend_Loader_PluginLoader(array(
                    'Waf_Model_Mapper_Property'     => dirname(__FILE__) . '/Property',
                    'App_Waf_Model_Mapper_Property' => 'App/Waf/Model/Mapper/Property'
                ))
            );
        }
        return $this->pluginLoader;
    }
}