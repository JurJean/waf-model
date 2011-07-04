<?php
/**
 * Generate Entities using injection - which is experimental at the moment
 *
 * As there's no need yet, this component is not complete and exists FOR YOU
 * TO FUCKING HELP WITH THIS SHIT FOR ONCE
 *
 * To be used with Waf_Model_Entity_Injectable instances ONLY.
 *
 * @category   Waf
 * @package    Waf_Model
 * @subpackage EntityGenerator_Inject
 * @version    $Id:$
 */
class Waf_Model_EntityGenerator_Inject
    extends Waf_Model_EntityGenerator_EntityGeneratorAbstract
{
    public function __construct($entityName)
    {
        parent::__construct($entityName);
        $this->_reflection = new ReflectionClass($this->_entityName);
        if (!$this->_reflection->isSubclassOf('Waf_Model_Entity_Injectable')) {
            throw new Waf_Model_EntityGenerator_Exception(
                'This entityGenerator can only be used in combination with '
                . 'Waf_Model_Entity_Injectable subclasses'
            );
        }
    }
    
    public function generateEntity($state)
    {

    }

    public function generateState($entity)
    {
        
    }
}