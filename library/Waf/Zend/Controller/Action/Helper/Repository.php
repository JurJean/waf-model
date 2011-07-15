<?php
/**
 * Access Waf_Model_Repository from your actions
 * 
 * @category   Waf
 * @package    Waf_Zend_Controller
 * @subpackage Action_Helper
 * @version    $Id: $
 */
class Waf_Zend_Controller_Action_Helper_Repository
    extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * @var Waf_Model_EntityManager
     */
    private $_entityManager;

    /**
     * Direct method proxies to getRepository()
     *
     * @param string $entity
     * @param null|string $module
     * @return Waf_Model_Repository
     */
    public function direct($entity = null, $module = null)
    {
        return $this->getRepository($entity, $module);
    }

    /**
     * Get Waf_Model_EntityManager from registered Waf_Model instance
     *
     * @return Waf_Model_EntityManager
     */
    public function getEntityManager()
    {
        if (null === $this->_entityManager) {
            $this->_entityManager = Waf_Model::getRegistered()->getEntityManager();
        }

        return $this->_entityManager;
    }

    /**
     * Get Entity name based on $entity and $module
     * 
     * @param null|string $entity
     * @param null|string $module
     * @return string
     */
    public function getEntityName($entity = null, $module = null)
    {
        $dashToCamelCaseFilter = new Zend_Filter_Word_DashToCamelCase();

        if (null === $entity) {
            $entity = ucfirst(
                $dashToCamelCaseFilter->filter(
                    $this->getRequest()->getControllerName()
                )
            );

            $module = $this->getRequest()->getModuleName();
        }

        if (is_string($module)) {
            $module = ucfirst(
                $dashToCamelCaseFilter->filter($module)
            );
            $entity = $module . '_Model_Entity_' . $entity;
        }

        return $entity;
    }

    /**
     * Get a new instance of the Repository based on $entity and $module
     * 
     * @param null|string $entity
     * @param null|string $module
     * @return Waf_Model_Repository
     */
    public function getRepository($entity = null, $module = null)
    {
        $repository = $this->getEntityManager()->getRepository(
            $this->getEntityName($entity, $module)
        );

        $repository->getQueryFilter()->setNamespace(
            str_replace(
                '_Entity_',
                '_QueryFilter_',
                $repository->getEntityName()
            )
        );

        return $repository;
    }

    /**
     * Proxy undefined method to getRepository()
     *
     * @param string $method
     * @param array $params
     * @return mixed
     */
    public function __call($method, $params)
    {
        return call_user_func_array(
            array($this->getRepository(), $method), $params
        );
    }
}