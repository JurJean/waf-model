<?php
/**
 * Extends the DbSelect adapter provided by ZF and includes the remaining
 * Waf_Model logic
 *
 * @category   Waf
 * @package    Waf_Model
 * @subpackage Paginator_Adapter_ZendDb
 * @version    $Id:$
 */
class Waf_Model_Paginator_Adapter_ZendDb extends Zend_Paginator_Adapter_DbSelect
    implements Waf_Model_Paginator_Adapter_AdapterInterface
{
    private $_mapper;

    public function setMapper(Waf_Model_Mapper_MapperAbstract $mapper)
    {
        $this->_mapper = $mapper;

        return $this;
    }

    public function getMapper()
    {
        if (null === $this->_mapper) {
            throw new Waf_Model_Paginator_Exception(
                'No mapper set'
            );
        }

        return $this->_mapper;
    }

    public function getItems($offset, $itemCountPerPage)
    {
        $items = parent::getItems($offset, $itemCountPerPage);

        foreach ($items as $key => $data) {
            $items[$key] = $this->getMapper()->toEntity($data);
        }

        return $items;
    }
}