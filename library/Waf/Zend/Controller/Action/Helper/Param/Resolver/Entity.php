<?php
/**
 * Resolve parameter from Repository
 *
 * @category   Waf
 * @package    Waf_Zend_Controller
 * @subpackage Action_Helper_Param_Resolver
 * @version    $Id: $
 */
class Waf_Zend_Controller_Action_Helper_Param_Resolver_Entity
    extends Waf_Zend_Controller_Action_Helper_Param_Resolver_ResolverAbstract
{
    protected $_repository;
    protected $_entityName;

    /**
     * Resolve object from Repository based on $value using getResolveMethod()
     * 
     * @param mixed $value
     * @return mixed
     * @throws Waf_Zend_Controller_Action_Helper_Param_Resolver_Exception
     *         if nothing matched $value
     */
    public function resolve($value)
    {
        $resolveMethod = $this->getMethod();
        $repository = $this->getRepository()->$resolveMethod($value);

        if (!$repository->exists()) {
            throw new Waf_Zend_Controller_Action_Helper_Param_Resolver_Exception(
                sprintf(
                    'Could not find %s based on %s using %s',
                    $this->getRepository()->getEntityName(),
                    $value,
                    $this->getMethod()
                )
            );
        }

        return $repository->find();
    }

    /**
     * Set the repository to fetch result from
     *
     * @param Waf_Model_Repository $repository
     * @return Waf_Zend_Controller_Action_Helper_Param_Resolver_ResolverAbstract
     */
    public function setRepository(Waf_Model_Repository $repository)
    {
        $this->_repository = $repository;
        return $this;
    }

    /**
     * Get the repository to fetch result from
     *
     * @return Waf_Model_Repository
     */
    public function getRepository()
    {
        if (null === $this->_repository) {
            $this->_repository = Waf_Model::getRegistered()
                ->getEntityManager()
                ->getRepository($this->getEntityName());
        }

        return $this->_repository;
    }

    public function setEntityName($entityName)
    {
        $this->_entityName = $entityName;
        return $this;
    }

    public function getEntityName()
    {
        if (null === $this->_entityName) {
            throw new Waf_Zend_Controller_Action_Helper_Param_Resolver_Exception(
                'No EntityName set to resolve'
            );
        }

        return $this->_entityName;
    }
}