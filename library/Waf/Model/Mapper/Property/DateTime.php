<?php
/**
 * MapperPropery which handles dateTimes, and is not to be confused with the
 * Date MapperProperty. Creates and receives Zend_Date instances.
 *
 * @category   Waf
 * @package    Waf_Model
 * @subpackage Mapper_Property_DateTime
 * @version    $Id: $
 */
class Waf_Model_Mapper_Property_DateTime extends Waf_Model_Mapper_Property_PropertyAbstract
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
     * Convert a dateTime string to a Zend_Date instance
     *
     * @param string $value
     * @return Zend_Date
     */
    public function toEntity($state)
    {
        $value = $state[$this->getFieldName()];
        
        if (null === $value && false === $this->_notNull) {
            return null;
        } else { 
            $value = new Zend_Date($value, 'yyyy-MM-dd HH:mm:ss');
        }
            
        return $value;
    }

    /**
     * Convert Zend_Date instance to a dateTime string
     *
     * @param Zend_Date $value
     * @return string
     */
    public function toStorage($state)
    {
        $value = $state[$this->getPropertyName()];

        if (null === $value && false === $this->_notNull) {
            return null;
        }

        if (!$value instanceof Zend_Date) {
            throw new Waf_Model_Mapper_Exception(
                '$value must be an instance of Zend_Date'
            );
        }
        
        return $value->get('yyyy-MM-dd HH:mm:ss');
    }
}