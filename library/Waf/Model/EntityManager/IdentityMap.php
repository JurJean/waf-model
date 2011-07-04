<?php
/**
 * IdentityMap contains a reference of all managed Entities by Identity
 *
 * @category   Waf
 * @package    Waf_Model
 * @subpackage EntityManager_IdentityMap
 * @version    $Id:$
 */
class Waf_Model_EntityManager_IdentityMap
{
    /**
     * @var array of Entities indexed by Identity
     */
    private $_map = array();

    /**
     * Get managed $entity
     *
     * @param Waf_Model_Entity_EntityInterface $entity
     * @return Waf_Model_Entity_EntityInterface
     */
    public function get($entityOrId)
    {
        $id = $this->_getEntityId($entityOrId);
        if (!$this->contains($id)) {
            throw new Waf_Model_EntityManager_NotManagedException(
                sprintf(
                    'Entity with id %d is not managed',
                    $id
                )
            );
        }

        return $this->_map[$id];
    }

    /**
     * Checks whether $entity is managed
     *
     * @param Waf_Model_Entity_EntityInterface $entity
     * @return boolean
     */
    public function contains($entityOrId)
    {
        $id = $this->_getEntityId($entityOrId);
        return isset($this->_map[$id]);
    }

    /**
     * Manage $entity
     * 
     * @param Waf_Model_Entity_EntityInterface $entity
     * @return Waf_Model_Entity_EntityInterface
     */
    public function manage(Waf_Model_Entity_EntityInterface $entity)
    {
        if ($this->contains($entity)) {
            return $this->get($entity);
        }
        $this->_map[$entity->getId()] = $entity;
        return $entity;
    }

    /**
     * Detach $entity
     * 
     * @param Waf_Model_Entity_EntityInterface $entity
     * @return Waf_Model_EntityManager_IdentityMap
     */
    public function detach(Waf_Model_Entity_EntityInterface $entity)
    {
        if (!$this->contains($entity)) {
            throw new Waf_Model_EntityManager_NotManagedException(
                sprintf(
                    'Cannot detach not managed entity %s with id %s',
                    get_class($entity),
                    $entity->getId()
                )
            );
        }

        unset($this->_map[$entity->getId()]);
        return $this;
    }

    protected function _getEntityId($entityOrId)
    {
        if ($entityOrId instanceof Waf_Model_Entity_EntityInterface) {
            return $entityOrId->getId();
        }
        return $entityOrId;
    }
}