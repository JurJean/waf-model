<?php
/**
 * When you're pulling data in and out of a database, it's important to keep
 * track of what you've changed; otherwise, that data won't be written back into
 * the database. Similarly you have to insert new objects you create and remove
 * any objects you delete.
 *
 * You can change the database with each change to your object model, but this
 * can lead to lots of very small database calls, which ends up being very slow.
 *
 * @todo automatic detection of changes
 * @todo optimize
 * @category   Waf
 * @package    Waf_Model
 * @subpackage UnitOfWork
 * @version    $Id:$

 */
class Waf_Model_UnitOfWork
{
    /**
     * @var Waf_Model_EntityManager
     */
    private $_entityManager;

    /**
     * @var array Entities to insert
     */
    private $_inserts = array();

    /**
     * @var array Entities to update
     */
    private $_updates = array();

    /**
     * @var array Entities delete
     */
    private $_deletes = array();
    
    /**
     * @var array Query actions (sql queries for instance)
     */
    private $_querys = array();

    /**
     * @var array of open transactions indexed by StorageAdapter name
     */
    private $_transactions = array();

    /**
     * Constructor requires an EntityManager
     *
     * @see Waf_Model_EntityManager
     * @param Waf_Model_EntityManager $entityManager
     * @return void
     */
    public function __construct(Waf_Model_EntityManager $entityManager)
    {
        $this->_entityManager = $entityManager;
    }

    /**
     * Get the EntityManager that owns this UnitOfWork
     *
     * @return Waf_Model_EntityManager
     */
    public function getEntityManager()
    {
        return $this->_entityManager;
    }

    /**
     * Get Mapper for $entityName
     *
     * @param string $entityName
     * @return Waf_Model_Mapper_MapperAbstract
     */
    public function getMapper($entityName)
    {
        return $this->getEntityManager()->getMapper($entityName);
    }

    /**
     * Notify that $entity should be persisted. If the Entity has an Identity,
     * it will be scheduled for insert. Otherwise it will be scheduled for
     * update.
     *
     * @param Waf_Model_Entity_EntityAbstract $entity
     * @return Waf_Model_UnitOfWork
     */
    public function persist(Waf_Model_Entity_EntityAbstract $entity)
    {
        $this->_notifyPreHook('persist', $entity);

        if (null === $entity->getId()) {
            $this->scheduleForInsert($entity);
        } else {
            $this->scheduleForUpdate($entity);
        }

        $this->_notifyPostHook('persist', $entity);
        
        return $this;
    }

    /**
     * Notify that $entity should be removed. The entitiy gets scheduled for
     * delete.
     * 
     * @param Waf_Model_Entity_EntityAbstract $entity
     * @return Waf_Model_UnitOfWork
     */
    public function remove(Waf_Model_Entity_EntityAbstract $entity)
    {
        $this->_notifyPreHook('remove', $entity);
        $this->scheduleForDelete($entity);
        $this->_notifyPostHook('remove', $entity);

        return $this;
    }

    /**
     * Execute all scheduled operations, thus synchronizing the application
     * context to Storage
     *
     * @return Waf_Model_UnitOfWork
     */
    public function flush()
    {
        $this->_execute();
        $this->_commitTransactions();

        return $this;
    }

    /**
     * Method to detect if a transaction is open for given $storage
     *
     * @param Waf_Model_Storage_AdapterAbstract $storage
     * @return boolean
     */
    protected function _hasTransaction(Waf_Model_Storage_AdapterAbstract $storage)
    {
        return isset($this->_transactions[get_class($storage)]);
    }

    /**
     * This method helps to begin and keep track of transaction for given
     * $storage
     *
     * @param Waf_Model_Storage_AdapterAbstract $storage
     * @return void
     */
    protected function _beginTransaction(Waf_Model_Storage_AdapterAbstract $storage)
    {
        if (!$this->_hasTransaction($storage)) {
            $storage->beginTransaction();
            $this->_transactions[get_class($storage)] = $storage;
        }
    }

    /**
     * This method helps to commit all open transactions
     *
     * @return void
     */
    protected function _commitTransactions()
    {
        foreach ($this->_transactions as $storageName => $storage) {
            if ($this->_hasTransaction($storage)) {
                $storage->commitTransaction();
                unset($this->_transactions[$storageName]);
            }
        }
    }

    /**
     * Execute all pending operations
     *
     * @return void
     */
    private function _execute()
    {
        foreach ($this->_querys as $key => $query) {
            $this->_executeQuery($query);
            unset($this->_querys[$key]);
        }
        
        foreach (array('insert', 'update', 'delete') as $action) {
            $handle  = '_' . $action . 's';
            $execute = '_execute' . ucfirst($action);
            foreach ($this->$handle as $hash => $entity) {
                $mapper  = $this->getMapper($entity);
                $this->_beginTransaction($mapper->getStorageAdapter());

                $this->_notifyPreHook($action, $entity);
                $this->$execute($entity);
                $this->_notifyPostHook($action, $entity);
                unset($this->{$handle}[$hash]);
            }
        }

        if ($this->hasScheduledOperations()) {
            $this->_execute();
        }

        $this->_commitTransactions();
    }

    /**
     * check if there are oprations left to run
     *
     * @return boolean
     */
    public function hasScheduledOperations()
    {
        return $this->hasScheduledInserts() || $this->hasScheduledUpdates() || $this->hasScheduledDeletes();
    }

    /**
     * check if there are updates left to run
     *
     * @return boolean
     */
    public function hasScheduledUpdates()
    {
        return !empty($this->_updates);
    }

    /**
     * check if there are inserts left to run
     *
     * @return boolean
     */
    public function hasScheduledInserts()
    {
        return !empty($this->_inserts);
    }

    /**
     * check if there are deletes left to run
     *
     * @return boolean
     */
    public function hasScheduledDeletes()
    {
        return !empty($this->_deletes);
    }

    private function _notifyPreHook($hook, $entity)
    {
        $method    = 'pre' . ucfirst($hook);
        $interface = 'Waf_Model_Hookable_' . ucfirst($method);
        if ($entity instanceof $interface) {
            $entity->$method($this->getEntityManager());
        }
    }

    private function _notifyPostHook($hook, $entity)
    {
        $method    = 'post' . ucfirst($hook);
        $interface = 'Waf_Model_Hookable_' . ucfirst($method);
        if ($entity instanceof $interface) {
            $entity->$method($this->getEntityManager());
        }
    }
    
    /**
     * Execute query
     * 
     * @param mixed $query 
     * @return void
     */
    private function _executeQuery($query)
    {
        $this->getEntityManager()->getModel()->getStorageAdapter()->query($query);
    }

    /**
     * Execute scheduled inserts.
     *
     * The methods of the following Interfaces will be executed if an Entity
     * implements them:
     * Waf_Model_Hookable_PreInsert
     * Waf_Model_Hookable_PostInsert
     *
     * If a QueryCache is enabled, it will be informed about the change.
     * 
     * @return void
     */
    private function _executeInsert($entity)
    {
        $mapper = $this->getMapper($entity);
        $identityField = $mapper->getIdentity()->getPropertyName();
        $state = $mapper->getEntityGenerator()->generateState($entity);
        $state[$identityField] = $mapper->getStorageAdapter()->insert(
            $mapper->getStorageReference(),
            $mapper->toStorage($entity)
        );
        $entity = $mapper->getEntityGenerator()->generateEntity($state, $entity);
    }

    /**
     * Execute scheduled updates
     *
     * The methods of the following Interfaces will be executed if an Entity
     * implements them:
     * Waf_Model_Hookable_PreUpdate
     * Waf_Model_Hookable_PostUpdate
     *
     * If a QueryCache is enabled, it will be informed about the change.
     *
     * @todo fix identity handling when this is fixed in Mapper
     * @return void
     */
    private function _executeUpdate($entity)
    {
        $mapper = $this->getMapper($entity);
        $mapper->getStorageAdapter()->update(
            $mapper->getStorageReference(),
            $mapper->toStorage($entity),
            $entity->getId()
        );
    }

    /**
     * Execute scheduled deletes
     *
     * The methods of the following Interfaces will be executed if an Entity
     * implements them:
     * Waf_Model_Hookable_PreDelete
     * Waf_Model_Hookable_PostDelete
     *
     * If a QueryCache is enabled, it will be informed about the change.
     *
     * @todo fix identity handling when this is fixed in Mapper
     * @return void
     */
    private function _executeDelete($entity)
    {
        $mapper = $this->getMapper($entity);
        $mapper->getStorageAdapter()->delete(
            $mapper->getStorageReference(),
            $entity->getId()
        );
    }
    
    /**
     * Schedule query
     * 
     * @param mixed $query
     * @return Waf_Model_UnitOfWork 
     */
    public function scheduleQuery($query)
    {
        $this->_querys[] = $query;
        
        return $this;
    }

    /**
     * Schedule Entity to be inserted to Storage
     *
     * @param Waf_Model_Entity_EntityAbstract $entity
     * @return Waf_Model_UnitOfWork
     */
    public function scheduleForInsert(Waf_Model_Entity_EntityAbstract $entity)
    {
        $this->_inserts[spl_object_hash($entity)] = $entity;

        return $this;
    }
    
    /**
     * Is $query scheduled?
     * 
     * @param mixed $query
     * @return boolean
     */
    public function isScheduledQuery($query)
    {
        return in_array($query, $this->_querys);
    }

    /**
     * Is the Entity scheduled for insert?
     *
     * @param Waf_Model_Entity_EntityAbstract $entity
     * @return boolean
     */
    public function isScheduledForInsert(Waf_Model_Entity_EntityAbstract $entity)
    {
        return isset($this->_inserts[spl_object_hash($entity)]);
    }

    /**
     * Schedule Entity to be updated in Storage
     *
     * @param Waf_Model_Entity_EntityAbstract $entity
     * @return Waf_Model_UnitOfWork
     */
    public function scheduleForUpdate(Waf_Model_Entity_EntityAbstract $entity)
    {
        $this->_updates[spl_object_hash($entity)] = $entity;

        return $this;
    }

    /**
     * Is the Entity scheduled for update?
     *
     * @param Waf_Model_Entity_EntityAbstract $entity
     * @return boolean
     */
    public function isScheduledForUpdate(Waf_Model_Entity_EntityAbstract $entity)
    {
        return isset($this->_updates[spl_object_hash($entity)]);
    }

    /**
     * Schedule Entity to be deleted from Storage
     *
     * @param Waf_Model_Entity_EntityAbstract $entity
     * @return Waf_Model_UnitOfWork
     */
    public function scheduleForDelete(Waf_Model_Entity_EntityAbstract $entity)
    {
        $this->_deletes[spl_object_hash($entity)] = $entity;

        return $this;
    }

    /**
     * Is the Entity scheduled for delete?
     *
     * @param Waf_Model_Entity_EntityAbstract $entity
     * @return boolean
     */
    public function isScheduledForDelete(Waf_Model_Entity_EntityAbstract $entity)
    {
        return isset($this->_deletes[spl_object_hash($entity)]);
    }
}
