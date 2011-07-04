<?php
/**
 * Do something on PreRemove
 *
 * @author jur
 */
interface Waf_Model_Hookable_PreRemove
{
    public function preRemove(Waf_Model_EntityManager $entityManager);
}