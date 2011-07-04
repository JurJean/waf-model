<?php
/**
 * Maps Entity Identities
 *
 * Provides a default configuration and allows specific behaviours in the future
 *
 *
 */
class Waf_Model_Mapper_Identity extends Waf_Model_Mapper_MapperAbstract
{
    /**
     * @var string
     */
    private $_propertyName = '_id';

    /**
     * @var string
     */
    private $_fieldName = 'id';

    /**
     * Set Property name for Identity
     *
     * @param string $propertyName
     * @return Waf_Model_Mapper_Identity
     */
    public function setPropertyName($propertyName)
    {
        $this->_propertyName = $propertyName;

        return $this;
    }

    /**
     * Get the Property name for Identity - defaults to 'id'
     *
     * @return string
     */
    public function getPropertyName()
    {
        return $this->_propertyName;
    }

    /**
     * Set the Field name for Identity
     *
     * @param string $fieldName
     * @return Waf_Model_Mapper_Identity
     */
    public function setFieldName($fieldName)
    {
        $this->_fieldName = $fieldName;

        return $this;
    }

    /**
     * Get the Field name for Identity - defaults to 'id'
     *
     * @return string
     */
    public function getFieldName()
    {
        return $this->_fieldName;
    }

    /**
     * Convert Storage Identity to Entity Identity
     *
     * @param array $state
     * @return integer
     */
    public function toEntity($state)
    {
        return array(
            $this->getPropertyName() => $state[$this->getFieldName()]
        );
    }

    /**
     * Convert Entity Identity to Storage Identity.
     *
     * @param array $state
     * @return null|integer
     */
    public function toStorage($state)
    {
        return array(
            $this->getFieldName() => $state[$this->getPropertyName()]
        );
    }
}