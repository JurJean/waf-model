<?php
/**
 *
 * @todo determine if this class is wanted/useful
 * @category   Waf
 * @package    Waf_Model
 * @subpackage QueryFilter_ByIdentity
 * @version    $Id: $
 */
class Waf_Model_QueryFilter_ByIdentity implements Waf_Model_QueryFilter_QueryFilterInterface
{
    private $_fieldName;
    private $_id;

    public function __construct($fieldName, $id)
    {
        $this->_fieldName = $fieldName;
        $this->_id = $id;
    }

    public function filter($query)
    {
        return $query->where($this->_fieldName . ' = ?', $this->_id);
    }
}