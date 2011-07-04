<?php
/**
 * MapperPropery which handles float values
 *
 * @category   Waf
 * @package    Waf_Model
 * @subpackage Mapper_Property_Float
 * @version    $Id: Float.php 555 2010-12-06 19:21:20Z jur $
 */
class Waf_Model_Mapper_Property_Float extends Waf_Model_Mapper_Property_PropertyAbstract
{
    /**
     * Converts $value to Float
     *
     * @param float $value
     * @return float
     */
    public function toEntity($state)
    {
        $value = $state[$this->getFieldName()];
        return (float) $value;
    }
    
    /**
     * Converts $value to Float
     *
     * @param float $value
     * @return float
     */
    public function toStorage($state)
    {
        $value = $state[$this->getPropertyName()];
        return (float) $value;
    }
}