<?php
/**
 * Helps in matching an Entity to a group of Entities of another type
 *
 * @category   Waf
 * @package    Waf_Model
 * @subpackage Mapper_Property_OneToMany_QueryFilter
 * @version    $Id: $
 */
class Waf_Model_Mapper_Property_OneToMany_QueryFilter
    extends Waf_Model_QueryFilter
{
    private $_relatedFieldName;
    private $_fieldName;
    private $_state;
    
    public function __construct(Waf_Model_Mapper_Property_OneToMany $oneToMany, $state)
    {
        $this->_relatedFieldName = $oneToMany->getRelatedFieldName();
        $this->_fieldName        = $oneToMany->getFieldName();
        $this->_state            = $state;
    }

    public function filter($query)
    {
        $query->where(
            sprintf('%s = ?', $this->_relatedFieldName),
            $this->_state[$this->_fieldName]
        );
        return parent::filter($query);
    }

    public function __sleep()
    {
        return array(
            '_relatedFieldName',
            '_fieldName',
            '_state',
            '_filters',
            '_namespace'
        );
    }
}