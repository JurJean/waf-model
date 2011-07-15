<?php
/**
 * Resource to initialize a Waf_Model
 *
 * Main options:
 *
 * - class : the Waf_Model_ModelAbstract class to use, defaults to Waf_Model
 * - basePath : the base path to the models, defaults to APPLICATION_PATH /models
 * - prefix
 * - storage
 *  - zendDb = true/false : use the multidb and/or db resources to initialize the storage adapters (default true)
 *
 * Besides 'class', all options are passed to the Waf_Model constructor
 *
 * @category   Waf
 * @package    Waf_Zend_Application
 * @package    Waf_Model
 * @subpackage Resource
 * @version    $Id: Model.php 379 2010-08-06 13:46:21Z rick $
 */
class Waf_Zend_Application_Resource_Model extends Zend_Application_Resource_ResourceAbstract
{
    protected $_model = null;

    public function init()
    {
        $model   = $this->getModel();
        $options = $this->getOptions();

        if (isset($options['storage'])) {
            $storageOptions = $options['storage'];
            unset($options['storage']);
        } else {
            $storageOptions = array('zendDb' => true);
        }
        $storage = $this->_getStorage($storageOptions);


        if (null !== $storage) {
            $this->_model->setStorageHandler($storage);
        }

        $this->_model->register();
        $this->_model->initResourceLoader();

        $this->initMapperDriver();

        if (isset($options['cache']) && $options['cache']) {
            $this->initCache();
        }

        return $this;
    }

    public function getModel()
    {
        if (null === $this->_model) {
            $class = 'Waf_Model';
            $options = $this->getOptions();

            if (isset($options['class'])) {
                $class = $options['class'];
                unset($options['class']);
            }

            if (!isset($options['basePath']) && defined('APPLICATION_PATH')) {
                $options['basePath'] = APPLICATION_PATH;
            }

            if (!isset($options['prefix'])) {
                $options['prefix'] = '';
            }

            $this->_model = new $class($options);
            $this->_model->register();
            $this->_model->initResourceLoader();
        }
        
        return $this->_model;
    }

    protected $_storage = null;


    protected function _getStorage($options)
    {
        $bootstrap = $this->getBootstrap();
        if (isset($options['zendDb']) && $options['zendDb']) {

            if ($bootstrap->hasPluginResource('multidb')) {
                $bootstrap->bootstrap('multidb');
                $multidb = $bootstrap->getResource('multidb');
                $multidbOptions = $multidb->getOptions();
                foreach ($multidbOptions as $db => $options) {
                    $this->_addZendDbStorageAdapter($db, $multidb->getDb($db), $multidb->isDefault($db));
                }
            }

            if ($bootstrap->hasPluginResource('db')) {
                $bootstrap->bootstrap('db');
                $dbAdapter = $bootstrap->getResource('db');
                $this->_addZendDbStorageAdapter(
                    'db', $dbAdapter,
                    $bootstrap->getPluginResource('db')->isDefaultTableAdapter()
                );
            }
        }
        return $this->_storage;
    }

    protected function _addZendDbStorageAdapter($name, Zend_Db_Adapter_Abstract $db, $isDefault = false)
    {
        if (null === $this->_storage) {
            $this->_storage = new Waf_Model_Storage;
        }

        $adapter = new Waf_Model_Storage_Adapter_ZendDb();
        $adapter->setConnection($db);
        $this->_storage->addAdapter($adapter, $name);
        if ($isDefault) {
            $this->_storage->setDefaultAdapter($name);
        }
    }

    /**
     * Initialize the MapperDriver
     * Defaults to Waf_Model_Mapper_Driver_Config using ini configuration files.
     * Default path is the basePath()/models/mappers/configs
     * 
     * @return Waf_Model_Mapper_Driver_DriverAbstract
     */
    public function initMapperDriver()
    {
        $options = $this->getOptions();
        $model   = $this->getModel();

        if (!isset($options['mapperDriver'])) {
            $options['mapperDriver'] = array();
        }
        if (!isset($options['mapperDriver']['type'])) {
            $options['mapperDriver']['type'] = 'Waf_Model_Mapper_Driver_Zend_Config';
        }
        if (!isset($options['mapperDriver']['configType'])) {
            $options['mapperDriver']['configType'] = 'ini';
        }
        if (!isset($options['mapperDriver']['configPaths'])) {
            $options['mapperDriver']['configPaths'][] = $model->getBasePath() . '/models/mappers/configs';
        }
        
        $mapperDriverClass = $options['mapperDriver']['type'];
        unset($options['mapperDriver']['type']);
        $mapperDriver = new $mapperDriverClass($options['mapperDriver']);
        $this->getModel()->setMapperDriver($mapperDriver);

        return $mapperDriver;
    }

    public function initCache()
    {
        $options = $this->getOptions();
        if (!isset($options['cache'])) {
            return;
        }

        $this->getBootstrap()->bootstrap('cachemanager');
        $cachemanager = $this->getBootstrap()->getResource('cachemanager');

        if (isset($options['cache']['query'])) {
            $queryCache = new Waf_Model_Cache_Query();
            $queryCache->setCache(
                $cachemanager->getCache($options['cache']['query'])
            );
            $this->getModel()->setQueryCache($queryCache);
        }
    }
}