<?php
/**
 * Abstract class for a Model Manager
 * 
 * In the current design, there only is one Model object (Waf_Model),
 * but for the future we want to avoid coding against concrete implementations.
 * 
 * Therefor, common methods are in Waf_Model_ModelAbstract, and all classes
 * inside this component should be coded against this interface, not agains the 
 * concrete implementation.
 * 
 * Yes, what's in Waf_Model and what's in Waf_Model_ModelAbstract is rather
 * arbitrary at the moment, until we actually start implementing Mappers, Services
 * and Entities.
 * 
 * @category   Waf
 * @package    Waf_Model
 * @version    $Id: ModelAbstract.php 379 2010-08-06 13:46:21Z rick $
 */
abstract class Waf_Model_ModelAbstract extends Waf_Registry_RegisterableAbstract
{
    private $_optionKeys = array();
    private $_options = array();

    public function __construct($options = null)
    {
        if (null !== $options) {
            if (is_array($options)) {
                $this->setOptions($options);
            } else {
                throw new Waf_Model_Exception('Sorry, Waf_Model currently only accepts arrays as options');
            }
        }
    }

    /**
     * Set all the options as an array.
     * Note that this will overwrite existing options.
     * 
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $options = array_change_key_case($options, CASE_LOWER);
        $this->_options = $options;
        $this->_optionKeys = array_keys($options);
    }
    
    /**
     * Returns a key-value array containing all the *current* options.
     * The array may be empty if no options where set, but it may also
     * contain default values, so don't rely on this to reflect the explicitely
     * set options. Or the defaults for that matter, which may also be set
     * lazily later on.
     * 
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }
    
    /**
     * Set a single option
     * 
     * @param string $key
     * @param mixed $value
     */
    public function setOption($key, $value)
    {
        $this->_options[strtolower($key)] = $value;
        $this->_optionKeys[] = strtolower($key);
    }
    
    /**
     * Checks if the given option has been set.
     * 
     * @param string $key
     * @return bool
     */
    public function hasOption($key)
    {
        return in_array(strtolower($key), $this->_optionKeys);
    }
    
    /**
     * Returns the value of the given option.
     * Returns the default value if the option has not been set.
     * 
     * @param string $key - The requested option
     * @param mixed|null $default - The default value to return, NULL if none given
     * @return mixed|null
     */
    public function getOption($key, $default = null)
    {
        if ($this->hasOption($key)) {
            $options = $this->getOptions();
            $options = array_change_key_case($options, CASE_LOWER);
            return $options[strtolower($key)];
        }
        return $default;
    }
}