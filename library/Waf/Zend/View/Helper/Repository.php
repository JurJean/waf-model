<?php
/**
 * Access Waf_Model_Repository instances from the View
 *
 * @category   Waf
 * @package    Waf_Zend
 * @subpackage View_Helper
 * @version    $Id: $
 */
class Waf_Zend_View_Helper_Repository
    extends Waf_Zend_Controller_Action_Helper_Repository
        implements Zend_View_Helper_Interface
{
    /**
     * @var Zend_View
     */
    public $view;

    /**
     * Defined by Zend_View_Helper_Interface
     *
     * @param Zend_View_Interface $view
     * @return Waf_Zend_View_Helper_Repository
     */
    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Get Repository based on $entity and $module
     *
     * @see Waf_Zend_Controller_Action_Helper_Repository::getRepository()
     * @param string $entity
     * @param null|string $module
     * @return Waf_Model_Repository
     */
    public function repository($entity = null, $module = null)
    {
        return $this->getRepository($entity, $module);
    }
}