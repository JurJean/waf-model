<?php
/**
 * InjectableEntity should be used in combination with
 * Waf_Model_EntityGenerator_Inject on environments with PHP versions lower than
 * 5.3.
 *
 * The use of
 *
 * @author     ASOclusive
 * @category   
 * @package    
 * @subpackage 
 * @version    $Id:$
 */
class Waf_Model_Entity_Injectable extends Waf_Model_Entity
{
    /**
     * @var ReflectionClass
     */
    private $_reflection;
    
    /**
     * Set $state of the entity. All values in $state are directly injected in
     * protected properties, bypassing the defined Setters.
     *
     * The beginning underscore are added to the keys in $state before injecting.
     *
     * This method will be removed when we move to PHP5.3 as explained in the
     * class docblock
     *
     * @param mixed $state
     * @return Waf_Model_Entity_EntityAbstract
     */
    final public function __setState(array $state)
    {
        foreach ($state as $property => $value) {
            $property = $property;

            if (!$this->_getReflection()->hasProperty($property)) {
                throw new Waf_Model_Entity_Exception(sprintf(
                    'Property %s not defined',
                    $property
                ));
            }

            $this->$property = $value;
        }

        return $this;
    }

    /**
     * Get the State of the Entity. All values are directly retrieved from the
     * protected properties, bypassing the defined Getters.
     *
     * The beginning underscore of the property names are stripped.
     *
     * This method will be removed when we move to PHP5.3 as explained in the
     * class docblock.
     *
     * @param $state
     */
    final public function __getState()
    {
        $result     = array();
        $properties = $this->_getReflection()->getProperties(
            ReflectionProperty::IS_PROTECTED
        );

        foreach ($properties as $property) {
            $result[$property->name] = $this->{$property->name};
        }

        return $result;
    }

    /**
     * Get ReflectionClass for this Entity
     *
     * @return ReflectionClass
     */
    private function _getReflection()
    {
        if (null === $this->_reflection) {
            $this->_reflection = new ReflectionClass($this);
        }

        return $this->_reflection;
    }
}