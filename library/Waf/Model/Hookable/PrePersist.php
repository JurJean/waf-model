<?php
/**
 * Do something on PrePersist
 *
 * @author jur
 */
interface Waf_Model_Hookable_PrePersist
{
    public function prePersist(Waf_Model_EntityManager $entityManager);
}