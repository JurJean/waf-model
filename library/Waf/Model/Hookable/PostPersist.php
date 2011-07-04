<?php
/**
 * Do something on PostPersist
 *
 * @author jur
 */
interface Waf_Model_Hookable_PostPersist
{
    public function postPersist(Waf_Model_EntityManager $entityManager);
}