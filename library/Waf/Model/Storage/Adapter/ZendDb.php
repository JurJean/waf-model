<?php
/**
 * Storage Adapter for Zend_Db
 * 
 * @category   Waf
 * @package    Waf_Model
 * @subpackage Storage
 * @version    $Id: ZendDb.php 498 2010-11-09 14:54:08Z jur $
 */
class Waf_Model_Storage_Adapter_ZendDb extends Waf_Model_Storage_AdapterAbstract
{
    /**
     * Creates a Zend_Db_Adapter instance of the given type with the given
     * parameters.
     * 
     * @param string $zendDbAdapterType
     * @param array $params
     * @return void
     */
    public function __construct($zendDbAdapterType = null, $params = array())
    {
        if (null !== $zendDbAdapterType) {
            if ($zendDbAdapterType instanceof Zend_Db_Adapter_Abstract) {
                $connection = $zendDbAdapterType;
            } else {
                $connection = Zend_Db::factory($zendDbAdapterType, $params);
            }
            
            $this->setConnection($connection);
        }
    }

    /**
     * Creates a new Zend_Db_Select instance. $name should be the table you're
     * creating the query for.
     *
     * @param string $name
     * @return Zend_Db_Select
     */
    public function createQuery($name)
    {
        $select = new Zend_Db_Select($this->getConnection());
        $select->from($name);

        return $select;
    }

    /**
     * Begin transaction
     *
     * @return Waf_Model_Storage_Adapter_ZendDb
     */
    public function beginTransaction()
    {
        $this->getConnection()->beginTransaction();

        return $this;
    }

    /**
     * Commit transaction
     *
     * @return Waf_Model_Storage_Adapter_ZendDb
     */
    public function commitTransaction()
    {
        $this->getConnection()->commit();

        return $this;
    }

    /**
     * Roll back transaction
     *
     * @return Waf_Model_Storage_Adapter_ZendDb
     */
    public function rollbackTransaction()
    {
        $this->getConnection()->rollback();

        return $this;
    }
    
    /**
     * Execute query
     * 
     * @param mixed $query
     * @return Waf_Model_Storage_Adapter_ZendDb 
     */
    public function query($query)
    {
        $this->getConnection()->query($query);
        return $this;
    }

    /**
     * Insert $data to table with $name. Returns last inserted id.
     *
     * @param string $name
     * @param array $data
     * @return integer
     */
    public function insert($name, $data)
    {
        $this->getConnection()->insert($name, $data);
        return $this->getConnection()->lastInsertId($name);
    }

    /**
     * Populate $data in table with $name for all records matching the
     * $queryFilter
     *
     * $queryFilter can be either an integer representing primary key or an
     * instance of Waf_Model_QueryFilter_QueryFilterInterface
     *
     * @param string $name
     * @param array $data
     * @param mixed $queryFilter
     */
    public function update($name, $data, $queryFilter)
    {
        if ($queryFilter instanceof Waf_Model_QueryFilter_QueryFilterInterface) {
            $query = $this->createQuery($name);
            $queryFilter->filter($query);
            $where = $query->getPart(Zend_Db_Select::WHERE);
        } else if (is_numeric($queryFilter)) {
            $where = 'id = ' . $queryFilter;
        }
        
        $this->getConnection()->update($name, $data, $where);
    }

    /**
     * Delete all records in table with $name matching the $queryFilter
     *
     * $queryFilter can be either an integer representing primary key or an
     * instance of Waf_Model_QueryFilter_QueryFilterInterface
     *
     * @param string $name
     * @param mixed $queryFilter
     */
    public function delete($name, $queryFilter)
    {
        if ($queryFilter instanceof Waf_Model_QueryFilter_QueryFilterInterface) {
            $query = $this->createQuery($name);
            $queryFilter->filter($query);
            $where = $query->getPart(Zend_Db_Select::WHERE);
        } else if (is_numeric($queryFilter)) {
            $where = 'id = ' . $queryFilter;
        }
        

        $this->getConnection()->delete($name, $where);
    }

    /**
     * Count the number of records in table with $name matching the $queryFilter
     *
     * $queryFilter can be either multiple integers representing primary key or
     * an instance of Waf_Model_QueryFilter_QueryFilterInterface
     *
     * @param string $name
     * @param mixed $queryFilter
     */
    public function count($name, $queryFilter)
    {
        return $this->paginate($name, $queryFilter)->count();
    }

    /**
     * Find ONE record in table with $name matching the $queryFilter
     *
     * $queryFilter can be either an integer representing primary key or an
     * instance of Waf_Model_QueryFilter_QueryFilterInterface
     * 
     * @param string $name
     * @param mixed $queryFilter
     * @return null|array
     */
    public function find($name, $queryFilter)
    {
        return $this->getConnection()
            ->fetchRow($this->_parseQueryFilter($name, $queryFilter));
    }

    /**
     * Fetch ALL records in table with $name matching the $queryFilter
     *
     * $queryFilter can be either multiple integers representing primary key or
     * an instance of Waf_Model_QueryFilter_QueryFilterInterface
     *
     * @param string $name
     * @param mixed $queryFilter
     * @return null|array
     */
    public function fetch($name, $queryFilter)
    {
        return $this->getConnection()
            ->fetchAll($this->_parseQueryFilter($name, $queryFilter));
    }

    /**
     * Paginate records in table with $name matching the $queryFilter
     *
     * $queryFilter can be either multiple integers representing primary key or
     * an instance of Waf_Model_QueryFilter_QueryFilterInterface
     *
     * @param string $name
     * @param mixed $queryFilter
     * @return Zend_Paginator_Adapter
     */
    public function paginate($name, $queryFilter)
    {
        return new Waf_Model_Paginator_Adapter_ZendDb(
            $this->_parseQueryFilter($name, $queryFilter)
        );
    }

    /**
     * Parse $queryFilter
     * - If $queryFilter is an instance of Waf_Model_QueryFilter_QueryFilterInterface,
     *   it calls the filter() method populated with createQuery()
     * - If $queryFilter is numeric, it tries to detect primary key
     *
     * @todo proper checking of primary key
     * @param <type> $queryFilter
     */
    protected function _parseQueryFilter($name, $queryFilter)
    {
        if ($queryFilter instanceof Waf_Model_QueryFilter_QueryFilterInterface) {
            $query = $queryFilter->filter($this->createQuery($name));
        }

        if (is_numeric($queryFilter)) {
            $query = $this->createQuery($name)->where('id = ?', $queryFilter);
        }

        if (!isset($query)) {
            throw new Waf_Model_Storage_Exception('Invalid $queryFilter passed');
        }

        return $query;
    }
}