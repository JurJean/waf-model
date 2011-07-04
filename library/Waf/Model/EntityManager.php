<?php
/**
 * An EntityManager instance is associated with a persistence context. A
 * persistence context is a set of entity instances in which for any persistent
 * entity identity there is a unique entity instance. Within the persistence
 * context, the entity instances and their lifecycle are managed.
 *
 * The EntityManager API is used to create and remove persistent entity
 * instances, to find entities by their Identity, and to query over entities.
 *
 * @category   Waf
 * @package    Waf_Model
 * @subpackage EntityManager
 * @version    $Id:$
 */
class Waf_Model_EntityManager extends Waf_Model_ElementAbstract
{
    /**
     * @var Waf_Model_UnitOfWork
     */
    private $_unitOfWork;

    /**
     * @var array of Waf_Model_EntityManager_IdentityMap's
     */
    private $_identityMaps = array();

    private $_repositories = array();

    /**
     * Set a Waf_Model_UnitOfWork instance
     *
     * @see Waf_Model_UnitOfWork
     * @param Waf_Model_UnitOfWork $unitOfWork
     * @return Waf_Model_EntityManager
     */
    public function setUnitOfWork(Waf_Model_UnitOfWork $unitOfWork)
    {
        $this->_unitOfWork = $unitOfWork;

        return $this;
    }

    /**
     * Get Waf_Model_UnitOfWork instance. If not set it defaults to
     * Waf_Model_UnitOfWork
     *
     * @return Waf_Model_UnitOfWork
     */
    public function getUnitOfWork()
    {
        if (null === $this->_unitOfWork) {
            $this->_unitOfWork = new Waf_Model_UnitOfWork($this);
        }

        return $this->_unitOfWork;
    }

    /**
     * Set IdentityMap
     *
     * @param Waf_Model_EntityManager_IdentityMap $identityMap
     * @return Waf_Model_EntityManager
     */
    public function setIdentityMap(
        Waf_Model_EntityManager_IdentityMap $identityMap, $entity)
    {
        if (is_object($entity)) {
            $entity = get_class($entity);
        }
        $this->_identityMaps[$entity] = $identityMap;
        return $this;
    }

    /**
     * Get IdentityMap
     *
     * @return Waf_Model_EntityManager_IdentityMap
     */
    public function getIdentityMap($entity)
    {
        if (is_object($entity)) {
            $entity = get_class($entity);
        }

        if (!isset($this->_identityMaps[$entity])) {
            $this->setIdentityMap(
                new Waf_Model_EntityManager_IdentityMap(),
                $entity
            );
        }

        return $this->_identityMaps[$entity];
    }

    public function getQueryMap($context)
    {
        if (!isset($this->_queryMaps[$context])) {
            $this->_queryMaps[$context] = new Waf_Model_EntityManager_QueryMap();
        }
        return $this->_queryMaps[$context];
    }

    /**
     * Get Repository for $entityName. $entityName can be either the Entity name
     * or an instance of Waf_Model_Entity_EntityAbstract
     *
     * @see Waf_Model_Repository
     * @todo allow loading of custom Repositories
     * @param string|Waf_Model_Entity_EntityAbstract $entityName
     * @return Waf_Model_Repository
     */
    public function getRepository($entityName)
    {
        if (is_object($entityName)) {
            if (!$entityName instanceof Waf_Model_Entity_EntityInterface) {
                throw new Waf_Model_EntityManager_Exception(
                    '$entityName should be a string or an instance of '
                    . 'Waf_Model_Entity_EntityAbstract'
                );
            }

            $entityName = get_class($entityName);
        }

        // For performance the determined repository is saved, as class_exists
        // takes care of 0.2 seconds execution time easilly
        if (!isset($this->_repositories[$entityName])) {
            $repositoryName = str_replace('_Entity_', '_Repository_', $entityName);
            if (@class_exists($repositoryName)) {
                $this->_repositories[$entityName] = $repositoryName;
            } else {
                $this->_repositories[$entityName] = 'Waf_Model_Repository';
            }
        }

        $repositoryName = $this->_repositories[$entityName];
        return new $repositoryName($this, $entityName);
    }

    /**
     * Get Mapper by $entityName from Waf_Model. $entityName can be either a
     * string or an instance of Waf_Model_Entity_EntityAbstract
     *
     * @param string|Waf_Model_Entity_EntityAbstract $entity
     * @return Waf_Model_Mapper_MapperAbstract
     */
    public function getMapper($entityName)
    {
        if (is_object($entityName)) {
            $entityName = get_class($entityName);
        }

        return $this->getModel()->getMapper($entityName);
    }

    /**
     * Notify the EntityManager that $entities should be persisted. $entities
     * can be a single Entity, or an array/Traversable containing several
     * Entities.
     *
     * All Entities passed become managed if they weren't.
     *
     * @param Waf_Model_Entity_EntityAbstract|Traversable|array $entity
     * @return Waf_Model_EntityManager
     */
    public function persist($entities)
    {
        if (!is_array($entities) && !$entities instanceof Traversable) {
            $entities = array($entities);
        }

        foreach ($entities as $entity) {
            $this->getUnitOfWork()->persist($entity);
            $this->getIdentityMap($entity)->manage($entity);
        };

        return $this;
    }

    /**
     * Notify the EntityManager that $entities should be removed. $entities
     * can be a single Entity, or an array/Traversable containing several
     * Entities.
     *
     * All Entities passed become detached if they werent, and then passed to the
     * UnitOfWork::remove()
     *
     * @see Waf_Model_UnitOfWork::remove()
     * @param Waf_Model_Entity_EntityAbstract $entity
     * @return Waf_Model_EntityManager
     */
    public function remove($entities)
    {
        if (!is_array($entities) && !$entities instanceof Traversable) {
            $entities = array($entities);
        }

        foreach ($entities as $entity) {
            $this->getUnitOfWork()->remove($entity);
            $this->getIdentityMap($entity)->detach($entity);
        };

        return $this;
    }

    /**
     * Synchronize the persistence context to the underlying database
     *
     * Proxies internally to UnitOfWork::flush()
     *
     * @return Waf_Model_EntityManager
     */
    public function flush()
    {
        $this->getUnitOfWork()->flush();

        return $this;
    }

    /**
     * Refresh the state of the instance from the database, overwriting changes
     * made to the Entity, if any.
     *
     * @param Waf_Model_Entity_EntityAbstract $entity
     * @return Waf_Model_Entity_EntityAbstract
     */
    public function refresh(Waf_Model_Entity_EntityAbstract $entity)
    {
        return $this->getMapper($entity)->refresh($entity);
    }

    /**
     * Count through Waf_Model_Mapper
     *
     * @param string $entityName
     * @param mixed $queryFilter
     */
    public function count($entityName, $queryFilter)
    {
        return $this->getMapper($entityName)->count($queryFilter);
    }

    /**
     * Find ONE Entity with $entityName that matches the QueryFilter.
     *
     * If an Entity is found, it becomes managed and is returned. Otherwise null
     * is returned.
     *
     * @see Waf_Model_QueryFilter
     * @param string $entityName
     * @param mixed $queryFilter
     * @return null|Waf_Model_Entity_EntityAbstract
     */
    public function find($entityName, $queryFilter)
    {
        $entity = $this->getMapper($entityName)->find($queryFilter);

        if (null !== $entity) {
            $this->manage($entity);
        }

        return $entity;
    }

    /**
     * Fetch MULTIPLE Entities with $entityName that matches the QueryFilter.
     *
     * If Entities are found, they become managed and are returned as a
     * Waf_Model_Collection. Otherwise null is returned.
     *
     * @param string $entityName
     * @param mixed $queryFilter
     * @return null|Waf_Model_Collection_CollectionAbstract
     */
    public function fetch($entityName, $queryFilter)
    {
        $collection = $this->getMapper($entityName)->fetch($queryFilter);

        if (null !== $collection) {
            foreach ($collection as $entity) {
                $this->manage($entity);
            }
        }

        return $collection;
    }

    /**
     * Get paginated results
     *
     * @param string $entityName
     * @param mixed $queryFilter
     * @return mixed
     */
    public function paginate($entityName, $queryFilter)
    {
        return $this->getMapper($entityName)->paginate($queryFilter);
    }

    /**
     * Get managed $entity
     *
     * @param <type> $entity
     * @return <type>
     */
    public function get($entity)
    {
        return $this->getIdentityMap($entity)->get($entity);
    }

    /**
     * Is $entity managed?
     *
     * @param Waf_Model_Entity_EntityAbstract $entity
     * @return boolean
     */
    public function contains($entity)
    {
        return $this->getIdentityMap($entity)->contains($entity);
    }

    /**
     * Manage $entity
     *
     * @param Waf_Model_Entity_EntityInterface $entity
     * @return Waf_Model_EntityManager
     */
    public function manage($entity)
    {
        return $this->getIdentityMap($entity)->manage($entity);
    }

    /**
     * Detach $entity
     *
     * @param Waf_Model_Entity_EntityInterface $entity
     * @return Waf_Model_EntityManager
     */
    public function detach(Waf_Model_Entity_EntityInterface $entity)
    {
        $this->getIdentityMap($entity)->detach($entity);
        return $this;
    }
}