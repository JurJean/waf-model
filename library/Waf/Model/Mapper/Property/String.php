<?php
/**
 * MapperPropery which handles string values
 *
 * @category   Waf
 * @package    Waf_Model
 * @subpackage Mapper_Property_String
 * @version    $Id:$
 */
class Waf_Model_Mapper_Property_String
    extends Waf_Model_Mapper_Property_PropertyAbstract
{
    /**
     * @var boolean
     */
    protected $_notNull = true;

    /**
     * Set wether the property is allowed to be NULL
     *
     * @param boolean $flag
     * @return Waf_Model_Mapper_Property_String
     */
    public function setNotNull($flag)
    {
        $this->_notNull = (bool) $flag;

        return $this;
    }

    /**
     * Convert $value to a string.
     *
     * @param string $value
     * @return string
     */
    public function toStorage($state)
    {
        $value = $state[$this->getPropertyName()];
        if (null !== $value || true === $this->_notNull) {
            $value = (string) $value;
        }

        return $value;
    }

    /**
     * Convert $value to a string.
     *
     * @param string $value
     * @return string
     */
    public function toEntity($state)
    {
        $value = $state[$this->getFieldName()];
        if (null !== $value || true === $this->_notNull) {
            $value = (string) $value;
        }

        return $value;
    }
}