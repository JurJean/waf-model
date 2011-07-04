<?php
/**
 * MapperPropery which handles creation dates. The date is only generated the
 * first time toStorage() is called
 *
 * @category   Waf
 * @package    Waf_Model
 * @subpackage Mapper_Property_DateCreated
 * @version    $Id: DateCreated.php 555 2010-12-06 19:21:20Z jur $
 */
class Waf_Model_Mapper_Property_DateCreated
    extends Waf_Model_Mapper_Property_DateTime
{
    /**
     * Convert $value to the current dateTime, but only if it is NULL
     *
     * @param null|Zend_Date $value
     * @return string
     */
    public function toStorage($state)
    {
        $value = $state[$this->getPropertyName()];
        if (null === $value) {
            $state[$this->getPropertyName()] = Zend_Date::now();
        }

        return parent::toStorage($state);
    }
}