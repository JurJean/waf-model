<?php
/**
 * The QueryFilter is used by the Repository to create a chain of filters
 * which abstract the query implementation of the StorageAdapter. The logic
 * within the filter() method of the loaded filters WILL contain database
 * specific logic (at least at the moment).
 *
 * As this dependency is at one clear place this doesn't really matter, the only
 * drawback is that IF we want to switch to another StorageAdapter, we would
 * need to rewrite all of our filters.
 *
 * @category   Waf
 * @package    Waf_Model
 * @subpackage QueryFilter
 * @version    $Id:$
 */
class Waf_Model_QueryFilter
    implements Waf_Model_QueryFilter_QueryFilterInterface
{
    /**
     * @var array of Waf_Model_QueryFilter_QueryFilterInterface's
     */
    protected $_filters = array();

    /**
     * @var string
     */
    protected $_namespace;

    /**
     * Passes $query to all loaded filters filter() method
     *
     * @param mixed $query The StorageAdapter's query implementation
     * @return mixed
     */
    public function filter($query)
    {
        foreach ($this->getFilters() as $filter) {
            $filter->filter($query);
        }

        return $query;
    }

    /**
     * Set multiple Waf_Model_QueryFilter_QueryFilterInterface's at once,
     * overwriting any previously loaded $filters
     *
     * @param array $filters
     * @return Waf_Model_QueryFilter
     */
    public function setFilters(array $filters)
    {
        foreach ($filters as $key => $value) {
            if ($value instanceof Waf_Model_QueryFilter_QueryFilterInterface) {
                $this->addFilter($value);
            } else {
                $value = is_array($value) ? $value : array($value);
                array_unshift($value, ucfirst($key));
                call_user_func_array(
                    array($this, 'addFilter'),
                    $value
                );
            }
        }

        return $this;
    }

    /**
     * Add a $filter to the chain
     *
     * @param string|Waf_Model_QueryFilter_QueryFilterInterface $filter
     * @return Waf_Model_QueryFilter
     * @throws Waf_Model_QueryFilter_Exeption if $filter is not a string or not
     *         an instance of Waf_Model_QueryFilter_QueryFilterInterface
     */
    public function addFilter($filter, $params = array())
    {
        if (is_string($filter)) {
            $className = $this->hasNamespace()
                       ? $this->getNamespace() . '_' . $filter
                       : $filter;
            
            $filter = new $className($params);
        }

        if (!$filter instanceof Waf_Model_QueryFilter_QueryFilterInterface) {
            throw new Waf_Model_QueryFilter_Exception(
                '$filter must be a string or an instance of '
                . 'Waf_Model_QueryFilter_QueryFilterInterface'
            );
        }
        
        $this->_filters[get_class($filter)] = $filter;

        return $this;
    }

    /**
     * Checks whether a QueryFilter with $filterName exists
     *
     * @param string $filterName
     * @return boolean
     */
    public function hasFilter($filterName)
    {
        return isset($this->_filters[$filterName]);
    }

    /**
     * Get QueryFilter by class name
     *
     * @param string $filterName
     * @return Waf_Model_QueryFilter_QueryFilterInterface
     * @throws Waf_Model_QueryFilter_Exeption if there's no filter with
     *         $filterName in the chain
     */
    public function getFilter($filterName)
    {
        if (!isset($this->_filters[$filterName])) {
            throw new Waf_Model_QueryFilter_Exception(sprintf(
                'Filter by name %s is not in the filter chain',
                $filterName
            ));
        }

        return $this->_filters[$filterName];
    }

    /**
     * Get all filters in the chain
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->_filters;
    }

    /**
     * Clear all filters in the chain
     *
     * @return Waf_Model_QueryFilter
     */
    public function clearFilters()
    {
        $this->_filters = array();

        return $this;
    }

    /**
     * Set the QueryFilter Namespace. The namespace is used to autoload a
     * QueryFilter, to which the namespace is prepended. The Repository will
     * automatically set the namespace based on the EntityName
     *
     * @param string $namespace
     * @return Waf_Model_QueryFilter
     */
    public function setNamespace($namespace)
    {
        $this->_namespace = rtrim($namespace, '_');

        return $this;
    }

    /**
     * Is a namespace set?
     *
     * @return boolean
     */
    public function hasNamespace()
    {
        return null !== $this->_namespace;
    }

    /**
     * Get the namespace
     *
     * @return string
     * @throws Waf_Model_QueryFilter_Exception if no namespace is set
     */
    public function getNamespace()
    {
        if (!$this->hasNamespace()) {
            throw new Waf_Model_QueryFilter_Exception('No namespace defined');
        }

        return $this->_namespace;
    }

    /**
     * Only return important properties to serialize
     *
     * @return array
     */
    public function __sleep()
    {
        return array(
            '_filters',
            '_namespace'
        );
    }
}