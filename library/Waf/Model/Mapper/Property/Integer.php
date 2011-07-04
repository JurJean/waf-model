<?php
/**
 * MapperPropery which handles integer values
 *
 * @category   Waf
 * @package    Waf_Model
 * @subpackage Mapper_Property_Integer
 * @version    $Id: $
 */
class Waf_Model_Mapper_Property_Integer extends Waf_Model_Mapper_Property_PropertyAbstract
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
     * Forces $value to be an Integer
     *
     * @param integer $value
     * @return integer
     */
    public function toStorage($state)
    {
        $value = $state[$this->getPropertyName()];
        if (null !== $value || true === $this->_notNull) {
            $value = (int) $value;
        }
        return $value;
    }

    /**
     * Forces $value to be an Integer
     *
     * @param integer $value
     * @return integer
     */
    public function toEntity($state)
    {
        $value = $state[$this->getFieldName()];
        if (null !== $value || true === $this->_notNull) {
            $value = (int) $value;
        }
        
        return $value;
    }
}