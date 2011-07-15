<?php
/**
 * Access Waf_Model_EntityManager from your actions
 *
 * @category   Waf
 * @package    Waf_Zend_Controller
 * @subpackage Action_Helper
 * @version    $Id: $
 */
class Waf_Zend_Controller_Action_Helper_EntityManager
    extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * @var  Waf_Model_EntityManager
     */
    private $_entityManager;

    /**
     * Get the EntityManager from registered Waf_Model instance
     *
     * @return Waf_Model_EntityManager
     */
    public function getEntityManager()
    {
        if (null === $this->_entityManager) {
            $model = Waf_Model::getRegistered();
            $this->_entityManager = $model->getEntityManager();
        }

        return $this->_entityManager;
    }

    /**
     * Proxy calls to getEntityManager()
     *
     * @param string $method
     * @param array $params
     * @return mixed
     */
    public function __call($method, $params)
    {
        return call_user_func_array(
            array($this->getEntityManager(), $method),
            $params
        );
    }
}