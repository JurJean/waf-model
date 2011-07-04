<?php
/**
 * Do something on PreRemove
 *
 * @author jur
 */
interface Waf_Model_Hookable_PostRemove
{
    public function postRemove(Waf_Model_EntityManager $entityManager);
}