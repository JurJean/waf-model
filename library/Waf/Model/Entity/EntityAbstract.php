<?php
/**
 * EntityAbstract for the Waf_Model component
 *
 * An Entity represents about anything in the Model which has an Identity. All
 * properties defined as protected can be persisted to Storage.
 *
 * @category   Waf
 * @package    Waf_Model
 * @subpackage Entity
 * @version    $Id: EntityAbstract.php 526 2010-11-15 18:03:48Z jur $
 */
abstract class Waf_Model_Entity_EntityAbstract
    implements Waf_Model_Entity_EntityInterface
{
    /**
     * Waf_Model_Entity_EntityAbstract forces the use of Getters by catching all
     * calls to undefined properties.
     *
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        $method = 'get'.ucfirst($property);
        if (!method_exists($this, $method)) {
            throw new Waf_Model_Entity_Exception(sprintf(
                'No getter exists for property %s',
                $property
            ));
        }

        return $this->$method();
    }

    /**
     * Process an associative array through all defined getters. If a 'hasser'
     * exists which returns false, the getter is skipped.
     *
     * @return array
     */
    public function toArray()
    {
        $result = array();
        foreach (get_class_vars(get_class($this)) as $property => $value) {
            $name   = ltrim($property, '_');
            $hasMethod = 'has' . ucfirst($name);
            $getMethod = 'get' . ucfirst($name);

            if (method_exists($this, $getMethod)) {
                if ((method_exists($this, $hasMethod))
                    && (!$this->$hasMethod())) {
                    continue;
                }
                $result[$name] = $this->$getMethod();
            }
        }

        return $result;
    }
}