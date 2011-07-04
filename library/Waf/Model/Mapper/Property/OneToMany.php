<?php
/**
 * Handle OneToMany properties
 *
 * Converts related ids to a Waf_Model_Collection
 *
 * @todo toStorage is not supported, and is debatable if we want it to
 * @category   Waf
 * @package    Waf_Model
 * @subpackage Mapper_Property_OneToMany
 * @version    $Id: $
 */
class Waf_Model_Mapper_Property_OneToMany extends Waf_Model_Mapper_Property_OneToOne
{
    protected $_relatedFieldName;

    public function setRelatedFieldName($relatedFieldName)
    {
        $this->_relatedFieldName = $relatedFieldName;

        return $this;
    }

    public function getRelatedFieldName()
    {
        if (null === $this->_relatedFieldName) {
            throw new Waf_Model_Mapper_Exception(
                'Related field not set'
            );
        }

        return $this->_relatedFieldName;
    }

    public function toEntity($state)
    {
        $repository = $this->getRepository();
        $repository->setQueryFilter(
            new Waf_Model_Mapper_Property_OneToMany_QueryFilter($this, $state)
        );
        return $repository;
    }

    public function toStorage($state)
    {
        return null;
    }

    public function isStoraged()
    {
        return false;
    }
}