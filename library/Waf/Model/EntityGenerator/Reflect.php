<?php
/**
 * Generate Entities / EntityStates using reflection
 *
 * For use with PHP versions equal to of higher than 5.3
 *
 * @category   Waf
 * @package    Waf_Model
 * @subpackage EntityGenerator
 * @version    $Id:$
 */
class Waf_Model_EntityGenerator_Reflect
    extends Waf_Model_EntityGenerator_EntityGeneratorAbstract
{
    protected $_reflection;
    protected $_reflectionProperties = array();

    public function __construct($entityName)
    {
        parent::__construct($entityName);
        $this->_reflection = new ReflectionClass($this->_entityName);
        $this->_reflectionProperties = $this->_reflection->getProperties();
    }

    /**
     * Generate Entity from $state
     *
     * @param array $state
     * @return Waf_Model_Entity_EntityInterface
     */
    public function generateEntity($state, $entity = null)
    {
        if (null === $entity) {
            $entity = $this->_wakeup();
        }
        
        foreach ($this->_reflectionProperties as $property) {
            $propertyName = $property->getName();
            if (!isset($state[$propertyName])) {
                continue;
            }

            $property->setAccessible(true);
            $property->setValue($entity, $state[$propertyName]);
        }
        return $entity;
    }

    /**
     * Generate state from given Entity
     *
     * @param Waf_Model_Entity_EntityInterface $entity
     * @return array
     */
    public function generateState($entity)
    {
        $result = array();
        foreach ($this->_reflectionProperties as $property) {
            $property->setAccessible(true);
            $result[$property->getName()] = $property->getValue($entity);
        }
        return $result;
    }

    /**
     * Wake an Entity up using the most simple serialize string possible
     * (bypassing the constructor)
     *
     * @return Waf_Model_Entity_EntityInterface
     */
    protected function _wakeUp()
    {
        return unserialize(
            sprintf(
                'O:%d:"%s":0:{}',
                strlen($this->_entityName),
                $this->_entityName
            )
        );
    }
}