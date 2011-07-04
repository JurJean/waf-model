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
    private $_id;

    public function __construct($id)
    {
        $this->_id = $id;
    }

    public function filter($query)
    {
        return $query->where('id = ?', $this->_id);
    }
}