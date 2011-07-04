<?php
/**
 * Defines the interface for Waf_Model paginators
 *
 * @category   Waf
 * @package    Waf_Model
 * @subpackage Paginator_Adapter
 * @version    $Id:$
 */
interface Waf_Model_Paginator_Adapter_AdapterInterface
    extends Zend_Paginator_Adapter_Interface
{
    public function setMapper(Waf_Model_Mapper_MapperAbstract $mapper);

    public function getMapper();
}