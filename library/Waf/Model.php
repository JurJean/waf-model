<?php
/**
 * Main Model Manager
 * 
 * Conventions:
 * 
 * - {basePath} = APPLICATION_PATH "/models" (unless explicitely changed by configuration)
 * - {prefix} = "Model_" (unless explicitely changed by configuration)
 * 
 * Model element conventions:
 * - {type} -> {basePath}/models/{type}/{Name}.php -> {prefix}_Model_{Type}_{Name}
 * 
 * For instance:
 * - Mappers -> {basePath}/models/mappers/{Name}.php -> {prefix}_Model_Mapper_{Name}
 * - Entities -> {basePath}/models/entities/{Name}.php -> {prefix}_Model_Entity_{Name}
 * - Services -> {basePath}/models/services/{Name}.php -> {prefix}_Model_Service_{Name}
 * 
 * Concrete example:
 * - entity "User" -> /models/entity/User.php -> Model_Entity_User
 * 
 * @category   Waf
 * @package    Waf_Model
 * @version    $Id: Model.php 547 2010-12-06 17:02:05Z jur $
 */
class Waf_Model extends Waf_Model_ModelAbstract
{
    /**
     * @var array
     */
    protected $_elements = array(
        'entity'  => array(
            'namespace' => 'Model_Entity',
            'path'      => 'models/entities',
            'default'   => 'Waf_Model_Entity'),
        'repository'  => array(
            'namespace' => 'Model_Repository',
            'path'      => 'models/repositories'),
        'filter'  => array(
            'namespace' => 'Model_QueryFilter',
            'path'      => 'models/filters'),
        'mapper'  => array(
            'namespace' => 'Model_Mapper',
            'path'      => 'models/mappers',
            'default'   => 'Waf_Model_Mapper'),
        'service' => array(
            'namespace' => 'Model_Service',
            'path'      => 'models/services'),
        'table'   => array(
            'namespace' => 'Model_DbTable',
            'path'      => 'models/DbTable',
            'default'   => 'Zend_Db_Table'),
        );

    /**
     * @var Waf_Model_Storage
     */
    private $_storage;

    private $_storageAdapter;

    /**
     * @var Waf_Model_EntityManager
     */
    protected $_entityManager;

    /**
     * @var Waf_Model_Mapper_Driver
     */
    protected $_mapperDriver;

    /**
     * @var array of Waf_Model_Mapper_MapperAbstract instances
     */
    protected $_mappers = array();

    /**
     * @var Waf_Model_Cache_Query
     */
    protected $_cacheFactory;

    /**
     * Defined by Waf_Registry_RegisterableInterface
     */
    public function register()
    {
        Waf_Registry::set('Waf_Model', $this);
    }

    /**
     * Get the unique instance of the Object from the Registry.
     *
     * Defined by Waf_Registry_RegisterableInterface
     * 
     * @return mixed - the object
     * @throws Waf_Exception if the object is not in the registry.
     */
    public static function getRegistered()
    {
        $class = 'Waf_Model';

        if (Waf_Registry::isRegistered($class)) {
            return Waf_Registry::get($class);
        } else {
            throw new Waf_Exception($class . ' not registered');
        }
    }

    /**
     * Defined by Waf_Registry_RegisterableInterface
     */
    public static function isRegistered()
    {
        return Waf_Registry::isRegistered('Waf_Model');
    }

    /**
     * Initialize the ResourceLoader for set BasePath and Prefix as explained in
     * the class docblock. For example:
     *
     * $model = new Waf_Model();
     * $model->setBasePath(APPLICATION_PATH . '/modules/default')
     *       ->setPrefix('Default')
     *       ->initResourceLoader();
     */
    public function initResourceLoader()
    {
        $types = array();
        foreach ($this->_elements as $name => $element) {
            $types[$name] = array(
                'path'      => $element['path'] . '/',
                'namespace' => $element['namespace']
            );
        }
        return new Zend_Loader_Autoloader_Resource(array(
            'basePath'      => $this->getBasePath(),
            'namespace'     => $this->getPrefix(),
            'resourceTypes' => $types,
        ));
    }

    /**
     * Sets the application path, so this class doesn't have
     * to rely on the global ZF APPLICATION_PATH constant
     *
     * @param string $path
     */
    public function setApplicationPath($path)
    {
        $this->setOption('applicationpath', $path);

        return $this;
    }

    /**
     * Returns the application path as set by setApplicationPath()
     * However, if this is not explicitely set, it will check if
     * APPLICATION_PATH has been defined, and use that instead.
     *
     * @return string
     */
    public function getApplicationPath()
    {
        if (!$this->hasOption('applicationpath')) {
            if (defined('APPLICATION_PATH')) {
                $this->setApplicationPath(APPLICATION_PATH);
            } else {
                throw new Waf_Model_Exception('No ApplicationPath was set. Cannot figure it out either.');
            }
        }
        return $this->getOption('applicationpath');
    }

    /**
     * Set the base path where the model classes can be found.
     * Note that this must be a complete path, like for instance /home/luser/application/models.
     *
     * @param string $path
     */
    public function setBasePath($path)
    {
        $this->setOption('basepath', $path);
    }

    /**
     * Returns the base path where the model classes can be found.
     *
     * If no path is set, it will try to create one by attaching /models to the applicationpath.
     *
     * @return string
     */
    public function getBasePath()
    {
        if (!$this->hasOption('basepath')) {
            $this->setBasePath($this->getApplicationPath() . '/models');
        }
        return $this->getOption('basepath');
    }

    /**
     * Sets the prefix for Model classes
     *
     * @param string $prefix
     */
    public function setPrefix($prefix)
    {
        $this->setOption('prefix', $prefix);
    }

    /**
     * Returns the prefix for Model classes (without underscores)
     *
     * @return string (default 'Model')
     */
    public function getPrefix()
    {
        if (!$this->hasOption('prefix')) {
            $this->setPrefix('Model');
        }
        return $this->getOption('prefix');
    }

    /**
     * Set the StorageHandler
     *
     * @param Waf_Model_Storage $storage
     * @return Waf_Model
     */
    public function setStorageHandler(Waf_Model_Storage $storage)
    {
        $this->_storage = $storage;
        return $this;
    }

    /**
     * Get the StorageHandler
     *
     * @return Waf_Model_Storage
     */
    public function getStorageHandler()
    {
        if (null === $this->_storage) {
            $this->_storage = new Waf_Model_Storage;
        }
        return $this->_storage;
    }

    /**
     * @todo is this one still needed? can be automagically fetched via Mappers
     */
    public function getStorageAdapter()
    {
        if (null === $this->_storageAdapter) {
            if ($this->getStorageHandler()->hasDefaultAdapter()) {
                $this->_storageAdapter = $this->getStorageHandler()->getDefaultAdapter();
            }
        }
        if (null === $this->_storageAdapter) {
            throw new Waf_Model_Exception('No Storage Adapter available');
        }
        return $this->_storageAdapter;
    }
    
    /**
     * Factory method for Waf_Model elements
     * 
     * @param string $name - the name of the element
     * @param string $type - the type of element 'entity/mapper/service'
     * @param array|null $options - options to be passed to the element
     * @return mixed - the instatiated element
     */
    public function elementFactory($name, $type, $options = null)
    {
        if (null !== $options) {
            $options = array_change_key_case($options, CASE_LOWER);
        }
        if (file_exists($this->_getClassPath($name, $type))) {
            $class = $this->_getClassName($name, $type);
            $object = new $class($options);
        } elseif ($this->_hasDefaultClass($type)) {
            $class = $this->_getDefaultClassName($type);
//            if (!isset($options['name'])) {
//                $options['name'] = $name;
//            }
            $object = new $class($options);
        } else {
            throw new Waf_Model_Exception('Could not load ' . $type . ' class for "' . $name . '"');
        }
        if (method_exists($object, 'setModel')) {
            $object->setModel($this);
        }
        return $object;
    }
    
    /**
     * Creates a new Entity Object.
     * 
     * Based op the settings and the $name, it will first try to find a custom Entity class,
     * and if that fails, it will return a default Entity object, if such a default is configured
     * 
     * @param string $name - name of the Entity, also used to find a custom Entity
     * @param array|null $options - options to be passed to the Entity
     * @return object
     */
    public function getEntity($name, $options = null)
    {
        return $this->elementFactory($name, 'entity', $options);
    }

    /**
     * Set MapperDriver, not required but allows to parse Mapper configurations
     * on the fly using the various implementations
     *
     * @param Waf_Model_Mapper_Driver_DriverAbstract $mapperDriver
     * @return Waf_Model
     */
    public function setMapperDriver(Waf_Model_Mapper_Driver_DriverAbstract $mapperDriver)
    {
        $this->_mapperDriver = $mapperDriver;

        return $this;
    }

    /**
     * Is a MapperDriver set?
     *
     * @return boolean
     */
    public function hasMapperDriver()
    {
        return null !== $this->_mapperDriver;
    }

    /**
     * Get Waf_Model_Mapper_Driver_DriverAbstract instance
     *
     * @return Waf_Model_Mapper_Driver_DriverAbstract
     * @throws Waf_Model_Exception if no MapperDriver is set
     */
    public function getMapperDriver()
    {
        if (!$this->hasMapperDriver()) {
            throw new Waf_Model_Exception('MapperDriver not set');
        }
        
        return $this->_mapperDriver;
    }

    /**
     * Add Mapper:
     * - if $name is an instance of Waf_Model_Mapper_MapperAbstract, it will be
     *   added
     * - if a MapperDriver is set, $name is treated as an EntityName and used to
     *   create a new Mapper
     * - if $name is a string a mapper is created in the elementFactory() using
     *   $options
     *
     * @param string|Waf_Model_Mapper_MapperAbstract $name
     * @param null|array $options
     * @return Waf_Model
     */
    public function addMapper($name, $options)
    {
        if ($options instanceof Waf_Model_Mapper_MapperAbstract) {
            $mapper = $options;
            $mapper->setModel($this);
        } else {
            if ($this->hasMapperDriver()) {
                $mapper = $this->getMapperDriver()->getMapper($name);
                $mapper->setModel($this);
            } else {
                $mapper = $this->elementFactory($name, 'mapper', $options);
            }
        }
        
        $this->_mappers[$name] = $mapper;

        return $this;
    }

    /**
     * Get mapper by $name. Usually one will use the actual Entity or EntityName
     * to get the mapper for it. If the mapper is not present, it tries to
     * lazyload via addMapper($name, $options)
     * 
     * @param string $name - name of the Entity, also used to find a custom Mapper
     * @param array|null $options - options to be passed to the Mapper
     * @return object
     */
    public function getMapper($name, $options = null)
    {
        if ($name instanceof Waf_Model_Entity_EntityAbstract) {
            $name = get_class($name);
        }
        
        if (!isset($this->_mappers[$name])) {
            $this->addMapper($name, $options);
        }

        return $this->_mappers[$name];
    }

    /**
     * Set the EntityManager
     *
     * @see Waf_Model_EntityManager
     * @param Waf_Model_EntityManager $entityManager
     * @return Waf_Model
     */
    public function setEntityManager(Waf_Model_EntityManager $entityManager)
    {
        $this->_entityManager = $entityManager;
        $this->_entityManager->setModel($this);

        return $this;
    }

    /**
     * Get the EntityManager - if not set it defaults to Waf_Model_EntityManager
     *
     * @return Waf_Model_EntityManager
     */
    public function getEntityManager()
    {
        if (null === $this->_entityManager) {
            $this->setEntityManager(
                new Waf_Model_EntityManager()
            );
        }

        return $this->_entityManager;
    }
    
    /**
     * Creates a new Service Object.
     * 
     * Based op the settings and the $name, it will first try to find a custom Service class,
     * and if that fails, it will return a default Service object, if such a default is configured
     * 
     * @param string $name - name of the Entity, also used to find a custom Service
     * @param array|null $options - options to be passed to the Service
     * @return object
     */
    public function getService($name, $options = null)
    {
        return $this->elementFactory($name, 'service', $options);
    }
    
    protected function _hasDefaultClass($type)
    {
        return isset($this->_elements[$type]['default']);
    }
    
    protected function _getDefaultClassName($type)
    {
        return $this->_elements[$type]['default'];
    }
    
    protected function _getClassName($name, $type)
    {
        return $this->getElementNamespace($type) . '_' . ucfirst($name);
    }
    
    protected function _getClassPath($name, $type)
    {
        return $this->getBasePath() . '/' . $this->_getClassDir($type) . '/' . ucfirst($name) . '.php';
    }
    
    public function getElementNamespace($type)
    {
        return $this->getPrefix() . '_' . $this->_elements[$type]['namespace'];
    }
    
    /**
     * Returns the directory name (not the full path!) where classes of the given type can be found.
     * 
     * @param string $type (for instance: 'entitiy'/'service'/'mapper'
     * @return string
     */
    protected function _getClassDir($type)
    {
        return $this->_elements[$type]['path'];
    }
}