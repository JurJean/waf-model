<?php
/**
 * Resolve parameter from DbTable
 *
 * @category   Waf
 * @package    Waf_Zend_Controller
 * @subpackage Action_Helper_Param_Resolver
 * @version    $Id: $
 */
class Waf_Zend_Controller_Action_Helper_Param_Resolver_DbTableRow
    extends Waf_Zend_Controller_Action_Helper_Param_Resolver_ResolverAbstract
{
    protected $_dbTableClass;
    protected $_dbTable;
    protected $_resolveMethod = 'fetchRow';

    public function resolve($value)
    {
        $resolveMethod = $this->getMethod();
        $result = $this->getDbTable()->$resolveMethod($value);

        if (null === $result) {
            throw new Waf_Zend_Controller_Action_Helper_Param_Resolver_Exception(
                sprintf(
                    'Could not find anything based on %s using %s',
                    $value,
                    $this->getMethod()
                )
            );
        }

        return $result;
    }

    public function setDbTableClass($dbTableClass)
    {
        $this->_dbTableClass = $dbTableClass;
        return $this;
    }

    public function getDbTableClass()
    {
        if (null === $this->_dbTableClass) {
            throw new Waf_Zend_Controller_Action_Helper_Param_Resolver_Exception(
                'DbTableClass not defined'
            );
        }

        return $this->_dbTableClass;
    }

    public function setDbTable(Zend_Db_Table_Abstract $dbTable)
    {
        $this->_dbTable = $dbTable;
    }

    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $dbTableClass = $this->getDbTableClass();
            $this->_dbTable = new $dbTableClass();
        }

        return $this->_dbTable;
    }
}