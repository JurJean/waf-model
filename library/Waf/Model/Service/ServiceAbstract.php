<?php
/**
 * Service Abstract for the Waf_Model component
 * 
 * @category   Waf
 * @package    Waf_Model
 * @subpackage Service
 * @version    $Id: ServiceAbstract.php 502 2010-11-09 16:09:56Z pascaln $
 */
abstract class Waf_Model_Service_ServiceAbstract extends Waf_Model_ElementAbstract
{
    /**
     * Get EntityManager
     * @return Waf_Model_EntityManager
     */
    public function getEntityManager()
    {
        return $this->getModel()->getEntityManager();
    }


    /**
     * Get UnitOfWork
     * @return Waf_Model_UnitOfWork
     */
    public function getUnitOfWork()
    {
        return $this->getEntityManager()->getUnitOfWork();
    }

    /**
     * Schedule Query
     * @param (string) $query
     * @return Waf_Model_UnitOfWork
     */
    public function scheduleQuery($query)
    {
        return $this->getUnitOfWork()->scheduleQuery($query);
    }

    /**
     * Get Repository
     * @param (string) $entityName
     * @return Waf_Model_Repository
     */
    public function getRepository($entityName)
    {
        return $this->getEntityManager()->getRepository($entityName);
    }
}