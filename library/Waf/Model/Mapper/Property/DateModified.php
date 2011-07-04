<?php
/**
 * MapperPropery which handles modification dates. The date is regenerated every
 * time toStorage() is called
 *
 * @category   Waf
 * @package    Waf_Model
 * @subpackage Mapper_Property_DateModified
 * @version    $Id: DateModified.php 555 2010-12-06 19:21:20Z jur $
 */
class Waf_Model_Mapper_Property_DateModified extends Waf_Model_Mapper_Property_DateTime
{
    /**
     * Regenerates the current dateTime
     *
     * @param null|Zend_Date $value
     * @return string
     */
    public function toStorage($state)
    {
        $state[$this->getPropertyName()] = Zend_Date::now();
        return parent::toStorage($state);
    }
}