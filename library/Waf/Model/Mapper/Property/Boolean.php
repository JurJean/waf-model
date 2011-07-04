<?php
/**
 * MapperPropery which handles boolean values
 *
 * @category   Waf
 * @package    Waf_Model
 * @subpackage Mapper_Property_Boolean
 * @version    $Id:$
 */
class Waf_Model_Mapper_Property_Boolean
    extends Waf_Model_Mapper_Property_PropertyAbstract
{
    /**
     * Convert storage boolean values to php booleans
     *
     * @param mixed $value
     * @return boolean
     */
    public function toEntity($state)
    {
        $value = $state[$this->getFieldName()];
        return ((int) $value) ? true : false;
    }
    
    /**
     * Convert boolean false to 0, boolean true to 1
     *
     * @param boolean $value
     * @return integer
     */
    public function toStorage($state)
    {
        $value = $state[$this->getPropertyName()];
        return $value ? 1 : 0;
    }
}