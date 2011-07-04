<?php
/**
 * Resource description
 *
 * @category   
 * @package    
 * @subpackage Resource
 * @version    $Id: $
 */
class Waf_Model_Autoloader_Resource extends Zend_Loader_Autoloader_Resource
{
    /**
     * Call parent constructor and initialize default resource types
     *
     * @param mixed $options 
     */
    public function __construct($options)
    {
        parent::__construct($options);
        $this->initDefaultResourceTypes();
    }

    /**
     * Initialize default resource types
     *
     * @return void
     */
    public function initDefaultResourceTypes()
    {
        $this->addResourceTypes(array(
            'entities' => array(
                'namespace' => 'Model_Entity',
                'path'      => 'models/entities',
            ),
            'filters'  => array(
                'namespace' => 'Model_QueryFilter',
                'path'      => 'models/filters',
            ),
            'mappers'  => array(
                'namespace' => 'Model_Mapper',
                'path'      => 'models/mappers',
            ),
            'services' => array(
                'namespace' => 'Model_Service',
                'path'      => 'models/services',
            )
        ));
    }
}