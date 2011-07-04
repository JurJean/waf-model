<?php
/**
 * MapperPropery which handles dates, and is not to be confused with the
 * DateTime MapperProperty. Creates and receives Zend_Date instances.
 *
 * @category   Waf
 * @package    Waf_Model
 * @subpackage Mapper_Property_Date
 * @version    $Id:$
 */
class Waf_Model_Mapper_Property_Date extends Waf_Model_Mapper_Property_PropertyAbstract
{
    /**
     * Convert a date string to a Zend_Date instance
     *
     * @param string $value
     * @return Zend_Date
     */
    public function toEntity($state)
    {
        $value = $state[$this->getFieldName()];
        return new Zend_Date($value, 'yyyy-MM-dd');
    }

    /**
     * Convert Zend_Date instance to a date string
     *
     * @param Zend_Date $value
     * @return string
     */
    public function toStorage($state)
    {
        $value = $state[$this->getPropertyName()];
        if (!$value instanceof Zend_Date) {
            throw new Waf_Model_Mapper_Exception(
                '$value must be an instance of Zend_Date'
            );
        }

        return $value->get('yyyy-MM-dd');
    }
}