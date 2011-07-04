<?php
/**
 * Abstract definition of a Storage Adapter
 * 
 * Just to provide a standardized interface to retrieve the native
 * way of connecting to the storage mechanism used by the Adapter
 * 
 * 
 * @category   Waf
 * @package    Waf_Model
 * @subpackage Storage
 * @version    $Id: AdapterAbstract.php 498 2010-11-09 14:54:08Z jur $
 */
abstract class Waf_Model_Storage_AdapterAbstract
{
    /**
     * Connection object/variable
     * 
     * Could be anything that defines a connection to a storage mechanism.
     * like a file handle or a Zend_Db_Adapter instance
     * 
     * @var mixed
     */
    private $_connection = null;
    
    /**
     * Set the connection
     * 
     * @param mixed $connection
     */
    public function setConnection($connection)
    {
        $this->_connection = $connection;
    }
    
    /**
     * Returns the native storage Connection
     * 
     * @return mixed
     * @throws Waf_Model_Exception if none is set
     */
    public function getConnection()
    {
        if (null === $this->_connection) {
            throw new Waf_Model_Exception("No connection set");
        }
        return $this->_connection;
    }
    
    /**
     * Check if Adapter has a native Connection
     * 
     * @return bool
     */
    public function hasConnection()
    {
        return null !== $this->_connection;
    }

    /**
     * Abstract methods below may not be supported by every StorageAdapter,
     * but should be implemented as several Model elements depend on them. For
     * the most part find() and fetch() methods will be available.
     */
    abstract public function createQuery($name);

    abstract public function beginTransaction();

    abstract public function commitTransaction();

    abstract public function rollbackTransaction();
    
    abstract public function query($query);
    
    abstract public function insert($name, $data);

    abstract public function update($name, $data, $queryFilter);

    abstract public function delete($name, $queryFilter);

    abstract public function count($name, $queryFilter);

    abstract public function find($name, $queryFilter);

    abstract public function fetch($name, $queryFilter);

    abstract public function paginate($name, $queryFilter);
}