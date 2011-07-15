<?php
/**
 * Defines the interface for a ParamResolver
 *
 * @category   Waf
 * @package    Waf_Zend_Controller
 * @subpackage Action_Helper_Param_Resolver
 * @version    $Id: $
 */
interface Waf_Zend_Controller_Action_Helper_Param_Resolver
{
    public function resolve($value);
}