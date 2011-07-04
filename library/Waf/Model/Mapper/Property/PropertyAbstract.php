<?php
/**
 * Abstract MapperProperty defined the mail property logic
 *
 * @todo 'notnull' setting for all properties
 * @category   Waf
 * @package    Waf_Model
 * @subpackage Mapper_Property_PropertyAbstract
 * @version    $Id: $
 */
abstract class Waf_Model_Mapper_Property_PropertyAbstract
{
    /**
     * @var Waf_Model
     */
    private $_model;

    /**
     * @var string The property name as defined in the Entity
     */
    private $_propertyName;

    /**
     * @var string The property name as defined in the Storage
     */
    private $_fieldName;

    private $_isStoraged = true;

    /**
     * Constructor requires the property name as defined in the Entity.
     *
     * - If $options is an array, the property gets configured using
     *   Waf_Model_Configurator
     * - If $options is a string, it is treated as it would be the property name
     *   as defined in the Storage
     *
     * @param string $propertyName
     * @param array|string $options
     */
    public function __construct($options = array())
    {
        if (is_string($options)) {
            $options = array(
                'propertyName' => $options
            );
        }

        if (!isset($options['propertyName'])) {
            throw new Waf_Model_Mapper_Exception(
                'PropertyName is required for configuring a Property'
            );
        }

        Waf_Model_Configurator::setOptions($this, $options);
    }

    public function setModel(Waf_Model $model)
    {
        $this->_model = $model;
        return $this;
    }

    public function getModel()
    {
        if (null === $this->_model) {
            $this->setModel(Waf_Model::getRegistered());
        }

        return $this->_model;
    }

    public function setPropertyName($propertyName)
    {
        $this->_propertyName = $propertyName;

        return $this;
    }

    /**
     * Get the property name as defined in the Entity
     *
     * @return string
     */
    public function getPropertyName()
    {
        return $this->_propertyName;
    }

    public function setFieldName($fieldName)
    {
        $this->_fieldName = $fieldName;

        return $this;
    }

    /**
     * Get the property name as defined in the Storage
     *
     * @return string
     */
    public function getFieldName()
    {
        if (null === $this->_fieldName) {
            $filter = new Zend_Filter_Word_CamelCaseToUnderscore();
            $this->_fieldName = ltrim(
                strtolower(
                    $filter->filter($this->getPropertyName())
                ),
                '_'
            );
        }

        return $this->_fieldName;
    }

    /**
     * Abstract method to be called when a Storage value is converted to an
     * Entity
     *
     * @param mixed $value
     * @return mixed
     */
    abstract public function toEntity($state);

    /**
     * Abstract method to be called when an Entity value is converted to Storage
     *
     * @param mixed $value
     * @return mixed
     */
    abstract public function toStorage($state);

    /**
     * Disable the Property to be Storaged
     *
     * @return Waf_Model_Mapper_Property_PropertyAbstract
     */
    public function disableStorage()
    {
        $this->_isStoraged = false;
    }

    /**
     * Not all properties may be saved to Storage.
     *
     * A property can override this method to disable the toStorage functionality
     * 
     * @return boolean
     */
    public function isStoraged()
    {
        return $this->_isStoraged;
    }
}