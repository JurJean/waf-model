<?php
/**
 * This class helps with the configuration of several Model components
 *
 * @category   Waf
 * @package    Waf_Model
 * @subpackage Configurator
 * @version    $Id: Configurator.php 495 2010-11-09 08:55:37Z jur $
 */
class Waf_Model_Configurator
{
    /**
     * Set constructor $options for $object - in addition to setOptions(),
     * $options is allowed to be null or a Zend_Config instance
     *
     * @param object $object The object to be configured
     * @param mixed $options
     * @return void
     */
    public static function setConstructorOptions($object, $options)
    {
        if (null === $options) {
            return;
        }

        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }

        if (is_callable(array($object, 'setOptions'))) {
            return $object->setOptions($options);
        }

        self::setOptions($object, $options);
    }

    /**
     * Set $options for $object. Prepends 'set' to every key in passed $options
     * and tries to call that method with the corresponding value
     *
     * @param $object The object to be configured
     * @param array $options
     * @return void
     */
    public static function setOptions($object, $options)
    {
        if (!is_object($object)) {
            throw new Waf_Model_Configurator_Exception(
                '$object must be a configurable object'
            );
        }

        if (!is_array($options)) {
            throw new Waf_Model_Configurator_Exception(
                '$options must be an array'
            );
        }

        foreach ($options as $key => $value) {
            $method = 'set'.ucfirst($key);
            if (is_callable(array($object, $method))) {
                $object->$method($value);
            }
        }
    }
}