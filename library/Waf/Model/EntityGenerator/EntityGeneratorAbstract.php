<?php
/**
 * Abstract Entity Generator
 *
 * @category   Waf
 * @package    Waf_Model
 * @subpackage EntityGenerator
 * @version    $Id:$
 */
abstract class Waf_Model_EntityGenerator_EntityGeneratorAbstract
{
    /**
     * @var string
     */
    protected $_entityName;

    /**
     * Constructor expects $entityName to be passed
     *
     * @param string $entityName
     * @return void
     */
    public function __construct($entityName)
    {
        $this->_entityName = $entityName;
    }

    abstract public function generateEntity($state);

    abstract public function generateState($entity);
}