<?php
require_once 'Waf/Registry.php';

/**
 * 
 * @category   Waf
 * @package    Waf_Registry
 * @subpackage UnitTests
 * @version    $Id: RegistryTest.php 30 2010-01-25 20:50:35Z rick $
 */
class Waf_RegistryTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        /*
         * This is a bit dirty, since Zend_Registry should be "hidden",
         * but this is the only way to test this.
         * 
         * But since Waf_Registry does rely on Zend_Registry, and probably
         * always will, I see no point in contaminating 2 classes with these
         * dirty tricks to make it unit testable.
         * 
         */
        Zend_Registry::_unsetInstance();
    }
    
    /**
     * 
     * @expectedException Waf_Exception
     */
    public function testCloningForbidden()
    {
        $org = Waf_Registry::getInstance();
        $clone = clone $org;
    }
    
    public function testStaticSetStaticGet()
    {
        Waf_Registry::set('foo', 'bar');
        $this->assertEquals('bar', Waf_Registry::get('foo'));
    }
    
    public function testStaticSetObjectGet()
    {
        Waf_Registry::set('foo', 'baz');
        $this->assertEquals('baz', Waf_Registry::getInstance()->foo);
    }
    
    /*
     * No, you should not need to know Waf_Registry depends on Zend_Registry,
     * but we need somehow to test if the prefix hasn't been foo-barred.
     */
    public function testStaticSetGetFromZend()
    {
        Waf_Registry::set('foo', 'bat');
        $this->assertEquals('bat', Zend_Registry::get('Waf_foo'));
    }
    
    public function testActiveSet()
    {
        Waf_Registry::getInstance()->foo = 'bar';
        $this->assertEquals('bar', Waf_Registry::get('foo'));
    }
    
    public function testIsRegisteredTrue()
    {
        Waf_Registry::getInstance()->foo = 'bar';
        $this->assertTrue(Waf_Registry::isRegistered('foo'));
    }
    
    public function testIsRegisteredFalse()
    {
        $this->assertFalse(Waf_Registry::isRegistered('foo'));
    }
    
    public function testIssetTrue()
    {
        Waf_Registry::getInstance()->foo = 'bar';
        $this->assertTrue(isset(Waf_Registry::getInstance()->foo));
    }
    
    public function testIssetFalse()
    {
        $this->assertFalse(isset(Waf_Registry::getInstance()->foo));
    }

}