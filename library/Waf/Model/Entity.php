<?php
/**
 * Default concrete Entity for the Waf_Model component
 * 
 * @category   Waf
 * @package    Waf_Model
 * @subpackage Entity
 * @version    $Id: Entity.php 388 2010-08-12 09:53:11Z jur $
 */
class Waf_Model_Entity extends Waf_Model_Entity_EntityAbstract
{
    protected $_id;

    public function getId()
    {
        return $this->_id;
    }
}