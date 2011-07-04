<?php
/**
 * Base mapper managing all different mappings
 * - Identity
 * - Properties (this name is to be discussed)
 *
 * Naming conventions are as follows.
 * - Entities have EntityNames
 * - Entities have Properties
 * - Storage has ReferenceNames
 * - Storage has Fields
 *
 * @category   Waf
 * @package    Waf_Model
 * @subpackage Mapper
 * @version    $Id: $
 */
class Waf_Model_Mapper extends Waf_Model_Mapper_MapperAbstract
{
    /**
     * @var Waf_Model_EntityGenerator
     */
    private $_entityGenerator;

    /**
     * @var Waf_Model_Mapper_Identity
     */
    private $_identity;

    /**
     * @var Waf_Model_Mapper_Property
     */
    private $_propertyMapper;

    /**
     * @var Waf_Model_Storage_AdapterAbstract
     */
    private $_storageAdapter;

    /**
     * @var string
     */
    private $_entityName;

    /**
     * @var Waf_Model_EntityManager
     */
    private $_entityManager;

    /**
     * @var string
     */
    private $_storageReference;

    /**
     * Constructor - configures the Mapper
     *
     * @param array $options
     * @return void
     */
    public function __construct($options = null)
    {
        Waf_Model_Configurator::setConstructorOptions($this, $options);
    }

    public function setOptions(array $options)
    {
        if (!isset($options['identity'])) {
            $options['identity'] = array();
        }
        $this->setIdentity($options['identity']);
        unset($options['identity']);

        Waf_Model_Configurator::setOptions($this, $options);
    }

    /**
     * Recursively set Waf_Model instance - passes Waf_Model to the
     * IdentityMapper and PropertyMapper
     *
     * @param Waf_Model_ModelAbstract $model
     * @return Waf_Model_Mapper
     */
    public function setModel(Waf_Model_ModelAbstract $model)
    {
        parent::setModel($model);
        $this->getIdentity()->setModel($model);
        $this->getPropertyMapper()->setModel($model);

        return $this;
    }

    /**
     * Set EntityManager instance
     * 
     * @param Waf_Model_EntityManager $entityManager
     * @return Waf_Model_Mapper
     */
    public function setEntityManager(Waf_Model_EntityManager $entityManager)
    {
        $this->_entityManager = $entityManager;
        return $this;
    }

    /**
     * Get EntityManager instance, lazy loads from Waf_Model
     *
     * @return Waf_Model_EntityManager
     */
    public function getEntityManager()
    {
        if (null === $this->_entityManager) {
            $this->setEntityManager(
                $this->getModel()->getEntityManager()
            );
        }

        return $this->_entityManager;
    }

    /**
     * Set Waf_Model_Mapper_Serializer instance
     *
     * @param Waf_Model_Mapper_Serializer $serializer
     * @return Waf_Model_Mapper
     */
    public function setEntityGenerator(
        Waf_Model_EntityGenerator_EntityGeneratorAbstract $entityGenerator)
    {
        $this->_entityGenerator = $entityGenerator;

        return $this;
    }

    /**
     * Get Waf_Model_Mapper_Serializer instance
     *
     * @return Waf_Model_Mapper_Serializer
     */
    public function getEntityGenerator()
    {
        if (null === $this->_entityGenerator) {
            $this->setEntityGenerator(
                new Waf_Model_EntityGenerator_Reflect($this->getEntityName())
            );
        }

        return $this->_entityGenerator;
    }

    /**
     * Set the EntityName this mapper is working for
     *
     * @param string $entityName
     * @return Waf_Model_Mapper
     */
    public function setEntityName($entityName)
    {
        $this->_entityName = $entityName;

        return $this;
    }

    /**
     * Get the EntityName this mapper is working for
     *
     * @return string
     */
    public function getEntityName()
    {
        if (null === $this->_entityName) {
            throw new Waf_Model_Mapper_Exception(
                'EntityName is not set'
            );
        }

        return $this->_entityName;
    }

    /**
     * Set the reference Storage uses
     *
     * @param string $storageReference
     * @return Waf_Model_Mapper
     */
    public function setStorageReference($storageReference)
    {
        $this->_storageReference = $storageReference;

        return $this;
    }

    /**
     * Get the reference Storage uses
     *
     * @return string
     */
    public function getStorageReference()
    {
        if (null === $this->_storageReference) {
            $filter = new Zend_Filter_Word_CamelCaseToUnderscore();
            $this->setStorageReference(
                strtolower($filter->filter(
                    $this->getEntityName()
                ))
            );
        }

        return $this->_storageReference;
    }

    /**
     * Set StorageAdapter to use with this Mapper
     *
     * @param Waf_Model_Storage_AdapterAbstract $storageAdapter
     * @return self Fluent interface
     */
    public function setStorageAdapter($storageAdapter)
    {
        $this->_storageAdapter = $storageAdapter;

        return $this;
    }

    /**
     * Get the StorageAdapter for this Mapper, defaults to the default
     * StorageAdapter from the StorageHandler
     *
     * @return Waf_Model_Storage_AdapterAbstract
     */
    public function getStorageAdapter()
    {
        if (is_string($this->_storageAdapter)) {
            $this->_storageAdapter = $this->getModel()
                ->getStorageHandler()
                ->getAdapter($this->_storageAdapter);
        }
        
        if (null === $this->_storageAdapter) {
            $this->setStorageAdapter($this->getModel()->getStorageAdapter());
        }
        
        if (!$this->_storageAdapter instanceof Waf_Model_Storage_AdapterAbstract) {
            throw new Waf_Model_Mapper_Exception(
                '$storageAdapter must be an instance of Waf_Model_Storage_AdapterAbstract'
            );
        }

        return $this->_storageAdapter;
    }

    /**
     * Set the Identity
     *
     * @param Waf_Model_Mapper_Identity $identity
     * @return Waf_Model_Mapper
     */
    public function setIdentity($identity)
    {
        if (is_array($identity)) {
            $identity = new Waf_Model_Mapper_Identity($identity);
        }

        $this->_identity = $identity;

        return $this;
    }

    /**
     * Get the Identity
     *
     * @return Waf_Model_Mapper_Identity
     */
    public function getIdentity()
    {
        if (null === $this->_identity) {
            $this->setIdentity(new Waf_Model_Mapper_Identity());
        }

        return $this->_identity;
    }

    /**
     * Set the PropertyMapper
     *
     * @param Waf_Model_Mapper_Property $propertyMapper
     * @return Waf_Model_Mapper
     */
    public function setPropertyMapper($propertyMapper)
    {
        if (is_array($propertyMapper)) {
            $propertyMapper = new Waf_Model_Mapper_Property(
                $propertyMapper
            );
        }

        $this->_propertyMapper = $propertyMapper;

        return $this;
    }

    /**
     * Get the PropertyMapper
     *
     * @return Waf_Model_Mapper_Property
     */
    public function getPropertyMapper()
    {
        if (null === $this->_propertyMapper) {
            $this->setPropertyMapper(array());
        }

        return $this->_propertyMapper;
    }

    /**
     * Convert $state to the defined Entity
     *
     * @param array $state
     * @param null|Waf_del_Entity_EntityAbstract $entity
     * @return Waf_Model_Entity_EntityAbstract
     */
    public function toEntity($state, $entity = null)
    {
        $id = $state[$this->getIdentity()->getFieldName()];
        $identityMap = $this->getEntityManager()
            ->getIdentityMap($this->getEntityName());

        if ($identityMap->contains($id)) {
            return $identityMap->get($id);
        }
        
        $mapped = $this->getIdentity()->toEntity($state);
        $mapped += $this->getPropertyMapper()->toEntity($state);

        return $this->getEntityGenerator()->generateEntity($mapped);
    }

    /**
     * Convert an Entity to an array prepared for Storage
     *
     * @param entityName $state
     */
    public function toStorage($state)
    {
        $entityName = $this->getEntityName();
        if (!$state instanceof $entityName) {
            throw new Waf_Model_Mapper_Exception(sprintf(
                'Invalid Entity: %s expected, %s given',
                $entityName,
                get_class($state)
            ));
        }

        $state  = $this->getEntityGenerator()->generateState($state);
        $mapped = $this->getIdentity()->toStorage($state);
        $mapped += $this->getPropertyMapper()->toStorage($state);

        return $mapped;
    }

    /**
     * Map a multi dimensional array to a Collection of Entities
     *
     * @param array $data
     * @return Waf_Model_Collection_CollectionAbstract
     */
    public function toCollection(array $data)
    {
        $collection = new Waf_Model_Collection();

        foreach ($data as $i => $row) {
            $collection->offsetSet($i, $this->toEntity($row));
        }

        return $collection;
    }

    /**
     * Count Entities matching the QueryFilter
     *
     * @param mixed $queryFilter
     * @return integer
     */
    public function count($queryFilter)
    {
        return $this->getStorageAdapter()->count(
            $this->getStorageReference(),
            $queryFilter
        );
    }

    /**
     * Find Entity matching $queryFilter. Proxies the $queryFilter to the
     * StorageAdapter and returns the value of toEntity() if successfull.
     *
     * @param mixed $queryFilter
     * @return null|Waf_Model_Entity_EntityAbstract
     */
    public function find($queryFilter)
    {
        $result = $this->getStorageAdapter()->find(
            $this->getStorageReference(),
            $queryFilter
        );

        return (false === $result) ? null : $this->toEntity($result);
    }


    /**
     * Fetch a Collection of Entities matching $queryFilter. Proxies the
     * $queryFilter to the StorageAdapter and returns the value of
     * toCollection() if successfull.
     *
     * @param mixed $queryFilter
     * @return null|Waf_Model_Entity_EntityAbstract
     */
    public function fetch($queryFilter)
    {
        $result = $this->getStorageAdapter()->fetch(
            $this->getStorageReference(),
            $queryFilter
        );

        $result = (false === $result) ? null : $this->toCollection($result);
        
        return $result;
    }

    /**
     * Fetch paginated Entities matching the QueryFilter
     *
     * @param mixed $queryFilter
     * @return Zend_Paginator
     */
    public function paginate($queryFilter)
    {
        $paginatorAdapter = $this->getStorageAdapter()->paginate(
            $this->getStorageReference(),
            $queryFilter
        );
        $paginatorAdapter->setMapper($this);
        return new Zend_Paginator($paginatorAdapter);
    }

    /**
     * Synchronize an Entity with the state of Storage
     *
     * @param Waf_Model_Entity_EntityAbstract $entity
     * @return Waf_Model_Entity_EntityAbstract
     */
    public function refresh(Waf_Model_Entity_EntityAbstract $entity)
    {
        $property = $this->getIdentity()->getStorageReference();
        $state = $this->getEntityGenerator()->generateState($entity);

        $data = $this->getStorageAdapter()->find(
            $this->getStorageReference(),
            $state[$property]
        );

        return $this->getEntityGenerator()->generateEntity($state);
    }
}