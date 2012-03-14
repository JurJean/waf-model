<?php
/**
 * Maps another Entity to the mapped Entity by ID
 *
 * @category   Waf
 * @package    Waf_Model
 * @subpackage Mapper_Property_OneToOne
 * @version    $Id: $
 */
class Waf_Model_Mapper_Property_OneToOne
    extends Waf_Model_Mapper_Property_PropertyAbstract
{
    private $_relatedEntity;
    private $_relatedFieldName;

    /**
     * @var boolean
     */
    protected $_notNull = true;

    /**
     * Set wether the property is allowed to be NULL
     *
     * @param boolean $flag
     * @return Waf_Model_Mapper_Property_String
     */
    public function setNotNull($flag)
    {
        $this->_notNull = (bool) $flag;

        return $this;
    }

    /**
     * Set the Entity the property relates to
     *
     * @param string|Waf_Model_Entity_EntityAbstract $relatedEntity
     * @return Waf_Model_Mapper_Property_OneToOne
     */
    public function setRelatedEntity($relatedEntity)
    {
        if ($relatedEntity instanceof Waf_Model_Entity_EntityAbstract) {
            $relatedEntity = get_class($relatedEntity);
        }

        $this->_relatedEntity = $relatedEntity;

        return $this;
    }
    
    public function setRelatedFieldName($relatedFieldName)
    {
        $this->_relatedFieldName = $relatedFieldName;

        return $this;
    }

    public function getRelatedFieldName()
    {
        if (null === $this->_relatedFieldName) {
            $this->setRelatedFieldName('id');
        }

        return $this->_relatedFieldName;
    }

//    public function setLazyLoad($flag)
//    {
//        $this->_lazyLoad = (bool) $flag;
//        return $this;
//    }
//
//    public function isLazyLoaded()
//    {
//        return $this->_lazyLoad;
//    }

    /**
     * Get the Entity the property relates to
     *
     * @return string
     */
    public function getRelatedEntity()
    {
        if (null === $this->_relatedEntity) {
            throw new Waf_Model_Mapper_Exception(
                'Related Entity not set'
            );
        }

        return $this->_relatedEntity;
    }

    public function getRepository()
    {
        return $this->getModel()
            ->getEntityManager()
            ->getRepository($this->getRelatedEntity());
    }

    public function toStorage($state)
    {
        $value = $state[$this->getPropertyName()];
        if (is_null($value) && true !== $this->_notNull) {
            return null;
        }
        return $value->getId();
    }

    public function toEntity($state)
    {
        $value = $state[$this->getFieldName()];
        if (is_null($value) && true !== $this->_notNull) {
            return null;
        }
        return $this->getRepository()->find(
            new Waf_Model_QueryFilter_ByIdentity(
                $this->getRelatedFieldName(),
                $value
            )
        );
    }
}