<?php

/**
 * Interface to let several elements in Waf_Model (currently only the
 * UnitOfWork) know that a method should be called before or after a specific
 * operation.
 *
 * @category   Waf
 * @package    Waf_Model
 * @subpackage Hookable
 * @version    $Id:$
 */
interface Waf_Model_Hookable_PostInsert
{
    public function postInsert(Waf_Model_EntityManager $entityManager);
}