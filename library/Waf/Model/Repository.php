<?php
/**
 * Waf_Model_Repository allows you to retrieve the stored Entities
 *
 * You can see this class just like Waf_Model_Collection, but with filtering
 * options.
 *
 * A Repository acts like an in-memory collection of Entities, which can be
 * easelly retrieved using QueryFilters. The Repository uses the
 * Waf_Model_QueryFilter by default, and allows you to chain several QueryFilters
 * together.
 *
 * Chaining is done by fetching calls to Methods, and use the method name to
 * load the QueryFilter from set namespace.
 *
 * The QueryFilter gets the correct namespace automatically set by replacing
 * 'Entity' in the EntityName to 'QueryFilter'. So Model_Entity_User becomes the
 * Model_QueryFilter_User namespace.
 *
 * For example $repository->orderByName(); will add the
 * Model_QueryFilter_User_OrderByName() QueryFilter in the chain
 *
 * Every time you need to retrieve Entities, create a new Repository and chain
 * together what you want. Then call one of the find(), fetch(), count(),
 * exists() or paginate() methods.
 *
 * @category   Waf
 * @package    Waf_Model
 * @subpackage Repository
 * @version    $Id:$
 */
class Waf_Model_Repository implements IteratorAggregate, Countable
{
    /**
     * @var string
     */
    protected $_entityName;
    /**
     * @var Waf_Model_EntityManager
     */
    protected $_entityManager;

    /**
     * @var Waf_Model_QueryFilter_QueryFilterInterface
     */
    protected $_queryFilter;

    /**
     * Constructor
     *
     * @param Waf_Model_EntityManager $entityManager
     * @param string $entityName
     */
    public function __construct(Waf_Model_EntityManager $entityManager, $entityName)
    {
        $this->_entityManager = $entityManager;
        $this->_entityName    = $entityName;
    }

    /**
     * Get the EntityManager
     *
     * @return Waf_Model_EntityManager
     */
    public function getEntityManager()
    {
        return $this->_entityManager;
    }

    /**
     * Get the name of the Entity  this repository is working for
     *
     * @return string
     */
    public function getEntityName()
    {
        return $this->_entityName;
    }

    /**
     * Set the QueryFilter
     *
     * @param Waf_Model_QueryFilter $queryFilter
     * @return self Fluent interface
     */
    public function setQueryFilter(Waf_Model_QueryFilter $queryFilter)
    {
        $this->_queryFilter = $queryFilter;

        return $this;
    }

    /**
     * Get the QueryFilter, defaults to Waf_Model_QueryFilter which allows to
     * chain several QueryFilters tegether
     *
     * @return self Fluent interface
     */
    public function getQueryFilter()
    {
        if (null === $this->_queryFilter) {
            $this->setQueryFilter(new Waf_Model_QueryFilter());
        }

        if (!$this->_queryFilter->hasNamespace()) {
            $this->_queryFilter->setNamespace(
                str_replace('_Entity_', '_QueryFilter_', $this->getEntityName())
            );
        }

        return $this->_queryFilter;
    }

    /**
     * Reset the QueryFilter, creates a new instance of last known QueryFilter
     *
     * @return self Fluent interface
     */
    public function resetQueryFilter()
    {
        $className = get_class($this->getQueryFilter());
        $this->setQueryFilter(new $className);

        return $this;
    }

    /**
     * Copy the repository in it's current status
     * 
     * @return Waf_Model_Repository
     */
    public function copy()
    {
        $copy = clone $this;
        return $copy;
    }

    /**
     * Proxy undefined methods to the QueryFilter's addFilter() method
     *
     * @param string $method
     * @param array $params
     */
    public function __call($method, $params)
    {
        array_unshift($params, ucfirst($method));
        call_user_func_array(
            array($this->getQueryFilter(), 'addFilter'),
            $params
        );

        return $this;
    }

    /**
     * Do Entities exist matching the QueryFilter?
     *
     * @todo optimize!!!
     * @param mixed $queryFilter
     * @return boolean
     */
    public function exists($queryFilter = null)
    {
        return (bool) $this->count($queryFilter);
    }

    /**
     * Count Entities matching the QueryFilter
     *
     * @todo optimize!!!
     * @param mixed $queryFilter
     * @return integer
     */
    public function count($queryFilter = null)
    {
        return $this->getEntityManager()->count(
            $this->getEntityName(),
            $this->_buildQueryFilter($queryFilter)
        );
    }

    /**
     * Find Entities matching the QueryFilter
     *
     * @param mixed $queryFilter
     * @return null|Waf_Model_Entity_EntityAbstract
     */
    public function find($queryFilter = null)
    {
        return $this->getEntityManager()->find(
            $this->getEntityName(),
            $this->_buildQueryFilter($queryFilter)
        );
    }

    /**
     * Fetch Entities matching the QueryFilter
     *
     * @param mixed $queryFilter
     * @return null|Waf_Model_Collection_CollectionAbstract
     */
    public function fetch($queryFilter = null)
    {
        return $this->getEntityManager()->fetch(
            $this->getEntityName(),
            $this->_buildQueryFilter($queryFilter)
        );
    }

    /**
     * Paginate Entities matching the QueryFilter
     *
     * @todo optimize!!!
     * @param mixed $queryFilter
     * @return null|Zend_Paginator
     */
    public function paginate($queryFilter = null)
    {
        if (null === $queryFilter) {
            $queryFilter = $this->getQueryFilter();
        }

        return $this->getEntityManager()->paginate(
            $this->getEntityName(),
            $queryFilter
        );
    }

    /**
     * Build queryFilter either from parameter or current state
     *
     * @param Waf_Model_QueryFilter_QueryFilterInterface $queryFilter
     * @return Waf_Model_QueryFilter_QueryFilterInterface
     */
    private function _buildQueryFilter($queryFilter)
    {
        if (null === $queryFilter) {
            return $this->getQueryFilter();
        }

        if ($queryFilter instanceof Waf_Model_QueryFilter_QueryFilterInterface) {
            return $queryFilter;
        }

        return new Waf_Model_QueryFilter_ByIdentity($queryFilter);
    }

    /**
     * Return a new Waf_Model_Collection instance with the use of fetch() to
     * iterate over
     *
     * @defined by IteratorAggregate
     * @return Waf_Model_Collection
     */
    public function getIterator()
    {
        return $this->fetch();
    }

    /**
     * Also clone the queryFilter
     *
     * @return void
     */
    public function __clone()
    {
        $this->_queryFilter = clone $this->_queryFilter;
    }

    /**
     * Don't sleep EntityManager to improve caching
     *
     * @return array
     */
    public function __sleep()
    {
        return array(
            '_entityName',
            '_queryFilter'
        );
    }

    /**
     * Get EntityManager from registered Waf_Model on wakeup
     *
     * @return void
     */
    public function __wakeup()
    {
        $this->_entityManager = Waf_Model::getRegistered()->getEntityManager();
    }
}