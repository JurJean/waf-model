<?php
/**
 * Abstract for a Waf_Model element
 * 
 * @category   Waf
 * @package    Waf_Model
 * @version    $Id: ElementAbstract.php 379 2010-08-06 13:46:21Z rick $
 */
abstract class Waf_Model_ElementAbstract
{
    /**
     * Holds the Model this Element belongs to.
     * 
     * @var Waf_Model_ModelAbstract
     */
    private $_model = null;
    
    public function __construct($options = null)
    {
        Waf_Model_Configurator::setConstructorOptions($this, $options);
    }
    
    public function setModel(Waf_Model_ModelAbstract $model)
    {
        $this->_model = $model;
        return $this;
    }
    
    public function getModel()
    {
        if (null === $this->_model) {
            throw new Waf_Model_Exception('No Model registered with this Element');
        }
        return $this->_model;
    }
}