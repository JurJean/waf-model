<?php
/**
 * Waf_Model Collection abstract class
 * 
 * Since there seems to be very little reason to customize this,
 * it is likely to be overkill to have anything more but a default
 * concrete implementation.
 * 
 * However, just so no other classes need to be changed if I'm wrong,
 * we code against Waf_Model_Collection_CollectionAbstract
 * 
 * @category   Waf
 * @package    Waf_Model
 * @version    $Id: CollectionAbstract.php 379 2010-08-06 13:46:21Z rick $
 */
class Waf_Model_Collection_CollectionAbstract extends ArrayObject
{
    /**
     * Convert the Collection and Entities within to an array
     *
     * @return array
     */
    public function toArray()
    {
        $results = array();

        foreach ($this->getArrayCopy() as $entity) {
            $results[] = $entity->toArray();
        }

        return $results;
    }
}