<?php
/**
 * MapperPropery handling Zend_Currency instances
 *
 * @category   Waf
 * @package    Waf_Model
 * @subpackage Mapper_Property_Currency
 * @version    $Id:$
 */
class Waf_Model_Mapper_Property_Currency
    extends Waf_Model_Mapper_Property_PropertyAbstract
{
    private $_valueFieldName;
    private $_currencyFieldName;

    public function setValueFieldName($valueFieldName)
    {
        $this->_valueFieldName = $valueFieldName;

        return $this;
    }

    public function getValueFieldName()
    {
        if (null === $this->_valueFieldName) {
            throw new Waf_Model_Mapper_Exception('Value field name not set');
        }

        return $this->_valueFieldName;
    }

    public function setCurrencyFieldName($currencyFieldName)
    {
        $this->_currencyFieldName = $currencyFieldName;

        return $this;
    }

    public function getCurrencyFieldName()
    {
        if (null === $this->_currencyFieldName) {
            throw new Waf_Model_Mapper_Exception('Currency field name not set');
        }

        return $this->_currencyFieldName;
    }

    public function toEntity($state)
    {
        $currency = new Zend_Currency();
        $currency->setValue(
            $state[$this->getValueFieldName()],
            $state[$this->getCurrencyFieldName()]
        );
        return $currency;
    }

    public function toStorage($state)
    {
        $currency = $state[$this->getPropertyName()];
        return array(
            $this->getValueFieldName()    => $currency->getValue(),
            $this->getCurrencyFieldName() => $currency->getShortName()
        );
    }
}