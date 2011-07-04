<?php 
/**
 * Defines the Interface for implementing "registerable" objects, i.e., objects 
 * that can put *only one* instance of themselves in the registry.
 * 
 * This obviates the need for singletons in many use cases whilst providing a 
 * standard way of having a single instance globally available via the Registry.
 * 
 * (If you want more than one instance in the registry, do it yourself.
 * Extending Registerable with the option of having more than one instance has 
 * virtually no advantages over calling Waf_Registry directly.)
 * 
 * @category   Waf
 * @package    Waf_Registry
 * @subpackage Registerable
 * @version    $Id: RegisterableInterface.php 51 2010-02-17 12:35:48Z cruisecontrol $
 */
interface Waf_Registry_RegisterableInterface
{
    public function register();
    public static function getRegistered();
    public static function isRegistered();
}
